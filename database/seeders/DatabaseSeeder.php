<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin
        User::create([
            'nama' => 'Administrator Glamping',
            'email' => 'admin@gmail.com',
            'no_hp' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status_akun' => 'disetujui',
        ]);

        // Akun Pemilik
        User::create([
            'nama' => 'Pemilik Glamping',
            'email' => 'pemilik@gmail.com',
            'no_hp' => '081298765432',
            'password' => Hash::make('password'),
            'role' => 'pemilik',
            'status_akun' => 'disetujui',
        ]);

        // Akun Pengunjung
        User::create([
            'nama' => 'Budi Pengunjung',
            'email' => 'user@gmail.com',
            'no_hp' => '081311223344',
            'password' => Hash::make('password'),
            'role' => 'pengunjung',
            'status_akun' => 'disetujui',
        ]);

         User::create([
            'nama' => 'Pengunjung',
            'email' => 'abc@gmail.com',
            'no_hp' => '081311223345',
            'password' => Hash::make('password'),
            'role' => 'pengunjung',
            'status_akun' => 'disetujui',
        ]);
    }
}
