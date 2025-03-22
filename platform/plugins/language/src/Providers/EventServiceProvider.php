<?php

namespace Srapid\Language\Providers;

use Srapid\Base\Events\CreatedContentEvent;
use Srapid\Base\Events\DeletedContentEvent;
use Srapid\Base\Events\UpdatedContentEvent;
use Srapid\Language\Listeners\ActivatedPluginListener;
use Srapid\Language\Listeners\AddHrefLangListener;
use Srapid\Language\Listeners\CreatedContentListener;
use Srapid\Language\Listeners\DeletedContentListener;
use Srapid\Language\Listeners\ThemeRemoveListener;
use Srapid\Language\Listeners\UpdatedContentListener;
use Srapid\PluginManagement\Events\ActivatedPluginEvent;
use Srapid\Theme\Events\RenderingSingleEvent;
use Srapid\Theme\Events\ThemeRemoveEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UpdatedContentEvent::class  => [
            UpdatedContentListener::class,
        ],
        CreatedContentEvent::class  => [
            CreatedContentListener::class,
        ],
        DeletedContentEvent::class  => [
            DeletedContentListener::class,
        ],
        ThemeRemoveEvent::class     => [
            ThemeRemoveListener::class,
        ],
        ActivatedPluginEvent::class => [
            ActivatedPluginListener::class,
        ],
        RenderingSingleEvent::class => [
            AddHrefLangListener::class,
        ],
    ];
}
