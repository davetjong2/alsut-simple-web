<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class SawitPlant extends Model
{    
    protected $fillable = [
        'user_id',
        'quantity',
        'quantity_harvested',
        'cost',
        'planted_at',
        'fully_harvested_at',
        'status',
    ];

    protected $casts = [
        'planted_at' => 'datetime',
        'fully_harvested_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SawitTransaction::class);
    }

    /**
     * Sisa sawit yang belum dipanen dari batch ini.
     */
    public function getRemainingAttribute(): int
    {
        return $this->quantity - $this->quantity_harvested;
    }

    /**
     * Batch ini sudah siap dipanen atau belum (tanam >= 1 menit lalu).
     */
    public function getIsReadyAttribute(): bool
    {
        return Carbon::now()->gte($this->planted_at->addMinute());
    }

    /**
     * Berapa detik lagi sampai bisa dipanen.
     */
    public function getSecondsUntilReadyAttribute(): int
    {
        $readyAt = $this->planted_at->copy()->addMinute();
        $diff    = Carbon::now()->diffInSeconds($readyAt, false);

        return max(0, $diff);
    }

    /**
     * Batch yang belum sepenuhnya dipanen.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'harvested');
    }

    /**
     * Batch yang sudah siap dipanen (tanam >= 1 menit lalu).
     */
    public function scopeReady($query)
    {
        return $query->where('status', '!=', 'harvested')
                     ->where('planted_at', '<=', Carbon::now()->subMinute());
    }

    /**
     * Batch yang masih tumbuh (belum 1 menit).
     */
    public function scopeGrowing($query)
    {
        return $query->where('status', '!=', 'harvested')
                     ->where('planted_at', '>', Carbon::now()->subMinute());
    }
}