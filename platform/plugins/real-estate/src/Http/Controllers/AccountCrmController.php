<?php

namespace Srapid\RealEstate\Http\Controllers;

use Illuminate\Http\Request;
use Srapid\Base\Events\BeforeEditContentEvent;
use Srapid\Base\Events\CreatedContentEvent;
use Srapid\Base\Events\DeletedContentEvent;
use Srapid\Base\Events\UpdatedContentEvent;
use Srapid\Base\Forms\FormBuilder;
use Srapid\Base\Http\Responses\BaseHttpResponse;
use Srapid\RealEstate\Http\Requests\CrmRequest;
use Srapid\RealEstate\Repositories\Interfaces\CrmInterface;
use Srapid\RealEstate\Models\Property;
use Srapid\RealEstate\Tables\CrmTable;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;
use Assets;
use RvMedia;
use Form;
use SeoHelper;

class AccountCrmController extends CrmController
{
    /**
     * AccountCrmController constructor.
     * @param CrmInterface $crmRepository
     */
    public function __construct(CrmInterface $crmRepository)
    {
        parent::__construct($crmRepository);
        
        // Carregar assets necessários
        Assets::addScriptsDirectly([
            'vendor/core/plugins/real-estate/js/account-crm.js',
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/real-estate/css/account-crm.css',
        ]);
    }

    /**
     * @param CrmTable $table
     * @param Request $request
     * @return View|\Illuminate\Http\JsonResponse
     * @throws Throwable
     */
    public function index(CrmTable $table, Request $request)
    {
        // Definir título da página
        SeoHelper::setTitle(trans('plugins/real-estate::crm.name'));
        
        // Verificar se é uma solicitação AJAX para dados do kanban
        if ($request->has('_ajax') && $request->input('_ajax') === 'get_leads') {
            return $this->getAccountLeadsJson($request);
        }
        
        // Verificar se é uma ação de atualização de status
        if ($request->has('_action') && $request->input('_action') === 'update_status') {
            return $this->updateAccountLeadStatus($request);
        }
        
        // Verificar se a visualização em tabela é explicitamente solicitada
        $view = $request->input('view', 'kanban');
        if ($view === 'table') {
            return view('plugins/real-estate::account.crm.table-view');
        }
        
        // Padrão para visualização kanban
        return view('plugins/real-estate::account.crm.kanban.index');
    }
    
    /**
     * Obter leads do corretor atual como JSON para visualização kanban
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getAccountLeadsJson(Request $request)
    {
        $accountId = auth('account')->id();
        
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
            ->where('account_id', $accountId)
            ->get();
            
        // Garantir que cada lead tenha uma cor
        foreach ($leads as $lead) {
            if (empty($lead->lead_color)) {
                $lead->lead_color = 'blue'; // Padrão para leads frios
                
                // Salvar a cor padrão no banco de dados
                $this->crmRepository->createOrUpdate(['lead_color' => 'blue'], ['id' => $lead->id]);
            }
        }
            
        // Adicionar nomes de categoria formatados
        $leads->transform(function ($lead) {
            // Obter nome da categoria de CrmTable::CATEGORIES se existir
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
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        SeoHelper::setTitle(trans('plugins/real-estate::crm.create'));

        return view('plugins/real-estate::account.crm.form', [
            'action' => route('public.account.crm.store'),
        ]);
    }

    /**
     * @param CrmRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CrmRequest $request, BaseHttpResponse $response)
    {
        try {
            // Adicionar account_id aos dados e processar os valores numéricos
            $data = $request->input();
            $data['account_id'] = auth('account')->id();
            
            // Processar números formatados em pt-BR para formato decimal
            $data = $this->processNumericFields($data);
            
            $crm = $this->crmRepository->createOrUpdate($data);

            event(new CreatedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));

            return $response
                ->setPreviousUrl(route('public.account.crm.index'))
                ->setNextUrl(route('public.account.crm.edit', $crm->id))
                ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (\Exception $e) {
            return $response
                ->setError()
                ->setMessage('Erro ao criar lead: ' . $e->getMessage());
        }
    }
    
    /**
     * Processa campos numéricos convertendo de formato brasileiro para decimal
     * 
     * @param array $data
     * @return array
     */
    protected function processNumericFields($data)
    {
        // Campos que devem ser tratados como números
        $numericFields = ['min_price', 'max_price', 'property_value'];
        
        foreach ($numericFields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                // Remover formatação em pt-BR (R$, pontos e espaços, e substitui vírgula por ponto)
                $value = $data[$field];
                
                // Remover caracteres não numéricos exceto vírgula e ponto
                $value = preg_replace('/[^\d,.]/', '', $value);
                
                // Remover pontos (usado como separador de milhar em pt-BR)
                $value = str_replace('.', '', $value);
                
                // Substituir vírgula por ponto (decimal)
                $value = str_replace(',', '.', $value);
                
                // Verificar se é um número válido após a conversão
                if (is_numeric($value)) {
                    // Converter para float
                    $data[$field] = (float) $value;
                } else {
                    // Se não for um número válido após a conversão, definir como null
                    $data[$field] = null;
                }
            } else {
                $data[$field] = null; // Definir como null se estiver vazio
            }
        }
        
