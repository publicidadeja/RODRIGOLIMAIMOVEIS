<?php

namespace Srapid\Contact\Providers;

use EmailHandler;
use Illuminate\Routing\Events\RouteMatched;
use Srapid\Base\Traits\LoadAndPublishDataTrait;
use Srapid\Contact\Models\ContactReply;
use Srapid\Contact\Repositories\Caches\ContactReplyCacheDecorator;
use Srapid\Contact\Repositories\Eloquent\ContactReplyRepository;
use Srapid\Contact\Repositories\Interfaces\ContactInterface;
use Srapid\Contact\Models\Contact;
use Srapid\Contact\Repositories\Caches\ContactCacheDecorator;
use Srapid\Contact\Repositories\Eloquent\ContactRepository;
use Srapid\Contact\Repositories\Interfaces\ContactReplyInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ContactServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(ContactInterface::class, function () {
            return new ContactCacheDecorator(new ContactRepository(new Contact));
        });

        $this->app->bind(ContactReplyInterface::class, function () {
            return new ContactReplyCacheDecorator(new ContactReplyRepository(new ContactReply));
        });
    }

    public function boot()
    {
        $this
            ->setNamespace('plugins/contact')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadRoutes(['web'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations()
            ->publishAssets();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-contact',
                'priority'    => 120,
                'parent_id'   => null,
                'name'        => 'plugins/contact::contact.menu',
                'icon'        => 'far fa-envelope',
                'url'         => route('contacts.index'),
                'permissions' => ['contacts.index'],
            ]);

            EmailHandler::addTemplateSettings(CONTACT_MODULE_SCREEN_NAME, config('plugins.contact.email', []));
        });

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
