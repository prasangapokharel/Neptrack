<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
?>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
        <div class="bg-primary-light px-6 py-4">
            <h2 class="text-xl font-bold text-gray-800">Find Users</h2>
        </div>
        
        <div class="p-6">
            <div class="mb-4">
                <div class="relative">
                    <input type="text" id="search-users" placeholder="Search users..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-2.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            
            <div class="divide-y divide-gray-100" id="users-list">
                <?php if(empty($users)): ?>
                    <div class="py-4 text-center text-gray-500">No users found.</div>
                <?php else: ?>
                    <?php foreach($users as $user): ?>
                        <div class="py-4 flex items-center justify-between user-item">
                            <div class="flex items-center space-x-3">
                                <?php if($user['profile_image']): ?>
                                    <img src="<?php echo $user['profile_image']; ?>" alt="<?php echo $user['username']; ?>" class="w-10 h-10 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <a href="/profile/<?php echo $user['id']; ?>" class="font-medium text-gray-800 hover:text-primary transition duration-200"><?php echo $user['username']; ?></a>
                                </div>
                            </div>
                            
                            <?php 
                            $current_user = new User($db);
                            $current_user->id = $_SESSION['user_id'];
                            $is_following = $current_user->isFollowing($user['id']);
                            ?>
                            
                            <?php if($is_following): ?>
                                <button class="unfollow-btn bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded-xl text-sm transition duration-200" data-user-id="<?php echo $user['id']; ?>">Unfollow</button>
                            <?php else: ?>
                                <button class="follow-btn bg-primary hover:bg-green-600 text-white px-3 py-1 rounded-xl text-sm transition duration-200" data-user-id="<?php echo $user['id']; ?>">Follow</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('search-users');
    const usersList = document.getElementById('users-list');
    const userItems = usersList.querySelectorAll('.user-item');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        userItems.forEach(item => {
            const username = item.querySelector('a').textContent.toLowerCase();
            
            if(username.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Follow functionality
    const followButtons = document.querySelectorAll('.follow-btn');
    followButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            
            fetch('/api/follow', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'following_id=' + userId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Change button to unfollow
                    this.textContent = 'Unfollow';
                    this.classList.remove('bg-primary', 'hover:bg-green-600', 'text-white');
                    this.classList.add('bg-gray-200', 'hover:bg-gray-300', 'text-gray-800');
                    this.classList.remove('follow-btn');
                    this.classList.add('unfollow-btn');
                    
                    // Add event listener for unfollow
                    this.addEventListener('click', unfollowHandler);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
    
    // Unfollow functionality
    const unfollowButtons = document.querySelectorAll('.unfollow-btn');
    unfollowButtons.forEach(button => {
        button.addEventListener('click', unfollowHandler);
    });
    
    function unfollowHandler() {
        const userId = this.getAttribute('data-user-id');
        
        fetch('/api/unfollow', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'following_id=' + userId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Change button to follow
                this.textContent = 'Follow';
                this.classList.remove('bg-gray-200', 'hover:bg-gray-300', 'text-gray-800');
                this.classList.add('bg-primary', 'hover:bg-green-600', 'text-white');
                this.classList.remove('unfollow-btn');
                this.classList.add('follow-btn');
                
                // Remove this event listener and add follow event listener
                this.removeEventListener('click', unfollowHandler);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>

