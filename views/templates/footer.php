</div>
<!-- Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 py-2 px-4 flex justify-around items-center z-10">
    <a href="/" class="flex flex-col items-center text-xs <?php echo ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/dashboard') ? 'text-[#3B82F6]' : 'text-[#3B82F6]/70'; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
        <span>Home</span>
    </a>
    
    <a href="/food" class="flex flex-col items-center text-xs <?php echo strpos($_SERVER['REQUEST_URI'], '/food') === 0 ? 'text-[#3B82F6]' : 'text-[#3B82F6]/70'; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
        </svg>
        <span>Food</span>
    </a>
    
    <a href="/feed" class="flex flex-col items-center text-xs <?php echo strpos($_SERVER['REQUEST_URI'], '/feed') === 0 || strpos($_SERVER['REQUEST_URI'], '/post') === 0 ? 'text-[#3B82F6]' : 'text-[#3B82F6]/70'; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <span>Post</span>
    </a>
    
    <a href="/workouts" class="flex flex-col items-center text-xs <?php echo strpos($_SERVER['REQUEST_URI'], '/workout') === 0 ? 'text-[#3B82F6]' : 'text-[#3B82F6]/70'; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
        </svg>
        <span>Workouts</span>
    </a>
    
    <a href="/goal" class="flex flex-col items-center text-xs <?php echo strpos($_SERVER['REQUEST_URI'], '/goal') === 0 ? 'text-[#3B82F6]' : 'text-[#3B82F6]/70'; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
            <line x1="9" y1="9" x2="9.01" y2="9"></line>
            <line x1="15" y1="9" x2="15.01" y2="9"></line>
        </svg>
        <span>Goal</span>
    </a>
    
    <a href="/profile" class="flex flex-col items-center text-xs <?php echo strpos($_SERVER['REQUEST_URI'], '/profile') === 0 || strpos($_SERVER['REQUEST_URI'], '/users') === 0 || strpos($_SERVER['REQUEST_URI'], '/followers') === 0 || strpos($_SERVER['REQUEST_URI'], '/following') === 0 ? 'text-[#3B82F6]' : 'text-[#3B82F6]/70'; ?>">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
        <span>Profile</span>
    </a>
</nav>

<script src="/assets/js/tailwind.js"></script>

<!-- JavaScript -->
<script src="/assets/js/main.js"></script>
</body>
</html>

