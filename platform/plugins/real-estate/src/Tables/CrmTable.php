<?php

namespace Srapid\RealEstate\Tables;

use Auth;
use BaseHelper;
use Srapid\Base\Enums\BaseStatusEnum;
use Srapid\RealEstate\Repositories\Interfaces\CrmInterface;
use Srapid\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Throwable;
use Yajra\DataTables\DataTables;
use Html;
use Exception;

class CrmTable extends TableAbstract
{
    protected $hasActions = true;
    protected $hasFilter = true;

    /**
     * Category mapping
     */
    const CATEGORIES = [
        'casa'              => 'Casa',
        'casa_condominio'   => 'Casa em Condomínio',
        'sobrado'           => 'Sobrado',
        'apartamento'       => 'Apartamento',
        'studio'            => 'Studio/Kitnet',
        'cobertura'         => 'Cobertura',
        'flat'              => 'Flat',
        'loft'              => 'Loft',
        'chacara'           => 'Chácara',
        'sitio'             => 'Sítio',
        'fazenda'           => 'Fazenda',
        'rancho'            => 'Rancho',
        'terreno'           => 'Terreno',
        'terreno_cond'      => 'Terreno em Condomínio',
        'lote'              => 'Lote',
        'area_rural'        => 'Área Rural',
        'comercial_sala'    => 'Sala Comercial',
        'comercial_loja'    => 'Loja',
        'comercial_galpao'  => 'Galpão',
        'comercial_predio'  => 'Prédio Comercial',
        'aluguel'           => 'Aluguel',
        'temporada'         => 'Temporada',
        'industrial'        => 'Área Industrial',
        'hotel_pousada'     => 'Hotel/Pousada',
        'imovel_na_planta'  => 'Imóvel na Planta',
        'outros'            => 'Outros'
    ];

