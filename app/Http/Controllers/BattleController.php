<?php

namespace App\Http\Controllers;

use App\Game\BattleManager;
use App\Game\Hero;
use App\Game\Monster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BattleController extends Controller
{
    /**
     * Simulate a battle between Kratos and a Wild Monster.
     * Returns the battle status and detailed round-by-round logs.
     */
    public function simulate(Request $request): JsonResponse
    {
        // Allow passing custom stats in request (useful for testing or customized simulations)
        $heroStats = $request->input('hero_stats', []);
        $monsterStats = $request->input('monster_stats', []);

        // Initialize Kratos and the Wild Monster
        $hero = new Hero($heroStats);
        $monster = new Monster($monsterStats);

        // Orchestrate the battle using the BattleManager service
        $battleManager = new BattleManager($hero, $monster);
        $battle = $battleManager->simulate();

        // Eager-load the detailed battle logs
        $battle->load('logs');

        // Return a beautifully structured JSON response
        return response()->json([
            'success' => true,
            'message' => 'Battle simulation completed successfully.',
            'data'    => [
                'battle' => $battle
            ]
        ], 200);
    }
}
