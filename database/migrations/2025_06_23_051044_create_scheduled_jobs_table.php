<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scheduled_jobs', function (Blueprint $table) {
            $table->id();
            $table->morphs('schedulable'); // schedulable_id + schedulable_type
            $table->uuid('job_id')->index(); // Laravel 12 usa UUIDs para los jobs por defecto
            $table->string('job_type',255);
            $table->json('payload')->nullable(); // datos adicionales del job
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_jobs');
    }
};
