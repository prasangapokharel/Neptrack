<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
  header('Location: /login');
  exit;
}

// Make sure $user is defined
if(!isset($user) || !is_object($user)) {
  $database = new Database();
  $db = $database->connect();
  $user = new User($db);
  $user->id = isset($user_id) ? $user_id : $_SESSION['user_id'];
  $user->read_single();

  // Get user's posts
  $post = new Post($db);
  $posts = $post->getUserPosts($user->id);

  // Get followers and following counts
  $followers_count = $user->getFollowersCount();
  $following_count = $user->getFollowingCount();

  // Check if current user is following this user
  $is_following = false;
  if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user->id) {
    $current_user = new User($db);
    $current_user->id = $_SESSION['user_id'];
    $is_following = $current_user->isFollowing($user->id);
  }
}
?>

<div class="max-w-md mx-auto pb-20">
  <!-- Profile Header -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="px-6 py-8 text-center border-b border-white/10">
      <?php if($user->profile_image): ?>
        <img src="<?php echo $user->profile_image; ?>" alt="<?php echo $user->username; ?>" class="w-24 h-24 rounded-full object-cover mx-auto mb-4 border-2 border-white/30 shadow-md">
      <?php else: ?>
        <div class="w-24 h-24 rounded-full glass-dark flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
          <?php echo strtoupper(substr($user->username, 0, 1)); ?>
        </div>
      <?php endif; ?>
      <h2 class="text-2xl font-medium text-white mb-2"><?php echo $user->username; ?></h2>
      <p class="text-white/80 font-light"><?php echo $user->email; ?></p>
      
      <!-- Follow/Unfollow Button (only show if viewing another user's profile) -->
      <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user->id): ?>
        <div class="mt-4">
          <?php if($is_following): ?>
            <button id="unfollow-btn" data-user-id="<?php echo $user->id; ?>" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl transition duration-200 flex items-center mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12z" />
              </svg>
              Unfollow
            </button>
          <?php else: ?>
            <button id="follow-btn" data-user-id="<?php echo $user->id; ?>" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl transition duration-200 flex items-center mx-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z" />
                <path d="M16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
              </svg>
              Follow
            </button>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      
      <!-- Stats -->
      <div class="flex justify-center mt-4 space-x-6">
        <a href="/followers/<?php echo $user->id; ?>" class="text-center">
          <div class="text-xl font-medium text-white"><?php echo $followers_count; ?></div>
          <div class="text-sm text-white/80 font-light">Followers</div>
        </a>
        <a href="/following/<?php echo $user->id; ?>" class="text-center">
          <div class="text-xl font-medium text-white"><?php echo $following_count; ?></div>
          <div class="text-sm text-white/80 font-light">Following</div>
        </a>
        <div class="text-center">
          <div class="text-xl font-medium text-white"><?php echo count($posts); ?></div>
          <div class="text-sm text-white/80 font-light">Posts</div>
        </div>
      </div>
    </div>
    
    <div class="p-6">
      <div class="space-y-3">
        <?php if($_SESSION['user_id'] == $user->id): ?>
          <a href="/edit-profile" class="flex items-center justify-between p-4 glass-dark rounded-xl hover:bg-white/10 transition duration-200">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white mr-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
              <span class="text-white">Edit Profile</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </a>
          
          <a href="/users" class="flex items-center justify-between p-4 glass-dark rounded-xl hover:bg-white/10 transition duration-200">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white mr-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
              </svg>
              <span class="text-white">Find Users</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </a>
          
          <a href="/logout" class="flex items-center justify-between p-4 glass-dark rounded-xl hover:bg-white/10 transition duration-200">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-300 mr-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm11 3a1 1 0 10-2 0v6.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L14 12.586V6z" clip-rule="evenodd" />
              </svg>
              <span class="text-red-300">Logout</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- User Posts -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-white/10 flex justify-between items-center">
      <h3 class="text-lg font-medium text-white">Posts</h3>
      <?php if($_SESSION['user_id'] == $user->id): ?>
        <a href="/feed" class="inline-flex items-center bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl text-sm font-medium transition duration-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
          </svg>
          New Post
        </a>
      <?php endif; ?>
    </div>
    
    <div class="divide-y divide-white/10">
      <?php if(empty($posts)): ?>
        <div class="p-8 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-white/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
          </svg>
          <p class="text-white/80 font-light">No posts yet.</p>
        </div>
      <?php else: ?>
        <?php foreach($posts as $post): ?>
          <div class="p-6">
            <div class="flex items-start space-x-3 mb-3">
              <?php if($post['profile_image']): ?>
                <img src="<?php echo $post['profile_image']; ?>" alt="<?php echo $post['username']; ?>" class="w-10 h-10 rounded-full object-cover">
              <?php else: ?>
                <div class="w-10 h-10 rounded-full glass-dark flex items-center justify-center text-white font-medium">
                  <?php echo strtoupper(substr($post['username'], 0, 1)); ?>
                </div>
              <?php endif; ?>
              <div>
                <div class="font-medium text-white"><?php echo $post['username']; ?></div>
                <div class="text-xs text-white/70 font-light"><?php echo date('M d, Y \a\t h:i A', strtotime($post['created_at'])); ?></div>
              </div>
            </div>
            
            <div class="mb-4">
              <p class="text-white font-light"><?php echo nl2br($post['content']); ?></p>
            </div>
            
            <?php if($post['image']): ?>
              <div class="mb-4">
                <img src="<?php echo $post['image']; ?>" alt="Post image" class="rounded-xl w-full h-auto">
              </div>
            <?php endif; ?>
            
            <div class="flex items-center justify-between text-sm text-white/70">
              <div class="flex items-center space-x-4">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                  </svg>
                  <span><?php echo $post['like_count']; ?> likes</span>
                </div>
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" />
                  </svg>
                  <span><?php echo $post['comment_count']; ?> comments</span>
                </div>
              </div>
              
              <a href="/post/<?php echo $post['id']; ?>" class="text-white hover:text-white/80 transition duration-200">View Post</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Follow button
  const followBtn = document.getElementById('follow-btn');
  if(followBtn) {
    followBtn.addEventListener('click', function() {
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
          // Reload page to update UI
          window.location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    });
  }

  // Unfollow button
  const unfollowBtn = document.getElementById('unfollow-btn');
  if(unfollowBtn) {
    unfollowBtn.addEventListener('click', function() {
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
          // Reload page to update UI
          window.location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    });
  }
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>

