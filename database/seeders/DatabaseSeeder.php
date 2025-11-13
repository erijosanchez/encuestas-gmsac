<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Survey;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        $admin = User::create([
            'name' => 'Administrador TRIMAX',
            'email' => 'admin@trimax.com',
            'password' => Hash::make('Trimax2024!'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        echo "✅ Admin creado: admin@trimax.com / Trimax2024!\n";

        // Consultores de ejemplo
        $consultores = [
            [
                'name' => 'Juan Pérez Gonzales',
                'email' => 'juan.perez@trimax.com',
                'phone' => '+51 999 888 777',
            ],
            [
                'name' => 'María López Rodriguez',
                'email' => 'maria.lopez@trimax.com',
                'phone' => '+51 988 777 666',
            ],
            [
                'name' => 'Carlos Ramírez Silva',
                'email' => 'carlos.ramirez@trimax.com',
                'phone' => '+51 977 666 555',
            ],
            [
                'name' => 'Ana Torres Mendoza',
                'email' => 'ana.torres@trimax.com',
                'phone' => '+51 966 555 444',
            ],
            [
                'name' => 'Roberto Flores Castillo',
                'email' => 'roberto.flores@trimax.com',
                'phone' => '+51 955 444 333',
            ],
        ];

        foreach ($consultores as $consultor) {
            $user = User::create([
                'name' => $consultor['name'],
                'email' => $consultor['email'],
                'password' => Hash::make('password123'),
                'role' => 'consultor',
                'phone' => $consultor['phone'],
                'unique_token' => Str::random(32),
                'is_active' => true,
            ]);

            echo "✅ Consultor creado: {$user->name} - URL: {$user->survey_url}\n";

            // Crear encuestas de ejemplo para cada consultor
            $this->createSampleSurveys($user);
        }

        // Obtener IDs de consultores para asignar a sedes
        $consultorIds = User::where('role', 'consultor')->pluck('id')->toArray();

        // Sedes de ejemplo
        $sedes = [
            [
                'name' => 'Sede Lima Centro',
                'email' => 'lima.centro@trimax.com',
                'location' => 'Av. Arequipa 1234, Lima',
                'phone' => '+51 01 444 5555',
                'consultor_id' => $consultorIds[0] ?? null, // Juan Pérez
            ],
            [
                'name' => 'Sede San Isidro',
                'email' => 'san.isidro@trimax.com',
                'location' => 'Av. Conquistadores 567, San Isidro',
                'phone' => '+51 01 555 6666',
                'consultor_id' => $consultorIds[1] ?? null, // María López
            ],
            [
                'name' => 'Sede Miraflores',
                'email' => 'miraflores@trimax.com',
                'location' => 'Av. Larco 890, Miraflores',
                'phone' => '+51 01 666 7777',
                'consultor_id' => $consultorIds[0] ?? null, // Juan Pérez (tiene 2 sedes)
            ],
            [
                'name' => 'Sede Surco',
                'email' => 'surco@trimax.com',
                'location' => 'Av. Primavera 2345, Surco',
                'phone' => '+51 01 777 8888',
                'consultor_id' => $consultorIds[2] ?? null, // Carlos Ramírez
            ],
        ];

        foreach ($sedes as $sede) {
            $user = User::create([
                'name' => $sede['name'],
                'email' => $sede['email'],
                'password' => Hash::make('password123'),
                'role' => 'sede',
                'consultor_id' => $sede['consultor_id'], // Asignar consultor
                'location' => $sede['location'],
                'phone' => $sede['phone'],
                'unique_token' => Str::random(32),
                'is_active' => true,
            ]);

            $consultorName = $user->consultor ? $user->consultor->name : 'Sin asignar';
            echo "✅ Sede creada: {$user->name} (Consultor: {$consultorName}) - URL: {$user->survey_url}\n";

            // Crear encuestas de ejemplo para cada sede
            $this->createSampleSurveys($user);
        }

        echo "\n🎉 Base de datos poblada exitosamente!\n";
        echo "📧 Admin: admin@trimax.com\n";
        echo "🔑 Password: Trimax2024!\n";
    }

    /**
     * Crear encuestas de ejemplo
     */
    private function createSampleSurveys($user)
    {
        $ratings = [1, 2, 3, 4];
        $clientNames = [
            'José Martínez', 'Laura Sánchez', 'Pedro García', 'Carmen Rojas',
            'Miguel Vega', 'Rosa Herrera', 'Luis Morales', 'Sofía Castillo',
            'Diego Torres', 'Lucía Mendoza', 'Fernando Díaz', 'Patricia Ruiz'
        ];

        $comments = [
            'Excelente servicio, muy profesional',
            'Buena atención pero podría mejorar',
            'Servicio regular, esperaba más',
            'No quedé satisfecho con la atención',
            'Muy buena experiencia, totalmente recomendado',
            'El personal fue muy amable y resolutivo',
            'Tardaron un poco pero resolvieron mi problema',
            'No me gustó la atención recibida',
        ];

        // Crear entre 8 y 15 encuestas por usuario
        $numSurveys = rand(8, 15);

        for ($i = 0; $i < $numSurveys; $i++) {
            $rating = $ratings[array_rand($ratings)];
            
            Survey::create([
                'user_id' => $user->id,
                'client_name' => $clientNames[array_rand($clientNames)],
                'client_email' => strtolower(str_replace(' ', '.', $clientNames[array_rand($clientNames)])) . '@gmail.com',
                'experience_rating' => $rating,
                'service_quality_rating' => $ratings[array_rand($ratings)],
                'response_time_rating' => $ratings[array_rand($ratings)],
                'recommendation_rating' => $ratings[array_rand($ratings)],
                'comments' => rand(0, 1) ? $comments[array_rand($comments)] : null,
                'ip_address' => rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255),
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(1, 60)),
            ]);
        }
    }
}
