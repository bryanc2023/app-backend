<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'adpostulate08@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('postulateadministrador2024.'), // Asegúrate de usar una contraseña segura
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            'role_id' => 1, // Asegúrate de que tienes esta columna en tu tabla
        ]);
    }
}
