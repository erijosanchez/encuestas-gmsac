<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_name',
        'client_email',
        'experience_rating',
        'service_quality_rating',
        'response_time_rating',
        'recommendation_rating',
        'comments',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'experience_rating' => 'integer',
        'service_quality_rating' => 'integer',
        'response_time_rating' => 'integer',
        'recommendation_rating' => 'integer',
    ];

    // Relación con el usuario (consultor o sede)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Obtener el texto de la calificación
    public function getRatingTextAttribute()
    {
        return match($this->experience_rating) {
            4 => 'Excelente',
            3 => 'Bueno',
            2 => 'Regular',
            1 => 'Malo',
            default => 'N/A',
        };
    }

    // Obtener el emoji de la calificación
    public function getRatingEmojiAttribute()
    {
        return match($this->experience_rating) {
            4 => '😊',
            3 => '🙂',
            2 => '😐',
            1 => '😞',
            default => '❓',
        };
    }

    // Scope para filtrar por rango de fechas
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Scope para filtrar por usuario
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope para filtrar por calificación
    public function scopeByRating($query, $rating)
    {
        return $query->where('experience_rating', $rating);
    }
}
