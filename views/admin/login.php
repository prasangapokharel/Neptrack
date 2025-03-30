<?php include BASE_PATH . '/views/templates/header.php'; ?>

<div class="max-w-md mx-auto">
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="px-6 py-8 text-center border-b border-white/10">
      <h2 class="text-2xl font-medium text-white mb-2">Admin Login</h2>
      <p class="text-white/80 font-light">Log in to manage workouts and users</p>
    </div>
    
    <div class="p-6">
      <form id="admin-login-form">
        <div class="mb-4">
          <label for="username" class="block text-sm font-light text-white mb-2">Username</label>
          <input type="text" id="username" name="username" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
        </div>
        
        <div class="mb-6">
          <label for="password" class="block text-sm font-light text-white mb-2">Password</label>
          <input type="password" id="password" name="password" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
        </div>
        
        <div id="error-message" class="mb-4 text-red-200 text-sm hidden"></div>
        
        <div class="flex justify-center">
          <button type="submit" class="bg-white/20 hover:bg-white/30 text-white px-6 py-2 rounded-xl transition duration-200 w-full">
            Log In
          </button>
        </div>
      </form>
      
      <div class="mt-6 text-center">
        <p class="text-white/80 font-light">Return to <a href="/" class="text-white hover:text-white/80 transition duration-200">main site</a></p>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('admin-login-form');
  const errorMessage = document.getElementById('error-message');

  loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    if(!username || !password) {
      errorMessage.textContent = 'Please fill in all fields';
      errorMessage.classList.remove('hidden');
      return;
    }
    
    fetch('/api/admin/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password)
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        window.location.href = '/admin/dashboard';
      } else {
        errorMessage.textContent = data.message;
        errorMessage.classList.remove('hidden');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      errorMessage.textContent = 'An error occurred. Please try again.';
      errorMessage.classList.remove('hidden');
    });
  });
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>