        return $data;
    }

    /**
     * @param int $id
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $accountId = auth('account')->id();
        
        $crm = $this->crmRepository->findOrFail($id);
        
        // Verificar se o lead pertence ao corretor atual
        if ($crm->account_id != $accountId) {
            abort(403, 'Este lead não pertence ao seu perfil.');
        }
        
        event(new BeforeEditContentEvent($request, $crm));

        SeoHelper::setTitle(trans('plugins/real-estate::crm.edit') . ' "' . $crm->name . '"');

        return view('plugins/real-estate::account.crm.form', [
            'lead' => $crm,
            'action' => route('public.account.crm.update', $crm->id),
        ]);
    }

    /**
     * @param int $id
     * @param CrmRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, CrmRequest $request, BaseHttpResponse $response)
    {
        $accountId = auth('account')->id();
        
        $crm = $this->crmRepository->findOrFail($id);
        
        // Verificar se o lead pertence ao corretor atual
        if ($crm->account_id != $accountId) {
            abort(403, 'Este lead não pertence ao seu perfil.');
        }

        try {
            $data = $request->input();
            $data['account_id'] = $accountId; // Garantir que o account_id não seja alterado
            
            // Processar números formatados em pt-BR para formato decimal
            $data = $this->processNumericFields($data);
            
            $crm->fill($data);

            $this->crmRepository->createOrUpdate($crm);

            event(new UpdatedContentEvent(CRM_MODULE_SCREEN_NAME, $request, $crm));

            return $response
                ->setPreviousUrl(route('public.account.crm.index'))
                ->setMessage(trans('core/base::notices.update_success_message'));
        } catch (\Exception $e) {
            return $response
                ->setError()
                ->setMessage('Erro ao atualizar lead: ' . $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        $accountId = auth('account')->id();
        
        try {
            $crm = $this->crmRepository->findOrFail($id);
            
            // Verificar se o lead pertence ao corretor atual
            if ($crm->account_id != $accountId) {
                return $response
                    ->setError()
                    ->setMessage('Este lead não pertence ao seu perfil.');
            }

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
     * Update lead status (for drag and drop in kanban) - versão específica para accounts
     * Verifica se o lead pertence ao corretor logado antes de atualizar
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function updateAccountLeadStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $accountId = auth('account')->id();
        
        if (!$id || !$status) {
            return response()->json([
                'success' => false,
                'message' => 'ID e status são obrigatórios',
            ]);
        }
        
        try {
            $lead = $this->crmRepository->findOrFail($id);
            
            // Verificar se o lead pertence ao corretor atual
            if ($lead->account_id != $accountId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lead não pertence ao seu perfil.',
                ]);
            }
            
            $lead->lead_color = $status;
            $lead->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Get matching properties for a lead
     * 
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function matchProperties($id, Request $request, BaseHttpResponse $response)
    {
        $accountId = auth('account')->id();
        
        try {
            $lead = $this->crmRepository->findOrFail($id);
            
            // Verificar se o lead pertence ao corretor atual
            if ($lead->account_id != $accountId) {
                return $response
                    ->setError()
                    ->setMessage('Este lead não pertence ao seu perfil.');
            }
            
            $query = Property::query()
                ->where('moderation_status', 'approved');
            
            // Filtrar também propriedades do corretor atual
            $query->where(function ($query) use ($accountId) {
                $query->where('author_id', $accountId)
                    ->where('author_type', 'Srapid\\RealEstate\\Models\\Account');
            });
            
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
                $publicUrl = route('public.properties') . '/' . $property->slug;
                
                $data[] = [
                    'id' => $property->id,
                    'name' => $property->name,
                    'price' => format_price($property->price, $property->currency),
                    'price_raw' => $property->price,
                    'location' => $property->location,
                    'category' => $property->category ? $property->category->name : 'N/A',
                    'image' => $property->image ? RvMedia::getImageUrl($property->image, 'thumb', false, RvMedia::getDefaultImage()) : null,
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