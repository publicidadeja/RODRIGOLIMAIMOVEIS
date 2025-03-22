<?php

namespace Srapid\AuditLog\Providers;

use Srapid\AuditLog\Commands\ActivityLogClearCommand;
use Srapid\AuditLog\Commands\CleanOldLogsCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActivityLogClearCommand::class,
                CleanOldLogsCommand::class,
            ]);
        }
    }
}
