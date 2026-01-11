<!-- navbar.php -->
<div x-data="{ open: true }" class="w-full bg-gray-800 text-white shadow-lg transition-all duration-300">
    
    <!-- Navbar Container -->
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
        <!-- Left Section -->
        <div class="flex items-center space-x-4">
            <!-- Toggle Button -->
            <button @click="open = !open" class="focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            <!-- Brand -->
            <span x-show="open" class="text-lg font-bold">🏆 Admin Panel</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex items-center space-x-6">
            <!-- Dashboard -->
            <a href="/dashboard" class="flex items-center space-x-2 hover:text-gray-300 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 12l2-2m0 0l7-7 7 7m-9 2v8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <span x-show="open">Dashboard</span>
            </a>

            <!-- Participants -->
            <a href="/participants" class="flex items-center space-x-2 hover:text-gray-300 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M5.121 17.804A6 6 0 0112 15a6 6 0 016.879 2.804" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <span x-show="open">Participants</span>
            </a>

            <!-- Fixtures -->
            <a href="/fixtures" class="flex items-center space-x-2 hover:text-gray-300 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M8 17l4-4-4-4m8 8l-4-4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <span x-show="open">Fixtures</span>
            </a>

            <!-- Logout -->
            <a href="/logout" class="flex items-center space-x-2 hover:text-red-400 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <span x-show="open">Logout</span>
            </a>
        </nav>
    </div>
</div>
