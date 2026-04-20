<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            // Ubah queue_number dari integer ke string
            $table->string('queue_number', 20)->change();

            $table->foreignId('hospital_id')
                  ->after('patient_id')
                  ->constrained('hospitals')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->unsignedInteger('queue_number')->change();
            $table->dropForeign(['hospital_id']);
            $table->dropColumn('hospital_id');
        });
    }
};