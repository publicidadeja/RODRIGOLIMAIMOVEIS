<?php

namespace Srapid\Base\Facades;

use Srapid\Base\Supports\BreadcrumbsManager;
use Illuminate\Support\Facades\Facade;

class BreadcrumbsFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BreadcrumbsManager::class;
    }
}
