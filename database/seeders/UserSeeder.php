<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin
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
