<?php

namespace Srapid\Base\Providers;

use Srapid\Base\Commands\ClearLogCommand;
use Srapid\Base\Commands\InstallCommand;
use Srapid\Base\Commands\PublishAssetsCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            ClearLogCommand::class,
            InstallCommand::class,
            PublishAssetsCommand::class,
        ]);
    }
}
