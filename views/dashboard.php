<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
  header('Location: /login');
  exit;
}

// Get current user
$database = new Database();
$db = $database->connect();
$user = new User($db);
$user->id = $_SESSION['user_id'];
$user->read_single();

// Get today's goal
$goal = new Goal($db);
$goal->user_id = $_SESSION['user_id'];
$goal->date = date('Y-m-d');
$today_goal = $goal->getTodayGoal();

// Get today's food entries
$food = new Food($db);
$food->user_id = $_SESSION['user_id'];
$food->date = date('Y-m-d');
$food_entries = $food->getTodayEntries();

// Calculate total protein consumed today
$total_consumed = 0;
foreach($food_entries as $entry) {
  $total_consumed += $entry['protein_grams'];
}

// Calculate remaining protein
$remaining = isset($today_goal['protein_goal']) ? $today_goal['protein_goal'] - $total_consumed : 0;
$goal_percentage = isset($today_goal['protein_goal']) && $today_goal['protein_goal'] > 0 ? 
  min(100, round(($total_consumed / $today_goal['protein_goal']) * 100)) : 0;

// Get stats
$stats = [
  'avg_daily' => $food->getAverageDailyProtein(),
  'highest_day' => $food->getHighestProteinDay(),
  'total_entries' => $food->getTotalEntriesCount(),
  'streak' => $goal->getCurrentStreak()
];

// Get latest posts from all users
$post = new Post($db);
$latest_posts = $post->read();

// Check if each post is liked by the current user
$like = new Like($db);
$like->user_id = $_SESSION['user_id'];

foreach($latest_posts as &$post_item) {
  $like->post_id = $post_item['id'];
  $post_item['is_liked'] = $like->isLiked();
}
?>

