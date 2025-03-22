<?php

namespace Srapid\RealEstate\Facades;

use Srapid\RealEstate\Supports\RealEstateHelper;
use Illuminate\Support\Facades\Facade;

class RealEstateHelperFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return RealEstateHelper::class;
    }
}
