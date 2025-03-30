
<?php include BASE_PATH . '/views/templates/admin_header.php'; ?>

<div class="max-w-6xl mx-auto pb-20">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-medium text-white">Manage Workouts</h1>
    <a href="/admin/add-workout" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm transition duration-200 flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      Add New Workout
    </a>
  </div>
  
  <!-- Filter by Category -->
  <div class="glass rounded-xl p-6 mb-6">
    <h2 class="text-lg font-medium text-white mb-4">Filter by Category</h2>
    <div class="flex flex-wrap gap-2">
      <a href="/admin/workouts" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1 rounded-lg text-sm transition duration-200">
        All
      </a>
      <?php foreach($categories as $category): ?>
        <a href="/admin/workouts?category=<?php echo $category['id']; ?>" class="bg-white/10 hover:bg-white/20 text-white/80 hover:text-white px-3 py-1 rounded-lg text-sm transition duration-200">
          <?php echo $category['name']; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  
  <!-- Workouts Table -->
  <div class="glass rounded-xl p-6">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-white/10">
            <th class="text-left py-3 px-4 text-white/70 font-medium">Name</th>
            <th class="text-left py-3 px-4 text-white/70 font-medium">Category</th>
            <th class="text-left py-3 px-4 text-white/70 font-medium">Difficulty</th>
            <th class="text-left py-3 px-4 text-white/70 font-medium">Date Added</th>
            <th class="text-right py-3 px-4 text-white/70 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($workouts)): ?>
            <tr>
              <td colspan="5" class="py-4 px-4 text-center text-white/70">No workouts found.</td>
            </tr>
          <?php else: ?>
            <?php foreach($workouts as $workout): ?>
              <tr class="border-b border-white/10 hover:bg-white/5">
                <td class="py-3 px-4 text-white"><?php echo $workout['name']; ?></td>
                <td class="py-3 px-4 text-white/70"><?php echo $workout['category_name']; ?></td>
                <td class="py-3 px-4 text-white/70 capitalize"><?php echo $workout['difficulty_level']; ?></td>
                <td class="py-3 px-4 text-white/70"><?php echo date('M d, Y', strtotime($workout['created_at'])); ?></td>
                <td class="py-3 px-4 text-right">
                  <a href="/admin/edit-workout/<?php echo $workout['id']; ?>" class="text-blue-400 hover:text-blue-300 mr-3 transition duration-200">Edit</a>
                  <button class="delete-workout text-red-400 hover:text-red-300 transition duration-200" data-id="<?php echo $workout['id']; ?>">Delete</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Delete workout functionality
  const deleteButtons = document.querySelectorAll('.delete-workout');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
      if(confirm('Are you sure you want to delete this workout?')) {
        const workoutId = this.getAttribute('data-id');
        
        fetch('/api/admin/delete-workout', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'id=' + workoutId
        })
        .then(response => response.json())
        .then(data => {
          if(data.success) {
            // Remove the row from the table
            this.closest('tr').remove();
            
            // Show success message
            alert('Workout deleted successfully');
          } else {
            alert(data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
        });
      }
    });
  });
});
</script>

<?php include BASE_PATH . '/views/templates/admin_footer.php'; ?>

