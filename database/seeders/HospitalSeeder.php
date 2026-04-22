<?php
namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        $hospitals = [
            ['name' => 'RSUD Kota Medika', 'code' => 'RSUDKM', 'address' => 'Jl. Kesehatan No. 1, Jakarta', 'tagline' => 'Melayani dengan Hati'],
            ['name' => 'RS Harapan Sehat',  'code' => 'RSHS',   'address' => 'Jl. Sehat Raya No. 45, Bandung', 'tagline' => 'Kesehatan Prioritas Kami'],
        ];

        $poliTemplate = [
            ['name' => 'Poli Umum',    'code' => 'PU', 'location' => 'Lantai 1, Gedung A'],
            ['name' => 'Poli Gigi',    'code' => 'PG', 'location' => 'Lantai 1, Gedung B'],
            ['name' => 'Poli Anak',    'code' => 'PA', 'location' => 'Lantai 2, Gedung A'],
            ['name' => 'Poli Mata',    'code' => 'PM', 'location' => 'Lantai 2, Gedung B'],
            ['name' => 'Poli Jantung', 'code' => 'PJ', 'location' => 'Lantai 3, Gedung A'],
        ];

        foreach ($hospitals as $data) {
            $hospital = Hospital::firstOrCreate(['code' => $data['code']], $data);

            foreach ($poliTemplate as $poli) {
                Clinic::firstOrCreate(
                    ['hospital_id' => $hospital->id, 'name' => $poli['name']],
                    array_merge($poli, ['is_active' => true])
                );
            }

            $this->command->info("✅ {$hospital->name} seeded.");
        }
    }
}