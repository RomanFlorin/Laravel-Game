<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BattleLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'battle_id',
        'round_number',
        'attacker',
        'defender',
        'action_description',
        'skills_triggered',
        'damage_dealt',
        'defender_health_after',
    ];

    protected $casts = [
        'skills_triggered' => 'array',
    ];

    /**
     * Get the battle that owns the log.
     */
    public function battle(): BelongsTo
    {
        return $this->belongsTo(Battle::class);
    }
}
