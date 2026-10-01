<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kratos Battle Arena</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Cinzel:wght@500;700;900&display=swap" rel="stylesheet">

    <!-- CSS Styling -->
    <style>
        :root {
            --bg-color: #0b0c10;
            --card-bg: rgba(22, 24, 35, 0.65);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f5f6fa;
            --text-secondary: #a0a5b5;
            
            --hero-color: #ff3e3e;
            --hero-glow: rgba(255, 62, 62, 0.45);
            --monster-color: #00e676;
            --monster-glow: rgba(0, 230, 118, 0.45);
            --gold-color: #ffd32a;
            --gold-glow: rgba(255, 211, 42, 0.55);
            
            --font-sans: 'Outfit', sans-serif;
            --font-display: 'Cinzel', serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 62, 62, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(0, 230, 118, 0.08) 0%, transparent 40%);
            color: var(--text-primary);
            font-family: var(--font-sans);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
            padding: 2rem 1rem;
        }

        header {
            text-align: center;
            margin-bottom: 2rem;
            z-index: 10;
        }

        h1 {
            font-family: var(--font-display);
            font-size: 2.8rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            background: linear-gradient(135deg, #ffffff 30%, #ff3e3e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 4px 20px rgba(255, 62, 62, 0.25);
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        main {
            width: 100%;
            max-w: 1100px;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            z-index: 10;
        }

        /* Arena Layout */
        .arena-grid {
            display: grid;
            grid-template-columns: 1fr 180px 1fr;
            gap: 1.5rem;
            align-items: center;
        }

        @media (max-width: 850px) {
            .arena-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .vs-container {
                order: -1;
            }
        }

        /* Character Cards */
        .character-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .character-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            padding: 1px;
            background: linear-gradient(to bottom, rgba(255,255,255,0.1), rgba(255,255,255,0.02));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .character-card.hero-active {
            border-color: rgba(255, 62, 62, 0.4);
            box-shadow: 0 0 40px var(--hero-glow);
        }

        .character-card.monster-active {
            border-color: rgba(0, 230, 118, 0.4);
            box-shadow: 0 0 40px var(--monster-glow);
        }

        .character-avatar-frame {
            width: 220px;
            height: 220px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--card-border);
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.8);
            position: relative;
            transition: all 0.3s ease;
        }

        .hero-active .character-avatar-frame {
            border-color: var(--hero-color);
            box-shadow: 0 0 20px var(--hero-glow);
        }

        .monster-active .character-avatar-frame {
            border-color: var(--monster-color);
            box-shadow: 0 0 20px var(--monster-glow);
        }

        .character-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .character-avatar:hover {
            transform: scale(1.1);
        }

        .character-name {
            font-family: var(--font-display);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
        }

        .hero-active .character-name {
            color: var(--hero-color);
            text-shadow: 0 0 10px rgba(255, 62, 62, 0.3);
        }

        .monster-active .character-name {
            color: var(--monster-color);
            text-shadow: 0 0 10px rgba(0, 230, 118, 0.3);
        }

        /* Health Bar */
        .health-bar-container {
            width: 100%;
            height: 24px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            margin-bottom: 1.5rem;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.5);
        }

        .health-bar {
            height: 100%;
            width: 100%;
            transition: width 0.4s cubic-bezier(0.075, 0.82, 0.165, 1);
            position: relative;
            border-radius: 12px;
        }

        .hero-health-bar {
            background: linear-gradient(90deg, #b33939, #ff3e3e);
            box-shadow: 0 0 10px var(--hero-glow);
        }

        .monster-health-bar {
            background: linear-gradient(90deg, #218c74, #00e676);
            box-shadow: 0 0 10px var(--monster-glow);
        }

        .health-text {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
        }

        /* Stats Grid */
        .stats-grid {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .stat-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 0.8rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: background 0.3s;
        }

        .stat-box:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            margin-bottom: 0.2rem;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.1rem;
            font-weight: 700;
        }

        /* Floating Damage Text */
        .damage-float {
            position: absolute;
            top: 20%;
            font-size: 2.5rem;
            font-weight: 800;
            color: #ff3e3e;
            text-shadow: 0 4px 10px rgba(0,0,0,0.8);
            animation: floatUp 1.2s forwards ease-out;
            pointer-events: none;
            z-index: 15;
            display: none;
        }

        .damage-float.miss {
            color: var(--gold-color);
        }

        /* VS Controls Container */
        .vs-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
        }

        .vs-badge {
            font-family: var(--font-display);
            font-size: 3rem;
            font-weight: 900;
            color: var(--text-secondary);
            opacity: 0.3;
            text-shadow: 0 0 15px rgba(255,255,255,0.1);
        }

        .simulate-btn {
            background: linear-gradient(135deg, #ff3e3e 0%, #b33939 100%);
            border: none;
            color: #fff;
            padding: 1.2rem 2.2rem;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(255, 62, 62, 0.4);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .simulate-btn:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 30px rgba(255, 62, 62, 0.6);
        }

        .simulate-btn:active {
            transform: translateY(1px);
        }

        .playback-controls {
            display: none;
            align-items: center;
            gap: 0.8rem;
        }

        .control-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--card-border);
            color: var(--text-primary);
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Skills Alert Panel */
        .skills-alert {
            position: absolute;
            top: -20px;
            background: linear-gradient(135deg, var(--gold-color), #f0932b);
            color: #000;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 5px 15px var(--gold-glow);
            animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            display: none;
            z-index: 20;
        }

        /* Battle Logs Section */
        .logs-section {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 1.5rem;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            display: flex;
            flex-direction: column;
            max-height: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .logs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding-bottom: 0.8rem;
            margin-bottom: 1rem;
        }

        .logs-title {
            font-family: var(--font-display);
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .round-counter {
            background: var(--card-border);
            padding: 0.3rem 0.8rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .logs-feed {
            overflow-y: auto;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            padding-right: 0.5rem;
        }

        .logs-feed::-webkit-scrollbar {
            width: 6px;
        }

        .logs-feed::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        .logs-feed::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
        }

        .log-entry {
            padding: 0.8rem 1.2rem;
            border-radius: 10px;
            font-size: 0.9rem;
            line-height: 1.4;
            animation: slideIn 0.3s ease forwards;
            border-left: 4px solid transparent;
        }

        .log-hero {
            background: rgba(255, 62, 62, 0.07);
            border-color: var(--hero-color);
        }

        .log-monster {
            background: rgba(0, 230, 118, 0.07);
            border-color: var(--monster-color);
        }

        .log-skill {
            background: rgba(255, 211, 42, 0.08);
            border-color: var(--gold-color);
            font-weight: 600;
        }

        .skill-badge {
            display: inline-block;
            background: var(--gold-color);
            color: #000;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            margin-left: 0.3rem;
            text-transform: uppercase;
        }

        /* Winner Screen Modal overlay */
        .winner-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 100;
            padding: 1rem;
            animation: fadeIn 0.4s ease forwards;
        }

        .winner-content {
            background: radial-gradient(circle at top, rgba(22, 24, 35, 0.95) 0%, rgba(11, 12, 16, 0.98) 100%);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 30px;
            max-width: 500px;
            width: 100%;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
            position: relative;
            animation: scaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        .winner-crown {
            font-size: 4rem;
            color: var(--gold-color);
            text-shadow: 0 0 20px var(--gold-glow);
            margin-bottom: 1rem;
            animation: pulseCrown 2s infinite ease-in-out;
        }

        .winner-title {
            font-family: var(--font-display);
            font-size: 2.2rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .winner-subtitle {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .winner-stats {
            background: rgba(0,0,0,0.4);
            border: 1px solid var(--card-border);
            border-radius: 15px;
            padding: 1.5rem;
            display: flex;
            justify-content: space-around;
            margin-bottom: 2rem;
        }

        .winner-stat-val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gold-color);
        }

        /* Animations */
        @keyframes floatUp {
            0% {
                transform: translateY(0) scale(0.8);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            80% {
                opacity: 1;
            }
            100% {
                transform: translateY(-80px) scale(1.1);
                opacity: 0;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(15px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3) translateY(-20px); opacity: 0; }
            50% { transform: scale(1.1) translateY(0); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }

        @keyframes pulseCrown {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Action Effects (Shake & Flash) */
        .shake {
            animation: shakeCard 0.4s ease-in-out;
        }

        @keyframes shakeCard {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }

        .strike-left {
            animation: strikeLeftEffect 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes strikeLeftEffect {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(40px) scale(1.05); }
        }

        .strike-right {
            animation: strikeRightEffect 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes strikeRightEffect {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(-40px) scale(1.05); }
        }
    </style>
</head>
<body>

    <header>
        <h1>Kratos Battle Arena</h1>
        <div class="subtitle">Greek Legend vs. Forest Beast</div>
    </header>

    <main>
        <!-- The Interactive Arena -->
        <div class="arena-grid">
            <!-- Hero Panel -->
            <div class="character-card" id="hero-card">
                <!-- Skill Bubble notification -->
                <div class="skills-alert" id="hero-skill-bubble">Skill Active</div>
                
                <div class="character-avatar-frame">
                    <img src="/images/kratos.png" alt="Kratos" class="character-avatar">
                </div>
                
                <div class="character-name">Kratos</div>
                
                <div class="health-bar-container">
                    <div class="health-bar hero-health-bar" id="hero-health-bar" style="width: 100%;"></div>
                    <div class="health-text" id="hero-health-text">HP: 100/100</div>
                </div>

                <div class="damage-float" id="hero-damage-float">-25</div>

                <div class="stats-grid">
                    <div class="stat-box"><span class="stat-label">Strength</span><span class="stat-value" id="hero-strength">75-90</span></div>
                    <div class="stat-box"><span class="stat-label">Defence</span><span class="stat-value" id="hero-defence">40-50</span></div>
                    <div class="stat-box"><span class="stat-label">Speed</span><span class="stat-value" id="hero-speed">40-50</span></div>
                    <div class="stat-box"><span class="stat-label">Luck</span><span class="stat-value" id="hero-luck">10-20%</span></div>
                </div>
            </div>

            <!-- VS Controls Center -->
            <div class="vs-container">
                <div class="vs-badge" id="vs-symbol">VS</div>
                <button class="simulate-btn" id="simulate-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                    Simulate Battle
                </button>
                <div class="playback-controls" id="playback-controls">
                    <button class="control-btn" id="skip-btn">Skip Playback</button>
                    <button class="control-btn" id="speed-btn">Speed: 1x</button>
                </div>
            </div>

            <!-- Monster Panel -->
            <div class="character-card" id="monster-card">
                <div class="skills-alert" id="monster-skill-bubble" style="background: linear-gradient(135deg, var(--gold-color), var(--monster-color));">Evaded</div>
                
                <div class="character-avatar-frame">
                    <img src="/images/monster.png" alt="Monster" class="character-avatar">
                </div>
                
                <div class="character-name">Wild Monster</div>
                
                <div class="health-bar-container">
                    <div class="health-bar monster-health-bar" id="monster-health-bar" style="width: 100%;"></div>
                    <div class="health-text" id="monster-health-text">HP: 80/80</div>
                </div>

                <div class="damage-float" id="monster-damage-float">-25</div>

                <div class="stats-grid">
                    <div class="stat-box"><span class="stat-label">Strength</span><span class="stat-value" id="monster-strength">55-80</span></div>
                    <div class="stat-box"><span class="stat-label">Defence</span><span class="stat-value" id="monster-defence">50-70</span></div>
                    <div class="stat-box"><span class="stat-label">Speed</span><span class="stat-value" id="monster-speed">40-60</span></div>
                    <div class="stat-box"><span class="stat-label">Luck</span><span class="stat-value" id="monster-luck">30-45%</span></div>
                </div>
            </div>
        </div>

        <!-- Real-Time Combat Log Feed -->
        <div class="logs-section">
            <div class="logs-header">
                <div class="logs-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                    Battle Log
                </div>
                <div class="round-counter" id="round-counter">Round 0 / 15</div>
            </div>
            <div class="logs-feed" id="logs-feed">
                <div class="log-entry" style="background: rgba(255,255,255,0.02); text-align: center; color: var(--text-secondary);">
                    Press "Simulate Battle" above to begin the combat playback.
                </div>
            </div>
        </div>
    </main>

    <!-- Celebration Modal -->
    <div class="winner-modal" id="winner-modal">
        <div class="winner-content">
            <div class="winner-crown">👑</div>
            <div class="winner-title" id="winner-title">Kratos Wins</div>
            <div class="winner-subtitle">Victory is achieved!</div>
            
            <div class="winner-stats">
                <div>
                    <div class="stat-label">Total Rounds</div>
                    <div class="winner-stat-val" id="modal-rounds">12</div>
                </div>
                <div>
                    <div class="stat-label">Final Health</div>
                    <div class="winner-stat-val" id="modal-hp">45 HP</div>
                </div>
            </div>

            <button class="simulate-btn mx-auto" id="close-modal-btn">
                Close Arena
            </button>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        // DOM Elements
        const simulateBtn = document.getElementById('simulate-btn');
        const playbackControls = document.getElementById('playback-controls');
        const skipBtn = document.getElementById('skip-btn');
        const speedBtn = document.getElementById('speed-btn');
        const logsFeed = document.getElementById('logs-feed');
        const roundCounter = document.getElementById('round-counter');

        const heroCard = document.getElementById('hero-card');
        const heroHealthBar = document.getElementById('hero-health-bar');
        const heroHealthText = document.getElementById('hero-health-text');
        const heroStrength = document.getElementById('hero-strength');
        const heroDefence = document.getElementById('hero-defence');
        const heroSpeed = document.getElementById('hero-speed');
        const heroLuck = document.getElementById('hero-luck');
        const heroDamageFloat = document.getElementById('hero-damage-float');
        const heroSkillBubble = document.getElementById('hero-skill-bubble');

        const monsterCard = document.getElementById('monster-card');
        const monsterHealthBar = document.getElementById('monster-health-bar');
        const monsterHealthText = document.getElementById('monster-health-text');
        const monsterStrength = document.getElementById('monster-strength');
        const monsterDefence = document.getElementById('monster-defence');
        const monsterSpeed = document.getElementById('monster-speed');
        const monsterLuck = document.getElementById('monster-luck');
        const monsterDamageFloat = document.getElementById('monster-damage-float');
        const monsterSkillBubble = document.getElementById('monster-skill-bubble');

        const winnerModal = document.getElementById('winner-modal');
        const winnerTitle = document.getElementById('winner-title');
        const modalRounds = document.getElementById('modal-rounds');
        const modalHp = document.getElementById('modal-hp');
        const closeModalBtn = document.getElementById('close-modal-btn');

        // Playback State
        let isPlaying = false;
        let playSpeed = 1000; // ms per step
        let simulationLogs = [];
        let playbackIndex = 0;
        let timeoutId = null;
        let initialHeroHp = 100;
        let initialMonsterHp = 80;

        // Simulate API request
        simulateBtn.addEventListener('click', async () => {
            if (isPlaying) return;

            // Reset UI
            logsFeed.innerHTML = '';
            roundCounter.innerText = 'Initializing...';
            simulateBtn.disabled = true;
            simulateBtn.style.opacity = '0.5';

            try {
                const response = await fetch('/api/battle/simulate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                const result = await response.json();
                
                if (result.success) {
                    const battle = result.data.battle;
                    simulationLogs = battle.logs;
                    
                    // Show initial stats
                    setupInitialStats(battle);
                    
                    // Show playback controllers
                    playbackControls.style.display = 'flex';
                    isPlaying = true;
                    playbackIndex = 0;
                    
                    // Start stepping
                    stepPlayback();
                } else {
                    alert('Simulation failed.');
                    resetControls();
                }
            } catch (err) {
                console.error(err);
                alert('Connection to local API failed. Make sure your server is running.');
                resetControls();
            }
        });

        // Initialize Panel with simulation initial parameters
        function setupInitialStats(battle) {
            initialHeroHp = battle.hero_initial_health;
            initialMonsterHp = battle.monster_initial_health;

            // Hero
            heroHealthBar.style.width = '100%';
            heroHealthText.innerText = `HP: ${initialHeroHp}/${initialHeroHp}`;
            heroStrength.innerText = battle.hero_initial_strength;
            heroDefence.innerText = battle.hero_initial_defence;
            heroSpeed.innerText = battle.hero_initial_speed;
            heroLuck.innerText = `${Math.round(battle.hero_initial_luck * 100)}%`;

            // Monster
            monsterHealthBar.style.width = '100%';
            monsterHealthText.innerText = `HP: ${initialMonsterHp}/${initialMonsterHp}`;
            monsterStrength.innerText = battle.monster_initial_strength;
            monsterDefence.innerText = battle.monster_initial_defence;
            monsterSpeed.innerText = battle.monster_initial_speed;
            monsterLuck.innerText = `${Math.round(battle.monster_initial_luck * 100)}%`;
        }

        // Stepping Playback Logic
        function stepPlayback() {
            if (playbackIndex >= simulationLogs.length) {
                endBattlePlayback();
                return;
            }

            const log = simulationLogs[playbackIndex];
            roundCounter.innerText = `Round ${log.round_number} / 15`;

            // 1. Visually show who is attacking
            const isHeroAttacking = (log.attacker === 'Kratos');
            
            // Highlights & Actions
            if (isHeroAttacking) {
                heroCard.classList.add('hero-active');
                monsterCard.classList.remove('monster-active');
                heroCard.classList.add('strike-left');
                setTimeout(() => heroCard.classList.remove('strike-left'), 300);
            } else {
                monsterCard.classList.add('monster-active');
                heroCard.classList.remove('hero-active');
                monsterCard.classList.add('strike-right');
                setTimeout(() => monsterCard.classList.remove('strike-right'), 300);
            }

            // 2. Animate defender impact (shake & floating damage & skill activations)
            setTimeout(() => {
                const defenderCard = isHeroAttacking ? monsterCard : heroCard;
                const damageFloatEl = isHeroAttacking ? monsterDamageFloat : heroDamageFloat;
                const skillBubbleEl = isHeroAttacking ? heroSkillBubble : heroSkillBubble; // Default bubble
                
                // Shake defender
                if (log.damage_dealt > 0) {
                    defenderCard.classList.add('shake');
                    setTimeout(() => defenderCard.classList.remove('shake'), 400);
                }

                // Show floating damage
                if (log.action_description.includes('lucky and avoided')) {
                    damageFloatEl.innerText = 'Miss!';
                    damageFloatEl.className = 'damage-float miss';
                    // Show evasion bubble for defender
                    const defenderSkillEl = isHeroAttacking ? document.getElementById('monster-skill-bubble') : heroSkillBubble;
                    defenderSkillEl.innerText = 'Evaded!';
                    defenderSkillEl.style.display = 'block';
                    setTimeout(() => defenderSkillEl.style.display = 'none', 900);
                } else {
                    damageFloatEl.innerText = `-${log.damage_dealt}`;
                    damageFloatEl.className = 'damage-float';
                }

                // Animate floating text
                damageFloatEl.style.display = 'block';
                setTimeout(() => damageFloatEl.style.display = 'none', 1200);

                // Show skills triggered badges/alerts
                const skills = log.skills_triggered || [];
                if (skills.length > 0) {
                    // Check if Magic Armour triggered for hero (on defense)
                    if (skills.includes('Magic Armour') && !isHeroAttacking) {
                        heroSkillBubble.innerText = 'Magic Armour!';
                        heroSkillBubble.style.display = 'block';
                        setTimeout(() => heroSkillBubble.style.display = 'none', 1200);
                    }
                    // Check if Rapid Fire triggered for hero (on attack)
                    if (skills.includes('Rapid Fire') && isHeroAttacking) {
                        heroSkillBubble.innerText = 'Rapid Fire!';
                        heroSkillBubble.style.display = 'block';
                        setTimeout(() => heroSkillBubble.style.display = 'none', 1200);
                    }
                }

                // 3. Update Health bar and text
                if (isHeroAttacking) {
                    const healthPercentage = Math.max(0, (log.defender_health_after / initialMonsterHp) * 100);
                    monsterHealthBar.style.width = `${healthPercentage}%`;
                    monsterHealthText.innerText = `HP: ${log.defender_health_after}/${initialMonsterHp}`;
                } else {
                    const healthPercentage = Math.max(0, (log.defender_health_after / initialHeroHp) * 100);
                    heroHealthBar.style.width = `${healthPercentage}%`;
                    heroHealthText.innerText = `HP: ${log.defender_health_after}/${initialHeroHp}`;
                }

                // 4. Append log to feed
                appendLogFeed(log);

                // Setup next step
                playbackIndex++;
                timeoutId = setTimeout(stepPlayback, playSpeed);

            }, 200);
        }

        // Add item to scrollable log
        function appendLogFeed(log) {
            const el = document.createElement('div');
            el.className = 'log-entry';

            const skills = log.skills_triggered || [];
            let skillBadges = '';
            skills.forEach(skill => {
                skillBadges += `<span class="skill-badge">${skill}</span>`;
            });

            if (log.action_description.includes('lucky and avoided')) {
                el.classList.add('log-skill');
            } else if (log.attacker === 'Kratos') {
                el.classList.add('log-hero');
            } else {
                el.classList.add('log-monster');
            }

            el.innerHTML = `<strong>Turn ${log.round_number}:</strong> ${log.action_description} ${skillBadges}`;
            logsFeed.appendChild(el);
            logsFeed.scrollTop = logsFeed.scrollHeight;
        }

        // Skip playback to end instantly
        skipBtn.addEventListener('click', () => {
            if (!isPlaying) return;
            clearTimeout(timeoutId);
            
            // Fast-forward logs
            logsFeed.innerHTML = '';
            simulationLogs.forEach(log => appendLogFeed(log));

            // Set final stats instantly
            const lastLog = simulationLogs[simulationLogs.length - 1];
            
            // Locate last states
            let finalHeroHp = lastLog.defender === 'Kratos' ? lastLog.defender_health_after : document.getElementById('hero-health-text').innerText.split('/')[0].replace(/\D/g, '');
            let finalMonsterHp = lastLog.defender === 'Wild Monster' ? lastLog.defender_health_after : document.getElementById('monster-health-text').innerText.split('/')[0].replace(/\D/g, '');

            // Fallback scan loop
            simulationLogs.forEach(l => {
                if (l.defender === 'Kratos') finalHeroHp = l.defender_health_after;
                if (l.defender === 'Wild Monster') finalMonsterHp = l.defender_health_after;
            });

            heroHealthBar.style.width = `${(finalHeroHp / initialHeroHp) * 100}%`;
            heroHealthText.innerText = `HP: ${finalHeroHp}/${initialHeroHp}`;

            monsterHealthBar.style.width = `${(finalMonsterHp / initialMonsterHp) * 100}%`;
            monsterHealthText.innerText = `HP: ${finalMonsterHp}/${initialMonsterHp}`;

            endBattlePlayback();
        });

        // Toggle playback speed
        speedBtn.addEventListener('click', () => {
            if (playSpeed === 1000) {
                playSpeed = 400;
                speedBtn.innerText = 'Speed: 2x.5';
            } else if (playSpeed === 400) {
                playSpeed = 150;
                speedBtn.innerText = 'Speed: 5x';
            } else {
                playSpeed = 1000;
                speedBtn.innerText = 'Speed: 1x';
            }
        });

        // End Playback and open celebratory Modal
        function endBattlePlayback() {
            isPlaying = false;
            clearTimeout(timeoutId);

            // Hide highlights
            heroCard.classList.remove('hero-active');
            monsterCard.classList.remove('monster-active');

            // Find winner (from last log record or calculations)
            const lastLog = simulationLogs[simulationLogs.length - 1];
            
            // Let's call the server endpoint state or check who had higher health percentage
            // We can determine the winner from our database logs
            // Wait, we know the winner is recorded inside the database model, but since we are client side:
            let finalHeroHp = initialHeroHp;
            let finalMonsterHp = initialMonsterHp;
            simulationLogs.forEach(l => {
                if (l.defender === 'Kratos') finalHeroHp = l.defender_health_after;
                if (l.defender === 'Wild Monster') finalMonsterHp = l.defender_health_after;
            });

            let winnerName = 'Draw';
            let winnerHpText = '0 HP';
            if (finalHeroHp <= 0) {
                winnerName = 'Wild Monster';
                winnerHpText = `${finalMonsterHp}/${initialMonsterHp} HP`;
            } else if (finalMonsterHp <= 0) {
                winnerName = 'Kratos';
                winnerHpText = `${finalHeroHp}/${initialHeroHp} HP`;
            } else {
                // Percentage check
                const heroPct = finalHeroHp / initialHeroHp;
                const monsterPct = finalMonsterHp / initialMonsterHp;
                if (heroPct > monsterPct) {
                    winnerName = 'Kratos';
                    winnerHpText = `${finalHeroHp}/${initialHeroHp} HP`;
                } else if (monsterPct > heroPct) {
                    winnerName = 'Wild Monster';
                    winnerHpText = `${finalMonsterHp}/${initialMonsterHp} HP`;
                }
            }

            // Setup Modal text
            winnerTitle.innerText = `${winnerName} Wins`;
            modalRounds.innerText = simulationLogs[simulationLogs.length - 1].round_number;
            modalHp.innerText = winnerHpText;

            // Open Modal
            setTimeout(() => {
                winnerModal.style.display = 'flex';
            }, 600);

            resetControls();
        }

        // Close Winner Modal
        closeModalBtn.addEventListener('click', () => {
            winnerModal.style.display = 'none';
        });

        // Reset control states
        function resetControls() {
            simulateBtn.disabled = false;
            simulateBtn.style.opacity = '1';
            playbackControls.style.display = 'none';
        }
    </script>
</body>
</html>
