<?php

namespace Srapid\Menu\Providers;

use Srapid\Base\Events\DeletedContentEvent;
use Srapid\Menu\Listeners\DeleteMenuNodeListener;
use Srapid\Menu\Listeners\UpdateMenuNodeUrlListener;
use Srapid\Slug\Events\UpdatedSlugEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UpdatedSlugEvent::class    => [
            UpdateMenuNodeUrlListener::class,
        ],
        DeletedContentEvent::class => [
            DeleteMenuNodeListener::class,
        ],
    ];
}
