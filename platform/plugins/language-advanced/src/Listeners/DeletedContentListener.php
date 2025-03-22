<?php

namespace Srapid\LanguageAdvanced\Listeners;

use Srapid\Base\Events\DeletedContentEvent;
use Srapid\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Illuminate\Support\Facades\DB;

class DeletedContentListener
{

    /**
     * Handle the event.
     *
     * @param DeletedContentEvent $event
     * @return void
     */
    public function handle(DeletedContentEvent $event)
    {
        if (LanguageAdvancedManager::isSupported($event->data)) {

            $table = $event->data->getTable() . '_translations';

            DB::table($table)->where([$event->data->getTable() . '_id' => $event->data->id])->delete();
        }
    }
}
