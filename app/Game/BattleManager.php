<?php

namespace App\Game;

use App\Models\Battle;
use App\Models\BattleLog;

class BattleManager
{
    private Hero $hero;
    private Monster $monster;
    private array $initialStats;

    public function __construct(Hero $hero, Monster $monster)
    {
        $this->hero = $hero;
        $this->monster = $monster;

        // Save initial stats to record them in the database
        $this->initialStats = [
            'hero_health'      => $hero->health,
            'hero_strength'    => $hero->strength,
            'hero_defence'     => $hero->defence,
            'hero_speed'       => $hero->speed,
            'hero_luck'        => $hero->luck,
            'monster_health'   => $monster->health,
            'monster_strength' => $monster->strength,
            'monster_defence'  => $monster->defence,
            'monster_speed'    => $monster->speed,
            'monster_luck'     => $monster->luck,
        ];
    }

    /**
     * Run the complete battle simulation.
     */
    public function simulate(): Battle
    {
        // 1. Create the battle record in the database
        $battle = Battle::create([
            'hero_initial_health'      => $this->initialStats['hero_health'],
            'hero_initial_strength'    => $this->initialStats['hero_strength'],
            'hero_initial_defence'     => $this->initialStats['hero_defence'],
            'hero_initial_speed'       => $this->initialStats['hero_speed'],
            'hero_initial_luck'        => $this->initialStats['hero_luck'],
            'monster_initial_health'   => $this->initialStats['monster_health'],
            'monster_initial_strength' => $this->initialStats['monster_strength'],
            'monster_initial_defence'  => $this->initialStats['monster_defence'],
            'monster_initial_speed'    => $this->initialStats['monster_speed'],
            'monster_initial_luck'     => $this->initialStats['monster_luck'],
        ]);

        // 2. Determine who attacks first
        $attacker = $this->determineFirstAttacker();
        $defender = ($attacker === $this->hero) ? $this->monster : $this->hero;

        $roundNumber = 1;
        $maxRounds = 15;

        // 3. Play rounds up to 15 turns
        while ($roundNumber <= $maxRounds && !$this->hero->isDead() && !$this->monster->isDead()) {
            
            // Check for Kratos's Rapid Fire skill if Kratos is the attacker
            if ($attacker === $this->hero && $this->hero->hasTriggeredRapidFire()) {
                // Strike 1
                $this->executeStrike($attacker, $defender, $battle, $roundNumber, ['Rapid Fire'], 'first strike');
                
                // Strike 2 (only if defender didn't die from first strike)
                if (!$defender->isDead()) {
                    $this->executeStrike($attacker, $defender, $battle, $roundNumber, ['Rapid Fire'], 'second strike');
                }
            } else {
                // Normal single strike
                $this->executeStrike($attacker, $defender, $battle, $roundNumber);
            }

            // If a participant dies, terminate immediately
            if ($this->hero->isDead() || $this->monster->isDead()) {
                break;
            }

            // Switch roles for next round
            $temp = $attacker;
            $attacker = $defender;
            $defender = $temp;

            $roundNumber++;
        }

        // 4. Determine and record the winner
        $winnerName = $this->determineWinner($roundNumber - 1);
        $battle->update([
            'winner' => $winnerName,
            'total_rounds' => min($roundNumber, $maxRounds)
        ]);

        return $battle;
    }

    /**
     * Determine who goes first.
     * Rules: Higher Speed attacks first. If equal, higher Luck attacks first.
     * If both are equal, default to Hero.
     */
    public function determineFirstAttacker(): object
    {
        if ($this->hero->speed > $this->monster->speed) {
            return $this->hero;
        } elseif ($this->monster->speed > $this->hero->speed) {
            return $this->monster;
        }

        // Speeds are equal, compare Luck
        if ($this->hero->luck > $this->monster->luck) {
            return $this->hero;
        } elseif ($this->monster->luck > $this->hero->luck) {
            return $this->monster;
        }

        // Both Speed and Luck are equal, default to Hero
        return $this->hero;
    }

