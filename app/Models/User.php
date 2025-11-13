<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Survey;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'consultor_id',
        'unique_token',
        'is_active',
        'phone',
        'location',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Generar token único al crear usuario
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->unique_token)) {
                $user->unique_token = Str::random(32);
            }
        });
    }

    // Relación con encuestas
    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    // Relación: Consultor asignado a la sede (si es sede)
    public function consultor()
    {
        return $this->belongsTo(User::class, 'consultor_id');
    }

    // Relación: Sedes que tiene asignadas (si es consultor)
    public function sedes()
    {
        return $this->hasMany(User::class, 'consultor_id');
    }

    // Obtener todas las encuestas incluyendo las de las sedes asignadas (si es consultor)
    public function allSurveys()
    {
        if ($this->role === 'consultor') {
            $sedesIds = $this->sedes->pluck('id')->toArray();
            return Survey::whereIn('user_id', array_merge([$this->id], $sedesIds));
        }
        return $this->surveys();
    }

    // Obtener URL de encuesta
    public function getSurveyUrlAttribute()
    {
        return url("/encuesta/{$this->unique_token}");
    }

    // Verificar si es admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Verificar si es consultor
    public function isConsultor()
    {
        return $this->role === 'consultor';
    }

    // Verificar si es sede
    public function isSede()
    {
        return $this->role === 'sede';
    }

    // Obtener estadísticas de encuestas (incluye sedes si es consultor)
    public function getStatistics()
    {
        // Si es consultor, incluir encuestas de sus sedes
        if ($this->role === 'consultor') {
            $surveys = $this->allSurveys()->get();
        } else {
            $surveys = $this->surveys;
        }
        
        $total = $surveys->count();
        
        if ($total === 0) {
            return [
                'total' => 0,
                'excellent' => 0,
                'good' => 0,
                'regular' => 0,
                'bad' => 0,
                'average_rating' => 0,
                'percentage_excellent' => 0,
                'percentage_good' => 0,
                'percentage_regular' => 0,
                'percentage_bad' => 0,
                'sedes_count' => $this->role === 'consultor' ? $this->sedes->count() : 0,
            ];
        }

        $excellent = $surveys->where('experience_rating', 4)->count();
        $good = $surveys->where('experience_rating', 3)->count();
        $regular = $surveys->where('experience_rating', 2)->count();
        $bad = $surveys->where('experience_rating', 1)->count();
        
        $avgRating = $surveys->avg('experience_rating');

        return [
            'total' => $total,
            'excellent' => $excellent,
            'good' => $good,
            'regular' => $regular,
            'bad' => $bad,
            'average_rating' => round($avgRating, 2),
            'percentage_excellent' => round(($excellent / $total) * 100, 1),
            'percentage_good' => round(($good / $total) * 100, 1),
            'percentage_regular' => round(($regular / $total) * 100, 1),
            'percentage_bad' => round(($bad / $total) * 100, 1),
            'sedes_count' => $this->role === 'consultor' ? $this->sedes->count() : 0,
        ];
    }
}
