<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOrderIntegerDataTypeInMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('skijasi.database.prefix').'menus', function (Blueprint $table) {
            if (Schema::hasColumn(config('skijasi.database.prefix').'menus', 'order')) {
                $table->dropColumn('order');
            }
        });
        Schema::table(config('skijasi.database.prefix').'menus', function (Blueprint $table) {
            if (! Schema::hasColumn(config('skijasi.database.prefix').'menus', 'order')) {
                $table->integer('order')->nullable()->before('icon');
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
    }
}
