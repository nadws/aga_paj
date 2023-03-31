<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\Posisi::create([
        //     'nm_posisi' => 'Presiden'
        // ]);
        // \App\Models\Posisi::create([
        //     'nm_posisi' => 'Admin'
        // ]);

        // \App\Models\User::factory()->create([
        //     'posisi_id' => 1,
        //     'name' => 'aldi',
        //     'email' => 'aldi@gmail.com',
        //     'password' => bcrypt('password'),
        // ]);

        for ($i = 0; $i < 500; $i++) {
            # code...
            \App\Models\Akun::create([
                'nm_akun' => 'Bank',
                'kode_akun' => $i,
                'id_klasifikasi' => '2'
            ]);
        }
    }
}
