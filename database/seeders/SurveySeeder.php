<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Survey;

class SurveySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('role', ['consultor', 'sede'])->get();

        $comentarios = [
            'Excelente atención, muy profesionales',
            'Me ayudaron mucho con mi problema',
            'Muy buena experiencia, recomendado',
            'Atención rápida y efectiva',
            'Personal muy amable y capacitado',
            'Podrían mejorar los tiempos de espera',
            'Buena atención pero el local es pequeño',
            'Todo perfecto, volveré sin duda',
            null, // Sin comentarios
            null,
        ];

        $nombres = [
            'Carlos Mendoza',
            'María Torres',
            'Juan Pérez',
            'Ana Rodríguez',
            'Luis García',
            'Carmen Silva',
            'Pedro Flores',
            'Rosa Martínez',
            null, // Anónimo
            null,
            null,
        ];

        foreach ($users as $user) {
            // Generar entre 5 y 20 encuestas por usuario
            $numEncuestas = rand(5, 20);

            for ($i = 0; $i < $numEncuestas; $i++) {
                // Generar ratings con tendencia positiva (70% calificaciones 3-4)
                $experienceRating = $this->getWeightedRating();
                $serviceQualityRating = $this->getWeightedRating();

                Survey::create([
                    'user_id' => $user->id,
                    'client_name' => $nombres[array_rand($nombres)],
                    'experience_rating' => $experienceRating,
                    'service_quality_rating' => $serviceQualityRating,
                    'comments' => $comentarios[array_rand($comentarios)],
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'created_at' => now()->subDays(rand(0, 60))->subHours(rand(0, 23)),
                ]);
            }
        }
    }

    private function getWeightedRating()
    {
        $rand = rand(1, 100);
        
        if ($rand <= 45) return 4; // 45% Muy Feliz
        if ($rand <= 75) return 3; // 30% Feliz
        if ($rand <= 90) return 2; // 15% Insatisfecho
        return 1; // 10% Muy Insatisfecho
    }

    private function generateRandomIP()
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 255);
    }
}
