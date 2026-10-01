<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Battle extends Model
{
    use HasFactory;

    protected $fillable = [
        'winner',
        'hero_initial_health',
        'hero_initial_strength',
        'hero_initial_defence',
        'hero_initial_speed',
        'hero_initial_luck',
        'monster_initial_health',
        'monster_initial_strength',
        'monster_initial_defence',
        'monster_initial_speed',
        'monster_initial_luck',
        'total_rounds',
    ];

    /**
     * Get the logs for the battle.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(BattleLog::class);
    }
}
