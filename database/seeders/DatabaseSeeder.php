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
                'name' => 'Lurah',
                'slug' => 'lurah',
                'description' => 'Kepala kelurahan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sekretaris',
                'slug' => 'sekretaris',
                'description' => 'Sekretaris kelurahan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kasi Pemerintahan',
                'slug' => 'kasi-pemerintahan',
                'description' => 'Kepala seksi pemerintahan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kasi Kesejahteraan',
                'slug' => 'kasi-kesejahteraan',
                'description' => 'Kepala seksi kesejahteraan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kasi Pelayanan',
                'slug' => 'kasi-pelayanan',
                'description' => 'Kepala seksi pelayanan umum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Staf kelurahan',
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
            'email' => 'lurah@pemantapan2.com',
            'password' => Hash::make('123456'),
            'jabatan_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        ]);
    }
}
