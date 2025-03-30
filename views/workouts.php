<?php include BASE_PATH . '/views/templates/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Workout Library</h1>
        <a href="/workout-plans" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
            </svg>
            My Workout Plans
        </a>
    </div>
    
    <!-- Filter by category -->
    <div class="mb-6">
        <label for="category-filter" class="block text-sm font-medium mb-2">Filter by Category:</label>
        <select id="category-filter" class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-md">
            <option value="">All Categories</option>
            <?php foreach($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <!-- Filter by difficulty -->
    <div class="mb-6">
        <label class="block text-sm font-medium mb-2">Filter by Difficulty:</label>
        <div class="flex space-x-4">
            <label class="inline-flex items-center">
                <input type="checkbox" class="difficulty-filter" value="beginner" checked>
                <span class="ml-2">Beginner</span>
            </label>
            <label class="inline-flex items-center">
                <input type="checkbox" class="difficulty-filter" value="intermediate" checked>
                <span class="ml-2">Intermediate</span>
            </label>
            <label class="inline-flex items-center">
                <input type="checkbox" class="difficulty-filter" value="advanced" checked>
                <span class="ml-2">Advanced</span>
            </label>
        </div>
    </div>
    
    <!-- Search -->
    <div class="mb-6">
        <label for="workout-search" class="block text-sm font-medium mb-2">Search:</label>
        <input type="text" id="workout-search" class="w-full px-4 py-2 border border-gray-300 rounded-md" placeholder="Search workouts...">
    </div>
    
    <!-- Workouts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(empty($workouts)): ?>
            <div class="col-span-3 bg-gray-100 rounded-lg p-6 text-center">
                <p class="text-gray-600">No workouts found.</p>
            </div>
        <?php else: ?>
            <?php foreach($workouts as $workout): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden workout-card" 
                     data-category="<?php echo $workout['category_id']; ?>" 
                     data-difficulty="<?php echo $workout['difficulty_level']; ?>">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($workout['name']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($workout['description']); ?></p>
                        
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span class="inline-block px-2 py-1 bg-gray-100 rounded-full text-xs text-gray-600 mr-2">
                                <?php echo htmlspecialchars($workout['category_name'] ?? 'General'); ?>
                            </span>
                            <span class="inline-block px-2 py-1 bg-gray-100 rounded-full text-xs text-gray-600">
                                <?php echo ucfirst($workout['difficulty_level']); ?>
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                <?php if(isset($workout['muscle_groups']) && !empty($workout['muscle_groups'])): ?>
                                    <span class="font-medium">Targets:</span> <?php echo htmlspecialchars($workout['muscle_groups']); ?>
                                <?php endif; ?>
                            </span>
                            
                            <button class="add-to-plan-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm" data-workout-id="<?php echo $workout['id']; ?>">
                                Add to Plan
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add to Plan Modal -->
<div id="add-to-plan-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-4">Add to Workout Plan</h3>
        <p id="selected-workout-name" class="text-gray-600 mb-4"></p>
        
        <?php if(empty($user_plans)): ?>
            <p class="text-gray-600 mb-4">You don't have any workout plans yet.</p>
            <div class="flex justify-between">
                <button type="button" id="cancel-add-to-plan" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                <a href="/workout-plans/create" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Create Plan</a>
            </div>
        <?php else: ?>
            <form id="add-to-plan-form">
                <input type="hidden" id="selected-workout-id" name="workout_id">
                
                <div class="mb-4">
                    <label for="plan-select" class="block text-sm font-medium mb-2">Select Plan:</label>
                    <select id="plan-select" name="plan_id" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
                        <option value="">Select a plan</option>
                        <?php foreach($user_plans as $plan): ?>
                            <option value="<?php echo $plan['id']; ?>"><?php echo htmlspecialchars($plan['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="modal-sets" class="block text-sm font-medium mb-2">Sets:</label>
                        <input type="number" id="modal-sets" name="sets" class="w-full px-4 py-2 border border-gray-300 rounded-md" min="1" value="3" required>
                    </div>
                    
                    <div>
                        <label for="modal-reps" class="block text-sm font-medium mb-2">Reps:</label>
                        <input type="number" id="modal-reps" name="reps" class="w-full px-4 py-2 border border-gray-300 rounded-md" min="1" value="10" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="modal-weight" class="block text-sm font-medium mb-2">Weight (optional):</label>
                    <input type="text" id="modal-weight" name="weight" class="w-full px-4 py-2 border border-gray-300 rounded-md" placeholder="e.g., 20kg, bodyweight">
                </div>
                
                <div class="mb-4">
                    <label for="modal-notes" class="block text-sm font-medium mb-2">Notes (optional):</label>
                    <textarea id="modal-notes" name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Any specific instructions or tips"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-add-to-plan" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Add to Plan</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    // Category filter functionality
    document.getElementById('category-filter').addEventListener('change', function() {
        filterWorkouts();
    });
    
    // Difficulty filter functionality
    document.querySelectorAll('.difficulty-filter').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            filterWorkouts();
        });
    });
    
    // Search functionality
    document.getElementById('workout-search').addEventListener('input', function() {
        filterWorkouts();
    });
    
    function filterWorkouts() {
        const selectedCategory = document.getElementById('category-filter').value;
        const searchTerm = document.getElementById('workout-search').value.toLowerCase();
        const selectedDifficulties = Array.from(document.querySelectorAll('.difficulty-filter:checked')).map(cb => cb.value);
        
        const workoutCards = document.querySelectorAll('.workout-card');
        
        workoutCards.forEach(card => {
            const categoryMatch = !selectedCategory || card.dataset.category === selectedCategory;
            const difficultyMatch = selectedDifficulties.includes(card.dataset.difficulty);
            
            const workoutName = card.querySelector('h3').textContent.toLowerCase();
            const workoutDesc = card.querySelector('p').textContent.toLowerCase();
            const searchMatch = workoutName.includes(searchTerm) || workoutDesc.includes(searchTerm);
            
            if (categoryMatch && difficultyMatch && searchMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    // Add to plan modal
    const addToPlanModal = document.getElementById('add-to-plan-modal');
    const addToPlanForm = document.getElementById('add-to-plan-form');
    const selectedWorkoutIdInput = document.getElementById('selected-workout-id');
    const selectedWorkoutNameElement = document.getElementById('selected-workout-name');
    
    // Open add to plan modal
    document.querySelectorAll('.add-to-plan-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const workoutId = this.dataset.workoutId;
            const workoutName = this.closest('.workout-card').querySelector('h3').textContent;
            
            selectedWorkoutIdInput.value = workoutId;
            selectedWorkoutNameElement.textContent = workoutName;
            
            addToPlanModal.classList.remove('hidden');
        });
    });
    
    // Close add to plan modal
    document.getElementById('cancel-add-to-plan').addEventListener('click', function() {
        addToPlanModal.classList.add('hidden');
    });
    
    // Submit add to plan form
    if (addToPlanForm) {
        addToPlanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/api/workout-plan/add-exercise', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Exercise added to workout plan successfully');
                    addToPlanModal.classList.add('hidden');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    }
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>