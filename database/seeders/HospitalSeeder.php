<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hospital;
use App\Models\Clinic;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        $hospitals = [
            [
                'name'    => 'RSUD Kota Medika',
                'address' => 'Jl. Kesehatan No. 1, Jakarta Pusat',
                'phone'   => '021-12345678',
                'email'   => 'info@rsud-medika.id',
                'tagline' => 'Melayani dengan Hati',
            ],
            [
                'name'    => 'RS Harapan Sehat',
                'address' => 'Jl. Sehat Raya No. 45, Bandung',
                'phone'   => '022-87654321',
                'email'   => 'info@harapansehat.id',
                'tagline' => 'Kesehatan Anda Prioritas Kami',
            ],
        ];

        foreach ($hospitals as $data) {
            $hospital = Hospital::firstOrCreate(['name' => $data['name']], $data);

            // Buat poli default per RS
            $polis = [
                ['name' => 'Poli Umum', 'code' => 'PU', 'location' => 'Lantai 1, Gedung A'],
                ['name' => 'Poli Gigi', 'code' => 'PG', 'location' => 'Lantai 1, Gedung B'],
                ['name' => 'Poli Anak', 'code' => 'PA', 'location' => 'Lantai 2, Gedung A'],
            ];

            foreach ($polis as $poli) {
                Clinic::firstOrCreate(
                    ['hospital_id' => $hospital->id, 'name' => $poli['name']],
                    ['code' => $poli['code'], 'location' => $poli['location'], 'is_active' => true]
                );
            }
        }

        $this->command->info('✅ Hospitals & Clinics seeded!');
    }
}