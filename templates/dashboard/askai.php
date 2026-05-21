<?php
use Dell7420\Academy\Database;
session_start();

// Check login
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? htmlspecialchars($_SESSION['user_name']) : 'Guest';
$profile_picture = $is_logged_in
    ? htmlspecialchars($_SESSION['profile_picture'] ?? 'avatar.png')
    : 'avatar.png';

if ($is_logged_in) {
    require_once __DIR__ . '/../../src/Database.php';
}
$user_email = $is_logged_in ? htmlspecialchars($_SESSION['user_email'] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask AI | Academy.AI Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #0B0F1A;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --accent-primary: #6366F1;
            --accent-secondary: #A855F7;
            --text-main: #E2E8F0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1E1B4B, #0B0F1A 60%);
            color: var(--text-main);
            height: 100vh;
            overflow: hidden;
        }

        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .message-bubble {
            max-width: 85%;
            animation: fadeInSlide 0.4s cubic-bezier(0, 0, 0.2, 1);
            position: relative;
        }

        @keyframes fadeInSlide {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ai-bubble {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 4px 20px 20px 20px;
        }

        .user-bubble {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.25);
            border-radius: 20px 20px 4px 20px;
        }

        .typing-dot {
            width: 6px;
            height: 6px;
            background: var(--accent-primary);
            border-radius: 50%;
            display: inline-block;
            margin: 0 1px;
            animation: bounce 1.4s infinite ease-in-out;
        }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0.3); opacity: 0.3; }
            40% { transform: scale(1); opacity: 1; }
        }

        .input-glow:focus-within {
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.2);
            border-color: rgba(99, 102, 241, 0.5);
        }

        .history-item .delete-chat-btn {
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .history-item:hover .delete-chat-btn {
            opacity: 1;
        }

        code {
            font-family: 'Consolas', monospace;
            background: rgba(0,0,0,0.3);
            padding: 2px 6px;
            border-radius: 4px;
            color: #4ade80;
            font-size: 0.9em;
        }

        pre {
            background: #0d1117;
            padding: 16px;
            border-radius: 12px;
            margin: 12px 0;
            overflow-x: auto;
            border: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }

        .mesh-gradient {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.1) 0px, transparent 50%);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body class="flex flex-col md:flex-row p-4 md:p-6 gap-4 md:gap-6 h-screen max-h-screen overflow-hidden">

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
            <a href="askai.php" class="flex items-center space-x-4 p-4 rounded-2xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 shadow-xl shadow-indigo-500/5 transition-all">
                <span class="text-xl">✨</span> 
                <span class="font-bold">Ask AI</span>
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

        <!-- Recent Chats -->
        <div class="w-full flex-1 min-h-0 hidden md:flex flex-col">
            <div class="flex items-center justify-between mb-4 px-4">
                <h4 class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Recent Chats</h4>
                <button id="clearAllChatsBtn" onclick="clearAllChats()" class="text-[10px] font-bold text-red-400/70 hover:text-red-400 uppercase tracking-wider hidden transition-colors" title="Delete all chats">Clear all</button>
            </div>
            <div id="historyList" class="space-y-2 flex-1 overflow-y-auto custom-scrollbar px-2 max-h-[30vh]">
                <p class="text-xs text-gray-600 px-2 italic">No recent chats</p>
            </div>
        </div>

        <!-- User Profile Card -->
        <div class="w-full p-4 glass rounded-3xl border-white/5 flex items-center space-x-4">
            <img src="../../assets/images/<?php echo $profile_picture; ?>" class="w-10 h-10 rounded-full border border-indigo-500/30">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold truncate"><?php echo $user_name; ?></p>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Free Plan</p>
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

    <!-- Main Chat Window -->
    <main class="flex-1 flex flex-col h-full max-w-6xl mx-auto w-full min-h-0">
        
        <!-- Premium Header -->
        <header class="flex items-center justify-between mb-4 md:mb-8 px-2 md:px-4 mt-2 md:mt-0">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Ask Academy AI</h2>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <p class="text-xs text-gray-400 font-medium">Assistant is online & ready</p>
                </div>
            </div>
            <div class="flex space-x-2 md:space-x-3">
                <button id="newChatBtn" class="glass px-3 py-1.5 md:px-4 md:py-2 rounded-xl text-xs md:text-sm font-bold hover:bg-white/5 transition-all">+ New Thread</button>
                <div class="glass w-9 h-9 md:w-10 md:h-10 rounded-xl flex items-center justify-center text-base md:text-lg">💡</div>
            </div>
        </header>

        <!-- Chat Container -->
        <div id="messages" class="flex-1 glass rounded-[2rem] md:rounded-[2.5rem] p-4 sm:p-6 md:p-8 mb-4 md:mb-6 overflow-y-auto custom-scrollbar flex flex-col space-y-6 md:space-y-8 min-h-0">
            <!-- Welcome Screen -->
            <div id="welcomeScreen" class="flex-1 flex flex-col items-center justify-center text-center space-y-6">
                <div class="w-20 h-20 bg-indigo-600/10 rounded-[2rem] flex items-center justify-center text-4xl mb-2">🤖</div>
                <div>
                    <h3 class="text-2xl font-bold mb-2">How can I help you today?</h3>
                    <p class="text-gray-400 max-w-md mx-auto">Ask me about complex topics, help with homework, or generating study guides. I'm here to assist your learning journey.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full max-w-lg mt-8">
                    <button onclick="fillInput('Explain Quantum Physics simply')" class="glass p-4 rounded-2xl text-left text-sm hover:bg-white/5 transition-all border-white/5">
                        <p class="font-bold text-indigo-400 mb-1">Explain Concept</p>
                        <p class="text-gray-500">"Explain Quantum Physics simply"</p>
                    </button>
                    <button onclick="fillInput('Help me solve this math problem')" class="glass p-4 rounded-2xl text-left text-sm hover:bg-white/5 transition-all border-white/5">
                        <p class="font-bold text-purple-400 mb-1">Problem Solver</p>
                        <p class="text-gray-500">"Help me solve this math problem"</p>
                    </button>
                </div>
            </div>
        </div>

        <!-- Premium Input Bar -->
        <div class="glass rounded-[2.5rem] p-4 input-glow transition-all duration-500 shrink-0">
            <div class="flex items-center space-x-4">
                <div class="p-2 glass rounded-2xl text-xl">🖇️</div>
                <input id="userInput" type="text" placeholder="Message Academy AI..." 
                    class="flex-1 bg-transparent border-none focus:ring-0 text-white placeholder-gray-500 py-3 text-lg font-medium outline-none">
                <button id="sendBtn" class="bg-indigo-600 hover:bg-indigo-500 text-white w-14 h-14 rounded-2xl flex items-center justify-center transition-all shadow-2xl shadow-indigo-600/40 active:scale-90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </div>
        </div>
    </main>

