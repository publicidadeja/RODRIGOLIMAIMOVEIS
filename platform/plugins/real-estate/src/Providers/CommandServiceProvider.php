<?php

namespace Srapid\RealEstate\Providers;

use Srapid\RealEstate\Commands\RenewPropertiesCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            RenewPropertiesCommand::class,
        ]);
    }
}
