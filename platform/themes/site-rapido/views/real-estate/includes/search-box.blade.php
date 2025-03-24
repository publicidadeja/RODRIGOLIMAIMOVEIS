<div class="search-box">
    <div class="screen-darken"></div>
    <div class="search-box-content">
        @php
            use Illuminate\Support\Facades\Route;
            $currentRoute = Route::currentRouteName();
            $isHomePage = $currentRoute === 'public.index';
            
            $bannerType = theme_option('home_banner_type', 'image');
            $bannerImage = theme_option('home_banner') ? RvMedia::url(theme_option('home_banner')) : Theme::asset()->url('images/banner-du-an.jpg');
            $bannerVideo = theme_option('home_banner_video') ? RvMedia::url(theme_option('home_banner_video')) : '';
            $bannerGif = theme_option('home_banner_gif') ? RvMedia::url(theme_option('home_banner_gif')) : '';
        @endphp
        
        <!-- Background Media (Image, Video or GIF) -->
        @if ($isHomePage)
            @if ($bannerType == 'video' && $bannerVideo)
                <div class="background-media video-container">
                    <video autoplay muted loop id="background-video">
                        <source src="{{ $bannerVideo }}" type="video/mp4">
                    </video>
                    <div class="video-fallback" style="background-image: url('{{ $bannerImage }}')"></div>
                </div>
            @elseif ($bannerType == 'gif' && $bannerGif)
                <div class="background-media" style="background-image: url('{{ $bannerGif }}')"></div>
            @else
                <div class="background-media" style="background-image: url('{{ $bannerImage }}')"></div>
            @endif
        @else
            <div class="background-media" style="background-image: url('{{ $bannerImage }}')"></div>
        @endif

        <!-- O resto do seu código permanece igual -->
        <div class="d-md-none bg-primary p-2">
            <span class="text-white">{{ __('Filter') }}</span>
            <span class="float-right toggle-filter-offcanvas text-white">
                <i class="far fa-times-circle"></i>
            </span>
        </div>
        <div class="search-box-items">
            <!-- ... resto do código ... -->
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