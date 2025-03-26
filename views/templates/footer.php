</div>
  <!-- Bottom Navigation -->
  <nav class="fixed bottom-0 left-0 right-0 glass-dark py-2 px-4 flex justify-around items-center z-10">
      <a href="/" class="flex flex-col items-center text-xs <?php echo ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/dashboard') ? 'text-white' : 'text-white/70'; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="7" height="9"></rect>
              <rect x="14" y="3" width="7" height="5"></rect>
              <rect x="14" y="12" width="7" height="9"></rect>
              <rect x="3" y="16" width="7" height="5"></rect>
          </svg>
          <span>Dashboard</span>
      </a>
      
      <a href="/feed" class="flex flex-col items-center text-xs <?php echo strpos($_SERVER['REQUEST_URI'], '/feed') === 0 || strpos($_SERVER['REQUEST_URI'], '/post') === 0 ? 'text-white' : 'text-white/70'; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
          </svg>
          <span>Social</span>
      </a>
      
      <a href="/food" class="flex flex-col items-center text-xs <?php echo strpos($_SERVER['REQUEST_URI'], '/food') === 0 ? 'text-white' : 'text-white/70'; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
              <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
              <line x1="6" y1="1" x2="6" y2="4"></line>
              <line x1="10" y1="1" x2="10" y2="4"></line>
              <line x1="14" y1="1" x2="14" y2="4"></line>
          </svg>
          <span>Food</span>
      </a>
      
      <a href="/goal" class="flex flex-col items-center text-xs <?php echo strpos($_SERVER['REQUEST_URI'], '/goal') === 0 ? 'text-white' : 'text-white/70'; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
              <line x1="9" y1="9" x2="9.01" y2="9"></line>
              <line x1="15" y1="9" x2="15.01" y2="9"></line>
          </svg>
          <span>Goal</span>
      </a>
      
      <a href="/profile" class="flex flex-col items-center text-xs <?php echo strpos($_SERVER['REQUEST_URI'], '/profile') === 0 || strpos($_SERVER['REQUEST_URI'], '/users') === 0 || strpos($_SERVER['REQUEST_URI'], '/followers') === 0 || strpos($_SERVER['REQUEST_URI'], '/following') === 0 ? 'text-white' : 'text-white/70'; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
          </svg>
          <span>Profile</span>
      </a>
  </nav>
  <script src="/assets/js/tailwind.js"></script>

  <!-- JavaScript -->
  <script src="/assets/js/main.js"></script>
</body>
</html>

