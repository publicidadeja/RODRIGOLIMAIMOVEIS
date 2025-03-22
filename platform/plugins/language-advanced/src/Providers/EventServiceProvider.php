<?php

namespace Srapid\LanguageAdvanced\Providers;

use Srapid\Base\Events\CreatedContentEvent;
use Srapid\Base\Events\DeletedContentEvent;
use Srapid\LanguageAdvanced\Listeners\AddDefaultTranslations;
use Srapid\LanguageAdvanced\Listeners\DeletedContentListener;
use Srapid\LanguageAdvanced\Listeners\PriorityLanguageAdvancedPluginListener;
use Srapid\PluginManagement\Events\ActivatedPluginEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        DeletedContentEvent::class  => [
            DeletedContentListener::class,
        ],
        CreatedContentEvent::class  => [
            AddDefaultTranslations::class,
        ],
        ActivatedPluginEvent::class => [
            PriorityLanguageAdvancedPluginListener::class,
        ],
    ];
}
