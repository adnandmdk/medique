<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $doctor  = Role::firstOrCreate(['name' => 'doctor']);
        $patient = Role::firstOrCreate(['name' => 'patient']);

        $permissions = [
            'clinic.view','clinic.create','clinic.edit','clinic.delete',
            'doctor.view','doctor.create','doctor.edit','doctor.delete',
            'schedule.view','schedule.create','schedule.edit','schedule.delete',
            'queue.view','queue.create','queue.edit','queue.delete',
            'queue.call','queue.process',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin->syncPermissions(Permission::all());
        $doctor->syncPermissions(['schedule.view','queue.view','queue.call','queue.process']);
        $patient->syncPermissions(['queue.view','queue.create']);

        $hospital = Hospital::first();

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@medique.test'],
            [
                'hospital_id'    => null,
                'name'           => 'Super Admin',
                'password'       => Hash::make('password'),
                'phone'          => '081200000000',
                'role'           => 'admin',
                'is_super_admin' => true,
            ]
        );
        $superAdmin->assignRole('admin');

        // Admin RS
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@medique.test'],
            [
                'hospital_id' => optional($hospital)->id,
                'name'        => 'Admin Medika',
                'password'    => Hash::make('password'),
                'phone'       => '081200000001',
                'role'        => 'admin',
            ]
        );
        $adminUser->assignRole('admin');

        // Doctor
        $doctorUser = User::firstOrCreate(
            ['email' => 'doctor@medique.test'],
            [
                'hospital_id' => optional($hospital)->id,
                'name'        => 'Dr. John Doe',
                'password'    => Hash::make('password'),
                'phone'       => '081200000002',
                'role'        => 'doctor',
            ]
        );
        $doctorUser->assignRole('doctor');

        // Patient
        $patientUser = User::firstOrCreate(
            ['email' => 'patient@medique.test'],
            [
                'hospital_id' => optional($hospital)->id,
                'name'        => 'Jane Patient',
                'password'    => Hash::make('password'),
                'phone'       => '081200000003',
                'role'        => 'patient',
            ]
        );
        $patientUser->assignRole('patient');

        $this->command->info('✅ Roles & Users seeded!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'superadmin@medique.test', 'password'],
                ['Admin',       'admin@medique.test',       'password'],
                ['Doctor',      'doctor@medique.test',      'password'],
                ['Patient',     'patient@medique.test',     'password'],
            ]
        );
    }
}