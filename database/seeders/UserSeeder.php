<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador TRIMAX',
            'email' => 'admin@trimax.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Crear consultores de ejemplo
        $consultores = [
            'Juan Pérez Consultor',
            'María García Consultor',
            'Carlos López Consultor',
            'Ana Martínez Consultor',
        ];

        foreach ($consultores as $nombre) {
            User::create([
                'name' => $nombre,
                'email' => strtolower(str_replace(' ', '.', $nombre)) . '@trimax.com',
                'password' => Hash::make('password123'),
                'role' => 'consultor',
                'is_active' => true,
            ]);
        }

        // Crear sedes de ejemplo
        $sedes = [
            ['name' => 'Sede Lima Centro', 'location' => 'Lima Centro'],
            ['name' => 'Sede Lima Norte', 'location' => 'Lima Norte'],
            ['name' => 'Sede Arequipa', 'location' => 'Arequipa'],
            ['name' => 'Sede Trujillo', 'location' => 'Trujillo'],
            ['name' => 'Sede Cusco', 'location' => 'Cusco'],
        ];

        foreach ($sedes as $sede) {
            User::create([
                'name' => $sede['name'],
                'email' => strtolower(str_replace(' ', '.', $sede['name'])) . '@trimax.com',
                'password' => Hash::make('password123'),
                'role' => 'sede',
                'location' => $sede['location'],
                'is_active' => true,
            ]);
        }
    }
}
