<?php

namespace Srapid\LanguageAdvanced\Listeners;

use Srapid\LanguageAdvanced\Plugin;
use Exception;

class PriorityLanguageAdvancedPluginListener
{

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Plugin::activated();
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
