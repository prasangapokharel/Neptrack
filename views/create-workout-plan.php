<?php include BASE_PATH . '/views/templates/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <a href="/workout-plans" class="inline-flex items-center text-blue-500 hover:text-blue-600 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Back to Workout Plans
    </a>
    
    <h1 class="text-2xl font-bold mb-6">Create New Workout Plan</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form id="create-plan-form">
            <div class="mb-4">
                <label for="plan-name" class="block text-sm font-medium mb-2">Plan Name:</label>
                <input type="text" id="plan-name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
            </div>
            
            <div class="mb-4">
                <label for="plan-description" class="block text-sm font-medium mb-2">Description:</label>
                <textarea id="plan-description" name="description" class="w-full px-4 py-2 border border-gray-300 rounded-md" rows="3" required></textarea>
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_public" value="1" class="mr-2">
                    <span class="text-sm">Make this plan public (visible to other users)</span>
                </label>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md">Create Plan</button>
            </div>
        </form>
    </div>
    
    <div id="exercise-selection" class="hidden">
        <h2 class="text-xl font-semibold mb-4">Add Exercises to Your Plan</h2>
        
        <div class="mb-6">
            <label for="exercise-category" class="block text-sm font-medium mb-2">Filter by Category:</label>
            <select id="exercise-category" class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-md">
                <option value="">All Categories</option>
                <?php foreach($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach($workouts as $workout): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden exercise-card" data-category="<?php echo $workout['category_id']; ?>">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($workout['name']); ?></h3>
                        <p class="text-gray-600 mb-3 text-sm line-clamp-2"><?php echo htmlspecialchars($workout['description']); ?></p>
                        
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <span class="inline-block px-2 py-1 bg-gray-100 rounded-full text-xs text-gray-600 mr-2">
                                <?php echo htmlspecialchars($workout['category_name'] ?? 'General'); ?>
                            </span>
                            <span class="inline-block px-2 py-1 bg-gray-100 rounded-full text-xs text-gray-600">
                                <?php echo ucfirst($workout['difficulty_level']); ?>
                            </span>
                        </div>
                        
                        <button class="add-exercise-btn w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm" data-workout-id="<?php echo $workout['id']; ?>" data-workout-name="<?php echo htmlspecialchars($workout['name']); ?>">
                            Add to Plan
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4">Your Selected Exercises</h3>
            
            <div id="selected-exercises" class="mb-4">
                <p class="text-gray-500 text-center py-4">No exercises added yet</p>
            </div>
            
            <div class="flex justify-end">
                <a href="/workout-plans" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md">Finish</a>
            </div>
        </div>
    </div>
</div>

<!-- Add Exercise Modal -->
<div id="add-exercise-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-4">Add Exercise</h3>
        <p id="exercise-name" class="text-gray-600 mb-4"></p>
        
        <form id="add-exercise-form">
            <input type="hidden" id="workout-id" name="workout_id">
            <input type="hidden" id="plan-id-hidden" name="plan_id">
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="sets" class="block text-sm font-medium mb-2">Sets:</label>
                    <input type="number" id="sets" name="sets" class="w-full px-4 py-2 border border-gray-300 rounded-md" min="1" value="3" required>
                </div>
                
                <div>
                    <label for="reps" class="block text-sm font-medium mb-2">Reps:</label>
                    <input type="number" id="reps" name="reps" class="w-full px-4 py-2 border border-gray-300 rounded-md" min="1" value="10" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="weight" class="block text-sm font-medium mb-2">Weight (optional):</label>
                <input type="text" id="weight" name="weight" class="w-full px-4 py-2 border border-gray-300 rounded-md" placeholder="e.g., 20kg, bodyweight">
            </div>
            
            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium mb-2">Notes (optional):</label>
                <textarea id="notes" name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Any specific instructions or tips"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-add" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Add</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Create plan form
    const createPlanForm = document.getElementById('create-plan-form');
    const exerciseSelection = document.getElementById('exercise-selection');
    let planId = null;
    
    createPlanForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/api/workout-plan/create', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                planId = data.plan_id;
                document.getElementById('plan-id-hidden').value = planId;
                
                // Hide the form and show exercise selection
                createPlanForm.parentElement.classList.add('hidden');
                exerciseSelection.classList.remove('hidden');
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
    
    // Category filter for exercises
    document.getElementById('exercise-category').addEventListener('change', function() {
        const selectedCategory = this.value;
        const exerciseCards = document.querySelectorAll('.exercise-card');
        
        exerciseCards.forEach(card => {
            if (!selectedCategory || card.dataset.category === selectedCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Add exercise modal
    const addExerciseModal = document.getElementById('add-exercise-modal');
    const addExerciseForm = document.getElementById('add-exercise-form');
    const workoutIdInput = document.getElementById('workout-id');
    const exerciseNameElement = document.getElementById('exercise-name');
    
    // Open add exercise modal
    document.querySelectorAll('.add-exercise-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const workoutId = this.dataset.workoutId;
            const workoutName = this.dataset.workoutName;
            
            workoutIdInput.value = workoutId;
            exerciseNameElement.textContent = workoutName;
            
            addExerciseModal.classList.remove('hidden');
        });
    });
    
    // Close add exercise modal
    document.getElementById('cancel-add').addEventListener('click', function() {
        addExerciseModal.classList.add('hidden');
    });
    
    // Submit add exercise form
    addExerciseForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!planId) {
            alert('Please create a plan first');
            return;
        }
        
        const formData = new FormData(this);
        
        fetch('/api/workout-plan/add-exercise', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addExerciseModal.classList.add('hidden');
                updateSelectedExercises();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
    
    // Update selected exercises list
    function updateSelectedExercises() {
        if (!planId) return;
        
        fetch(`/api/workout-plan/exercises?plan_id=${planId}`)
        .then(response => response.json())
        .then(data => {
            const selectedExercisesContainer = document.getElementById('selected-exercises');
            
            if (data.exercises && data.exercises.length > 0) {
                let html = '<ul class="divide-y divide-gray-200">';
                
                data.exercises.forEach(exercise => {
                    html += `
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <h4 class="font-medium">${exercise.workout_name}</h4>
                                <p class="text-sm text-gray-500">${exercise.sets} sets × ${exercise.reps} reps ${exercise.weight ? '• ' + exercise.weight : ''}</p>
                                ${exercise.notes ? `<p class="text-xs text-gray-500 mt-1">${exercise.notes}</p>` : ''}
                            </div>
                            <button class="remove-exercise-btn text-red-500 hover:text-red-600" data-exercise-id="${exercise.id}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </li>
                    `;
                });
                
                html += '</ul>';
                selectedExercisesContainer.innerHTML = html;
                
                // Add event listeners to remove buttons
                document.querySelectorAll('.remove-exercise-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const exerciseId = this.dataset.exerciseId;
                        removeExercise(exerciseId);
                    });
                });
            } else {
                selectedExercisesContainer.innerHTML = '<p class="text-gray-500 text-center py-4">No exercises added yet</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Remove exercise from plan
    function removeExercise(exerciseId) {
        if (!planId) return;
        
        const formData = new FormData();
        formData.append('plan_id', planId);
        formData.append('exercise_id', exerciseId);
        
        fetch('/api/workout-plan/remove-exercise', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSelectedExercises();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>