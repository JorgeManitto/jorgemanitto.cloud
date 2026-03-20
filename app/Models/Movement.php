<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movement extends Model
{
    protected $fillable = [
        'statement_id',
        'date',
        'description',
        'operation_id',
        'amount',
        'balance',
        'type',
        'category',
    ];

    protected $casts = [
        'date'    => 'date',
        'amount'  => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /* ------------------------------------------------------------------ */

    public function statement(): BelongsTo
    {
        return $this->belongsTo(Statement::class);
    }

    /* ------------------------------------------------------------------ */

    public function getIsIncomeAttribute(): bool
    {
        return $this->type === 'income';
    }

    public function getIsExpenseAttribute(): bool
    {
        return $this->type === 'expense';
    }
}
