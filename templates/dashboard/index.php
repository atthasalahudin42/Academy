<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study Helper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    
        
        .float {
            position: absolute;
            border-radius: 50%;
            opacity: 0.2;
            animation: floatAnim 10s linear infinite;
        }
        
        @keyframes floatAnim {
            0% {
                transform: translateY(0px) translateX(0px);
            }
            50% {
                transform: translateY(-30px) translateX(20px);
            }
            100% {
                transform: translateY(0px) translateX(0px);
            }
        }
        
     
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 50;
            justify-content: center;
            align-items: center;
        }
        
        .modal-overlay.active {
            display: flex;
        }
    </style>
</head>

<body class="min-h-screen font-sans relative bg-gradient-to-b from-[#142047] to-[#0F0E0E] overflow-x-hidden overflow-y-auto flex flex-col justify-between">

    <!-- Animated Background Shapes -->
    <div class="float w-32 h-32 md:w-40 md:h-40 bg-blue-500 top-20 left-10"></div>
    <div class="float w-24 h-24 md:w-32 md:h-32 bg-purple-500 top-60 right-20 animation-delay-2s"></div>
    <div class="float w-36 h-36 md:w-48 md:h-48 bg-green-400 bottom-20 left-1/3 md:left-1/2 animation-delay-4s"></div>

    <!-- Navbar: Logo Left, Buttons Right -->
    <div class="absolute top-4 left-0 right-0 px-4 sm:px-8 flex justify-between items-center z-20">
        <!-- Logo + Website Name -->
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-100 text-blue-500 flex items-center justify-center rounded-full text-lg font-bold">
                🤖
            </div>
            <span class="text-white font-bold text-lg">AI-Guide</span>
        </div>

        <!-- Buttons -->
        <div class="flex gap-2 sm:gap-4">
            <a href="../../templates/auth/login.php" class="px-3 py-1.5 sm:px-5 sm:py-2 text-sm sm:text-base rounded-md bg-blue-700 text-white font-semibold shadow-lg flex items-center justify-center
                  hover:scale-105 hover:shadow-2xl hover:ring-4 hover:ring-blue-900 transition transform duration-300">
                Login
            </a>
            <a href="../../templates/auth/signup.php" class="px-3 py-1.5 sm:px-5 sm:py-2 text-sm sm:text-base rounded-md bg-purple-600 text-white font-semibold shadow-lg flex items-center justify-center
                  hover:scale-105 hover:shadow-2xl hover:ring-4 hover:ring-purple-900 transition transform duration-300">
                Sign Up
            </a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="flex-1 flex flex-col items-center justify-center px-4 pt-24 pb-12 sm:pt-28 md:pt-32 relative z-10 w-full max-w-7xl mx-auto">

        <!-- Hero Section -->
        <div class="text-center mb-8 md:mb-12 relative z-10 mt-6">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-2 leading-tight">AI-Powered</h1>
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">Your Learning Companion</span>
            </h1>
            <p class="text-gray-300 text-sm sm:text-base md:text-lg max-w-2xl mx-auto px-2 leading-relaxed">
                Study smarter with AI assistance, practice at your own pace, and watch yourself <br class="hidden md:inline">improve every day.
            </p>
        </div>

        <!-- Cards Container -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 max-w-5xl w-full mx-auto px-2 sm:px-4 mb-8">

            <!-- Card 1: ASK AI -->
            <div class="block w-full h-full cursor-pointer" onclick="handleAskAI()">
                <div class="bg-gradient-to-r from-blue-950 to-purple-950 rounded-xl shadow-lg p-6 sm:p-8 flex flex-col items-center
                    hover:shadow-2xl hover:scale-105 hover:ring-4 hover:ring-blue-900 transition duration-500 w-full h-full">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-blue-500 text-2xl font-bold">🤖</span>
                    </div>
                    <h2 class="text-lg sm:text-xl text-white font-semibold mb-2">Ask AI</h2>
                    <p class="text-gray-300 text-center text-sm sm:text-base">Your personal AI study assistant.</p>
                    <p class="text-gray-500 text-xs mt-2">(Guest access available)</p>
                </div>
            </div>

            <!-- Card 2: Practice Mode -->
            <div class="block w-full h-full cursor-pointer" onclick="handlePracticeMode()">
                <div class="bg-gradient-to-r from-blue-950 to-purple-950 rounded-xl shadow-lg p-6 sm:p-8 flex flex-col items-center
                    hover:shadow-2xl hover:scale-105 hover:ring-4 hover:ring-blue-900 transition duration-500 w-full h-full">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-green-500 text-2xl font-bold">📚</span>
                    </div>
                    <h2 class="text-lg sm:text-xl text-white font-semibold mb-2">Practice Mode</h2>
                    <p class="text-gray-300 text-center text-sm sm:text-base">Improve your skills with AI-generated quizzes.</p>
                    <p class="text-gray-500 text-xs mt-2">(Guest access available)</p>
                </div>
            </div>

            <!-- Card 3: My Progress Dashboard -->
            <div class="block w-full h-full cursor-pointer md:col-span-2 lg:col-span-1 md:max-w-md md:mx-auto lg:max-w-none" onclick="handlePerformance()">
                <div class="bg-gradient-to-r from-blue-950 to-purple-950 rounded-xl shadow-lg p-6 sm:p-8 flex flex-col items-center
                    hover:shadow-2xl hover:scale-105 hover:ring-4 hover:ring-blue-900 transition duration-500 w-full h-full">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-purple-500 text-2xl font-bold">📊</span>
                    </div>
                    <h2 class="text-lg sm:text-xl text-white font-semibold mb-2">Performance Dashboard</h2>
                    <p class="text-gray-300 text-center text-sm sm:text-base">Track your quiz results and monitor your improvement.</p>
                    <p class="text-purple-400 text-xs mt-2">(Login required)</p>
                </div>
            </div>

        </div>

    </div>
    <footer class="w-full text-center py-4 text-gray-500 text-xs border-t border-white/5 relative z-10 shrink-0">
        © 6th Semester | AI Study Helper
    </footer>

    <!-- Login Required Modal -->
    <div id="loginModal" class="modal-overlay">
        <div class="bg-gradient-to-r from-blue-950 to-purple-950 rounded-xl p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-700">
            <div class="text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-yellow-500 text-3xl">🔒</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-2" id="modalTitle">Login Required</h3>
                <p class="text-gray-300 mb-6" id="modalMessage">Please login to access this feature.</p>
                <div class="flex flex-col gap-3" id="modalButtons">
                    <a href="../../templates/auth/login.php" class="w-full bg-blue-700 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition duration-300 text-center">
                        Login
                    </a>
                    <a href="../../templates/auth/signup.php" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-semibold py-3 rounded-lg transition duration-300 text-center">
                        Sign Up
                    </a>
                    <button onclick="closeModal()" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 rounded-lg transition duration-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Performance - Always requires login
        function handlePerformance() {
            if (localStorage.getItem('access_token')) {
                window.location.href = 'performance.php';
                return;
            }
            document.getElementById('modalTitle').textContent = 'Performance Tracking Requires Login';
            document.getElementById('modalMessage').textContent = 'To track your performance and view your progress history, you need to be logged in. Please login or sign up to continue.';
            document.getElementById('loginModal').classList.add('active');
        }

        // Ask AI - Guest access but login for saving history
        function handleAskAI() {
            if (localStorage.getItem('access_token')) {
                window.location.href = 'askai.php';
                return;
            }
            document.getElementById('modalTitle').textContent = 'Continue as Guest or Login?';
            document.getElementById('modalMessage').textContent = 'You can use Ask AI as a guest, but to save your chat history, you need to login. What would you like to do?';
            
            // Update modal buttons for Ask AI
            document.getElementById('modalButtons').innerHTML = `
                <a href="askai.php" class="w-full bg-green-600 hover:bg-green-500 text-white font-semibold py-3 rounded-lg transition duration-300 text-center">
                    Continue as Guest
                </a>
                <a href="../../templates/auth/login.php" class="w-full bg-blue-700 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition duration-300 text-center">
                    Login to Save History
                </a>
                <button onclick="closeModal()" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 rounded-lg transition duration-300">
                    Cancel
                </button>
            `;
            document.getElementById('loginModal').classList.add('active');
        }

        // Practice Mode - Guest access but login for saving history
        function handlePracticeMode() {
            if (localStorage.getItem('access_token')) {
                window.location.href = 'practice.php';
                return;
            }
            document.getElementById('modalTitle').textContent = 'Continue as Guest or Login?';
            document.getElementById('modalMessage').textContent = 'You can practice as a guest, but to save your quiz results and track progress, you need to login. What would you like to do?';
            
            // Update modal buttons for Practice Mode
            document.getElementById('modalButtons').innerHTML = `
                <a href="practice.php" class="w-full bg-green-600 hover:bg-green-500 text-white font-semibold py-3 rounded-lg transition duration-300 text-center">
                    Continue as Guest
                </a>
                <a href="../../templates/auth/login.php" class="w-full bg-blue-700 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition duration-300 text-center">
                    Login to Save Results
                </a>
                <button onclick="closeModal()" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 rounded-lg transition duration-300">
                    Cancel
                </button>
            `;
            document.getElementById('loginModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('loginModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>

</html>