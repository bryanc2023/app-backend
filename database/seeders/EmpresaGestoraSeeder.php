<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Empresa;

class EmpresaGestoraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear el usuario
        $user = User::create([
            'id' => 9,
            'name' => 'Proasetel',
            'email' => 'proasetel@gmail.com',
            'password' => Hash::make('Proasetel2024.'), // Hashear la contraseña
            'role_id' => 4, // Asigna el rol adecuado (si aplica)
            'email_verified_at' => now(),
        ]);
       

        // Crear la empresa asociada al usuario
        DB::table('empresa')->insert([
            'id_ubicacion' => 1, // Asigna el id de ubicación adecuado
            'id_usuario' => $user->id, // Usar el ID del usuario recién creado
            'id_sector' => 1, // Asigna el id de sector adecuado
            'nombre_comercial' => 'Proasetel',
            'tamanio' => 'Mediana',
            'descripcion' => 'Descripción de la empresa gestora',
            'logo' => 'https://firebasestorage.googleapis.com/v0/b/postu-a5f32.appspot.com/o/logos%2Fdescarga.jfif?alt=media&token=e4e04402-4bab-4915-8608-d8c7d5927054',
            'cantidad_empleados' => 50,
        ]);
    }
}
