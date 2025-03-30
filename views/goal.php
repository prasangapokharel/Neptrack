<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
  header('Location: /login');
  exit;
}
?>

<div class="max-w-[480px] mx-auto bg-[#F1F5F9] min-h-screen pb-10">
  <header class="bg-white p-1 flex items-center shadow-sm">
    <a href="/" class="mr-4">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#1E293B]">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
      </svg>
    </a>
    <h1 class="text-xl font-semibold text-[#1E293B]">Set Protein Goal</h1>
  </header>

  <!-- Alert Container (initially hidden) -->
  <div id="alert-container" class="hidden mx-5 mt-5"></div>
  
  <!-- Set Goal Card -->
  <div class="bg-white rounded-[16px] shadow-[0_2px_10px_rgba(0,0,0,0.05)] overflow-hidden m-5">
    <div class="p-[15px_20px] border-b border-[#F1F5F9] flex justify-between items-center">
      <h2 class="text-[#1E293B] font-semibold">Daily Protein Target</h2>
      <div class="bg-[#F1F5F9] w-10 h-10 rounded-[10px] flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3B82F6]">
          <path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path>
          <line x1="16" y1="8" x2="2" y2="22"></line>
          <line x1="17.5" y1="15" x2="9" y2="15"></line>
        </svg>
      </div>
    </div>
    
    <div class="p-5">
      <form id="goal-form">
        <div class="mb-4">
          <label for="protein-goal" class="block text-[14px] font-medium text-[#1E293B] mb-2">Daily Protein Goal (grams)</label>
          <input 
            type="number" 
            id="protein-goal" 
            name="protein_goal" 
            value="<?php echo isset($today_goal['protein_goal']) ? $today_goal['protein_goal'] : ''; ?>" 
            min="1" 
            required 
            class="w-full p-[12px_15px] border border-[#F1F5F9] rounded-[12px] focus:outline-none focus:border-[#3B82F6] focus:shadow-[0_0_0_2px_rgba(59,130,246,0.2)] transition-all duration-200"
          >
        </div>
        
        <div class="flex justify-end">
          <button 
            type="submit" 
            class="bg-[#3B82F6] hover:bg-[#2563EB] text-white px-5 py-[10px] rounded-[12px] font-medium transition-all duration-200 flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6L9 17l-5-5"></path>
            </svg>
            Save Goal
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Protein Goal Tips -->
  <div class="bg-white rounded-[16px] shadow-[0_2px_10px_rgba(0,0,0,0.05)] overflow-hidden m-5">
    <div class="p-[15px_20px] border-b border-[#F1F5F9]">
      <h2 class="text-[#1E293B] font-semibold">Protein Goal Tips</h2>
    </div>
    
    <div class="p-5">
      <ul class="space-y-4">
        <li class="flex items-start gap-4 pb-4 border-b border-[#F1F5F9]">
          <div class="flex-shrink-0 w-10 h-10 rounded-[10px] bg-[#F1F5F9] flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3B82F6]">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
          </div>
          <div>
            <h3 class="text-[#1E293B] font-medium">Recommended Daily Intake</h3>
            <p class="text-[#1E293B]/60 text-[14px] leading-[1.6]">The general recommendation is 0.8g of protein per kg of body weight for average adults.</p>
          </div>
        </li>
        
        <li class="flex items-start gap-4 pb-4 border-b border-[#F1F5F9]">
          <div class="flex-shrink-0 w-10 h-10 rounded-[10px] bg-[#F1F5F9] flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3B82F6]">
              <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
              <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
              <line x1="6" y1="1" x2="6" y2="4"></line>
              <line x1="10" y1="1" x2="10" y2="4"></line>
              <line x1="14" y1="1" x2="14" y2="4"></line>
            </svg>
          </div>
          <div>
            <h3 class="text-[#1E293B] font-medium">For Active Individuals</h3>
            <p class="text-[#1E293B]/60 text-[14px] leading-[1.6]">If you're physically active, aim for 1.2-2.0g of protein per kg of body weight.</p>
          </div>
        </li>
        
        <li class="flex items-start gap-4">
          <div class="flex-shrink-0 w-10 h-10 rounded-[10px] bg-[#F1F5F9] flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3B82F6]">
              <path d="M6.5 6.5L17.5 17.5"></path>
              <path d="M6.5 17.5L17.5 6.5"></path>
              <circle cx="12" cy="12" r="10"></circle>
              <circle cx="12" cy="12" r="4"></circle>
            </svg>
          </div>
          <div>
            <h3 class="text-[#1E293B] font-medium">For Muscle Building</h3>
            <p class="text-[#1E293B]/60 text-[14px] leading-[1.6]">When building muscle, consider 1.6-2.2g of protein per kg of body weight.</p>
          </div>
        </li>
      </ul>
    </div>
  </div>
  
  <!-- Confirmation Modal (initially hidden) -->
  <div id="confirmation-modal" class="hidden fixed inset-0 bg-[rgba(0,0,0,0.5)] flex items-center justify-center z-50">
    <div class="bg-white rounded-[16px] shadow-[0_10px_25px_rgba(0,0,0,0.1)] w-[90%] max-w-[400px] p-[25px]">
      <h3 class="text-[#1E293B] text-lg font-semibold mb-3">Goal Updated</h3>
      <p class="text-[#1E293B]/60 mb-5">Your protein goal has been successfully updated.</p>
      <div class="flex justify-end">
        <button id="confirm-button" class="bg-[#3B82F6] hover:bg-[#2563EB] text-white px-5 py-[10px] rounded-[12px] font-medium transition-all duration-200">
          Continue
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const goalForm = document.getElementById('goal-form');
  const alertContainer = document.getElementById('alert-container');
  const confirmationModal = document.getElementById('confirmation-modal');
  const confirmButton = document.getElementById('confirm-button');

  // Show alert function
  function showAlert(message, type) {
    // Create alert element
    const alert = document.createElement('div');
    
    // Set alert styles based on type
    if (type === 'success') {
      alert.className = 'flex items-center p-[15px] rounded-[8px] border-l-4 border-[#10b981] bg-white shadow-[0_4px_12px_rgba(0,0,0,0.1)] mb-4';
      alert.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#10b981] mr-3">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>${message}</span>
      `;
    } else {
      alert.className = 'flex items-center p-[15px] rounded-[8px] border-l-4 border-[#ef4444] bg-white shadow-[0_4px_12px_rgba(0,0,0,0.1)] mb-4';
      alert.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#ef4444] mr-3">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <span>${message}</span>
      `;
    }
    
    // Clear previous alerts
    alertContainer.innerHTML = '';
    
    // Add alert to container and show it
    alertContainer.appendChild(alert);
    alertContainer.classList.remove('hidden');
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
      alertContainer.classList.add('hidden');
    }, 5000);
  }

  // Form submission
  goalForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const proteinGoal = document.getElementById('protein-goal').value;
    
    // Form validation
    if(!proteinGoal || proteinGoal < 1) {
      showAlert('Please enter a valid protein goal', 'error');
      return;
    }
    
    // Submit form data
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
        // Show confirmation modal instead of alert
        confirmationModal.classList.remove('hidden');
      } else {
        showAlert(data.message, 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showAlert('An error occurred. Please try again.', 'error');
    });
  });
  
  // Confirmation modal button
  confirmButton.addEventListener('click', function() {
    confirmationModal.classList.add('hidden');
    window.location.href = '/';
  });
  
  // Close modal when clicking outside
  confirmationModal.addEventListener('click', function(e) {
    if (e.target === confirmationModal) {
      confirmationModal.classList.add('hidden');
    }
  });
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>