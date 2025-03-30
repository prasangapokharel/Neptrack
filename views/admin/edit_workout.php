<?php include BASE_PATH . '/views/templates/admin_header.php'; ?>

<div class="max-w-3xl mx-auto pb-20">
  <div class="flex items-center mb-6">
    <a href="/admin/workouts" class="text-white/70 hover:text-white mr-4 transition duration-200">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
      </svg>
    </a>
    <h1 class="text-2xl font-medium text-white">Edit Workout</h1>
  </div>
  
  <div class="glass rounded-xl p-6">
    <form id="edit-workout-form" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?php echo $workout_data['id']; ?>">
      
      <div class="mb-4">
        <label for="name" class="block text-sm font-light text-white mb-2">Workout Name</label>
        <input type="text" id="name" name="name" value="<?php echo $workout_data['name']; ?>" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
      </div>
      
      <div class="mb-4">
        <label for="category_id" class="block text-sm font-light text-white mb-2">Category</label>
        <select id="category_id" name="category_id" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
          <option value="">Select Category</option>
          <?php foreach($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $workout_data['category_id']) ? 'selected' : ''; ?>><?php echo $category['name']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="mb-4">
        <label for="difficulty_level" class="block text-sm font-light text-white mb-2">Difficulty Level</label>
        <select id="difficulty_level" name="difficulty_level" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
          <option value="beginner" <?php echo ($workout_data['difficulty_level'] == 'beginner') ? 'selected' : ''; ?>>Beginner</option>
          <option value="intermediate" <?php echo ($workout_data['difficulty_level'] == 'intermediate') ? 'selected' : ''; ?>>Intermediate</option>
          <option value="advanced" <?php echo ($workout_data['difficulty_level'] == 'advanced') ? 'selected' : ''; ?>>Advanced</option>
        </select>
      </div>
      
      <div class="mb-4">
        <label for="description" class="block text-sm font-light text-white mb-2">Description</label>
        <textarea id="description" name="description" rows="5" required class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"><?php echo $workout_data['description']; ?></textarea>
      </div>
      
      <div class="mb-6">
        <label for="image" class="block text-sm font-light text-white mb-2">Workout Image</label>
        <?php if($workout_data['image']): ?>
          <div class="mb-3">
            <p class="text-white/70 text-sm mb-2">Current Image:</p>
            <img src="<?php echo $workout_data['image']; ?>" alt="<?php echo $workout_data['name']; ?>" class="max-h-48 rounded-lg">
          </div>
        <?php endif; ?>
        <div class="flex items-center">
          <label class="cursor-pointer bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-xl transition duration-200">
            <span>Choose New File</span>
            <input type="file" name="image" id="image" class="hidden" accept="image/*">
          </label>
          <span id="file-name" class="ml-3 text-white/70 text-sm">No file chosen</span>
        </div>
        <div id="image-preview" class="mt-4 hidden">
          <p class="text-white/70 text-sm mb-2">New Image Preview:</p>
          <img id="preview-image" src="#" alt="Preview" class="max-h-48 rounded-lg">
        </div>
      </div>
      
      <div id="error-message" class="mb-4 text-red-200 text-sm hidden"></div>
      
      <div class="flex justify-end">
        <a href="/admin/workouts" class="bg-white/10 hover:bg-white/20 text-white px-6 py-2 rounded-xl transition duration-200 mr-3">Cancel</a>
        <button type="submit" class="bg-white/20 hover:bg-white/30 text-white px-6 py-2 rounded-xl transition duration-200">Update Workout</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const editWorkoutForm = document.getElementById('edit-workout-form');
  const errorMessage = document.getElementById('error-message');
  const imageInput = document.getElementById('image');
  const fileName = document.getElementById('file-name');
  const imagePreview = document.getElementById('image-preview');
  const previewImage = document.getElementById('preview-image');
  
  // Show file name when selected
  imageInput.addEventListener('change', function() {
    if(this.files && this.files[0]) {
      fileName.textContent = this.files[0].name;
      
      // Show image preview
      const reader = new FileReader();
      reader.onload = function(e) {
        previewImage.src = e.target.result;
        imagePreview.classList.remove('hidden');
      }
      reader.readAsDataURL(this.files[0]);
    } else {
      fileName.textContent = 'No file chosen';
      imagePreview.classList.add('hidden');
    }
  });
  
  // Form submission
  editWorkoutForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/api/admin/update-workout', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        alert('Workout updated successfully!');
        window.location.href = '/admin/workouts';
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

<?php include BASE_PATH . '/views/templates/admin_footer.php'; ?>

