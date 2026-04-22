<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('clinics', function (Blueprint $table) {
            $table->foreignId('hospital_id')->after('id')
                  ->constrained('hospitals')->cascadeOnDelete();
            $table->string('code', 10)->nullable()->after('name');
            $table->index('hospital_id');
        });
    }
    public function down(): void {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropColumn(['hospital_id','code']);
        });
    }
};