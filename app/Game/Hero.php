<?php

namespace App\Game;

class Hero
{
    public string $name = 'Kratos';
    public int $health;
    public int $strength;
    public int $defence;
    public int $speed;
    public float $luck;

    /**
     * Hero constructor.
     * Randomizes stats if not explicitly provided (useful for deterministic testing).
     */
    public function __construct(array $stats = [])
    {
        $this->health = $stats['health'] ?? rand(65, 100);
        $this->strength = $stats['strength'] ?? rand(75, 90);
        $this->defence = $stats['defence'] ?? rand(40, 50);
        $this->speed = $stats['speed'] ?? rand(40, 50);
        
        if (isset($stats['luck'])) {
            $this->luck = (float) $stats['luck'];
        } else {
            // Luck: 10% - 20% -> expressed as 0.10 - 0.20
            $this->luck = rand(10, 20) / 100;
        }
    }

    /**
     * Rapid fire: Strike twice while it's his turn to attack;
     * 15% chance he'll use this skill every time he attacks.
     */
    public function hasTriggeredRapidFire(): bool
    {
        return (rand(1, 100) <= 15);
    }

    /**
     * Magic armour: Takes only half of the usual damage when an enemy attacks;
     * 15% chance he'll use this skill every time he defends.
     */
    public function hasTriggeredMagicArmour(): bool
    {
        return (rand(1, 100) <= 15);
    }

    /**
     * Apply damage to the hero, reducing health.
     */
    public function takeDamage(int $damage): void
    {
        $this->health = max(0, $this->health - $damage);
    }

    /**
     * Check if the hero is dead (health <= 0).
     */
    public function isDead(): bool
    {
        return $this->health <= 0;
    }
}
