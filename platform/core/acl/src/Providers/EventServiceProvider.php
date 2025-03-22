<?php

namespace Srapid\ACL\Providers;

use Srapid\ACL\Events\RoleAssignmentEvent;
use Srapid\ACL\Events\RoleUpdateEvent;
use Srapid\ACL\Listeners\LoginListener;
use Srapid\ACL\Listeners\RoleAssignmentListener;
use Srapid\ACL\Listeners\RoleUpdateListener;
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
        RoleUpdateEvent::class     => [
            RoleUpdateListener::class,
        ],
        RoleAssignmentEvent::class => [
            RoleAssignmentListener::class,
        ],
        Login::class               => [
            LoginListener::class,
        ],
    ];
}
