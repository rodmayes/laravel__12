<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClubAddColumnBookingHourTable extends Migration
{
    public function up()
    {
        Schema::table('playtomic_club', function($table) {
            $table->time('booking_hour')->default('08:00:00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('playtomic_club', function($table) {
            $table->dropColumn('booking_hour');
        });
    }
}
