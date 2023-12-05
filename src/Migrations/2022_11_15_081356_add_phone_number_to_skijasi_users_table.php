<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneNumberToSkijasiUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('skijasi.database.prefix').'users', function (Blueprint $table) {
            $table->string('brojmobitela', 20)->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('skijasi.database.prefix').'users', function (Blueprint $table) {
            $table->dropColumn('brojmobitela');
        });
    }
}
