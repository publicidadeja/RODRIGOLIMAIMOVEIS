<?php

namespace Srapid\Sitemap\Providers;

use Srapid\Base\Events\CreatedContentEvent;
use Srapid\Base\Events\DeletedContentEvent;
use Srapid\Base\Events\UpdatedContentEvent;
use Srapid\Base\Traits\LoadAndPublishDataTrait;
use Srapid\Sitemap\Sitemap;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class SitemapServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->setNamespace('packages/sitemap')
            ->loadAndPublishConfigurations(['config'])
            ->loadAndPublishViews()
            ->publishAssets();

        Event::listen(CreatedContentEvent::class, function () {
            cache()->forget('public.sitemap');
        });

        Event::listen(UpdatedContentEvent::class, function () {
            cache()->forget('public.sitemap');
        });

        Event::listen(DeletedContentEvent::class, function () {
            cache()->forget('public.sitemap');
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('sitemap', function ($app) {
            $config = config('packages.sitemap.config');

            return new Sitemap(
                $config,
                $app['Illuminate\Cache\Repository'],
                $app['config'],
                $app['files'],
                $app['Illuminate\Contracts\Routing\ResponseFactory'],
                $app['view']
            );
        });

        $this->app->alias('sitemap', Sitemap::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sitemap', Sitemap::class];
    }
}
