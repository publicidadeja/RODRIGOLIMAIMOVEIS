<?php

namespace Srapid\Slug\Providers;

use BaseHelper;
use Srapid\Base\Models\BaseModel;
use Srapid\Base\Traits\LoadAndPublishDataTrait;
use Srapid\Page\Models\Page;
use Srapid\Slug\Models\Slug;
use Srapid\Slug\Repositories\Caches\SlugCacheDecorator;
use Srapid\Slug\Repositories\Eloquent\SlugRepository;
use Srapid\Slug\Repositories\Interfaces\SlugInterface;
use Srapid\Slug\SlugHelper;
use Illuminate\Support\Facades\Event;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;
use MacroableModels;

class SlugServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * This provider is deferred and should be lazy loaded.
     *
     * @var boolean
     */
    protected $defer = true;

    public function register()
    {
        $this->app->bind(SlugInterface::class, function () {
            return new SlugCacheDecorator(new SlugRepository(new Slug));
        });

        $this->app->singleton(SlugHelper::class, function () {
            return new SlugHelper;
        });

        $this->setNamespace('packages/slug')
            ->loadHelpers();
    }

    public function boot()
    {
        $this
            ->loadAndPublishConfigurations(['general'])
            ->loadAndPublishViews()
            ->loadRoutes(['web'])
            ->loadAndPublishTranslations()
            ->loadMigrations()
            ->publishAssets();

        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id'          => 'cms-packages-slug-permalink',
                    'priority'    => 5,
                    'parent_id'   => 'cms-core-settings',
                    'name'        => 'packages/slug::slug.permalink_settings',
                    'icon'        => null,
                    'url'         => route('slug.settings'),
                    'permissions' => ['setting.options'],
                ]);
        });

        $this->app->booted(function () {
            $this->app->register(FormServiceProvider::class);

            foreach (array_keys($this->app->make(SlugHelper::class)->supportedModels()) as $item) {
                if (!class_exists($item)) {
                    continue;
                }

                /**
                 * @var BaseModel $item
                 */
                $item::resolveRelationUsing('slugable', function ($model) {
                    return $model->morphOne(Slug::class, 'reference');
                });

                MacroableModels::addMacro($item, 'getSlugAttribute', function () {
                    /**
                     * @var BaseModel $this
                     */
                    return $this->slugable ? $this->slugable->key : '';
                });

                MacroableModels::addMacro($item, 'getSlugIdAttribute', function () {
                    /**
                     * @var BaseModel $this
                     */
                    return $this->slugable ? $this->slugable->id : '';
                });

                MacroableModels::addMacro($item,
                    'getUrlAttribute',
                    function () {
                        /**
                         * @var BaseModel $this
                         */

                        if (!$this->slug) {
                            return url('');
                        }

                        if (get_class($this) == Page::class && BaseHelper::isHomepage($this->id)) {
                            return url('');
                        }

                        $prefix = $this->slugable ? $this->slugable->prefix : null;
                        $prefix = apply_filters(FILTER_SLUG_PREFIX, $prefix);

                        return apply_filters('slug_filter_url', url($prefix ? $prefix . '/' . $this->slug : $this->slug));
                    });
            }

            $this->app->register(HookServiceProvider::class);
        });
    }

    /**
     * Which IoC bindings the provider provides.
     *
     * @return array
     */
    public function provides()
    {
        return [
            SlugHelper::class,
        ];
    }
}
