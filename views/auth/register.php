<?php include BASE_PATH . '/views/templates/header.php'; ?>

<div class="max-w-md mx-auto">
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="px-6 py-8 text-center border-b border-white/10">
      <h2 class="text-2xl font-medium text-white mb-2">Create an Account</h2>
      <p class="text-white/80 font-light">Start tracking your protein intake today</p>
    </div>
    
    <div class="p-6">
      <form id="register-form">
        <div class="mb-4">
          <label for="username" class="block text-sm font-light text-white mb-2">Username</label>
          <input type="text" id="username" name="username" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
        </div>
        
        <div class="mb-4">
          <label for="email" class="block text-sm font-light text-white mb-2">Email</label>
          <input type="email" id="email" name="email" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
        </div>
        
        <div class="mb-4">
          <label for="password" class="block text-sm font-light text-white mb-2">Password</label>
          <input type="password" id="password" name="password" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
        </div>
        
        <div class="mb-6">
          <label for="confirm-password" class="block text-sm font-light text-white mb-2">Confirm Password</label>
          <input type="password" id="confirm-password" name="confirm_password" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
        </div>
        
        <div id="error-message" class="mb-4 text-red-200 text-sm hidden"></div>
        
        <div class="flex justify-center">
          <button type="submit" class="bg-white/20 hover:bg-white/30 text-white px-6 py-2 rounded-xl transition duration-200 w-full">
            Register
          </button>
        </div>
      </form>
      
      <div class="mt-6 text-center">
        <p class="text-white/80 font-light">Already have an account? <a href="/login" class="text-white hover:text-white/80 transition duration-200">Log In</a></p>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const registerForm = document.getElementById('register-form');
  const errorMessage = document.getElementById('error-message');

  registerForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    if(!username || !email || !password || !confirmPassword) {
      errorMessage.textContent = 'Please fill in all fields';
      errorMessage.classList.remove('hidden');
      return;
    }
    
    if(password !== confirmPassword) {
      errorMessage.textContent = 'Passwords do not match';
      errorMessage.classList.remove('hidden');
      return;
    }
    
    fetch('/api/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'username=' + encodeURIComponent(username) + 
            '&email=' + encodeURIComponent(email) + 
            '&password=' + encodeURIComponent(password) + 
            '&confirm_password=' + encodeURIComponent(confirmPassword)
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        window.location.href = '/';
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

