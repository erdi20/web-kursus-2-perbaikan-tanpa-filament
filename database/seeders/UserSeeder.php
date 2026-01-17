<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $password = Hash::make('password'); // Password default: "password"

        // 1 Admin
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => $password,
            'phone' => '081234567890',
            'address' => 'Jakarta, Indonesia',
            'birth_date' => '1990-01-01',
            'gender' => 'L',
            'education_level' => 'S2',
        ]);

        // 2 Mentor
        User::create([
            'name' => 'Mentor Satu',
            'email' => 'mentor1@example.com',
            'password' => $password,
            'phone' => '081234567891',
            'address' => 'Bandung, Indonesia',
            'birth_date' => '1985-05-15',
            'gender' => 'L',
            'education_level' => 'S3',
        ]);

        User::create([
            'name' => 'Mentor Dua',
            'email' => 'mentor2@example.com',
            'password' => $password,
            'phone' => '081234567892',
            'address' => 'Surabaya, Indonesia',
            'birth_date' => '1988-08-22',
            'gender' => 'P',
            'education_level' => 'S2',
        ]);

        // 3 Student
        User::create([
            'name' => 'Siswa Satu',
            'email' => 'siswa1@example.com',
            'password' => $password,
            'phone' => '081234567893',
            'address' => 'Yogyakarta, Indonesia',
            'birth_date' => '2000-03-10',
            'gender' => 'L',
            'education_level' => 'S1',
        ]);

        User::create([
            'name' => 'Siswa Dua',
            'email' => 'siswa2@example.com',
            'password' => $password,
            'phone' => '081234567894',
            'address' => 'Medan, Indonesia',
            'birth_date' => '2001-07-18',
            'gender' => 'P',
            'education_level' => 'SMA',
        ]);

        User::create([
            'name' => 'Siswa Tiga',
            'email' => 'siswa3@example.com',
            'password' => $password,
            'phone' => '081234567895',
            'address' => 'Makassar, Indonesia',
            'birth_date' => '1999-11-30',
            'gender' => 'L',
            'education_level' => 'S1',
        ]);
    }
}
