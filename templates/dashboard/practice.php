<?php
use Dell7420\Academy\Database;
session_start();
require_once __DIR__ . '/../../src/Database.php';

// Check login
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? htmlspecialchars($_SESSION['user_name']) : 'Guest';
$user_email = $is_logged_in ? htmlspecialchars($_SESSION['user_email'] ?? '') : '';
$profile_picture = $is_logged_in ? htmlspecialchars($_SESSION['profile_picture'] ?? 'avatar.png') : 'avatar.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practice | Academy.AI Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@300;400;500&display=swap"
        rel="stylesheet">
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

        .step-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .type-btn-active {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)) !important;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
            border-color: transparent !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .mesh-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(at 0% 100%, rgba(99, 102, 241, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.05) 0px, transparent 50%);
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
            <a href="practice.php" class="flex items-center space-x-4 p-4 rounded-2xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 shadow-xl shadow-indigo-500/5 transition-all">
                <span class="text-xl">🎯</span> 
                <span class="font-bold">Practice</span>
            </a>
            <a href="performance.php" class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-white/5 transition-all group">
                <span class="text-xl opacity-60 group-hover:opacity-100">📊</span> 
                <span class="font-medium text-gray-400 group-hover:text-white">Performance</span>
            </a>
        </nav>

        <div class="w-full hidden md:block">
            <h4 class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-4 px-4">Recent Sessions</h4>
            <div id="historyList" class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar px-2">
                <p class="text-xs text-gray-600 px-2 italic">No sessions yet</p>
            </div>
        </div>

        <div class="w-full p-4 glass rounded-3xl border-white/5 flex items-center space-x-4">
            <img src="../../assets/images/<?php echo $profile_picture; ?>" class="w-10 h-10 rounded-full border border-indigo-500/30">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold truncate"><?php echo $user_name; ?></p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Level 12</p>
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
    <main class="flex-1 flex flex-col max-w-5xl mx-auto w-full">

        <!-- Header -->
        <header class="mb-10 mt-4 px-4">
            <h2 class="text-3xl font-bold tracking-tight">Practice Arena</h2>
            <p class="text-gray-400 mt-1">Master any subject with AI-generated challenges</p>
        </header>

        <!-- Step-by-Step UI -->
        <div id="practice-setup" class="space-y-8 px-4">

            <!-- Step 1: Topic -->
            <div class="step-card rounded-[2.5rem] p-8">
                <div class="flex items-center space-x-4 mb-6">
                    <span
                        class="w-10 h-10 glass rounded-xl flex items-center justify-center font-bold text-indigo-400">1</span>
                    <h3 class="text-xl font-bold">What are you studying?</h3>
                </div>
                <input id="topic-input" type="text" placeholder="Enter topic (e.g. Molecular Biology, Calculus, WW2)..."
                    class="w-full glass bg-transparent px-6 py-4 rounded-2xl focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all text-lg font-medium">
            </div>

            <!-- Step 2: Config -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="step-card rounded-[2.5rem] p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <span
                            class="w-10 h-10 glass rounded-xl flex items-center justify-center font-bold text-emerald-400">2</span>
                        <h3 class="text-xl font-bold">Question Type</h3>
                    </div>
                    <div class="w-full">
                        <button onclick="selectType(this, 'mcq')"
                            class="type-btn w-full glass p-4 rounded-2xl text-sm font-bold hover:bg-white/5 transition-all">MCQs</button>
                        <!-- <button onclick="selectType(this, 'true_false')" class="type-btn glass p-4 rounded-2xl text-sm font-bold hover:bg-white/5 transition-all">True/False</button> -->
                    </div>
                </div>

                <div class="step-card rounded-[2.5rem] p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <span
                            class="w-10 h-10 glass rounded-xl flex items-center justify-center font-bold text-purple-400">3</span>
                        <h3 class="text-xl font-bold">Settings</h3>
                    </div>
                    <div class="flex space-x-4">
                        <select id="difficulty"
                            class="flex-1 glass bg-transparent px-4 py-3 rounded-2xl outline-none font-bold text-sm">
                            <option class="bg-gray-900" value="easy">Easy</option>
                            <option class="bg-gray-900" value="medium" selected>Medium</option>
                            <option class="bg-gray-900" value="hard">Hard</option>
                        </select>
                        <input id="num-questions" type="number" value="5" min="1" max="15"
                            class="w-24 glass bg-transparent px-4 py-3 rounded-2xl outline-none font-bold text-center">
                    </div>
                </div>
            </div>

            <button id="generateBtn" onclick="generateQuiz()"
                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-6 rounded-[2rem] font-bold text-xl transition-all shadow-2xl shadow-indigo-600/30 active:scale-[0.98]">
                Generate Practice Quiz 🚀
            </button>
        </div>

        <!-- Output Area -->
        <div id="practiceOutput" class="px-4 mt-8 hidden">
            <!-- Questions load here -->
        </div>

    </main>

    <script src="../../assets/js/api-auth.js"></script>
    <script>
        window.ACADEMY_USER_EMAIL = <?php echo json_encode($user_email); ?>;
        window.ACADEMY_USER_NAME  = <?php echo json_encode($user_name); ?>;

        let selectedType = 'mcq';
        let currentSessionId = null;
        let questions = [];

        function selectType(btn, type) {
            selectedType = type;
            document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('type-btn-active'));
            btn.classList.add('type-btn-active');
        }

        window.onload = async () => {
            selectType(document.querySelector('.type-btn'), 'mcq');
            await ensureAccessToken();
            loadPracticeHistory();
        };

        async function generateQuiz() {
            const topic = document.getElementById('topic-input').value.trim();
            if (!topic) return alert("Please enter a topic first!");

            const btn = document.getElementById('generateBtn');
            const output = document.getElementById('practiceOutput');

            btn.disabled = true;
            btn.innerHTML = `<span class="animate-pulse">Synthesizing Questions...</span>`;

            output.classList.remove('hidden');
            output.innerHTML = `
            <div class="glass rounded-[2.5rem] p-12 text-center">
                <div class="w-20 h-20 bg-indigo-500/10 rounded-[2rem] flex items-center justify-center text-4xl mx-auto mb-6 animate-bounce">✨</div>
                <h3 class="text-2xl font-bold mb-2">Generating your quiz...</h3>
                <p class="text-gray-500 italic">Academy AI is browsing your materials to create high-quality challenges.</p>
            </div>
        `;

            try {
                await ensureAccessToken();
                const response = await fetch('http://127.0.0.1:8001/practice/generate', {
                    method: 'POST',
                    headers: authHeaders(),
                    body: JSON.stringify({
                        topic: topic,
                        question_type: selectedType,
                        difficulty: document.getElementById('difficulty').value,
                        num_questions: parseInt(document.getElementById('num-questions').value)
                    })
                });

                const data = await response.json();
                if (response.ok && data.questions) {
                    questions = data.questions;
                    currentSessionId = data.session_id;
                    renderQuestions();
                    document.getElementById('practice-setup').classList.add('hidden');
                } else {
                    output.innerHTML = `<div class="glass p-8 rounded-3xl text-red-400">⚠️ ${data.detail || 'Error generating quiz. Please check your API key.'}</div>`;
                }
            } catch (err) {
                output.innerHTML = `<div class="glass p-8 rounded-3xl text-red-400">⚠️ Connection failed. Is the backend running?</div>`;
            } finally {
                btn.disabled = false;
                btn.innerHTML = `Generate Practice Quiz 🚀`;
            }
        }

        function renderQuestions() {
            const output = document.getElementById('practiceOutput');
            output.innerHTML = `
            <div class="space-y-8 pb-12">
                ${questions.map((q, i) => `
                    <div class="glass rounded-[2.5rem] p-10 space-y-6">
                        <div class="flex items-start space-x-4">
                            <span class="text-indigo-400 font-bold mt-1">#${i + 1}</span>
                            <h4 class="text-xl font-semibold">${q.question_text}</h4>
                        </div>
                        <div class="grid grid-cols-1 gap-3 pl-8">
                            ${q.options.map(opt => `
                                <label class="glass p-5 rounded-2xl cursor-pointer hover:bg-white/5 transition-all flex items-center space-x-4 group">
                                    <input type="radio" name="q_${q.id}" value="${opt}" class="w-5 h-5 accent-indigo-500">
                                    <span class="text-gray-300 group-hover:text-white transition-colors">${opt}</span>
                                </label>
                            `).join('')}
                        </div>
                    </div>
                `).join('')}
                <button onclick="submitQuiz()" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-6 rounded-[2rem] font-bold text-xl shadow-xl shadow-emerald-900/20 transition-all">
                    Finish & Submit
                </button>
            </div>
        `;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        async function submitQuiz() {
            const answers = questions.map(q => {
                const selected = document.querySelector(`input[name="q_${q.id}"]:checked`);
                return { question_id: q.id, selected_answer: selected ? selected.value : "" };
            });

            try {
                await ensureAccessToken();
                const response = await fetch('http://127.0.0.1:8001/practice/submit', {
                    method: 'POST',
                    headers: authHeaders(),
                    body: JSON.stringify({ session_id: currentSessionId, answers })
                });

                const result = await response.json();
                if (response.ok) showResults(result);
            } catch (err) { alert("Submission failed"); }
        }

        function showResults(result) {
            const output = document.getElementById('practiceOutput');
            output.innerHTML = `
            <div class="glass rounded-[3rem] p-16 text-center space-y-8">
                <div class="w-24 h-24 bg-emerald-500/10 rounded-[2.5rem] flex items-center justify-center text-5xl mx-auto">🏆</div>
                <div>
                    <h2 class="text-5xl font-bold mb-2">Quiz Complete!</h2>
                    <p class="text-gray-400 text-xl">Brilliant work on this session.</p>
                </div>
                <div class="flex justify-center gap-8">
                    <div class="text-center">
                        <p class="text-gray-500 uppercase tracking-widest text-[10px] font-bold mb-1">Score</p>
                        <p class="text-4xl font-bold text-emerald-400">${result.score}%</p>
                    </div>
                    <div class="w-px h-12 bg-white/10"></div>
                    <div class="text-center">
                        <p class="text-gray-500 uppercase tracking-widest text-[10px] font-bold mb-1">Correct</p>
                        <p class="text-4xl font-bold">${result.correct_count}/${result.total}</p>
                    </div>
                </div>
                <button onclick="location.reload()" class="bg-white text-indigo-900 px-12 py-4 rounded-2xl font-bold text-lg hover:scale-105 transition-transform">
                    Back to Arena
                </button>
            </div>
        `;
        }

        async function loadPracticeHistory() {
            const token = await ensureAccessToken();
            if (!token) return;
            try {
                const response = await fetch('http://127.0.0.1:8001/practice/history', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const history = await response.json();
                const list = document.getElementById('historyList');
                if (history.length > 0) {
                    list.innerHTML = history.slice(0, 5).map(item => `
                    <div class="p-3 glass rounded-xl border-white/5 text-[11px] truncate">
                        <p class="font-bold text-gray-300 truncate">${item.topic}</p>
                        <p class="text-indigo-400 font-bold">${item.score}% Accurate</p>
                    </div>
                `).join('');
                }
            } catch (err) { }
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
    </script>

</body>

</html>