    /**
     * Lead color mapping
     */
    const LEAD_COLORS = [
        'red'    => ['text' => 'Lead Quente', 'color' => '#ff0000'],
        'blue'   => ['text' => 'Lead Frio', 'color' => '#0000ff'],
        'yellow' => ['text' => 'Em negociação', 'color' => '#ffcc00'],
        'gray'   => ['text' => 'Venda Perdida', 'color' => '#808080']
    ];

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CrmInterface $crmRepository)
    {
        parent::__construct($table, $urlGenerator);
        $this->repository = $crmRepository;
        
        if (!Auth::user()->hasAnyPermission(['crm.edit', 'crm.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
        
        // Use custom view for the table
        $this->view = 'plugins/real-estate::crm.table-view';
    }

    public function ajax(): JsonResponse
    {
        try {
            $data = $this->table
                ->eloquent($this->query())
                ->editColumn('name', function ($item) {
                    if (!Auth::user()->hasPermission('crm.edit')) {
                        return $item->name;
                    }
                    return Html::link(route('crm.edit', $item->id), $item->name);
                })
                ->editColumn('checkbox', function ($item) {
                    return $this->getCheckbox($item->id);
                })
                ->editColumn('content', function ($item) {
                    return \Str::limit($item->content, 70);
                })
                ->editColumn('category', function ($item) {
                    return self::CATEGORIES[$item->category] ?? $item->category;
                })
                ->editColumn('property_value', function ($item) {
                    $output = '';
                    
                    // Mostrar a faixa de preço se existir
                    if ($item->min_price || $item->max_price) {
                        $output .= '<div>' . $item->price_range . '</div>';
                    }
                    
                    // Mostrar o valor do imóvel se existir
                    if ($item->property_value) {
                        $output .= '<div>Valor: R$ ' . number_format($item->property_value, 2, ',', '.') . '</div>';
                    }
                    
                    // Adicionar badge para match
                    if ($item->has_match) {
                        $output .= '<div class="mt-1"><span class="badge badge-success property-match" 
                                        data-id="' . $item->id . '" 
                                        data-category="' . $item->category . '" 
                                        data-min="' . ($item->min_price ?: 0) . '" 
                                        data-max="' . ($item->max_price ?: 0) . '"
                                        style="cursor: pointer;">
                                        <i class="fas fa-check-circle"></i> Imóveis compatíveis
                                    </span></div>';
                    }
                    
                    return $output ?: 'N/A';
                })
                ->editColumn('lead_color', function ($item) {
                    if (isset(self::LEAD_COLORS[$item->lead_color])) {
                        $info = self::LEAD_COLORS[$item->lead_color];
                        return sprintf(
                            '<span class="badge" style="background-color: %s">%s</span>',
                            $info['color'],
                            $info['text']
                        );
                    }
                    return $item->lead_color;
                })
                ->editColumn('created_at', function ($item) {
                    return BaseHelper::formatDate($item->created_at);
                })
                ->addColumn('operations', function ($item) {
                    return $this->getOperations('crm.edit', 'crm.destroy', $item);
                })
                ->rawColumns(['lead_color', 'property_value', 'operations']);

            return $this->toJson($data);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function query()
    {
        try {
            $query = $this->repository->getModel()->select([
                're_crm.id',
                're_crm.name',
                're_crm.phone',
                're_crm.email',
                're_crm.content',
                're_crm.property_value',
                're_crm.category',     
                're_crm.lead_color',   
                're_crm.status',
                're_crm.created_at',
            ]);

            return $this->applyScopes($query);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'phone' => [
                'title' => trans('plugins/real-estate::crm.phone'),
                'class' => 'text-start',
            ],
            'email' => [
                'title' => trans('plugins/real-estate::crm.email'),
                'class' => 'text-start',
            ],
            'category' => [
                'title' => 'Categoria do Lead',
                'class' => 'text-start',
            ],
            'lead_color' => [
                'title' => 'Status do Lead',
                'class' => 'text-start',
            ],
            'property_value' => [
                'title' => trans('plugins/real-estate::crm.form.property_value'),
                'class' => 'text-start',
            ],
            'content' => [
                'title' => trans('plugins/real-estate::crm.content'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('crm.create'), 'crm.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('crm.deletes'), 'crm.destroy', parent::bulkActions());
    }

    public function getFilters(): array
    {
        return [
            'category' => [
                'title'    => 'Categoria do Lead',
                'type'     => 'select',
                'choices'  => self::CATEGORIES,
            ],
            'lead_color' => [
                'title'    => 'Status do Lead',
                'type'     => 'select',
                'choices'  => array_combine(
                    array_keys(self::LEAD_COLORS),
                    array_map(function($color) {
                        return $color['text'];
                    }, self::LEAD_COLORS)
                ),
            ],
            'property_value' => [
                'title'    => trans('plugins/real-estate::crm.form.property_value'),
                'type'     => 'text',
                'validate' => 'nullable|numeric',
                'attributes' => [
                    'class'       => 'form-control property-value-mask',
                    'placeholder' => 'R$ 0,00',
                    'data-mask'   => 'currency'
                ],
            ],
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'content' => [
                'title'    => trans('plugins/real-estate::crm.content'),
                'type'     => 'text',
                'validate' => 'max:255',
            ],
            'property_value' => [
                'title'    => trans('plugins/real-estate::crm.form.property_value'),
                'type'     => 'text',
                'validate' => 'nullable|numeric',
                'attributes' => [
                    'class'       => 'form-control property-value-mask',
                    'placeholder' => 'R$ 0,00',
                    'data-mask'   => 'currency'
                ],
            ],
            'category' => [
                'title'    => 'Categoria do Lead',
                'type'     => 'select',
                'choices'  => self::CATEGORIES,
            ],
            'lead_color' => [
                'title'    => 'Status do Lead',
                'type'     => 'select',
                'choices'  => array_combine(
                    array_keys(self::LEAD_COLORS),
                    array_map(function($color) {
                        return $color['text'];
                    }, self::LEAD_COLORS)
                ),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }
}