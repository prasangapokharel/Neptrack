<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
  header('Location: /login');
  exit;
}

// Get current user
$database = new Database();
$db = $database->connect();
$user = new User($db);
$user->id = $_SESSION['user_id'];
$user->read_single();

// Get today's goal
$goal = new Goal($db);
$goal->user_id = $_SESSION['user_id'];
$goal->date = date('Y-m-d');
$today_goal = $goal->getTodayGoal();

// Get today's food entries
$food = new Food($db);
$food->user_id = $_SESSION['user_id'];
$food->date = date('Y-m-d');
$food_entries = $food->getTodayEntries();

// Calculate total protein consumed today
$total_consumed = 0;
foreach($food_entries as $entry) {
  $total_consumed += $entry['protein_grams'];
}

// Calculate remaining protein
$remaining = isset($today_goal['protein_goal']) ? $today_goal['protein_goal'] - $total_consumed : 0;
$goal_percentage = isset($today_goal['protein_goal']) && $today_goal['protein_goal'] > 0 ? 
  min(100, round(($total_consumed / $today_goal['protein_goal']) * 100)) : 0;
?>

<style>
  :root {
    --primary: #3B82F6;    /* Vibrant blue */
    --dark: #1E293B;       /* Dark slate */
    --light: #F1F5F9;      /* Light gray */
    --font-main: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  }

  body {
    font-family: var(--font-main);
    background-color: var(--light);
    color: var(--dark);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
  }

  .container {
    max-width: 480px;
    margin: 0 auto;
    padding: 0;
    position: relative;
    min-height: 100vh;
    background-color: white;
  }

  .header {
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .page-title {
    font-size: 24px;
    font-weight: 600;
  }

  .content-wrapper {
    padding: 0 20px 20px;
  }

  .alert-container {
    margin-bottom: 16px;
    display: none;
  }

  .alert {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
  }

  .alert-success {
    border-left: 4px solid #10b981;
  }

  .alert-error {
    border-left: 4px solid #ef4444;
  }

  .alert-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .alert-success .alert-icon {
    background-color: #dcfce7;
    color: #16a34a;
  }

  .alert-error .alert-icon {
    background-color: #fee2e2;
    color: #dc2626;
  }

  .alert-content {
    flex: 1;
  }

  .alert-title {
    font-weight: 600;
    margin-bottom: 5px;
  }

  .alert-message {
    font-size: 14px;
    color: var(--dark);
    opacity: 0.8;
  }

  .card {
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
    overflow: hidden;
  }

  .card-header {
    padding: 15px 20px;
    border-bottom: 1px solid var(--light);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .card-title {
    font-size: 18px;
    font-weight: 600;
  }

  .card-body {
    padding: 20px;
  }

  .goal-summary {
    background-color: var(--light);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 20px;
  }

  .goal-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .goal-row:last-child {
    margin-bottom: 0;
  }

  .goal-label {
    font-size: 14px;
    color: var(--dark);
    opacity: 0.8;
  }

  .goal-value {
    font-weight: 600;
  }

  .goal-warning {
    color: #ef4444;
  }

  .goal-info {
    display: flex;
    align-items: center;
    background-color: var(--light);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 20px;
  }

  .goal-info-icon {
    margin-right: 12px;
    color: var(--primary);
  }

  .goal-info-text {
    font-size: 14px;
    color: var(--dark);
    opacity: 0.8;
  }

  .goal-info-link {
    color: var(--primary);
    text-decoration: underline;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 8px;
  }

  .form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--light);
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.2s ease;
  }

  .form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
  }

  .btn-primary {
    background-color: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background-color: #2563eb;
  }

  .btn-outline {
    background-color: transparent;
    border: 1px solid var(--light);
    color: var(--dark);
  }

  .btn-outline:hover {
    background-color: var(--light);
  }

  .btn-danger {
    background-color: #ef4444;
    color: white;
  }

  .btn-danger:hover {
    background-color: #dc2626;
  }

  .btn-icon {
    margin-right: 8px;
  }

  .food-list {
    border-top: 1px solid var(--light);
  }

  .food-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    border-bottom: 1px solid var(--light);
  }

  .food-item-left {
    display: flex;
    align-items: center;
  }

  .food-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background-color: var(--light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: var(--primary);
  }

  .food-details {
    display: flex;
    flex-direction: column;
  }

  .food-name {
    font-weight: 500;
    margin-bottom: 2px;
  }

  .food-time {
    font-size: 12px;
    color: var(--dark);
    opacity: 0.6;
  }

  .food-protein {
    font-weight: 600;
    text-align: right;
  }

  .food-actions {
    margin-top: 5px;
  }

  .btn-delete {
    background: none;
    border: none;
    color: #ef4444;
    font-size: 12px;
    cursor: pointer;
    padding: 0;
  }

  .btn-delete:hover {
    text-decoration: underline;
  }

  .empty-state {
    padding: 40px 20px;
    text-align: center;
  }

  .empty-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    color: var(--dark);
    opacity: 0.3;
  }

  .empty-title {
    font-weight: 600;
    margin-bottom: 8px;
  }

  .empty-text {
    font-size: 14px;
    color: var(--dark);
    opacity: 0.6;
    margin-bottom: 0;
  }

  .modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    display: none;
  }

  .modal-content {
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 400px;
    padding: 25px;
  }

  .modal-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 15px;
  }

  .modal-text {
    margin-bottom: 25px;
    color: var(--dark);
    opacity: 0.8;
  }

  .modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }

  .progress-bar-bg {
    height: 6px;
    background-color: var(--light);
    border-radius: 3px;
    margin-top: 8px;
  }

  .progress-bar-fill {
    height: 100%;
    background-color: var(--primary);
    border-radius: 3px;
  }
