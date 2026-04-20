<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('hospital_id')->nullable()
                  ->after('id')
                  ->constrained('hospitals')
                  ->nullOnDelete();
            $table->string('nik', 20)->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('nik');
            $table->enum('gender', ['male', 'female'])->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropColumn(['hospital_id', 'nik', 'date_of_birth', 'gender', 'address']);
        });
    }
};