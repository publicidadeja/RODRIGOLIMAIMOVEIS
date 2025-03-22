<?php

namespace Srapid\SocialLogin\Facades;

use Srapid\SocialLogin\Supports\SocialService;
use Illuminate\Support\Facades\Facade;

class SocialServiceFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SocialService::class;
    }
}
