<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario ONG demo
        $id = DB::table('usuarios')->insertGetId([
            'nombre_usuario'    => 'ong_demo',
            'correo_electronico'=> 'ong@demo.com',
            'contrasena'        => Hash::make('123456'),
            'tipo_usuario'      => 'ONG',
            'activo'            => true,
        ], 'id_usuario');

        DB::table('ongs')->insert([
            'user_id'     => $id,
            'nombre_ong'  => 'Fundación Demo',
            'NIT'         => '1234567',
            'telefono'    => '70000000',
            'direccion'   => 'Av. Siempre Viva 742',
            'sitio_web'   => 'https://demo.org',
            'descripcion' => 'ONG de prueba',
        ]);
    }
}
