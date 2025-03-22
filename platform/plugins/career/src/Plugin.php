<?php

namespace Srapid\Career;

use Schema;
use Srapid\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('careers');
        Schema::dropIfExists('careers_translations');
    }
}
