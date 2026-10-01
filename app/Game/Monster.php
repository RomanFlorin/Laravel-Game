<?php

namespace App\Game;

class Monster
{
    public string $name = 'Wild Monster';
    public int $health;
    public int $strength;
    public int $defence;
    public int $speed;
    public float $luck;

    /**
     * Monster constructor.
     * Randomizes stats if not explicitly provided (useful for deterministic testing).
     */
    public function __construct(array $stats = [])
    {
        $this->health = $stats['health'] ?? rand(50, 80);
        $this->strength = $stats['strength'] ?? rand(55, 80);
        $this->defence = $stats['defence'] ?? rand(50, 70);
        $this->speed = $stats['speed'] ?? rand(40, 60);

        if (isset($stats['luck'])) {
            $this->luck = (float) $stats['luck'];
        } else {
            // Luck: 30% - 45% -> expressed as 0.30 - 0.45
            $this->luck = rand(30, 45) / 100;
        }
    }

    /**
     * Apply damage to the monster, reducing health.
     */
    public function takeDamage(int $damage): void
    {
        $this->health = max(0, $this->health - $damage);
    }

    /**
     * Check if the monster is dead (health <= 0).
     */
    public function isDead(): bool
    {
        return $this->health <= 0;
    }
}
