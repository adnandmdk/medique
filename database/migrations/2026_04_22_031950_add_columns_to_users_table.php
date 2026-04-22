<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            
            $table->boolean('is_super_admin')->default(false)->after('address');
            
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropColumn(['hospital_id','nik','date_of_birth','gender','address','is_super_admin']);
        });
    }
};