<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsExpandInSkijasiMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('skijasi.database.prefix').'menu_items', function (Blueprint $table) {
            if (! Schema::hasColumn(config('skijasi.database.prefix').'menu_items', 'is_expand')) {
                $table->boolean('is_expand')->default(true)->after('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('skijasi.database.prefix').'menu_items', function (Blueprint $table) {
            if (Schema::hasColumn(config('skijasi.database.prefix').'menu_items', 'is_expand')) {
                $table->dropColumn('is_expand');
            }
        });
    }
}
