@extends('plugins/real-estate::account.layouts.skeleton')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="widget meta-boxes">
                    <div class="widget-title">
                        <h4>
                            <span>{{ isset($lead) ? 'Editar Lead: ' . $lead->name : 'Adicionar Novo Lead' }}</span>
                        </h4>
                    </div>
                    <div class="widget-body">
                        <form action="{{ $action }}" method="POST">
                            @csrf
                            @if (isset($lead))
                                @method('PUT')
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label required">Nome</label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name', isset($lead) ? $lead->name : '') }}" required>
                                        @error('name')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email', isset($lead) ? $lead->email : '') }}">
                                        @error('email')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="phone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control phone-mask" name="phone" id="phone" value="{{ old('phone', isset($lead) ? $lead->phone : '') }}">
                                        @error('phone')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="category" class="form-label">Tipo de Imóvel Desejado</label>
                                        <select class="form-control" name="category" id="category">
                                            <option value="">Selecione uma categoria</option>
                                            <option value="casa" {{ old('category', isset($lead) ? $lead->category : '') == 'casa' ? 'selected' : '' }}>Casa</option>
                                            <option value="casa_condominio" {{ old('category', isset($lead) ? $lead->category : '') == 'casa_condominio' ? 'selected' : '' }}>Casa em Condomínio</option>
                                            <option value="sobrado" {{ old('category', isset($lead) ? $lead->category : '') == 'sobrado' ? 'selected' : '' }}>Sobrado</option>
                                            <option value="apartamento" {{ old('category', isset($lead) ? $lead->category : '') == 'apartamento' ? 'selected' : '' }}>Apartamento</option>
                                            <option value="studio" {{ old('category', isset($lead) ? $lead->category : '') == 'studio' ? 'selected' : '' }}>Studio/Kitnet</option>
                                            <option value="cobertura" {{ old('category', isset($lead) ? $lead->category : '') == 'cobertura' ? 'selected' : '' }}>Cobertura</option>
                                            <option value="flat" {{ old('category', isset($lead) ? $lead->category : '') == 'flat' ? 'selected' : '' }}>Flat</option>
                                            <option value="loft" {{ old('category', isset($lead) ? $lead->category : '') == 'loft' ? 'selected' : '' }}>Loft</option>
                                            <option value="terreno" {{ old('category', isset($lead) ? $lead->category : '') == 'terreno' ? 'selected' : '' }}>Terreno</option>
                                            <option value="terreno_cond" {{ old('category', isset($lead) ? $lead->category : '') == 'terreno_cond' ? 'selected' : '' }}>Terreno em Condomínio</option>
                                            <option value="comercial_sala" {{ old('category', isset($lead) ? $lead->category : '') == 'comercial_sala' ? 'selected' : '' }}>Sala Comercial</option>
                                            <option value="comercial_loja" {{ old('category', isset($lead) ? $lead->category : '') == 'comercial_loja' ? 'selected' : '' }}>Loja</option>
                                            <option value="comercial_galpao" {{ old('category', isset($lead) ? $lead->category : '') == 'comercial_galpao' ? 'selected' : '' }}>Galpão</option>
                                            <option value="comercial_predio" {{ old('category', isset($lead) ? $lead->category : '') == 'comercial_predio' ? 'selected' : '' }}>Prédio Comercial</option>
                                            <option value="outros" {{ old('category', isset($lead) ? $lead->category : '') == 'outros' ? 'selected' : '' }}>Outros</option>
                                        </select>
                                        @error('category')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="min_price" class="form-label">Preço Mínimo</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control price-mask" name="min_price" id="min_price" value="{{ old('min_price', isset($lead) && $lead->min_price ? number_format($lead->min_price, 2, ',', '.') : '') }}">
                                        </div>
                                        @error('min_price')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="max_price" class="form-label">Preço Máximo</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control price-mask" name="max_price" id="max_price" value="{{ old('max_price', isset($lead) && $lead->max_price ? number_format($lead->max_price, 2, ',', '.') : '') }}">
                                        </div>
                                        @error('max_price')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="property_value" class="form-label">Valor da Propriedade (se específico)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control price-mask" name="property_value" id="property_value" value="{{ old('property_value', isset($lead) && $lead->property_value ? number_format($lead->property_value, 2, ',', '.') : '') }}">
                                        </div>
                                        @error('property_value')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="lead_color" class="form-label">Status do Lead</label>
                                        <select class="form-control" name="lead_color" id="lead_color">
                                            <option value="blue" {{ old('lead_color', isset($lead) ? $lead->lead_color : 'blue') == 'blue' ? 'selected' : '' }}>Lead Frio</option>
                                            <option value="yellow" {{ old('lead_color', isset($lead) ? $lead->lead_color : '') == 'yellow' ? 'selected' : '' }}>Em Negociação</option>
                                            <option value="red" {{ old('lead_color', isset($lead) ? $lead->lead_color : '') == 'red' ? 'selected' : '' }}>Lead Quente</option>
                                            <option value="gray" {{ old('lead_color', isset($lead) ? $lead->lead_color : '') == 'gray' ? 'selected' : '' }}>Venda Perdida</option>
                                        </select>
                                        @error('lead_color')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="content" class="form-label">Observações</label>
                                <textarea class="form-control" name="content" id="content" rows="5">{{ old('content', isset($lead) ? $lead->content : '') }}</textarea>
                                @error('content')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="status" class="form-label required">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="published" {{ old('status', isset($lead) ? $lead->status : '') == 'published' ? 'selected' : '' }}>Ativo</option>
                                    <option value="draft" {{ old('status', isset($lead) ? $lead->status : '') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                                </select>
                                @error('status')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3 text-end">
                                <a href="{{ route('public.account.crm.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">{{ isset($lead) ? 'Atualizar' : 'Salvar' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('header')
    <style>
        .form-label.required:after {
            content: "*";
            color: red;
            margin-left: 3px;
        }
    </style>
@endpush

@push('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            // Máscara para o telefone
            $('.phone-mask').mask('(00) 00000-0000');
            
            // Máscara para valores monetários
            $('.price-mask').mask('#.##0,00', {reverse: true});
        });
    </script>
@endpush