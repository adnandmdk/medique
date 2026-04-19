<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $doctor  = Role::firstOrCreate(['name' => 'doctor']);
        $patient = Role::firstOrCreate(['name' => 'patient']);

        $permissions = [
            'clinic.view', 'clinic.create', 'clinic.edit', 'clinic.delete',
            'doctor.view', 'doctor.create', 'doctor.edit', 'doctor.delete',
            'schedule.view', 'schedule.create', 'schedule.edit', 'schedule.delete',
            'queue.view', 'queue.create', 'queue.edit', 'queue.delete',
            'queue.call', 'queue.process',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin->syncPermissions(Permission::all());
        $doctor->syncPermissions([
            'schedule.view',
            'queue.view', 'queue.call', 'queue.process',
        ]);
        $patient->syncPermissions([
            'queue.view', 'queue.create',
        ]);

        $this->createUser('Super Admin',  'admin@medique.test',   '081200000000', 'admin');
        $this->createUser('Dr. John Doe', 'doctor@medique.test',  '081200000001', 'doctor');
        $this->createUser('Jane Patient', 'patient@medique.test', '081200000002', 'patient');

        $this->command->info('✅ Seeder selesai!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',   'admin@medique.test',   'password'],
                ['Doctor',  'doctor@medique.test',  'password'],
                ['Patient', 'patient@medique.test', 'password'],
            ]
        );
    }

    private function createUser(string $name, string $email, string $phone, string $role): User
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => Hash::make('password'),
                'phone'    => $phone,
                'role'     => $role,
            ]
        );

        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        return $user;
    }
}