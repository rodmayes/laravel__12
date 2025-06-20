<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaytomicBookingTable extends Migration
{
    public function up()
    {
        Schema::create('playtomic_booking', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->unsignedBigInteger('club_id');
            $table->string('resources');
            $table->string('booking_preference')->default('resource');
            $table->unsignedBigInteger('timetable_id');
            $table->dateTime('started_at');
            $table->text('log')->nullable();
            $table->enum('status', ['on-time', 'time-out', 'closed'])->default('on-time');
            $table->string('player_email')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedInteger('public')->default(0);
            $table->string('playtomic_url_check_match', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('club_id', 'fk_playtomic_booking_club')->references('id')->on('playtomic_club');
            $table->foreign('timetable_id', 'fk_playtomic_booking_timetable')->references('id')->on('playtomic_timetable');
            $table->foreign('player_email', 'fk_playtomic_booking_player_email')->references('email')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playtomic_booking');
    }
}
