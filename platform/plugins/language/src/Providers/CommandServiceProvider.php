<?php

namespace Srapid\Language\Providers;

use Srapid\Language\Commands\RouteCacheCommand;
use Srapid\Language\Commands\RouteClearCommand;
use Srapid\Language\Commands\SyncOldDataCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            SyncOldDataCommand::class,
        ]);

        $this->app->extend('command.route.cache', function () {
            return new RouteCacheCommand($this->app['files']);
        });

        $this->app->extend('command.route.clear', function () {
            return new RouteClearCommand($this->app['files']);
        });
    }
}
