<?php

namespace Srapid\RealEstate\Forms;

use Srapid\Base\Forms\FormAbstract;
use Srapid\RealEstate\Models\Crm;
use Srapid\RealEstate\Http\Requests\CrmRequest;

class CrmForm extends FormAbstract
{
    /**
     * @return mixed|void
     */
    public function buildForm()
    {
        $this
            ->setupModel(new Crm)
            ->setValidatorClass(CrmRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
                'wrapper'    => [
                    'class' => 'form-group',
                ],
                'order'      => 1,
            ])
            ->add('email', 'text', [
                'label'      => trans('plugins/real-estate::crm.form.email'),
                'label_attr' => ['class' => 'control-label'], // Removida a classe 'required'
                'attr'       => [
                    'placeholder'  => trans('plugins/real-estate::crm.form.email_placeholder'),
                    'data-counter' => 60,
                ],
                'wrapper'    => [
                    'class' => 'form-group',
                ],
                'order'      => 2,
            ])
            ->add('phone', 'text', [
                'label'      => trans('plugins/real-estate::crm.form.phone'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => trans('plugins/real-estate::crm.form.phone_placeholder'),
                    'data-counter' => 15,
                ],
                'wrapper'    => [
                    'class' => 'form-group',
                ],
                'order'      => 3,
            ])
            ->add('category', 'select', [
    'label'      => 'Categoria do Lead',
    'label_attr' => ['class' => 'control-label'],
    'choices'    => [
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
    ],
    'wrapper'    => [
        'class' => 'form-group',
    ],
    'order'      => 4,
])
            // Adicionando o campo de cor do lead
            ->add('lead_color', 'select', [
    'label'      => 'Cor do Lead',
    'label_attr' => ['class' => 'control-label'],
    'choices'    => [
        'red'    => 'Lead Quente (Vermelho)',
        'blue'   => 'Lead Frio (Azul)',
        'yellow' => 'Em negociação (Amarelo)', // Nova opção adicionada
        'gray'   => 'Venda Perdida (Cinza)'
    ],
    'wrapper'    => [
        'class' => 'form-group',
    ],
    'order'      => 5,
])
            ->add('content', 'textarea', [
                'label'      => trans('plugins/real-estate::crm.form.content'),
                'label_attr' => ['class' => 'control-label'], // Removida a classe 'required'
                'attr'       => [
                    'rows'         => 5,
                    'placeholder'  => trans('plugins/real-estate::crm.form.content_placeholder'),
                    'data-counter' => 400,
                ],
                'wrapper'    => [
                    'class' => 'form-group',
                ],
                'order'      => 6,
            ])
            ->add('price_range_group', 'html', [
                'html' => '<div class="form-group"><label class="control-label">Faixa de Preço</label></div>',
                'order' => 6,
            ])
            ->add('min_price', 'text', [
                'label'      => 'Preço Mínimo',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'R$ 0,00',
                    'class'        => 'form-control money-mask',
                    'id'           => 'min_price',
                    'data-counter' => 20,
                ],
                'wrapper'    => [
                    'class' => 'form-group col-md-6',
                ],
                'order'      => 7,
            ])
            ->add('max_price', 'text', [
                'label'      => 'Preço Máximo',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'R$ 0,00',
                    'class'        => 'form-control money-mask',
                    'id'           => 'max_price',
                    'data-counter' => 20,
                ],
                'wrapper'    => [
                    'class' => 'form-group col-md-6',
                ],
                'order'      => 8,
            ]);
        
        // Remova o setBreakFieldPoint para evitar problemas de layout
        // ->setBreakFieldPoint('content');
    }
}

