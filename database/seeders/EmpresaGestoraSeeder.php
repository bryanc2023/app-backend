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

        // Crear la empresa asociada al usuario
        DB::table('empresa')->insert([
            'id_ubicacion' => 1, // Asigna el id de ubicación adecuado
            'id_usuario' => 2,
            'id_sector' => 1, // Asigna el id de sector adecuado
            'nombre_comercial' => 'Proasetel',
            'tamanio' => 'Mediana',
            'descripcion' => 'Descripción de la empresa gestora',
            'logo' => 'https://firebasestorage.googleapis.com/v0/b/proajob-486e1.appspot.com/o/logos%2Fimages%20(1).jfif?alt=media&token=fdf3ed9c-9e3c-4505-9465-665e3cfe9d9f',
            'cantidad_empleados' => 50,

        ]);
    }
}
