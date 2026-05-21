<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get session values safely
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'Scholar');
$user_email = htmlspecialchars($_SESSION['user_email'] ?? '');
$profile_picture = htmlspecialchars($_SESSION['profile_picture'] ?? 'avatar.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Academy.AI Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #0B0F1A;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --accent-primary: #6366F1;
            --accent-secondary: #A855F7;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1E1B4B, #0B0F1A 60%);
            color: #E2E8F0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
        }

        .premium-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid rgba(255,255,255,0.05);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-card:hover {
            transform: translateY(-10px) scale(1.02);
            background: linear-gradient(145deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.05) 100%);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 20px rgba(99, 102, 241, 0.1);
        }

        .mesh-gradient {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.08) 0px, transparent 50%);
            z-index: -1;
            pointer-events: none;
        }

        .glow-text {
            text-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>
<body class="flex flex-col md:flex-row p-4 md:p-6 gap-6">

    <div class="mesh-gradient"></div>

    <!-- Hamburger Menu / Top Bar for Mobile -->
    <div class="flex md:hidden items-center justify-between w-full px-5 py-4 glass rounded-3xl z-20 shrink-0 shadow-lg border-white/5 mb-2">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center font-bold text-xl shadow-xl shadow-indigo-600/30">A</div>
            <div>
                <h1 class="text-base font-bold tracking-tight text-white">Academy.AI</h1>
                <p class="text-[8px] uppercase tracking-[0.1em] text-indigo-400 font-bold">Premium</p>
            </div>
        </div>
        <button onclick="toggleMobileSidebar()" class="w-10 h-10 rounded-xl glass border-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/5 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <aside id="sidebarMenu" class="fixed inset-y-0 left-0 z-40 w-72 glass p-8 flex flex-col items-center md:items-start space-y-10 transform -translate-x-full md:translate-x-0 md:relative md:flex shrink-0 transition-transform duration-300 ease-in-out h-full md:h-auto rounded-[2rem] md:rounded-[2rem]">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center font-bold text-2xl shadow-xl shadow-indigo-600/30">A</div>
            <div>
                <h1 class="text-xl font-bold tracking-tight">Academy.AI</h1>
                <p class="text-[10px] uppercase tracking-[0.2em] text-indigo-400 font-bold">Premium Edition</p>
            </div>
        </div>
        
        <nav class="flex-1 w-full space-y-3">
            <a href="main.php" class="flex items-center space-x-4 p-4 rounded-2xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 shadow-xl shadow-indigo-500/5 transition-all">
                <span class="text-xl">🏠</span> 
                <span class="font-bold">Dashboard</span>
            </a>
            <a href="askai.php" class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-white/5 transition-all group">
                <span class="text-xl opacity-60 group-hover:opacity-100">✨</span> 
                <span class="font-medium text-gray-400 group-hover:text-white">Ask AI</span>
            </a>
            <a href="practice.php" class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-white/5 transition-all group">
                <span class="text-xl opacity-60 group-hover:opacity-100">🎯</span> 
                <span class="font-medium text-gray-400 group-hover:text-white">Practice</span>
            </a>
            <a href="performance.php" class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-white/5 transition-all group">
                <span class="text-xl opacity-60 group-hover:opacity-100">📊</span> 
                <span class="font-medium text-gray-400 group-hover:text-white">Performance</span>
            </a>
        </nav>

        <div class="w-full p-4 glass rounded-3xl border-white/5 flex items-center space-x-4">
            <img src="../../assets/images/<?php echo $profile_picture; ?>" class="w-10 h-10 rounded-full border border-indigo-500/30">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold truncate"><?php echo $user_name; ?></p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Active Learner</p>
            </div>
            <a href="../../backend/logout.php" class="w-10 h-10 rounded-xl glass border-white/5 flex items-center justify-center text-gray-500 hover:text-red-400 hover:bg-red-500/10 hover:border-red-500/20 transition-all group" title="Logout">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </a>
        </div>
    </aside>

    <!-- Overlay backdrop for Mobile Sidebar -->
    <div id="sidebarOverlay" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 hidden md:hidden transition-all duration-300"></div>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col max-w-6xl mx-auto w-full">
        
        <!-- Welcome Header -->
        <header class="mb-12 mt-4 px-4">
            <h2 class="text-4xl font-bold tracking-tight glow-text">Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400"><?php echo explode(' ', $user_name)[0]; ?></span> 👋</h2>
            <p class="text-gray-400 mt-2 text-lg">Your learning journey is progressing beautifully. What's the goal for today?</p>
        </header>

        <!-- Feature Cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 px-4 mb-12">
            
            <!-- Ask AI Card -->
            <a href="askai.php" class="premium-card rounded-[2.5rem] p-8 md:p-10 flex flex-col group">
                <div class="w-16 h-16 bg-indigo-600/10 rounded-3xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform">🤖</div>
                <h3 class="text-2xl font-bold mb-3">Ask Academy AI</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-8 flex-1">Instant explanations, code help, and homework assistance using state-of-the-art AI.</p>
                <div class="flex items-center text-indigo-400 font-bold text-sm">
                    Open Assistant <span class="ml-2 group-hover:translate-x-2 transition-transform">→</span>
                </div>
            </a>

            <!-- Practice Card -->
            <a href="practice.php" class="premium-card rounded-[2.5rem] p-8 md:p-10 flex flex-col group">
                <div class="w-16 h-16 bg-emerald-600/10 rounded-3xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform">📝</div>
                <h3 class="text-2xl font-bold mb-3">Practice Mode</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-8 flex-1">Generate custom quizzes on any topic to test your knowledge and retain more info.</p>
                <div class="flex items-center text-emerald-400 font-bold text-sm">
                    Start Quiz <span class="ml-2 group-hover:translate-x-2 transition-transform">→</span>
                </div>
            </a>

            <!-- Performance Card -->
            <a href="performance.php" class="premium-card rounded-[2.5rem] p-8 md:p-10 flex flex-col sm:col-span-2 lg:col-span-1 group">
                <div class="w-16 h-16 bg-purple-600/10 rounded-3xl flex items-center justify-center text-3xl mb-8 group-hover:scale-110 transition-transform">📊</div>
                <h3 class="text-2xl font-bold mb-3">Performance</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-8 flex-1">Visualize your progress, check your accuracy, and see where you can improve.</p>
                <div class="flex items-center text-purple-400 font-bold text-sm">
                    View Analytics <span class="ml-2 group-hover:translate-x-2 transition-transform">→</span>
                </div>
            </a>

        </section>

        <!-- Quick Stats / Recent Activity Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 md:gap-8 px-4 mb-8">
            <div class="glass rounded-[2rem] p-6 sm:p-8 flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-1">Study Streak</p>
                    <h4 id="dash-streak" class="text-2xl sm:text-3xl font-bold">--</h4>
                </div>
                <div class="w-12 h-12 bg-orange-500/10 rounded-full flex items-center justify-center text-2xl">🔥</div>
            </div>
            <div class="glass rounded-[2rem] p-6 sm:p-8 flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-1">Total XP</p>
                    <h4 id="dash-xp" class="text-2xl sm:text-3xl font-bold">--</h4>
                </div>
                <div class="w-12 h-12 bg-blue-500/10 rounded-full flex items-center justify-center text-2xl">⭐</div>
            </div>
        </div>

    </main>

    <footer class="fixed bottom-6 right-10 text-[10px] text-gray-600 uppercase tracking-[0.3em] font-bold pointer-events-none hidden lg:block">
        Academy.AI © 2026
    </footer>

<script src="../../assets/js/api-auth.js"></script>
<script>
window.ACADEMY_USER_EMAIL = <?php echo json_encode($user_email); ?>;
window.ACADEMY_USER_NAME  = <?php echo json_encode($user_name); ?>;

function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

async function loadDashboardSummary() {
    const token = await ensureAccessToken();
    if (!token) return;

    try {
        const res = await fetch('http://127.0.0.1:8001/performance/dashboard-summary', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) return;
        const data = await res.json();

        const streakEl = document.getElementById('dash-streak');
        const xpEl     = document.getElementById('dash-xp');

        const streak = data.study_streak ?? 0;
        streakEl.textContent = streak === 0 ? '0 Days' : (streak === 1 ? '1 Day' : `${streak} Days`);
        xpEl.textContent = (data.total_xp ?? 0).toLocaleString();
    } catch (e) {
        console.warn('Could not load dashboard summary:', e);
    }
}

window.addEventListener('load', loadDashboardSummary);
</script>

</body>
</html>
