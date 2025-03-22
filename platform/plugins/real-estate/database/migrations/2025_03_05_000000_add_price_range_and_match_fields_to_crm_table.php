<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceRangeAndMatchFieldsToCrmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('re_crm')) {
            Schema::table('re_crm', function (Blueprint $table) {
                if (!Schema::hasColumn('re_crm', 'min_price')) {
                    $table->decimal('min_price', 15, 2)->nullable()->after('property_value');
                }
                
                if (!Schema::hasColumn('re_crm', 'max_price')) {
                    $table->decimal('max_price', 15, 2)->nullable()->after('min_price');
                }
                
                if (!Schema::hasColumn('re_crm', 'has_match')) {
                    $table->boolean('has_match')->default(false)->after('lead_color');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('re_crm')) {
            Schema::table('re_crm', function (Blueprint $table) {
                if (Schema::hasColumn('re_crm', 'min_price')) {
                    $table->dropColumn('min_price');
                }
                
                if (Schema::hasColumn('re_crm', 'max_price')) {
                    $table->dropColumn('max_price');
                }
                
                if (Schema::hasColumn('re_crm', 'has_match')) {
                    $table->dropColumn('has_match');
                }
            });
        }
    }
}