<script src="../../assets/js/api-auth.js"></script>
<script>
    window.ACADEMY_USER_EMAIL = <?php echo json_encode($user_email); ?>;
    window.ACADEMY_USER_NAME  = <?php echo json_encode($user_name); ?>;

    const sendBtn = document.getElementById("sendBtn");
    const userInput = document.getElementById("userInput");
    const messages = document.getElementById("messages");
    const welcomeScreen = document.getElementById("welcomeScreen");

    function fillInput(text) {
        userInput.value = text;
        userInput.focus();
    }

    function addMessage(content, type) {
        if (welcomeScreen) welcomeScreen.style.display = 'none';
        
        const messageWrapper = document.createElement("div");
        messageWrapper.className = type === "user" ? "flex justify-end" : "flex justify-start";

        const bubble = document.createElement("div");
        bubble.className = `message-bubble p-5 px-6 ${type === "user" ? "user-bubble" : "ai-bubble"}`;
        
        if (type === "typing") {
            bubble.innerHTML = `<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>`;
        } else {
            // Basic markdown formatting
            const formatted = content
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/`([^`]+)`/g, '<code>$1</code>')
                .replace(/\n/g, '<br>');
            bubble.innerHTML = formatted;
        }

        messageWrapper.appendChild(bubble);
        messages.appendChild(messageWrapper);

        // Scroll to bottom
        messages.scrollTo({ top: messages.scrollHeight, behavior: "smooth" });
        return messageWrapper;
    }

    async function sendMessage() {
        const text = userInput.value.trim();
        if (!text) return;

        addMessage(text, "user");
        userInput.value = "";

        const typingDiv = addMessage("", "typing");

        try {
            await ensureAccessToken();
            const headers = authHeaders();

            const response = await fetch("http://127.0.0.1:8001/ai/ask", {
                method: "POST",
                headers: headers,
                body: JSON.stringify({
                    prompt: text,
                    model: "gemini",
                    session_id: "default"
                })
            });

            const data = await response.json();
            typingDiv.remove();

            if (!response.ok || data.error) {
                addMessage("⚠️ " + (data.error || data.detail || "Quota exceeded or Server error"), "ai");
            } else {
                addMessage(data.response, "ai");
                loadChatHistory();
            }

        } catch (err) {
            typingDiv.remove();
            addMessage("⚠️ Connection failed. Is the FastAPI server running?", "ai");
        }
    }

    sendBtn.onclick = sendMessage;
    userInput.onkeypress = (e) => { if (e.key === "Enter") sendMessage(); };

    document.getElementById("newChatBtn").onclick = () => {
        messages.innerHTML = '';
        messages.appendChild(welcomeScreen);
        welcomeScreen.style.display = 'flex';
    };

    function loadHistoricalChat(promptText, responseText) {
        if (welcomeScreen) welcomeScreen.style.display = 'none';
        messages.innerHTML = '';
        addMessage(promptText, "user");
        addMessage(responseText, "ai");
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    async function deleteChat(id, event) {
        if (event) event.stopPropagation();
        if (!confirm('Delete this chat?')) return;

        const token = await ensureAccessToken();
        if (!token) return;

        try {
            const res = await fetch(`http://127.0.0.1:8001/ai/history/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (res.ok) loadChatHistory();
        } catch (err) {
            console.error('Failed to delete chat:', err);
        }
    }

    async function clearAllChats() {
        if (!confirm('Delete all recent chats? This cannot be undone.')) return;

        const token = await ensureAccessToken();
        if (!token) return;

        try {
            const res = await fetch('http://127.0.0.1:8001/ai/history', {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (res.ok) {
                loadChatHistory();
                messages.innerHTML = '';
                messages.appendChild(welcomeScreen);
                welcomeScreen.style.display = 'flex';
            }
        } catch (err) {
            console.error('Failed to clear chats:', err);
        }
    }

    async function loadChatHistory() {
        const token = await ensureAccessToken();
        if (!token) return;
        try {
            const response = await fetch('http://127.0.0.1:8001/ai/history', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (!response.ok) return;
            const history = await response.json();
            const list = document.getElementById('historyList');
            const clearBtn = document.getElementById('clearAllChatsBtn');

            if (!history.length) {
                list.innerHTML = '<p class="text-xs text-gray-600 px-2 italic">No recent chats</p>';
                if (clearBtn) clearBtn.classList.add('hidden');
                return;
            }

            if (clearBtn) clearBtn.classList.remove('hidden');
            list.innerHTML = '';

            history.forEach(item => {
                const el = document.createElement('div');
                el.className = 'history-item p-3 glass rounded-xl border-white/5 hover:bg-white/5 transition-all text-sm group flex items-center gap-2 cursor-pointer';

                const text = document.createElement('div');
                text.className = 'flex-1 min-w-0';
                text.innerHTML = `<p class="font-medium text-gray-300 truncate group-hover:text-indigo-300 transition-colors">💭 ${escapeHtml(item.prompt)}</p>`;
                text.addEventListener('click', () => loadHistoricalChat(item.prompt, item.response));

                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'delete-chat-btn shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all';
                delBtn.title = 'Delete chat';
                delBtn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`;
                delBtn.addEventListener('click', (e) => deleteChat(item.id, e));

                el.appendChild(text);
                el.appendChild(delBtn);
                list.appendChild(el);
            });
        } catch (err) {
            console.error('Failed to load chat history:', err);
        }
    }

    window.addEventListener('load', loadChatHistory);

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