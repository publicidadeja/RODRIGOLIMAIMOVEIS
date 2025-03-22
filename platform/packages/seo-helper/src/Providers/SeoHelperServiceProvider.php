<?php

namespace Srapid\SeoHelper\Providers;

use Srapid\Base\Traits\LoadAndPublishDataTrait;
use Srapid\SeoHelper\Contracts\SeoHelperContract;
use Srapid\SeoHelper\Contracts\SeoMetaContract;
use Srapid\SeoHelper\Contracts\SeoOpenGraphContract;
use Srapid\SeoHelper\Contracts\SeoTwitterContract;
use Srapid\SeoHelper\SeoHelper;
use Srapid\SeoHelper\SeoMeta;
use Srapid\SeoHelper\SeoOpenGraph;
use Srapid\SeoHelper\SeoTwitter;
use Illuminate\Support\ServiceProvider;

/**
 * @since 02/12/2015 14:09 PM
 */
class SeoHelperServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(SeoMetaContract::class, SeoMeta::class);
        $this->app->bind(SeoHelperContract::class, SeoHelper::class);
        $this->app->bind(SeoOpenGraphContract::class, SeoOpenGraph::class);
        $this->app->bind(SeoTwitterContract::class, SeoTwitter::class);

        $this->setNamespace('packages/seo-helper')
            ->loadHelpers();
    }

    public function boot()
    {
        $this
            ->loadAndPublishConfigurations(['general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app->register(EventServiceProvider::class);

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
