<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('schedule_id')
                  ->constrained('schedules')
                  ->cascadeOnDelete();
            $table->unsignedInteger('queue_number');
            $table->date('booking_date');
            $table->enum('status', [
                'waiting',
                'called',
                'in_progress',
                'done',
                'cancelled',
            ])->default('waiting');
            $table->string('token')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};