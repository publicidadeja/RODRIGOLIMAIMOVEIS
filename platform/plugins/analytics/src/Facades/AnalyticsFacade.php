<?php

namespace Srapid\Analytics\Facades;

use Srapid\Analytics\Analytics;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Srapid\Analytics\Analytics
 */
class AnalyticsFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Analytics::class;
    }
}
