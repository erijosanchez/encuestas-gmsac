<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Consultor o Sede evaluado
            $table->string('client_name')->nullable(); // Nombre del cliente (opcional)
            $table->string('client_email')->nullable(); // Email del cliente (opcional)

            // Preguntas de la encuesta (1-4: Malo, Regular, Bueno, Muy Bueno)
            $table->tinyInteger('experience_rating'); // ¿Cómo calificarías tu experiencia en TRIMAX?
            $table->tinyInteger('service_quality_rating'); // Calidad de atención
            $table->tinyInteger('response_time_rating')->nullable(); // Tiempo de respuesta
            $table->tinyInteger('recommendation_rating')->nullable(); // ¿Recomendarías?

            $table->text('comments')->nullable(); // Comentarios adicionales
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