    /**
     * Execute a single strike, including luck/miss and defense mechanics.
     * Separates the attack and skill calculations.
     */
    private function executeStrike(
        object $attacker,
        object $defender,
        Battle $battle,
        int $roundNumber,
        array $preTriggeredSkills = [],
        string $strikeType = ''
    ): void {
        $skillsTriggered = $preTriggeredSkills;
        $damageDealt = 0;
        $description = '';

        // 1. Check Luck/Evasion (Miss Mechanic)
        if ($this->checkEvasion($defender)) {
            $damageDealt = 0;
            $strikeContext = $strikeType ? " ({$strikeType})" : "";
            $description = "{$attacker->name} attacked {$defender->name}{$strikeContext}, but {$defender->name} was lucky and avoided the hit.";
        } else {
            // 2. Core Attack Damage Logic (Attack Function)
            $baseDamage = $this->calculateBaseDamage($attacker, $defender);
            
            // 3. Skill Algorithmic Calculations (Separate Function)
            $damageDealt = $this->applyDefensiveSkills($defender, $baseDamage, $skillsTriggered);
            
            // Apply damage to defender
            $defender->takeDamage($damageDealt);

            // Build action description
            $strikeContext = $strikeType ? " ({$strikeType})" : "";
            $skillNotice = !empty($skillsTriggered) ? " [Skills used: " . implode(', ', $skillsTriggered) . "]" : "";
            $description = "{$attacker->name} struck {$defender->name}{$strikeContext} for {$damageDealt} damage.{$skillNotice}";
        }

        // 4. Log to database
        BattleLog::create([
            'battle_id'             => $battle->id,
            'round_number'          => $roundNumber,
            'attacker'              => $attacker->name,
            'defender'              => $defender->name,
            'action_description'    => $description,
            'skills_triggered'      => $skillsTriggered,
            'damage_dealt'          => $damageDealt,
            'defender_health_after' => $defender->health,
        ]);
    }

    /**
     * Evasion check: Defender avoids hit if their random luck check succeeds.
     */
    public function checkEvasion(object $defender): bool
    {
        // Generate a random float between 0.0 and 1.0
        $roll = rand(0, 1000) / 1000;
        return $roll <= $defender->luck;
    }

    /**
     * Core Attack Function: Damage = Attacker Strength - Defender Defence.
     */
    public function calculateBaseDamage(object $attacker, object $defender): int
    {
        return max(0, $attacker->strength - $defender->defence);
    }

    /**
     * Skill Algorithmic Logic: Separated from the base damage and attack function.
     * Evaluates defense skills such as Magic Armour.
     */
    public function applyDefensiveSkills(object $defender, int $baseDamage, array &$skillsTriggered): int
    {
        $finalDamage = $baseDamage;

        // Magic Armour: Takes only half of the usual damage when Kratos (Hero) defends
        if ($defender instanceof Hero && $defender->hasTriggeredMagicArmour()) {
            $skillsTriggered[] = 'Magic Armour';
            $finalDamage = (int) round($baseDamage / 2);
        }

        return $finalDamage;
    }

    /**
     * Determine the winner after simulation ends.
     */
    private function determineWinner(int $roundsPlayed): string
    {
        if ($this->hero->isDead()) {
            return $this->monster->name;
        }

        if ($this->monster->isDead()) {
            return $this->hero->name;
        }

        // Max rounds reached: evaluate deterministic winning logic.
        // Winner is the one with the higher remaining HP percentage.
        $heroHpPercentage = $this->hero->health / $this->initialStats['hero_health'];
        $monsterHpPercentage = $this->monster->health / $this->initialStats['monster_health'];

        if ($heroHpPercentage > $monsterHpPercentage) {
            return $this->hero->name;
        } elseif ($monsterHpPercentage > $heroHpPercentage) {
            return $this->monster->name;
        }

        // Draw if percentages are exactly equal
        return 'Draw';
    }
}
