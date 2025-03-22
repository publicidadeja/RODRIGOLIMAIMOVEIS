<?php

namespace Srapid\RealEstate\Providers;

use Srapid\Base\Supports\Helper;
use Srapid\Base\Traits\LoadAndPublishDataTrait;
use Srapid\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Srapid\RealEstate\Commands\RenewPropertiesCommand;
use Srapid\RealEstate\Facades\RealEstateHelperFacade;
use Srapid\RealEstate\Http\Middleware\RedirectIfAccount;
use Srapid\RealEstate\Http\Middleware\RedirectIfNotAccount;
use Srapid\RealEstate\Models\Account;
use Srapid\RealEstate\Models\AccountActivityLog;
use Srapid\RealEstate\Models\Category;
use Srapid\RealEstate\Models\Consult;
use Srapid\RealEstate\Models\Currency;
use Srapid\RealEstate\Models\Facility;
use Srapid\RealEstate\Models\Feature;
use Srapid\RealEstate\Models\Investor;
use Srapid\RealEstate\Models\Package;
use Srapid\RealEstate\Models\Project;
use Srapid\RealEstate\Models\Property;
use Srapid\RealEstate\Models\Transaction;
use Srapid\RealEstate\Repositories\Caches\AccountActivityLogCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\AccountCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\CategoryCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\ConsultCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\CurrencyCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\FacilityCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\FeatureCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\InvestorCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\PackageCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\ProjectCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\PropertyCacheDecorator;
use Srapid\RealEstate\Repositories\Caches\TransactionCacheDecorator;
use Srapid\RealEstate\Repositories\Eloquent\AccountActivityLogRepository;
use Srapid\RealEstate\Repositories\Eloquent\AccountRepository;
use Srapid\RealEstate\Repositories\Eloquent\CategoryRepository;
use Srapid\RealEstate\Repositories\Eloquent\ConsultRepository;
use Srapid\RealEstate\Repositories\Eloquent\CurrencyRepository;
use Srapid\RealEstate\Repositories\Eloquent\FacilityRepository;
use Srapid\RealEstate\Repositories\Eloquent\FeatureRepository;
use Srapid\RealEstate\Repositories\Eloquent\InvestorRepository;
use Srapid\RealEstate\Repositories\Eloquent\PackageRepository;
use Srapid\RealEstate\Repositories\Eloquent\ProjectRepository;
use Srapid\RealEstate\Repositories\Eloquent\PropertyRepository;
use Srapid\RealEstate\Repositories\Eloquent\TransactionRepository;
use Srapid\RealEstate\Repositories\Interfaces\AccountActivityLogInterface;
use Srapid\RealEstate\Repositories\Interfaces\AccountInterface;
use Srapid\RealEstate\Repositories\Interfaces\CategoryInterface;
use Srapid\RealEstate\Repositories\Interfaces\ConsultInterface;
use Srapid\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Srapid\RealEstate\Repositories\Interfaces\FacilityInterface;
use Srapid\RealEstate\Repositories\Interfaces\FeatureInterface;
use Srapid\RealEstate\Repositories\Interfaces\InvestorInterface;
use Srapid\RealEstate\Repositories\Interfaces\PackageInterface;
use Srapid\RealEstate\Repositories\Interfaces\ProjectInterface;
use Srapid\RealEstate\Repositories\Interfaces\PropertyInterface;
use Srapid\RealEstate\Repositories\Interfaces\TransactionInterface;
use EmailHandler;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Language;
use RealEstateHelper;
use Route;
use SeoHelper;
use SlugHelper;
use SocialService;
use Srapid\RealEstate\Models\Crm;
use Srapid\RealEstate\Repositories\Eloquent\CrmRepository;
use Srapid\RealEstate\Repositories\Interfaces\CrmInterface;

class RealEstateServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->singleton(PropertyInterface::class, function () {
            return new PropertyCacheDecorator(
                new PropertyRepository(new Property)
            );
        });

        $this->app->bind(CrmInterface::class, function () {
            return new CrmRepository(new Crm);
        });

        $this->app->singleton(ProjectInterface::class, function () {
            return new ProjectCacheDecorator(
                new ProjectRepository(new Project)
            );
        });

        $this->app->singleton(FeatureInterface::class, function () {
            return new FeatureCacheDecorator(
                new FeatureRepository(new Feature)
            );
        });

        $this->app->bind(InvestorInterface::class, function () {
            return new InvestorCacheDecorator(new InvestorRepository(new Investor));
        });

        $this->app->bind(CurrencyInterface::class, function () {
            return new CurrencyCacheDecorator(
                new CurrencyRepository(new Currency)
            );
        });

        $this->app->bind(ConsultInterface::class, function () {
            return new ConsultCacheDecorator(
                new ConsultRepository(new Consult)
            );
        });

        $this->app->bind(CategoryInterface::class, function () {
            return new CategoryCacheDecorator(
                new CategoryRepository(new Category)
            );
        });

        $this->app->bind(FacilityInterface::class, function () {
            return new FacilityCacheDecorator(
                new FacilityRepository(new Facility)
            );
        });

        config([
            'auth.guards.account'     => [
                'driver'   => 'session',
                'provider' => 'accounts',
            ],
            'auth.providers.accounts' => [
                'driver' => 'eloquent',
                'model'  => Account::class,
            ],
            'auth.passwords.accounts' => [
                'provider' => 'accounts',
                'table'    => 're_account_password_resets',
                'expire'   => 60,
            ],
            'auth.guards.account-api' => [
                'driver'   => 'passport',
                'provider' => 'accounts',
            ],
        ]);

        $router = $this->app->make('router');

        $router->aliasMiddleware('account', RedirectIfNotAccount::class);
        $router->aliasMiddleware('account.guest', RedirectIfAccount::class);

        $this->app->bind(AccountInterface::class, function () {
            return new AccountCacheDecorator(new AccountRepository(new Account));
        });

        $this->app->bind(AccountActivityLogInterface::class, function () {
            return new AccountActivityLogCacheDecorator(new AccountActivityLogRepository(new AccountActivityLog));
        });

        $this->app->bind(PackageInterface::class, function () {
            return new PackageCacheDecorator(
                new PackageRepository(new Package)
            );
        });

        $this->app->singleton(TransactionInterface::class, function () {
            return new TransactionCacheDecorator(new TransactionRepository(new Transaction));
        });

        $loader = AliasLoader::getInstance();
        $loader->alias('RealEstateHelper', RealEstateHelperFacade::class);

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        // Definição da constante CRM_MODULE_SCREEN_NAME no início do método boot
        if (!defined('CRM_MODULE_SCREEN_NAME')) {
            define('CRM_MODULE_SCREEN_NAME', 'crm');
        }
        
        SlugHelper::registerModule(Property::class, 'Real Estate Properties');
        SlugHelper::registerModule(Category::class, 'Real Estate Property Categories');
        SlugHelper::registerModule(Project::class, 'Real Estate Projects');
        SlugHelper::setPrefix(Project::class, 'projects');
        SlugHelper::setPrefix(Property::class, 'properties');
        SlugHelper::setPrefix(Category::class, 'property-category');

        $this->setNamespace('plugins/real-estate')
            ->loadAndPublishConfigurations(['permissions', 'email', 'real-estate', 'assets'])
            ->loadMigrations()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes(['api', 'web'])
            ->publishAssets();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id'          => 'cms-plugins-real-estate',
                    'priority'    => 5,
                    'parent_id'   => null,
                    'name'        => 'plugins/real-estate::real-estate.name',
                    'icon'        => 'fa fa-bed',
                    'permissions' => ['projects.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-property',
                    'priority'    => 0,
                    'parent_id'   => 'cms-plugins-real-estate',
                    'name'        => 'plugins/real-estate::property.name',
                    'icon'        => null,
                    'url'         => route('property.index'),
                    'permissions' => ['property.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-project',
                    'priority'    => 1,
                    'parent_id'   => 'cms-plugins-real-estate',
                    'name'        => 'plugins/real-estate::project.name',
                    'icon'        => null,
                    'url'         => route('project.index'),
                    'permissions' => ['project.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-re-feature',
                    'priority'    => 2,
                    'parent_id'   => 'cms-plugins-real-estate',
                    'name'        => 'plugins/real-estate::feature.name',
                    'icon'        => null,
                    'url'         => route('property_feature.index'),
                    'permissions' => ['property_feature.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-facility',
                    'priority'    => 3,
                    'parent_id'   => 'cms-plugins-real-estate',
                    'name'        => 'plugins/real-estate::facility.name',
                    'icon'        => null,
                    'url'         => route('facility.index'),
                    'permissions' => ['facility.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-investor',
                    'priority'    => 3,
                    'parent_id'   => 'cms-plugins-real-estate',
                    'name'        => 'plugins/real-estate::investor.name',
                    'icon'        => null,
                    'url'         => route('investor.index'),
                    'permissions' => ['investor.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-real-estate-zap-imoveis',
                    'priority'    => 20,
                    'parent_id'   => 'cms-plugins-real-estate',
                    'name'        => 'plugins/real-estate::property.zap_imoveis_integration',
                    'icon'        => null,
                    'url'         => route('zap-imoveis.index'),
                    'permissions' => ['property.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-real-estate-settings',
                    'priority'    => 999,
                    'parent_id'   => 'cms-plugins-real-estate',
                    'name'        => 'plugins/real-estate::real-estate.settings',
                    'icon'        => null,
                    'url'         => route('real-estate.settings'),
                    'permissions' => ['real-estate.settings'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-consult',
                    'priority'    => 6,
                    'parent_id'   => null,
                    'name'        => 'plugins/real-estate::consult.name',
                    'icon'        => 'fas fa-headset',
                    'url'         => route('consult.index'),
                    'permissions' => ['consult.index'],
                ])
                // Item de menu CRM
                ->registerItem([
                    'id'          => 'cms-plugins-crm',
                    'priority'    => 7,
                    'parent_id'   => null,
                    'name'        => 'plugins/real-estate::consult.crm',
                    'icon'        => 'fas fa-tasks',
                    'url'         => route('crm.index'),
                    'permissions' => ['crm.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-real-estate-category',
                    'priority'    => 4,
                    'parent_id'   => 'cms-plugins-real-estate',
                    'name'        => 'plugins/real-estate::category.name',
                    'icon'        => null,
                    'url'         => route('property_category.index'),
                    'permissions' => ['property_category.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-real-estate-account',
                    'priority'    => 22,
                    'parent_id'   => null,
                    'name'        => 'plugins/real-estate::account.name',
                    'icon'        => 'fa fa-users',
                    'url'         => route('account.index'),
                    'permissions' => ['account.index'],
                ]);

            if (RealEstateHelper::isEnabledCreditsSystem()) {
                dashboard_menu()
                    ->registerItem([
                        'id'          => 'cms-plugins-package',
                        'priority'    => 23,
                        'parent_id'   => null,
                        'name'        => 'plugins/real-estate::package.name',
                        'icon'        => 'fas fa-money-check-alt',
                        'url'         => route('package.index'),
                        'permissions' => ['package.index'],
                    ]);
            }
          
          \Assets::addScriptsDirectly([
              'vendor/core/plugins/real-estate/js/crm-mask.js',
              'vendor/core/plugins/real-estate/js/crm-property-match.js'
          ]);

            if (defined('SOCIAL_LOGIN_MODULE_SCREEN_NAME')) {
                SocialService::registerModule([
                    'guard'        => 'account',
                    'model'        => Account::class,
                    'login_url'    => route('public.account.login'),
                    'redirect_url' => route('public.account.dashboard'),
                ]);
            }
        });

        $this->app->register(CommandServiceProvider::class);

        $useLanguageV2 = $this->app['config']->get('plugins.real-estate.real-estate.use_language_v2', false) &&
            defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME');

        if (defined('LANGUAGE_MODULE_SCREEN_NAME') && $useLanguageV2) {
            LanguageAdvancedManager::registerModule(Property::class, [
                'name',
                'description',
                'content',
                'location',
            ]);

            LanguageAdvancedManager::registerModule(Project::class, [
                'name',
                'description',
                'content',
                'location',
            ]);

            LanguageAdvancedManager::registerModule(Category::class, [
                'name',
                'description',
            ]);

            LanguageAdvancedManager::registerModule(Feature::class, [
                'name',
            ]);

            LanguageAdvancedManager::registerModule(Facility::class, [
                'name',
            ]);
        }

        $this->app->booted(function () use ($useLanguageV2) {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME') && !$useLanguageV2) {
                Language::registerModule([
                    Property::class,
                    Project::class,
                    Feature::class,
                    Investor::class,
                    Category::class,
                    Facility::class,
                ]);
            }
        });

        $this->app->booted(function () {
            SeoHelper::registerModule([
                Property::class,
                Project::class,
            ]);

            $this->app->make(Schedule::class)->command(RenewPropertiesCommand::class)->dailyAt('23:30');

            EmailHandler::addTemplateSettings(REAL_ESTATE_MODULE_SCREEN_NAME, config('plugins.real-estate.email', []));

            $this->app->register(HookServiceProvider::class);
        });

        $this->app->register(EventServiceProvider::class);

        if (is_plugin_active('rss-feed') && Route::has('feeds.properties')) {
            \RssFeed::addFeedLink(route('feeds.properties'), 'Properties feed');
        }
    }
}