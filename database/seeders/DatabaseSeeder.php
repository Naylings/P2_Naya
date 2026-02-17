<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        DB::table('jabatan')->insert([
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'Admin sistem',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Lurah',
                'slug' => 'kepala_lurah',
                'description' => 'Kepala lurah dengan akses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sekretaris Lurah',
                'slug' => 'sekre_lurah',
                'description' => 'Sekretaris lurah dengan akses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Staff Pelayanan Umum',
                'slug' => 'staff_pelayanan',
                'description' => 'Staff pelayanan umum dengan akses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('users')->insert([
        [
            'email' => 'admin@pemantapan2.com',
            'password' => Hash::make('123456'),
            'jabatan_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'email' => 'kepalalurah@pemantapan2.com',
            'password' => Hash::make('123456'),
            'jabatan_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'email' => 'sekre_lurah@pemantapan2.com',
            'password' => Hash::make('123456'),
            'jabatan_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'email' => 'staff_pelayanan@pemantapan2.com',
            'password' => Hash::make('123456'),
            'jabatan_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        ]);
        DB::table('user_detail')->insert([
        
            [
                'nip' => '19781212001',
                'name' => 'Budi Santoso',
                'no_hp' => '081234567890',
                'address' => 'Jl. Merdeka No. 10, Bandung',
                'birth_date' => '1978-12-12',
                'nik' => '3201011212780001',
                'status' => 'active',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '19850315002',
                'name' => 'Siti Aminah',
                'no_hp' => '082345678901',
                'address' => 'Jl. Asia Afrika No. 22, Bandung',
                'birth_date' => '1985-03-15',
                'nik' => '3201011503850002',
                'status' => 'active',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '19900120003',
                'name' => 'Andi Wijaya',
                'no_hp' => '083456789012',
                'address' => 'Jl. Dipatiukur No. 5, Bandung',
                'birth_date' => '1990-01-20',
                'nik' => '3201012001900003',
                'status' => 'active',
                'user_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]

        );
        
        $this->call(LurahConfigSeeder::class);

    }
}
