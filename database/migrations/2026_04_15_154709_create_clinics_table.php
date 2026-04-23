<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')
                  ->constrained('hospitals')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('hospital_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};