<?php include BASE_PATH . '/views/templates/admin_header.php'; ?>

<div class="max-w-6xl mx-auto pb-20">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-medium text-white">Admin Dashboard</h1>
    <div class="flex items-center">
      <span class="text-white/70 mr-4">Welcome, <?php echo $_SESSION['admin_username']; ?></span>
      <a href="/admin/logout" class="bg-red-500/20 hover:bg-red-500/30 text-red-300 px-4 py-2 rounded-lg text-sm transition duration-200">Logout</a>
    </div>
  </div>
  
  <!-- Stats Cards -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="glass rounded-xl p-6 text-center">
      <div class="text-3xl font-medium text-white mb-2"><?php echo count($workouts); ?></div>
      <div class="text-white/70 font-light">Total Workouts</div>
    </div>
    
    <div class="glass rounded-xl p-6 text-center">
      <div class="text-3xl font-medium text-white mb-2"><?php echo count($categories); ?></div>
      <div class="text-white/70 font-light">Categories</div>
    </div>
    
    <div class="glass rounded-xl p-6 text-center">
      <div class="text-3xl font-medium text-white mb-2"><?php echo $user_count; ?></div>
      <div class="text-white/70 font-light">Users</div>
    </div>
    
    <div class="glass rounded-xl p-6 text-center">
      <div class="text-3xl font-medium text-white mb-2"><?php echo $plan_count; ?></div>
      <div class="text-white/70 font-light">Workout Plans</div>
    </div>
  </div>
  
  <!-- Quick Actions -->
  <div class="glass rounded-xl p-6 mb-8">
    <h2 class="text-xl font-medium text-white mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <a href="/admin/workouts" class="glass-dark hover:bg-white/10 p-4 rounded-lg flex items-center transition duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white mr-3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
        </svg>
        <span class="text-white">Manage Workouts</span>
      </a>
      
      <a href="/admin/add-workout" class="glass-dark hover:bg-white/10 p-4 rounded-lg flex items-center transition duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white mr-3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span class="text-white">Add New Workout</span>
      </a>
      
      <a href="/admin/users" class="glass-dark hover:bg-white/10 p-4 rounded-lg flex items-center transition duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white mr-3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
        </svg>
        <span class="text-white">Manage Users</span>
      </a>
    </div>
  </div>
  
  <!-- Recent Workouts -->
  <div class="glass rounded-xl p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-medium text-white">Recent Workouts</h2>
      <a href="/admin/workouts" class="text-white/70 hover:text-white text-sm transition duration-200">View All</a>
    </div>
    
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
            <?php 
            // Show only the latest 5 workouts
            $recent_workouts = array_slice($workouts, 0, 5);
            foreach($recent_workouts as $workout): 
            ?>
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

