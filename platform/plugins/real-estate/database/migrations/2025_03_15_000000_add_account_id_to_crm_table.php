<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountIdToCrmTable extends Migration
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
                if (!Schema::hasColumn('re_crm', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable()->after('id');
                    $table->foreign('account_id')->references('id')->on('re_accounts')->onDelete('set null');
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
                if (Schema::hasColumn('re_crm', 'account_id')) {
                    $table->dropForeign(['account_id']);
                    $table->dropColumn('account_id');
                }
            });
        }
    }
}