<?php

namespace Srapid\RssFeed\Facades;

use Srapid\RssFeed\Supports\RssFeed;
use Illuminate\Support\Facades\Facade;

class RssFeedFacade extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return RssFeed::class;
    }
}
