<?php

namespace Srapid\PluginManagement\Providers;

use Srapid\PluginManagement\Commands\PluginActivateAllCommand;
use Srapid\PluginManagement\Commands\PluginActivateCommand;
use Srapid\PluginManagement\Commands\PluginAssetsPublishCommand;
use Srapid\PluginManagement\Commands\PluginDeactivateAllCommand;
use Srapid\PluginManagement\Commands\PluginDeactivateCommand;
use Srapid\PluginManagement\Commands\PluginRemoveCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PluginAssetsPublishCommand::class,
            ]);
        }

        $this->commands([
            PluginActivateCommand::class,
            PluginDeactivateCommand::class,
            PluginRemoveCommand::class,
            PluginActivateAllCommand::class,
            PluginDeactivateAllCommand::class,
        ]);
    }
}
