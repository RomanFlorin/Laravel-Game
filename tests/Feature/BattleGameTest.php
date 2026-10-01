<?php

namespace Tests\Feature;

use App\Game\BattleManager;
use App\Game\Hero;
use App\Game\Monster;
use App\Models\Battle;
use App\Models\BattleLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BattleGameTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test character randomized statistics fall strictly within their designated bounds.
     */
    public function test_character_stats_initialization_ranges(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $hero = new Hero();
            $this->assertGreaterThanOrEqual(65, $hero->health);
            $this->assertLessThanOrEqual(100, $hero->health);
            $this->assertGreaterThanOrEqual(75, $hero->strength);
            $this->assertLessThanOrEqual(90, $hero->strength);
            $this->assertGreaterThanOrEqual(40, $hero->defence);
            $this->assertLessThanOrEqual(50, $hero->defence);
            $this->assertGreaterThanOrEqual(40, $hero->speed);
            $this->assertLessThanOrEqual(50, $hero->speed);
            $this->assertGreaterThanOrEqual(0.10, $hero->luck);
            $this->assertLessThanOrEqual(0.20, $hero->luck);

            $monster = new Monster();
            $this->assertGreaterThanOrEqual(50, $monster->health);
            $this->assertLessThanOrEqual(80, $monster->health);
            $this->assertGreaterThanOrEqual(55, $monster->strength);
            $this->assertLessThanOrEqual(80, $monster->strength);
            $this->assertGreaterThanOrEqual(50, $monster->defence);
            $this->assertLessThanOrEqual(70, $monster->defence);
            $this->assertGreaterThanOrEqual(40, $monster->speed);
            $this->assertLessThanOrEqual(60, $monster->speed);
            $this->assertGreaterThanOrEqual(0.30, $monster->luck);
            $this->assertLessThanOrEqual(0.45, $monster->luck);
        }
    }

    /**
     * Test the first turn order logic.
     * Rules: Higher Speed attacks first. If equal, higher Luck attacks first.
     * If both Speed and Luck are equal, default to Hero.
     */
    public function test_first_turn_order_logic(): void
    {
        // Case 1: Hero speed is higher
        $hero = new Hero(['speed' => 50, 'luck' => 0.15]);
        $monster = new Monster(['speed' => 40, 'luck' => 0.35]);
        $battleManager = new BattleManager($hero, $monster);
        $this->assertSame($hero, $battleManager->determineFirstAttacker());

        // Case 2: Monster speed is higher
        $hero = new Hero(['speed' => 40, 'luck' => 0.15]);
        $monster = new Monster(['speed' => 50, 'luck' => 0.35]);
        $battleManager = new BattleManager($hero, $monster);
        $this->assertSame($monster, $battleManager->determineFirstAttacker());

        // Case 3: Speed is equal, Hero luck is higher
        $hero = new Hero(['speed' => 45, 'luck' => 0.20]);
        $monster = new Monster(['speed' => 45, 'luck' => 0.10]);
        $battleManager = new BattleManager($hero, $monster);
        $this->assertSame($hero, $battleManager->determineFirstAttacker());

        // Case 4: Speed is equal, Monster luck is higher
        $hero = new Hero(['speed' => 45, 'luck' => 0.10]);
        $monster = new Monster(['speed' => 45, 'luck' => 0.40]);
        $battleManager = new BattleManager($hero, $monster);
        $this->assertSame($monster, $battleManager->determineFirstAttacker());

        // Case 5: Both speed and luck are equal, default to Hero
        $hero = new Hero(['speed' => 45, 'luck' => 0.15]);
        $monster = new Monster(['speed' => 45, 'luck' => 0.15]);
        $battleManager = new BattleManager($hero, $monster);
        $this->assertSame($hero, $battleManager->determineFirstAttacker());
    }

    /**
     * Test core attack damage formula.
     * Damage = Attacker Strength - Defender Defence.
     * Min damage is 0.
     */
    public function test_damage_formula(): void
    {
        $hero = new Hero(['strength' => 80, 'defence' => 40]);
        $monster = new Monster(['strength' => 60, 'defence' => 50]);
        $battleManager = new BattleManager($hero, $monster);

        // Hero attacks Monster: 80 - 50 = 30 damage
        $this->assertEquals(30, $battleManager->calculateBaseDamage($hero, $monster));

        // Monster attacks Hero: 60 - 40 = 20 damage
        $this->assertEquals(20, $battleManager->calculateBaseDamage($monster, $hero));

        // Test defence exceeds strength (damage should be 0, not negative)
        $weakHero = new Hero(['strength' => 45, 'defence' => 40]);
        $strongMonster = new Monster(['strength' => 60, 'defence' => 70]);
        $this->assertEquals(0, $battleManager->calculateBaseDamage($weakHero, $strongMonster));
    }

    /**
     * Test the defensive skill "Magic Armour" which halves the damage.
     */
    public function test_magic_armour_skill_halves_damage(): void
    {
        // We set up Kratos to have Magic Armour always trigger (using a mock or custom override,
        // but since hasTriggeredMagicArmour uses rand(), we can mock the Hero object to return true).
        $heroMock = $this->getMockBuilder(Hero::class)
            ->setConstructorArgs([['health' => 100, 'defence' => 40]])
            ->onlyMethods(['hasTriggeredMagicArmour'])
            ->getMock();
        
        $heroMock->method('hasTriggeredMagicArmour')->willReturn(true);

        $monster = new Monster(['strength' => 80]);
        $battleManager = new BattleManager($heroMock, $monster);

        $baseDamage = $battleManager->calculateBaseDamage($monster, $heroMock); // 80 - 40 = 40 base damage
        $this->assertEquals(40, $baseDamage);

        $skillsTriggered = [];
        $finalDamage = $battleManager->applyDefensiveSkills($heroMock, $baseDamage, $skillsTriggered);

        $this->assertEquals(20, $finalDamage); // Magic Armour triggers and halves 40 to 20
        $this->assertContains('Magic Armour', $skillsTriggered);
    }

    /**
     * Test early termination when a participant's health reaches 0.
     */
    public function test_simulation_early_termination(): void
    {
        // We initialize a monster with 1 health so that it dies immediately on the first strike.
        $hero = new Hero(['health' => 100, 'strength' => 90, 'speed' => 50, 'luck' => 0.0]); // High speed, first attacker
        $monster = new Monster(['health' => 1, 'defence' => 40, 'speed' => 30, 'luck' => 0.0]); // Lower speed, 0 luck

        $battleManager = new BattleManager($hero, $monster);
        $battle = $battleManager->simulate();

        // The battle should end in round 1
        $this->assertEquals('Kratos', $battle->winner);
        $this->assertEquals(1, $battle->total_rounds);
        $this->assertTrue($monster->isDead());
        $this->assertFalse($hero->isDead());

        // Verify logs are stored in the DB
        $this->assertDatabaseHas('battles', ['id' => $battle->id, 'winner' => 'Kratos']);
        $this->assertDatabaseHas('battle_logs', [
            'battle_id' => $battle->id,
            'defender' => 'Wild Monster',
            'defender_health_after' => 0
        ]);
    }

    /**
     * Test maximum turns limit of 15 rounds.
     * Declares winner using remaining HP percentage.
     */
    public function test_max_rounds_limit_and_percentage_winner(): void
    {
        // High health and defence to ensure they survive 15 rounds.
        // We set luck to 0 to make it deterministic.
        // Hero: Speed 50 (first attacker), Health 100, Strength 75, Defence 50, Luck 0
        // Monster: Speed 40, Health 100, Strength 75, Defence 50, Luck 0
        // Damage Hero -> Monster: 75 - 50 = 25 damage per hit
        // Damage Monster -> Hero: 75 - 50 = 25 damage per hit
        // In 15 turns total (rounds):
        // Turn 1 (Hero -> Monster): Monster HP = 75 (75%)
        // Turn 2 (Monster -> Hero): Hero HP = 75 (75%)
        // Turn 3 (Hero -> Monster): Monster HP = 50 (50%)
        // ...
        // Turn 15 (Hero -> Monster): Monster HP = -25 -> Wait, since it's 15 turns, Hero attacks 8 times, Monster attacks 7 times.
        // Monster health will drop to 0 or below, so let's adjust stats so they both survive but have different health.
        // Let's make initial health huge so they survive 15 turns.
        // Hero: Health 1000, Strength 70, Defence 50 -> Monster deals max(0, 70-50) = 20 damage per turn.
        // Monster: Health 500, Strength 70, Defence 50 -> Hero deals max(0, 70-50) = 20 damage per turn.
        // After 15 turns:
        // Hero attacks 8 times, deals 8 * 20 = 160 damage. Monster health = 500 - 160 = 340 (68% remaining).
        // Monster attacks 7 times, deals 7 * 20 = 140 damage. Hero health = 1000 - 140 = 860 (86% remaining).
        // Hero remaining percentage (86%) > Monster remaining percentage (68%). Hero (Kratos) should win!
        
        $hero = $this->getMockBuilder(Hero::class)
            ->setConstructorArgs([['health' => 1000, 'strength' => 70, 'defence' => 50, 'speed' => 50, 'luck' => 0.0]])
            ->onlyMethods(['hasTriggeredMagicArmour', 'hasTriggeredRapidFire'])
            ->getMock();

        $hero->method('hasTriggeredMagicArmour')->willReturn(false);
        $hero->method('hasTriggeredRapidFire')->willReturn(false);

        $monster = new Monster(['health' => 500, 'strength' => 70, 'defence' => 50, 'speed' => 40, 'luck' => 0.0]);

        $battleManager = new BattleManager($hero, $monster);
        $battle = $battleManager->simulate();

        $this->assertEquals(15, $battle->total_rounds);
        $this->assertEquals('Kratos', $battle->winner);
        $this->assertFalse($hero->isDead());
        $this->assertFalse($monster->isDead());
        $this->assertEquals(860, $hero->health);
        $this->assertEquals(340, $monster->health);
    }

    /**
     * Test the JSON response structure of the API endpoint.
     */
    public function test_api_battle_simulate_endpoint(): void
    {
        $response = $this->postJson('/api/battle/simulate');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'battle' => [
                        'id',
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
                        'created_at',
                        'updated_at',
                        'logs' => [
                            '*' => [
                                'id',
                                'battle_id',
                                'round_number',
                                'attacker',
                                'defender',
                                'action_description',
                                'skills_triggered',
                                'damage_dealt',
                                'defender_health_after',
                                'created_at',
                                'updated_at'
                            ]
                        ]
                    ]
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('data.battle.logs'));
    }
}
