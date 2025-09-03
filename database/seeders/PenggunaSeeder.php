<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'nama_pengguna' => 'Haris Abidi',
                'username'      => 'haris abidi',
                'password'      => Hash::make('password123'), // ganti sesuai kebutuhan
                'role'          => 'admin',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama_pengguna' => 'Guru 1',
                'username'      => 'guru1',
                'password'      => Hash::make('password123'),
                'role'          => 'guru',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nama_pengguna' => 'Guru Piket 1',
                'username'      => 'gurupiket1',
                'password'      => Hash::make('password123'),
                'role'          => 'guru_piket',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        ]);
    }
}
