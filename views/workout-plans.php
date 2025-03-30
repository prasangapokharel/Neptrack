<?php include BASE_PATH . '/views/templates/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Workout Plans</h1>
        <a href="/workout-plans/create" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Create New Plan
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
    
    <!-- My Workout Plans -->
    <div class="mb-10">
        <h2 class="text-xl font-semibold mb-4">My Plans</h2>
        
        <?php if(empty($user_plans)): ?>
            <div class="bg-gray-100 rounded-lg p-6 text-center">
                <p class="text-gray-600">You haven't created any workout plans yet.</p>
                <a href="/workout-plans/create" class="inline-block mt-4 text-green-500 hover:text-green-600 font-medium">Create your first plan</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($user_plans as $plan): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden workout-plan-card" data-category="<?php echo $plan['category_id'] ?? ''; ?>">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($plan['name']); ?></h3>
                            <p class="text-gray-600 mb-4 line-clamp-2"><?php echo htmlspecialchars($plan['description']); ?></p>
                            
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <span class="flex items-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <?php echo count($plan['exercises'] ?? []); ?> Exercises
                                </span>
                                
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?php echo $plan['duration'] ?? '30-45'; ?> mins
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div class="text-sm">
                                    <span class="inline-block px-2 py-1 bg-gray-100 rounded-full text-gray-600">
                                        <?php echo $plan['is_public'] == 1 ? 'Public' : 'Private'; ?>
                                    </span>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button class="log-workout-btn text-green-500 hover:text-green-600" data-plan-id="<?php echo $plan['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                    
                                    <a href="/workout-plans/edit/<?php echo $plan['id']; ?>" class="text-blue-500 hover:text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Popular Workout Plans -->
    <div>
        <h2 class="text-xl font-semibold mb-4">Popular Plans</h2>
        
        <?php if(empty($popular_plans)): ?>
            <div class="bg-gray-100 rounded-lg p-6 text-center">
                <p class="text-gray-600">No popular workout plans available yet.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($popular_plans as $plan): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden workout-plan-card" data-category="<?php echo $plan['category_id'] ?? ''; ?>">
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <img src="<?php echo $plan['user_avatar'] ?? '/assets/images/default-avatar.png'; ?>" alt="User" class="w-8 h-8 rounded-full mr-2">
                                <span class="text-sm font-medium"><?php echo htmlspecialchars($plan['username'] ?? 'User'); ?></span>
                            </div>
                            
                            <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($plan['name']); ?></h3>
                            <p class="text-gray-600 mb-4 line-clamp-2"><?php echo htmlspecialchars($plan['description']); ?></p>
                            
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <span class="flex items-center mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <?php echo count($plan['exercises'] ?? []); ?> Exercises
                                </span>
                                
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?php echo $plan['duration'] ?? '30-45'; ?> mins
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div class="flex items-center text-sm">
                                    <button class="like-btn flex items-center <?php echo $plan['user_liked'] ? 'text-red-500' : 'text-gray-500'; ?> hover:text-red-500" data-plan-id="<?php echo $plan['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="<?php echo $plan['user_liked'] ? 'currentColor' : 'none'; ?>" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span><?php echo $plan['like_count'] ?? 0; ?></span>
                                    </button>
                                </div>
                                
                                <button class="clone-plan-btn text-green-500 hover:text-green-600" data-plan-id="<?php echo $plan['id']; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Log Workout Modal -->
<div id="log-workout-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-4">Log Workout</h3>
        
        <form id="log-workout-form">
            <input type="hidden" id="plan-id" name="plan_id">
            
            <div class="mb-4">
                <label for="workout-notes" class="block text-sm font-medium mb-2">Notes (optional):</label>
                <textarea id="workout-notes" name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-md" rows="3" placeholder="How was your workout?"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-log" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Log Workout</button>
            </div>
        </form>
    </div>
</div>

<!-- Clone Plan Modal -->
<div id="clone-plan-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-xl font-semibold mb-4">Clone Workout Plan</h3>
        <p class="text-gray-600 mb-4">Do you want to clone this workout plan to your collection?</p>
        
        <form id="clone-plan-form">
            <input type="hidden" id="clone-plan-id" name="plan_id">
            
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-clone" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Clone Plan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Category filter functionality
    document.getElementById('category-filter').addEventListener('change', function() {
        const selectedCategory = this.value;
        const workoutCards = document.querySelectorAll('.workout-plan-card');
        
        workoutCards.forEach(card => {
            if (!selectedCategory || card.dataset.category === selectedCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Log workout modal
    const logWorkoutModal = document.getElementById('log-workout-modal');
    const logWorkoutForm = document.getElementById('log-workout-form');
    const planIdInput = document.getElementById('plan-id');
    
    // Open log workout modal
    document.querySelectorAll('.log-workout-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            planIdInput.value = this.dataset.planId;
            logWorkoutModal.classList.remove('hidden');
        });
    });
    
    // Close log workout modal
    document.getElementById('cancel-log').addEventListener('click', function() {
        logWorkoutModal.classList.add('hidden');
    });
    
    // Submit log workout form
    logWorkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/api/workout-plan/log', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                logWorkoutModal.classList.add('hidden');
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
    
    // Like functionality
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const planId = this.dataset.planId;
            const likeCount = this.querySelector('span');
            const likeIcon = this.querySelector('svg');
            
            const formData = new FormData();
            formData.append('plan_id', planId);
            
            fetch('/api/workout-plan/like', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.liked) {
                        this.classList.remove('text-gray-500');
                        this.classList.add('text-red-500');
                        likeIcon.setAttribute('fill', 'currentColor');
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    } else {
                        this.classList.remove('text-red-500');
                        this.classList.add('text-gray-500');
                        likeIcon.setAttribute('fill', 'none');
                        likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
    
    // Clone plan modal
    const clonePlanModal = document.getElementById('clone-plan-modal');
    const clonePlanForm = document.getElementById('clone-plan-form');
    const clonePlanIdInput = document.getElementById('clone-plan-id');
    
    // Open clone plan modal
    document.querySelectorAll('.clone-plan-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            clonePlanIdInput.value = this.dataset.planId;
            clonePlanModal.classList.remove('hidden');
        });
    });
    
    // Close clone plan modal
    document.getElementById('cancel-clone').addEventListener('click', function() {
        clonePlanModal.classList.add('hidden');
    });
    
    // Submit clone plan form
    clonePlanForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/api/workout-plan/clone', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                clonePlanModal.classList.add('hidden');
                // Reload page to show the cloned plan
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
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>