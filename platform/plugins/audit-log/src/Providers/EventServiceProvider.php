<?php

namespace Srapid\AuditLog\Providers;

use Srapid\AuditLog\Events\AuditHandlerEvent;
use Srapid\AuditLog\Listeners\AuditHandlerListener;
use Srapid\AuditLog\Listeners\CreatedContentListener;
use Srapid\AuditLog\Listeners\DeletedContentListener;
use Srapid\AuditLog\Listeners\LoginListener;
use Srapid\AuditLog\Listeners\UpdatedContentListener;
use Srapid\Base\Events\CreatedContentEvent;
use Srapid\Base\Events\DeletedContentEvent;
use Srapid\Base\Events\UpdatedContentEvent;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        AuditHandlerEvent::class   => [
            AuditHandlerListener::class,
        ],
        Login::class               => [
            LoginListener::class,
        ],
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
        ],
        CreatedContentEvent::class => [
            CreatedContentListener::class,
        ],
        DeletedContentEvent::class => [
            DeletedContentListener::class,
        ],
    ];
}
