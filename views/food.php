<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
  header('Location: /login');
  exit;
}
?>

<div class="max-w-md mx-auto pb-20">
  <!-- Add Food Card -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-white/10">
      <h2 class="text-lg font-medium text-white">Add Food</h2>
    </div>
    
    <div class="p-4">
      <?php if(isset($today_goal['protein_goal'])): ?>
        <div class="mb-4 p-4 glass-dark rounded-xl">
          <div class="flex justify-between items-center mb-1">
            <span class="text-sm text-white/80 font-light">Today's Goal:</span>
            <span class="font-medium text-white"><?php echo $today_goal['protein_goal']; ?>g</span>
          </div>
          <div class="flex justify-between items-center mb-1">
            <span class="text-sm text-white/80 font-light">Consumed:</span>
            <span class="font-medium text-white"><?php echo $total_consumed; ?>g</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-white/80 font-light">Remaining:</span>
            <span class="font-medium <?php echo $remaining >= 0 ? 'text-white' : 'text-red-200'; ?>">
              <?php echo $remaining >= 0 ? $remaining . 'g' : abs($remaining) . 'g over goal'; ?>
            </span>
          </div>
        </div>
      <?php else: ?>
        <div class="mb-4 p-4 glass-dark rounded-xl">
          <div class="flex items-center text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <span class="font-light">You haven't set a protein goal for today yet. <a href="/goal" class="underline">Set a goal</a> to track your progress.</span>
          </div>
        </div>
      <?php endif; ?>
      
      <form id="food-form">
        <div class="mb-4">
          <label for="food-name" class="block text-sm font-light text-white mb-2">Food Name</label>
          <input type="text" id="food-name" name="food_name" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
        </div>
        
        <div class="mb-6">
          <label for="protein-grams" class="block text-sm font-light text-white mb-2">Protein (grams)</label>
          <input type="number" id="protein-grams" name="protein_grams" min="0" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
        </div>
        
        <div class="flex justify-end">
          <button type="submit" class="bg-white/20 hover:bg-white/30 text-white px-6 py-2 rounded-xl transition duration-200 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Food
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Today's Food Entries -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-white/10 flex justify-between items-center">
      <h2 class="text-lg font-medium text-white">Today's Food Entries</h2>
      <span class="text-sm font-light text-white/80"><?php echo count($food_entries); ?> entries</span>
    </div>
    
    <div class="divide-y divide-white/10">
      <?php if(empty($food_entries)): ?>
        <div class="p-6 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-white/60 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <p class="text-white font-light mb-2">No food entries for today.</p>
          <p class="text-white/80 text-sm font-light">Add your first food entry to track your protein intake.</p>
        </div>
      <?php else: ?>
        <?php foreach($food_entries as $entry): ?>
          <div class="p-4 flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-10 h-10 rounded-lg glass-dark flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                </svg>
              </div>
              <div>
                <div class="font-medium text-white"><?php echo $entry['food_name']; ?></div>
                <div class="text-xs text-white/70 font-light"><?php echo date('h:i A', strtotime($entry['created_at'])); ?></div>
              </div>
            </div>
            <div class="text-right">
              <div class="font-bold text-white"><?php echo $entry['protein_grams']; ?>g</div>
              <form action="/api/food/delete" method="POST" class="inline">
                <input type="hidden" name="food_id" value="<?php echo $entry['id']; ?>">
                <button type="submit" class="text-xs text-red-200 hover:text-red-100 transition duration-200" onclick="return confirm('Are you sure you want to delete this entry?')">
                  Delete
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const foodForm = document.getElementById('food-form');

  foodForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const foodName = document.getElementById('food-name').value;
    const proteinGrams = document.getElementById('protein-grams').value;
    
    if(!foodName || !proteinGrams) {
      alert('Please fill in all fields');
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
        alert('Food added successfully!');
        window.location.reload();
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

