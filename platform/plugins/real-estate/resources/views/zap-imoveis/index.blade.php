@extends('core/base::layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4><i class="icon-sync"></i> {{ trans('plugins/real-estate::property.zap_imoveis_integration') }}</h4>
                </div>
                <div class="widget-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="note note-info">
                                <p>{{ trans('plugins/real-estate::property.zap_imoveis_description') }}</p>
                                <ul>
                                    <li>{{ trans('plugins/real-estate::property.zap_imoveis_tip_1') }}</li>
                                    <li>{{ trans('plugins/real-estate::property.zap_imoveis_tip_2') }}</li>
                                    <li>{{ trans('plugins/real-estate::property.zap_imoveis_tip_3') }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card p-3">
                                <h5 class="mb-3">{{ trans('plugins/real-estate::property.zap_imoveis_xml_feed') }}</h5>
                                <div class="form-group mb-3">
                                    @if ($xmlExists)
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="xml-url" readonly value="{{ url('storage/zap-imoveis/zap_imoveis.xml') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="copy-btn" data-bs-toggle="tooltip" title="{{ trans('plugins/real-estate::property.copy_url') }}">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ trans('plugins/real-estate::property.last_updated') }}: {{ $lastUpdated }}</small>
                                            <a href="{{ route('zap-imoveis.download') }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i> {{ trans('plugins/real-estate::property.download_xml') }}
                                            </a>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-3">
                                            {{ trans('plugins/real-estate::property.zap_imoveis_xml_not_generated') }}
                                        </div>
                                    @endif
                                </div>
                                <button id="generate-xml" class="btn btn-primary">
                                    <i class="fas fa-sync"></i> {{ trans('plugins/real-estate::property.generate_xml') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
<script>
    $(document).ready(function() {
        $('#generate-xml').on('click', function() {
            $('#generate-xml').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ trans('plugins/real-estate::property.generating') }}');
            
            $.ajax({
                url: '{{ route('zap-imoveis.generate') }}',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.error) {
                        alert(res.message);
                    } else {
                        alert(res.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    }
                },
                error: function(res) {
                    alert('Erro ao gerar XML. Por favor, tente novamente ou contate o suporte.');
                    console.error(res);
                },
                complete: function() {
                    $('#generate-xml').prop('disabled', false).html('<i class="fas fa-sync"></i> {{ trans('plugins/real-estate::property.generate_xml') }}');
                }
            });
        });
        
        $('#copy-btn').on('click', function() {
            var copyText = document.getElementById("xml-url");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            
            alert('{{ trans('plugins/real-estate::property.url_copied') }}');
        });
    });
</script>
@endpush