<div class="max-w-md mx-auto pb-20">
  <!-- Date and Quick Actions -->
  <div class="flex items-center justify-between mb-4 px-2">
    <p class="text-sm font-light text-white/80"><?php echo date('l, F j'); ?></p>
    <div class="flex space-x-2">
      <a href="/goal" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-lg transition duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
      </a>
      <a href="/food" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-lg transition duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
      </a>
    </div>
  </div>

  <!-- Today's Progress Card -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-4">
    <div class="p-4">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center">
          <?php if($user->profile_image): ?>
            <img src="<?php echo $user->profile_image; ?>" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-white/30">
          <?php else: ?>
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white font-medium text-sm">
              <?php echo strtoupper(substr($user->username, 0, 1)); ?>
            </div>
          <?php endif; ?>
          <span class="ml-2 text-white font-medium text-sm"><?php echo $user->username; ?></span>
        </div>
        <span class="text-xs font-light text-white bg-white/20 px-2 py-1 rounded-full">Premium</span>
      </div>
      
      <?php if(isset($today_goal['protein_goal'])): ?>
        <div class="mb-3">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-light text-white/80">
              <span class="font-medium text-white"><?php echo $total_consumed; ?>g</span> of <?php echo $today_goal['protein_goal']; ?>g
            </span>
            <span class="text-xs font-medium <?php echo $remaining >= 0 ? 'text-white' : 'text-red-200'; ?>">
              <?php echo $remaining >= 0 ? $remaining . 'g left' : abs($remaining) . 'g over'; ?>
            </span>
          </div>
          <div class="w-full bg-white/20 rounded-full h-1.5">
            <div class="bg-white h-1.5 rounded-full" style="width: <?php echo $goal_percentage; ?>%"></div>
          </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-4 gap-2 mb-2">
          <div class="glass-dark rounded-lg p-2 text-center">
            <div class="text-lg font-medium text-white"><?php echo $stats['avg_daily']; ?></div>
            <div class="text-xs text-white/70 font-light">Avg/Day</div>
          </div>
          <div class="glass-dark rounded-lg p-2 text-center">
            <div class="text-lg font-medium text-white"><?php echo $stats['highest_day']['protein_grams']; ?></div>
            <div class="text-xs text-white/70 font-light">Best Day</div>
          </div>
          <div class="glass-dark rounded-lg p-2 text-center">
            <div class="text-lg font-medium text-white"><?php echo $stats['total_entries']; ?></div>
            <div class="text-xs text-white/70 font-light">Entries</div>
          </div>
          <div class="glass-dark rounded-lg p-2 text-center">
            <div class="text-lg font-medium text-white"><?php echo $stats['streak']; ?></div>
            <div class="text-xs text-white/70 font-light">Streak</div>
          </div>
        </div>
      <?php else: ?>
        <div class="text-center py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-white/60 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          <p class="text-white/80 text-sm font-light mb-3">Set your protein goal for today</p>
          <a href="/goal" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition duration-200 inline-flex items-center text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Set Goal
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Today's Food Entries -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-4">
    <div class="px-4 py-3 flex justify-between items-center border-b border-white/10">
      <h2 class="text-sm font-medium text-white">Today's Food</h2>
      <a href="/food" class="text-xs text-white/80 hover:text-white transition duration-200">View All</a>
    </div>
    
    <div class="divide-y divide-white/10 max-h-48 overflow-y-auto">
      <?php if(empty($food_entries)): ?>
        <div class="p-4 text-center">
          <p class="text-white/80 text-xs font-light">No food entries today</p>
          <a href="/food" class="text-white text-xs hover:text-white/80 transition duration-200 mt-1 inline-block">+ Add Food</a>
        </div>
      <?php else: ?>
        <?php 
        // Show only the latest 3 entries
        $latest_entries = array_slice($food_entries, 0, 3);
        foreach($latest_entries as $entry): 
        ?>
          <div class="p-3 flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-8 h-8 rounded-lg glass-dark flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                </svg>
              </div>
              <div>
                <div class="font-medium text-white text-sm"><?php echo $entry['food_name']; ?></div>
                <div class="text-xs text-white/70 font-light"><?php echo date('h:i A', strtotime($entry['created_at'])); ?></div>
              </div>
            </div>
            <div class="text-right">
              <div class="font-medium text-white text-sm"><?php echo $entry['protein_grams']; ?>g</div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if(count($food_entries) > 3): ?>
          <div class="p-2 text-center">
            <a href="/food" class="text-white text-xs hover:text-white/80 transition duration-200">View All Entries</a>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Latest Posts -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-4">
    <div class="px-4 py-3 flex justify-between items-center border-b border-white/10">
      <h2 class="text-sm font-medium text-white">Community Posts</h2>
      <a href="/feed" class="text-xs text-white/80 hover:text-white transition duration-200">View All</a>
    </div>
    
    <div class="divide-y divide-white/10">
      <?php if(empty($latest_posts)): ?>
        <div class="p-4 text-center">
          <p class="text-white/80 text-xs font-light">No posts yet</p>
          <a href="/feed" class="text-white text-xs hover:text-white/80 transition duration-200 mt-1 inline-block">Create a Post</a>
        </div>
      <?php else: ?>
        <?php 
        // Show only the latest 3 posts
        $display_posts = array_slice($latest_posts, 0, 3);
        foreach($display_posts as $post_item): 
        ?>
          <div class="p-4">
            <div class="flex items-start space-x-3 mb-2">
              <a href="/profile/<?php echo $post_item['user_id']; ?>">
                <?php if($post_item['profile_image']): ?>
                  <img src="<?php echo $post_item['profile_image']; ?>" alt="<?php echo $post_item['username']; ?>" class="w-8 h-8 rounded-full object-cover">
                <?php else: ?>
                  <div class="w-8 h-8 rounded-full glass-dark flex items-center justify-center text-white font-medium text-sm">
                    <?php echo strtoupper(substr($post_item['username'], 0, 1)); ?>
                  </div>
                <?php endif; ?>
              </a>
              <div>
                <a href="/profile/<?php echo $post_item['user_id']; ?>" class="font-medium text-white text-sm hover:text-white/80 transition duration-200"><?php echo $post_item['username']; ?></a>
                <div class="text-xs text-white/70 font-light"><?php echo date('M d', strtotime($post_item['created_at'])); ?></div>
              </div>
            </div>
            
            <div class="mb-3">
              <p class="text-white text-sm font-light"><?php echo nl2br(substr($post_item['content'], 0, 100)); ?><?php echo strlen($post_item['content']) > 100 ? '...' : ''; ?></p>
            </div>
            
            <?php if($post_item['image']): ?>
              <div class="mb-3">
                <img src="<?php echo $post_item['image']; ?>" alt="Post image" class="rounded-lg w-full h-auto">
              </div>
            <?php endif; ?>
            
            <div class="flex items-center justify-between text-xs">
              <div class="flex items-center space-x-4">
                <button class="like-button flex items-center <?php echo $post_item['is_liked'] ? 'text-white' : 'text-white/70 hover:text-white'; ?> transition duration-200" data-post-id="<?php echo $post_item['id']; ?>" data-liked="<?php echo $post_item['is_liked'] ? 'true' : 'false'; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="<?php echo $post_item['is_liked'] ? 'currentColor' : 'none'; ?>" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                  <span class="like-count"><?php echo $post_item['like_count']; ?></span>
                </button>
                
                <a href="/post/<?php echo $post_item['id']; ?>" class="flex items-center text-white/70 hover:text-white transition duration-200">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <span><?php echo $post_item['comment_count']; ?></span>
                </a>
              </div>
              
              <a href="/post/<?php echo $post_item['id']; ?>" class="text-white hover:text-white/80 transition duration-200">View</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="flex justify-center space-x-4 mb-4">
    <a href="/feed" class="glass-dark hover:bg-white/10 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center text-sm">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
      </svg>
      Social Feed
    </a>
    
    <a href="/profile" class="glass-dark hover:bg-white/10 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center text-sm">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
      </svg>
      Profile
    </a>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Like/unlike post
  const likeButtons = document.querySelectorAll('.like-button');
  likeButtons.forEach(button => {
    button.addEventListener('click', function() {
      const postId = this.getAttribute('data-post-id');
      const isLiked = this.getAttribute('data-liked') === 'true';
      const likeCount = this.querySelector('.like-count');
      const likeIcon = this.querySelector('svg');
      
      const endpoint = isLiked ? '/api/post/unlike' : '/api/post/like';
      
      fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'post_id=' + postId
      })
      .then(response => response.json())
      .then(data => {
        if(data.success) {
          // Update like count
          likeCount.textContent = data.like_count;
          
          // Toggle liked state
          if(isLiked) {
            this.setAttribute('data-liked', 'false');
            this.classList.remove('text-white');
            this.classList.add('text-white/70', 'hover:text-white');
            likeIcon.setAttribute('fill', 'none');
          } else {
            this.setAttribute('data-liked', 'true');
            this.classList.remove('text-white/70', 'hover:text-white');
            this.classList.add('text-white');
            likeIcon.setAttribute('fill', 'currentColor');
          }
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    });
  });
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>
