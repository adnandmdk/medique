<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    // JANGAN jalan saat build / console awal
    if (App::runningInConsole()) {
        return;
    }

    // Pastikan tabel sudah ada
    if (!Schema::hasTable('roles')) {
        return;
    }

    try {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'doctor']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'patient']);
    } catch (\Throwable $e) {
        // skip kalau DB belum ready
    }
}
}
