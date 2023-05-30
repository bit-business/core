<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameFCMMessagesTableToNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename(config('skijasi.database.prefix').'f_c_m_messages', config('skijasi.database.prefix').'notifications');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename(config('skijasi.database.prefix').'notifications', config('skijasi.database.prefix').'f_c_m_messages');
    }
}
