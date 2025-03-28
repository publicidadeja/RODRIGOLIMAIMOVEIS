<?php

use Srapid\PluginManagement\Services\PluginService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (is_plugin_active('language') && !is_plugin_active('language-advanced') && File::isDirectory(plugin_path('language-advanced'))) {
            app(PluginService::class)->activate('language-advanced');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
