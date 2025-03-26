<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
  header('Location: /login');
  exit;
}
?>

<div class="max-w-md mx-auto pb-20">
  <!-- Set Goal Card -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-white/10">
      <h2 class="text-lg font-medium text-white">Set Protein Goal</h2>
    </div>
    
    <div class="p-6">
      <form id="goal-form">
        <div class="mb-4">
          <label for="protein-goal" class="block text-sm font-light text-white mb-2">Daily Protein Goal (grams)</label>
          <input type="number" id="protein-goal" name="protein_goal" value="<?php echo isset($today_goal['protein_goal']) ? $today_goal['protein_goal'] : ''; ?>" min="1" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
        </div>
        
        <div class="flex justify-end">
          <button type="submit" class="bg-white/20 hover:bg-white/30 text-white px-6 py-2 rounded-xl transition duration-200 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Save Goal
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Protein Goal Tips -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-white/10">
      <h2 class="text-lg font-medium text-white">Protein Goal Tips</h2>
    </div>
    
    <div class="p-6">
      <div class="space-y-4">
        <div class="flex items-start space-x-3">
          <div class="flex-shrink-0 w-8 h-8 rounded-full glass-dark flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-white">Recommended Daily Intake</h3>
            <p class="text-sm text-white/80 font-light">The general recommendation is 0.8g of protein per kg of body weight for average adults.</p>
          </div>
        </div>
        
        <div class="flex items-start space-x-3">
          <div class="flex-shrink-0 w-8 h-8 rounded-full glass-dark flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-white">For Active Individuals</h3>
            <p class="text-sm text-white/80 font-light">If you're physically active, aim for 1.2-2.0g of protein per kg of body weight.</p>
          </div>
        </div>
        
        <div class="flex items-start space-x-3">
          <div class="flex-shrink-0 w-8 h-8 rounded-full glass-dark flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-white">For Muscle Building</h3>
            <p class="text-sm text-white/80 font-light">When building muscle, consider 1.6-2.2g of protein per kg of body weight.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const goalForm = document.getElementById('goal-form');

  goalForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const proteinGoal = document.getElementById('protein-goal').value;
    
    if(!proteinGoal || proteinGoal < 1) {
      alert('Please enter a valid protein goal');
      return;
    }
    
    fetch('/api/goal/set', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'protein_goal=' + proteinGoal
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        alert('Goal set successfully!');
        window.location.href = '/';
      } else {
        alert(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred. Please try again.');
    });
  });
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>

