<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')
                  ->constrained('queues')->cascadeOnDelete();
            $table->enum('action', ['called','started','finished','cancelled']);
            $table->timestamp('timestamp')->useCurrent();

            $table->index('queue_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_logs');
    }
};