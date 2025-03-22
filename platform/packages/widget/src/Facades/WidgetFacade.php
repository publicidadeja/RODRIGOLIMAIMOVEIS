<?php

namespace Srapid\Widget\Facades;

use Srapid\Widget\WidgetGroup;
use Illuminate\Support\Facades\Facade;

class WidgetFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'srapid.widget';
    }

    /**
     * Get the widget group object.
     *
     * @param string $name
     *
     * @return WidgetGroup
     */
    public static function group($name)
    {
        return app('srapid.widget-group-collection')->group($name);
    }
}
