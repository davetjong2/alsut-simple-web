<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SawitTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'sawit_plant_id',
        'type',
        'quantity',
        'amount',
        'coin_flow',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sawitPlant(): BelongsTo
    {
        return $this->belongsTo(SawitPlant::class);
    }
}