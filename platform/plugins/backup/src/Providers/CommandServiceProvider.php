<?php

namespace Srapid\Backup\Providers;

use Srapid\Backup\Commands\BackupCreateCommand;
use Srapid\Backup\Commands\BackupListCommand;
use Srapid\Backup\Commands\BackupRemoveCommand;
use Srapid\Backup\Commands\BackupRestoreCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackupCreateCommand::class,
                BackupRestoreCommand::class,
                BackupRemoveCommand::class,
                BackupListCommand::class,
            ]);
        }
    }
}
