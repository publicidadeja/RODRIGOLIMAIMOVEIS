<div class="search-box">
    <div class="screen-darken"></div>
    <div class="search-box-content">
        @php
            $bannerType = theme_option('home_banner_type', 'image');
            $bannerImage = theme_option('home_banner') ? RvMedia::url(theme_option('home_banner')) : Theme::asset()->url('images/banner-du-an.jpg');
            $bannerVideo = theme_option('home_banner_video') ? RvMedia::url(theme_option('home_banner_video')) : '';
            $bannerGif = theme_option('home_banner_gif') ? RvMedia::url(theme_option('home_banner_gif')) : '';
        @endphp
        
        <!-- Background Media (Image, Video or GIF) -->
        @if ($bannerType == 'image')
            <div class="background-media" style="background-image: url('{{ $bannerImage }}')"></div>
        @elseif ($bannerType == 'video' && $bannerVideo)
            <div class="background-media video-container">
                <video autoplay muted loop id="background-video">
                    <source src="{{ $bannerVideo }}" type="video/mp4">
                </video>
                <!-- Fallback image if video fails to load -->
                <div class="video-fallback" style="background-image: url('{{ $bannerImage }}')"></div>
            </div>
        @elseif ($bannerType == 'gif' && $bannerGif)
            <div class="background-media" style="background-image: url('{{ $bannerGif }}')"></div>
        @else
            <!-- Default fallback to image -->
            <div class="background-media" style="background-image: url('{{ $bannerImage }}')"></div>
        @endif
        
        <!-- Conteúdo do Search Box -->
        <div class="d-md-none bg-primary p-2">
            <span class="text-white">{{ __('Filter') }}</span>
            <span class="float-right toggle-filter-offcanvas text-white">
                <i class="far fa-times-circle"></i>
            </span>
        </div>
        <div class="search-box-items">
            <div class="row ml-md-0 mr-md-0">
                <div class="col-xl-3 col-lg-2 col-md-4 px-1">
                    {!! Theme::partial('real-estate.filters.keyword') !!}
                </div>
                <div class="col-lg-2 col-md-4 px-1">
                    {!! Theme::partial('real-estate.filters.city') !!}
                </div>
                <div class="col-lg-2 col-md-4 px-1">
                    {!! Theme::partial('real-estate.filters.choices', ['type' => $type, 'categories' => $categories, 'labelDefault' => __('Type, category...')]) !!}
                </div>
                @if ($type == 'property')
                    <div class="col-lg-2 col-md-4 px-1 mb-2">
                        <label for="select-type" class="control-label">{{ __('Price range') }}</label>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuPrice" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span>{{ __('All prices') }}</span>
                            </button>
                            <div class="dropdown-menu" style="min-width: 20em;" aria-labelledby="dropdownMenuPrice">
                                <div class="dropdown-item">
                                    {!! Theme::partial('real-estate.filters.price') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 px-1 mb-2">
                        <label for="select-type" class="control-label">{{ __('Square range') }}</label>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuSquare" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span>{{ __('All squares') }}</span>
                            </button>
                            <div class="dropdown-menu" style="min-width: 20em;" aria-labelledby="dropdownMenuSquare">
                                <div class="dropdown-item">
                                    {!! Theme::partial('real-estate.filters.square') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-lg-2 col-md-4 px-1">
                        {!! Theme::partial('real-estate.filters.floor') !!}
                    </div>
                    <div class="col-lg-2 col-md-4 px-1">
                        <label for="select-type" class="control-label">{{ __('Flat range') }}</label>
                        <div class="dropdown mb-2">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuFlat" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span>{{ __('All squares') }}</span>
                            </button>
                            <div class="dropdown-menu" style="min-width: 20em;" aria-labelledby="dropdownMenuFlat">
                                <div class="dropdown-item">
                                    {!! Theme::partial('real-estate.filters.flat') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-lg-2 col-xl-1 col-md-2 px-1 button-search-wrapper" style="align-self: flex-end;">
                    <div class="btn-group text-center w-100 ">
                        <button type="submit" class="btn btn-primary btn-full">{{ __('Search') }}</button>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <button class="dropdown-item" type="reset">{{ __('Reset filters') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para o background de mídia */
.search-box {
    position: relative;
    overflow: hidden;
    min-height: 500px; /* Altura mínima para garantir que o conteúdo seja visível */
}

.search-box-content {
    position: relative;
    z-index: 3; /* Aumentado para garantir que fique acima da screen-darken */
    padding: 30px;
}

.background-media {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
}

.video-container {
    height: 100%;
    width: 100%;
    overflow: hidden;
}

#background-video {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    z-index: 0;
    transform: translateX(-50%) translateY(-50%);
    object-fit: cover;
}

.video-fallback {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1; /* Fica abaixo do vídeo */
    background-size: cover;
    background-position: center;
}

.screen-darken {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 2; /* Entre o background e o conteúdo */
}

/* Estilos para os elementos do formulário */
.search-box-items {
    background-color: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 5px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var video = document.getElementById('background-video');
    if (video) {
        video.addEventListener('error', function() {
            console.error('Erro ao carregar o vídeo');
            document.querySelector('.video-fallback').style.zIndex = 1;
        });
        
        // Força o carregamento do vídeo
        video.load();
    }
});
</script>