</style>

<div class="container">
  <header class="header">
    <h1 class="page-title">Food Tracker</h1>
    <a href="/dashboard">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6"></path>
      </svg>
    </a>
  </header>

  <div class="content-wrapper">
    <!-- Alert Container -->
    <div id="alert-container" class="alert-container"></div>
    
    <!-- Add Food Card -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">Add Food</h2>
      </div>
      
      <div class="card-body">
        <?php if(isset($today_goal['protein_goal'])): ?>
          <div class="goal-summary">
            <div class="goal-row">
              <span class="goal-label">Today's Goal:</span>
              <span class="goal-value"><?php echo $today_goal['protein_goal']; ?>g</span>
            </div>
            <div class="goal-row">
              <span class="goal-label">Consumed:</span>
              <span class="goal-value"><?php echo $total_consumed; ?>g</span>
            </div>
            <div class="goal-row">
              <span class="goal-label">Remaining:</span>
              <span class="goal-value <?php echo $remaining < 0 ? 'goal-warning' : ''; ?>">
                <?php echo $remaining >= 0 ? $remaining . 'g' : abs($remaining) . 'g over goal'; ?>
              </span>
            </div>
            <div class="progress-bar-bg">
              <div class="progress-bar-fill" style="width: <?php echo $goal_percentage; ?>%"></div>
            </div>
          </div>
        <?php else: ?>
          <div class="goal-info">
            <div class="goal-info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
            </div>
            <div class="goal-info-text">
              You haven't set a protein goal for today yet. <a href="/goal" class="goal-info-link">Set a goal</a> to track your progress.
            </div>
          </div>
        <?php endif; ?>
        
        <form id="food-form">
          <div class="form-group">
            <label for="food-name" class="form-label">Food Name</label>
            <input type="text" id="food-name" name="food_name" required class="form-control" placeholder="Enter food name">
          </div>
          
          <div class="form-group">
            <label for="protein-grams" class="form-label">Protein (grams)</label>
            <input type="number" id="protein-grams" name="protein_grams" min="0" required class="form-control" placeholder="Enter protein amount">
          </div>
          
          <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-icon">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Add Food
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Today's Food Entries -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">Today's Food Entries</h2>
        <span style="font-size: 14px; color: var(--dark); opacity: 0.7;"><?php echo count($food_entries); ?> entries</span>
      </div>
      
      <?php if(empty($food_entries)): ?>
        <div class="empty-state">
          <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <h3 class="empty-title">No food entries for today</h3>
          <p class="empty-text">Add your first food entry to track your protein intake.</p>
        </div>
      <?php else: ?>
        <div class="food-list">
          <?php foreach($food_entries as $entry): ?>
            <div class="food-item">
              <div class="food-item-left">
                <div class="food-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path>
                    <path d="M7 2v20"></path>
                    <path d="M21 15V2"></path>
                    <path d="M18 15a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"></path>
                    <path d="M18 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"></path>
                  </svg>
                </div>
                <div class="food-details">
                  <div class="food-name"><?php echo $entry['food_name']; ?></div>
                  <div class="food-time"><?php echo date('h:i A', strtotime($entry['created_at'])); ?></div>
                </div>
              </div>
              <div>
                <div class="food-protein"><?php echo $entry['protein_grams']; ?>g</div>
                <div class="food-actions">
                  <button 
                    type="button" 
                    class="btn-delete delete-food-btn" 
                    data-food-id="<?php echo $entry['id']; ?>"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal">
  <div class="modal-content">
    <h3 class="modal-title">Confirm Deletion</h3>
    <p class="modal-text">Are you sure you want to delete this food entry? This action cannot be undone.</p>
    <div class="modal-actions">
      <button id="cancel-delete" class="btn btn-outline">
        Cancel
      </button>
      <button id="confirm-delete" class="btn btn-danger">
        Delete
      </button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const foodForm = document.getElementById('food-form');
  const alertContainer = document.getElementById('alert-container');
  const deleteModal = document.getElementById('delete-modal');
  const cancelDelete = document.getElementById('cancel-delete');
  const confirmDelete = document.getElementById('confirm-delete');
  let currentFoodId = null;

  // Show alert function
  function showAlert(type, title, message) {
    let alertHTML = '';
    
    if (type === 'success') {
      alertHTML = `
        <div class="alert alert-success">
          <div class="alert-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
          </div>
          <div class="alert-content">
            <div class="alert-title">${title}</div>
            <div class="alert-message">${message}</div>
          </div>
        </div>
      `;
    } else if (type === 'error') {
      alertHTML = `
        <div class="alert alert-error">
          <div class="alert-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
          </div>
          <div class="alert-content">
            <div class="alert-title">${title}</div>
            <div class="alert-message">${message}</div>
          </div>
        </div>
      `;
    }
    
    alertContainer.innerHTML = alertHTML;
    alertContainer.style.display = 'block';
    
    // Auto hide after 5 seconds
    setTimeout(() => {
      alertContainer.style.display = 'none';
    }, 5000);
  }

  // Add food form submission
  foodForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const foodName = document.getElementById('food-name').value;
    const proteinGrams = document.getElementById('protein-grams').value;
    
    if(!foodName || !proteinGrams) {
      showAlert('error', 'Error', 'Please fill in all fields');
      return;
    }
    
    fetch('/api/food/add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'food_name=' + encodeURIComponent(foodName) + '&protein_grams=' + proteinGrams
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        showAlert('success', 'Success', 'Food added successfully!');
        // Clear form fields
        document.getElementById('food-name').value = '';
        document.getElementById('protein-grams').value = '';
        // Reload page after a short delay
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      } else {
        showAlert('error', 'Error', data.message || 'An error occurred');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showAlert('error', 'Error', 'An error occurred. Please try again.');
    });
  });

  // Delete food entry
  document.querySelectorAll('.delete-food-btn').forEach(button => {
    button.addEventListener('click', function() {
      currentFoodId = this.getAttribute('data-food-id');
      deleteModal.style.display = 'flex';
    });
  });

  // Cancel delete
  cancelDelete.addEventListener('click', function() {
    deleteModal.style.display = 'none';
    currentFoodId = null;
  });

  // Confirm delete
  confirmDelete.addEventListener('click', function() {
    if (!currentFoodId) return;
    
    fetch('/api/food/delete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'food_id=' + currentFoodId
    })
    .then(response => response.json())
    .then(data => {
      deleteModal.style.display = 'none';
      
      if(data.success) {
        showAlert('success', 'Success', 'Food entry deleted successfully!');
        // Reload page after a short delay
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      } else {
        showAlert('error', 'Error', data.message || 'An error occurred');
      }
    })
    .catch(error => {
      deleteModal.style.display = 'none';
      console.error('Error:', error);
      showAlert('error', 'Error', 'An error occurred. Please try again.');
    });
  });

  // Close modal when clicking outside
  deleteModal.addEventListener('click', function(e) {
    if (e.target === deleteModal) {
      deleteModal.style.display = 'none';
      currentFoodId = null;
    }
  });

  // Close alert when clicking on it
  alertContainer.addEventListener('click', function() {
    alertContainer.style.display = 'none';
  });
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>