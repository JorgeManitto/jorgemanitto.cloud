<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Statement extends Model
{
    protected $fillable = [
        'holder_name',
        'cvu',
        'cuit',
        'period',
        'saldo_inicial',
        'entradas',
        'salidas',
        'saldo_final',
        'pdf_path',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'entradas'      => 'decimal:2',
        'salidas'       => 'decimal:2',
        'saldo_final'   => 'decimal:2',
    ];

    /* ------------------------------------------------------------------ */

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class)->orderBy('date');
    }

    /* ------------------------------------------------------------------ */

    public function getTotalIncomeAttribute(): float
    {
        return $this->movements()->where('type', 'income')->sum('amount');
    }

    public function getTotalExpenseAttribute(): float
    {
        return $this->movements()->where('type', 'expense')->sum('amount');
    }

    public function getMovementsCountAttribute(): int
    {
        return $this->movements()->count();
    }
}
