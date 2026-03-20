<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WatchlistItem extends Model
{
    protected $fillable = [
        'title',
        'type',
        'status',
        'genre',
        'year',
        'platform',
        'rating',
        'current_episode',
        'total_episodes',
        'seasons',
        'notes',
        'poster_url',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at'  => 'date',
        'finished_at' => 'date',
        'rating'      => 'integer',
    ];

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                            */
    /* ------------------------------------------------------------------ */

    public function scopeMovies($query)
    {
        return $query->where('type', 'movie');
    }

    public function scopeSeries($query)
    {
        return $query->where('type', 'series');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($filters['platform'] ?? null, fn ($q, $platform) => $q->where('platform', $platform));
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                         */
    /* ------------------------------------------------------------------ */

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => match ($this->status) {
            'pending'   => 'Pendiente',
            'watching'  => 'Viendo',
            'completed' => 'Completado',
            'dropped'   => 'Abandonado',
            default     => $this->status,
        });
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn () => match ($this->status) {
            'pending'   => 'warning',
            'watching'  => 'primary',
            'completed' => 'success',
            'dropped'   => 'destructive',
            default     => 'secondary',
        });
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::get(fn () => match ($this->type) {
            'movie'  => 'Película',
            'series' => 'Serie',
            default  => $this->type,
        });
    }

    protected function typeEmoji(): Attribute
    {
        return Attribute::get(fn () => match ($this->type) {
            'movie'  => '🎬',
            'series' => '📺',
            default  => '🎞️',
        });
    }

    protected function progress(): Attribute
    {
        return Attribute::get(function () {
            if ($this->type !== 'series' || !$this->total_episodes) {
                return null;
            }
            return round(($this->current_episode / $this->total_episodes) * 100);
        });
    }

    protected function ratingStars(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->rating) return null;
            $full  = intdiv($this->rating, 2);
            $half  = $this->rating % 2;
            $empty = 5 - $full - $half;
            return str_repeat('★', $full) . str_repeat('½', $half) . str_repeat('☆', $empty);
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Constants                                                         */
    /* ------------------------------------------------------------------ */

    public const TYPES = [
        'movie'  => 'Película',
        'series' => 'Serie',
    ];

    public const STATUSES = [
        'pending'   => 'Pendiente',
        'watching'  => 'Viendo',
        'completed' => 'Completado',
        'dropped'   => 'Abandonado',
    ];

    public const PLATFORMS = [
        'Netflix', 'HBO Max', 'Disney+', 'Amazon Prime',
        'Apple TV+', 'Star+', 'Paramount+', 'Crunchyroll',
        'YouTube', 'Otro',
    ];

    public const GENRES = [
        'Acción', 'Aventura', 'Animación', 'Ciencia Ficción',
        'Comedia', 'Crimen', 'Documental', 'Drama',
        'Fantasía', 'Horror', 'Misterio', 'Romance',
        'Suspenso', 'Terror', 'Otro',
    ];
}
