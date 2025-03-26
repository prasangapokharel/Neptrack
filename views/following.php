<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Make sure $user and $db are defined
$database = new Database();
$db = $database->connect();

// If $user is not defined in the controller, define it here
if(!isset($user) || !is_object($user)) {
    $user = new User($db);
    $user->id = isset($user_id) ? $user_id : $_SESSION['user_id'];
    $user->read_single();
}
?>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
        <div class="bg-primary-light px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Following</h2>
            <a href="/profile/<?php echo $user->id; ?>" class="text-primary hover:text-green-600 transition duration-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Profile
            </a>
        </div>
        
        <div class="p-6">
            <div class="mb-4 text-center">
                <div class="text-gray-800">
                    Users <span class="font-medium"><?php echo $user->username; ?></span> is following
                </div>
            </div>
            
            <div class="divide-y divide-gray-100">
                <?php if(empty($following)): ?>
                    <div class="py-8 text-center text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p>Not following anyone yet.</p>
                        <?php if($_SESSION['user_id'] == $user->id): ?>
                            <a href="/users" class="inline-block mt-4 bg-primary hover:bg-green-600 text-white px-4 py-2 rounded-xl transition duration-200">Find Users</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach($following as $follow): ?>
                        <div class="py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <a href="/profile/<?php echo $follow['id']; ?>">
                                    <?php if($follow['profile_image']): ?>
                                        <img src="<?php echo $follow['profile_image']; ?>" alt="<?php echo $follow['username']; ?>" class="w-10 h-10 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                                            <?php echo strtoupper(substr($follow['username'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                <div>
                                    <a href="/profile/<?php echo $follow['id']; ?>" class="font-medium text-gray-800 hover:text-primary transition duration-200"><?php echo $follow['username']; ?></a>
                                </div>
                            </div>
                            
                            <?php if($_SESSION['user_id'] == $user->id): ?>
                                <button class="unfollow-btn bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded-xl text-sm transition duration-200" data-user-id="<?php echo $follow['id']; ?>">Unfollow</button>
                            <?php elseif($_SESSION['user_id'] != $follow['id']): ?>
                                <?php 
                                $current_user = new User($db);
                                $current_user->id = $_SESSION['user_id'];
                                $is_following = $current_user->isFollowing($follow['id']);
                                ?>
                                
                                <?php if($is_following): ?>
                                    <button class="unfollow-btn bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded-xl text-sm transition duration-200" data-user-id="<?php echo $follow['id']; ?>">Unfollow</button>
                                <?php else: ?>
                                    <button class="follow-btn bg-primary hover:bg-green-600 text-white px-3 py-1 rounded-xl text-sm transition duration-200" data-user-id="<?php echo $follow['id']; ?>">Follow</button>
                                <?php endif; ?>
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
                // If on the following page, remove this user from the list
                if(window.location.pathname.includes('/following')) {
                    this.closest('.py-4').remove();
                    
                    // If no more following, show empty state
                    if(document.querySelectorAll('.py-4').length === 0) {
                        const emptyState = `
                        <div class="py-8 text-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p>Not following anyone yet.</p>
                            <a href="/users" class="inline-block mt-4 bg-primary hover:bg-green-600 text-white px-4 py-2 rounded-xl transition duration-200">Find Users</a>
                        </div>
                        `;
                        document.querySelector('.divide-y').innerHTML = emptyState;
                    }
                } else {
                    // Change button to follow
                    this.textContent = 'Follow';
                    this.classList.remove('bg-gray-200', 'hover:bg-gray-300', 'text-gray-800');
                    this.classList.add('bg-primary', 'hover:bg-green-600', 'text-white');
                    this.classList.remove('unfollow-btn');
                    this.classList.add('follow-btn');
                    
                    // Remove this event listener and add follow event listener
                    this.removeEventListener('click', unfollowHandler);
                }
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


