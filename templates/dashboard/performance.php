<?php
session_start();

// Check if logged in - redirect if not
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../../src/Database.php';
use Dell7420\Academy\Database;

$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User');
$user_email = htmlspecialchars($_SESSION['user_email'] ?? '');
$profile_picture = htmlspecialchars($_SESSION['profile_picture'] ?? 'avatar.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance | Academy.AI Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        }

        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
        }

        .stat-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid rgba(255,255,255,0.05);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: rgba(99, 102, 241, 0.3);
            background: rgba(99, 102, 241, 0.05);
        }

        .mesh-gradient {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.05) 0px, transparent 50%);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body class="min-h-screen font-sans relative bg-gradient-to-b from-[#0F0E0E] to-[#142047] overflow-x-hidden overflow-y-auto flex flex-col md:flex-row p-4 md:p-6 gap-4 md:gap-6">

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
    <aside id="sidebarMenu" class="fixed inset-y-0 left-0 z-40 w-72 glass p-8 flex flex-col items-center md:items-start space-y-10 transform -translate-x-full md:translate-x-0 md:relative md:flex shrink-0 transition-transform duration-300 ease-in-out h-full md:h-auto rounded-[2rem]">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center font-bold text-2xl shadow-xl shadow-indigo-600/30">A</div>
            <div>
                <h1 class="text-xl font-bold tracking-tight">Academy.AI</h1>
                <p class="text-[10px] uppercase tracking-[0.2em] text-indigo-400 font-bold">Premium Edition</p>
            </div>
        </div>
        
        <nav class="flex-1 w-full space-y-3">
            <a href="main.php" class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-white/5 transition-all group">
                <span class="text-xl opacity-60 group-hover:opacity-100">🏠</span> 
                <span class="font-medium text-gray-400 group-hover:text-white">Dashboard</span>
            </a>
            <a href="askai.php" class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-white/5 transition-all group">
                <span class="text-xl opacity-60 group-hover:opacity-100">✨</span> 
                <span class="font-medium text-gray-400 group-hover:text-white">Ask AI</span>
            </a>
            <a href="practice.php" class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-white/5 transition-all group">
                <span class="text-xl opacity-60 group-hover:opacity-100">🎯</span> 
                <span class="font-medium text-gray-400 group-hover:text-white">Practice</span>
            </a>
            <a href="performance.php" class="flex items-center space-x-4 p-4 rounded-2xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 shadow-xl shadow-indigo-500/5 transition-all">
                <span class="text-xl">📊</span> 
                <span class="font-bold">Performance</span>
            </a>
        </nav>

        <div class="w-full p-4 glass rounded-3xl border-white/5 flex items-center space-x-4">
            <img src="../../assets/images/<?php echo $profile_picture; ?>" class="w-10 h-10 rounded-full border border-indigo-500/30">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold truncate"><?php echo $user_name; ?></p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Active Learner</p>
            </div>
            <button onclick="handleLogout()" class="w-10 h-10 rounded-xl glass border-white/5 flex items-center justify-center text-gray-500 hover:text-red-400 hover:bg-red-500/10 hover:border-red-500/20 transition-all group" title="Logout">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </div>
    </aside>

    <!-- Overlay backdrop for Mobile Sidebar -->
    <div id="sidebarOverlay" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 hidden md:hidden transition-all duration-300"></div>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col max-w-6xl mx-auto w-full">
        
        <!-- Header -->
        <header class="flex items-center justify-between mb-10 mt-4 px-4">
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Performance Analytics</h2>
                <p class="text-gray-400 mt-1">Detailed insights into your learning progress</p>
            </div>
            <div class="flex space-x-3">
                <select id="timeRange" class="glass px-4 py-2 rounded-xl text-sm font-bold bg-transparent outline-none">
                    <option class="bg-gray-900">Last 7 Days</option>
                    <option class="bg-gray-900">Last 30 Days</option>
                </select>
            </div>
        </header>

        <!-- Stat Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 px-4 mb-10">
            <div class="stat-card p-6 rounded-3xl flex flex-col space-y-3 xl:col-span-1">
                <div class="w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center text-xl">📘</div>
                <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Total Sessions</p>
                <h3 id="stat-sessions" class="text-2xl font-bold">--</h3>
            </div>
            <div class="stat-card p-6 rounded-3xl flex flex-col space-y-3 xl:col-span-1">
                <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center text-xl">❓</div>
                <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Questions</p>
                <h3 id="stat-questions" class="text-2xl font-bold">--</h3>
            </div>
            <div class="stat-card p-6 rounded-3xl flex flex-col space-y-3 xl:col-span-1">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-xl">🎯</div>
                <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Accuracy</p>
                <h3 id="stat-accuracy" class="text-2xl font-bold">--</h3>
            </div>
            <div class="stat-card p-6 rounded-3xl flex flex-col space-y-3 xl:col-span-1">
                <div class="w-10 h-10 bg-orange-500/10 rounded-xl flex items-center justify-center text-xl">⏱</div>
                <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Study Time</p>
                <h3 id="stat-time" class="text-2xl font-bold">--</h3>
            </div>
            <div class="stat-card p-6 rounded-3xl flex flex-col space-y-3 xl:col-span-1 border border-orange-500/10">
                <div class="w-10 h-10 bg-orange-500/10 rounded-xl flex items-center justify-center text-xl">🔥</div>
                <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Study Streak</p>
                <h3 id="stat-streak" class="text-2xl font-bold text-orange-400">--</h3>
            </div>
            <div class="stat-card p-6 rounded-3xl flex flex-col space-y-3 xl:col-span-1 border border-yellow-500/10">
                <div class="w-10 h-10 bg-yellow-500/10 rounded-xl flex items-center justify-center text-xl">⭐</div>
                <p class="text-xs uppercase tracking-widest text-gray-500 font-bold">Total XP</p>
                <h3 id="stat-xp" class="text-2xl font-bold text-yellow-400">--</h3>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 px-4 mb-10">
            <div class="glass rounded-[2.5rem] p-8">
                <h4 class="text-lg font-bold mb-6 flex items-center">
                    <span class="mr-3">📈</span> Progress Over Time
                </h4>
                <div class="h-64 relative">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
            <div class="glass rounded-[2.5rem] p-8">
                <h4 class="text-lg font-bold mb-6 flex items-center">
                    <span class="mr-3">🧠</span> AI Learning Insights
                </h4>
                <div id="ai-insight-box" class="h-64 flex flex-col justify-center">
                    <div class="bg-indigo-600/5 border border-indigo-500/10 rounded-3xl p-6 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 text-6xl opacity-5">🤖</div>
                        <p id="ai-insight-text" class="text-gray-300 leading-relaxed italic">
                            Analyzing your performance data to generate personalized recommendations...
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>

