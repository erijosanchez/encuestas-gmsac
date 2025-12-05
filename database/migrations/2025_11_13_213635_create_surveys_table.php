<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Datos del cliente (opcional)
            $table->string('client_name')->nullable();
            
            // Pregunta 1: ¿Cómo calificarías tu experiencia en TRIMAX?
            $table->tinyInteger('experience_rating');
            
            // Pregunta 2: ¿Cómo evaluarías la atención y soporte? (Consultor/Sede)
            $table->tinyInteger('service_quality_rating');
            
            // Comentarios
            $table->text('comments')->nullable();
            
            // Información adicional
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Índices
            $table->index('user_id');
            $table->index('experience_rating');
            $table->index('service_quality_rating');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
