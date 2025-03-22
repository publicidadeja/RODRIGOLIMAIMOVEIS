<?php

namespace Srapid\RealEstate\Http\Controllers;

use Illuminate\Http\Request;
use Srapid\Base\Events\BeforeEditContentEvent;
use Srapid\Base\Events\CreatedContentEvent;
use Srapid\Base\Events\DeletedContentEvent;
use Srapid\Base\Events\UpdatedContentEvent;
use Srapid\Base\Forms\FormBuilder;
use Srapid\Base\Http\Controllers\BaseController;
use Srapid\Base\Http\Responses\BaseHttpResponse;
use Srapid\RealEstate\Forms\CrmForm;
use Srapid\RealEstate\Http\Requests\CrmRequest;
use Srapid\RealEstate\Repositories\Interfaces\CrmInterface;
use Srapid\RealEstate\Tables\CrmTable;
use Srapid\RealEstate\Models\Property;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;
use Illuminate\Support\Facades\DB;
use RvMedia;

class CrmController extends BaseController
{
    /**
     * @var CrmInterface
     */
    protected $crmRepository;

    /**
     * CrmController constructor.
     * @param CrmInterface $crmRepository
     */
    public function __construct(CrmInterface $crmRepository)
    {
        $this->crmRepository = $crmRepository;
    }

    /**
     * @param CrmTable $table
     * @return JsonResponse|View
     * @throws Throwable
     */
    public function index(CrmTable $table, Request $request)
    {
        page_title()->setTitle(trans('plugins/real-estate::crm.name'));
        
        // Check if AJAX request for kanban data
        if ($request->has('_ajax') && $request->input('_ajax') === 'get_leads') {
            return $this->getLeadsJson($request);
        }
        
        // Check if status update action
        if ($request->has('_action') && $request->input('_action') === 'update_status') {
            return $this->updateLeadStatus($request);
        }
        
        // Check if table view is explicitly requested
        $view = $request->input('view', 'kanban');
        if ($view === 'table') {
            return $table->renderTable();
        }
        
        // Default to kanban view
        return view('plugins/real-estate::crm.kanban.index');
    }
    
    /**
     * Get all leads as JSON for kanban view
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getLeadsJson(Request $request)
    {
        $leads = $this->crmRepository->getModel()
            ->select([
                're_crm.id',
                're_crm.name',
                're_crm.phone',
                're_crm.email',
                're_crm.content',
                're_crm.property_value',
                're_crm.min_price',
                're_crm.max_price',
                're_crm.category',
                're_crm.lead_color',
                're_crm.created_at',
            ])
            ->get();
            
        // Ensure each lead has a color
        foreach ($leads as $lead) {
            if (empty($lead->lead_color)) {
                $lead->lead_color = 'blue'; // Default to cold leads
                
                // Save the default color to database
                $this->crmRepository->createOrUpdate(['lead_color' => 'blue'], ['id' => $lead->id]);
            }
        }
            
        // Add formatted category names
        $leads->transform(function ($lead) {
            // Get category name from CrmTable::CATEGORIES if exists
            $categories = CrmTable::CATEGORIES;
            $lead->category_name = $categories[$lead->category] ?? $lead->category;
            
            return $lead;
        });
        
        return response()->json([
            'error' => false,
            'data' => $leads,
        ]);
    }
    
    /**
     * Update lead status (for drag and drop)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function updateLeadStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        
        if (!$id || !$status) {
            return response()->json([
                'success' => false,
                'message' => 'ID and status are required',
            ]);
        }
        
        try {
            $lead = $this->crmRepository->findOrFail($id);
            $lead->lead_color = $status;
            $lead->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/real-estate::crm.create'));

        return $formBuilder->create(CrmForm::class)->renderForm();
    }

    /**
     * @param CrmRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CrmRequest $request, BaseHttpResponse $response)
    {
        $crm = $this->crmRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));

        return $response
            ->setPreviousUrl(route('crm.index'))
            ->setNextUrl(route('crm.edit', $crm->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $crm = $this->crmRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $crm));

        page_title()->setTitle(trans('plugins/real-estate::crm.edit') . ' "' . $crm->name . '"');

        return $formBuilder->create(CrmForm::class, ['model' => $crm])->renderForm();
    }

    /**
     * @param int $id
     * @param CrmRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, CrmRequest $request, BaseHttpResponse $response)
    {
        $crm = $this->crmRepository->findOrFail($id);

        $crm->fill($request->input());

        $this->crmRepository->createOrUpdate($crm);

        event(new UpdatedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));

        return $response
            ->setPreviousUrl(route('crm.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $crm = $this->crmRepository->findOrFail($id);

            $this->crmRepository->delete($crm);

            event(new DeletedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $crm = $this->crmRepository->findOrFail($id);
            $this->crmRepository->delete($crm);
            event(new DeletedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
    
    /**
     * Check for property matches - Versão simplificada sem verificação automática
     * 
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function checkPropertyMatches(Request $request, BaseHttpResponse $response)
    {
        try {
            // Retornar resposta de sucesso sem realizar verificações
            return $response
                ->setMessage("Função desativada temporariamente.")
                ->setData(['match_count' => 0]);
                
        } catch (Exception $e) {
            return $response
                ->setError()
                ->setMessage('Erro ao verificar correspondências: ' . $e->getMessage());
        }
    }
    
    /**
     * Get matching properties for a lead - Versão simplificada
     * 
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function matchProperties($id, Request $request, BaseHttpResponse $response)
    {
        try {
            $lead = $this->crmRepository->findOrFail($id);
            
            $query = Property::query()
                ->where('moderation_status', 'approved');
            
            // Filtrar por categoria se definida
            if (!empty($lead->category)) {
                $query->whereHas('categories', function ($q) use ($lead) {
                    // Busca simples por categoria
                    $q->where('re_categories.name', 'like', '%' . $lead->category . '%');
                });
            }
            
            // Filtrar por faixa de preço se definida
            if (!empty($lead->min_price)) {
                $query->where('price', '>=', $lead->min_price);
            }
            
            if (!empty($lead->max_price)) {
                $query->where('price', '<=', $lead->max_price);
            }
            
            $properties = $query->with(['categories', 'currency'])->limit(10)->get();
            
            $data = [];
            foreach ($properties as $property) {
                $url = route('property.edit', $property->id);
                $publicUrl = route('public.properties') . '/' . $property->slug;
                
                $data[] = [
                    'id' => $property->id,
                    'name' => $property->name,
                    'price' => format_price($property->price, $property->currency),
                    'price_raw' => $property->price,
                    'location' => $property->location,
                    'category' => $property->category ? $property->category->name : 'N/A',
                    'image' => $property->image ? RvMedia::getImageUrl($property->image, 'thumb', false, RvMedia::getDefaultImage()) : null,
                    'edit_url' => $url,
                    'public_url' => $publicUrl,
                ];
            }
            
            return $response->setData($data);
        } catch (Exception $e) {
            return $response
                ->setError()
                ->setMessage('Erro ao buscar imóveis: ' . $e->getMessage());
        }
    }
}