<script src="../../assets/js/api-auth.js"></script>
<script>
    window.ACADEMY_USER_EMAIL = <?php echo json_encode($user_email); ?>;
    window.ACADEMY_USER_NAME  = <?php echo json_encode($user_name); ?>;
    let progressChart = null;

    function getDaysFilter() {
        const sel = document.getElementById('timeRange');
        return sel && sel.selectedIndex === 1 ? 30 : 7;
    }

    async function loadPerformance() {
        const token = await ensureAccessToken();
        if (!token) {
            document.getElementById('ai-insight-text').textContent =
                'Sign in and complete a practice quiz to see your analytics.';
            return;
        }

        const days = getDaysFilter();

        try {
            const statsRes = await fetch(`http://127.0.0.1:8001/performance/stats?days=${days}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!statsRes.ok) return;
            const stats = await statsRes.json();

            document.getElementById('stat-sessions').textContent = stats.total_sessions ?? 0;
            document.getElementById('stat-questions').textContent = stats.total_questions ?? 0;
            document.getElementById('stat-accuracy').textContent = (stats.accuracy ?? 0) + '%';
            document.getElementById('stat-time').textContent = (stats.study_time ?? 0) + 'm';

            const streak = stats.study_streak ?? 0;
            document.getElementById('stat-streak').textContent =
                streak === 0 ? '0 Days' : (streak === 1 ? '1 Day' : `${streak} Days`);
            document.getElementById('stat-xp').textContent = (stats.total_xp ?? 0).toLocaleString();

            const ctx = document.getElementById('progressChart').getContext('2d');
            if (progressChart) progressChart.destroy();

            const trends = stats.progress_trends || [];
            progressChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trends.length ? trends.map(t => t.date) : ['No data yet'],
                    datasets: [{
                        label: 'Score %',
                        data: trends.length ? trends.map(t => t.score) : [0],
                        borderColor: '#6366F1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#6366F1',
                        pointRadius: 4,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { display: true, beginAtZero: true, max: 100, ticks: { color: 'rgba(255,255,255,0.3)' } },
                        x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.4)', font: { size: 10 } } }
                    }
                }
            });

            const insightRes = await fetch('http://127.0.0.1:8001/performance/insights', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (insightRes.ok) {
                const insightData = await insightRes.json();
                document.getElementById('ai-insight-text').textContent =
                    insightData.insight || "You're doing great! Keep practicing to see more detailed insights.";
            }

        } catch (err) {
            console.error(err);
        }
    }

    function handleLogout() {
        localStorage.removeItem('access_token');
        window.location.href = '../../backend/logout.php';
    }

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

    document.getElementById('timeRange').addEventListener('change', loadPerformance);
    window.addEventListener('load', loadPerformance);
</script>

</body>
</html>
