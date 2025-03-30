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

<style>
  :root {
    --primary: #3B82F6;    /* Vibrant blue */
    --dark: #1E293B;       /* Dark slate */
    --light: #F1F5F9;      /* Light gray */
    --font-main: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  }

  body {
    font-family: var(--font-main);
    background-color: var(--light);
    color: var(--dark);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
  }

  .container {
    max-width: 480px;
    margin: 0 auto;
    padding: 0;
    position: relative;
    min-height: 100vh;
    background-color: white;
  }

  .header {
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    overflow: hidden;
  }

  .avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .greeting {
    font-size: 14px;
    color: var(--dark);
    opacity: 0.8;
  }

  .user-name {
    font-size: 18px;
    font-weight: 600;
  }

  .notification {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--light);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
  }

  .notification-dot {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 8px;
    height: 8px;
    background-color: var(--primary);
    border-radius: 50%;
  }

  .stats-container {
    padding: 20px;
  }

  .stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }

  .stats-title {
    font-size: 20px;
    font-weight: 600;
  }

  .stats-date {
    font-size: 14px;
    color: var(--dark);
    opacity: 0.7;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
  }

  .stat-card {
    background-color: var(--light);
    border-radius: 16px;
    padding: 15px;
    text-align: center;
  }

  .stat-value {
    font-size: 24px;
    font-weight: 700;
    margin: 5px 0;
  }

  .stat-label {
    font-size: 12px;
    color: var(--dark);
    opacity: 0.7;
  }

  .stat-icon {
    color: var(--primary);
    font-size: 16px;
    margin-bottom: 5px;
  }

  .stat-icon svg {
    width: 20px;
    height: 20px;
    stroke: var(--primary);
  }

  .community-container {
    padding: 20px;
  }

  .community-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }

  .community-title {
    font-size: 20px;
    font-weight: 600;
  }

  .view-all {
    color: var(--primary);
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
  }

  .post-card {
    background-color: white;
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }

  .post-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
  }

  .post-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
    background-color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    overflow: hidden;
  }

  .post-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .post-user {
    font-weight: 600;
  }

  .post-date {
    font-size: 12px;
    color: var(--dark);
    opacity: 0.7;
  }

  .post-content {
    margin-bottom: 10px;
  }

  .post-image {
    width: 100%;
    border-radius: 8px;
    margin-bottom: 10px;
    object-fit: cover;
  }

  .post-actions {
    display: flex;
    justify-content: space-between;
  }

  .action-button {
    display: flex;
    align-items: center;
    gap: 5px;
    background: none;
    border: none;
    color: var(--dark);
    opacity: 0.7;
    cursor: pointer;
    padding: 5px;
    border-radius: 5px;
  }

  .action-button:hover {
    opacity: 1;
    background-color: var(--light);
  }

  .action-button.active {
    color: #ef4444;
    opacity: 1;
  }

  .action-button.active svg {
    fill: #ef4444;
    stroke: #ef4444;
  }

  .action-button svg {
    width: 20px;
    height: 20px;
  }

  .content-wrapper {
    padding-bottom: 20px;
  }

  .goal-card {
    background-color: var(--primary);
    border-radius: 16px;
    padding: 20px;
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 20px;
  }

  .goal-card::before {
    content: '';
    position: absolute;
    top: -20px;
    right: -20px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
  }

  .goal-card::after {
    content: '';
    position: absolute;
    bottom: -30px;
    left: -30px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
  }

  .goal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    position: relative;
    z-index: 1;
  }

  .goal-title {
    font-size: 18px;
    font-weight: 600;
  }

  .goal-date {
    font-size: 14px;
    opacity: 0.9;
  }

  .goal-progress {
    position: relative;
    z-index: 1;
  }

  .goal-stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .goal-value {
    font-size: 24px;
    font-weight: 700;
  }

  .goal-label {
    font-size: 14px;
    opacity: 0.9;
  }

  .goal-bar {
    height: 8px;
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    overflow: hidden;
  }

  .goal-bar-fill {
    height: 100%;
    background-color: white;
    border-radius: 4px;
  }

  .quick-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    position: relative;
    z-index: 1;
  }

  .action-btn {
    background-color: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    color: white;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .action-btn:hover {
    background-color: rgba(255, 255, 255, 0.3);
  }

  .alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    max-width: 300px;
  }

  .alert {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease;
  }

  .alert.show {
    transform: translateX(0);
    opacity: 1;
  }

  .alert-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .alert-success .alert-icon {
    background-color: #dcfce7;
    color: #16a34a;
  }

  .alert-error .alert-icon {
    background-color: #fee2e2;
    color: #dc2626;
  }

  .alert-content {
    flex: 1;
  }

  .alert-title {
    font-weight: 600;
    margin-bottom: 5px;
  }

  .alert-message {
    font-size: 14px;
    color: var(--dark);
    opacity: 0.8;
  }
</style>

<div class="container">
  <div class="content-wrapper">
    <!-- Header -->
    <header class="header">
      <div class="user-info">
        <div class="avatar">
          <?php if($user->profile_image): ?>
            <img src="<?php echo $user->profile_image; ?>" alt="Profile">
          <?php else: ?>
            <?php echo strtoupper(substr($user->username, 0, 1)); ?>
          <?php endif; ?>
        </div>
        <div>
          <div class="greeting">Good <?php echo date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening'); ?></div>
          <div class="user-name"><?php echo $user->username; ?></div>
        </div>
      </div>
      <div class="notification">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
          <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
        </svg>
        <div class="notification-dot"></div>
      </div>
    </header>

    <!-- Today's Goal Card -->
    <section class="stats-container">
      <div class="goal-card">
        <div class="goal-header">
          <div class="goal-title">Today's Protein Goal</div>
          <div class="goal-date"><?php echo date('F j, Y'); ?></div>
        </div>
        
        <?php if(isset($today_goal['protein_goal'])): ?>
          <div class="goal-progress">
            <div class="goal-stats">
              <div>
                <div class="goal-value"><?php echo $total_consumed; ?>g</div>
                <div class="goal-label">Consumed</div>
              </div>
              <div>
                <div class="goal-value"><?php echo $today_goal['protein_goal']; ?>g</div>
                <div class="goal-label">Daily Goal</div>
              </div>
              <div>
                <div class="goal-value"><?php echo $remaining >= 0 ? $remaining . 'g' : '0g'; ?></div>
                <div class="goal-label"><?php echo $remaining >= 0 ? 'Remaining' : 'Exceeded'; ?></div>
              </div>
            </div>
            <div class="goal-bar">
              <div class="goal-bar-fill" style="width: <?php echo $goal_percentage; ?>%"></div>
            </div>
            <div class="quick-actions">
              <a href="/goal" class="action-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit Goal
              </a>
              <a href="/food" class="action-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Food
              </a>
            </div>
          </div>
        <?php else: ?>
          <div class="goal-progress" style="text-align: center; padding: 20px 0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 10px;">
              <circle cx="12" cy="12" r="10"></circle>
              <circle cx="12" cy="12" r="6"></circle>
              <circle cx="12" cy="12" r="2"></circle>
            </svg>
            <p style="margin-bottom: 15px;">Set your protein goal for today</p>
            <a href="/goal" class="action-btn" style="display: inline-flex;">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Set Goal
            </a>
          </div>
        <?php endif; ?>
      </div>

      <div class="stats-header">
        <div class="stats-title">Your Stats</div>
        <div class="stats-date"><?php echo date('F Y'); ?></div>
      </div>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
              <path d="m9 16 2 2 4-4"></path>
            </svg>
          </div>
          <div class="stat-value"><?php echo $stats['avg_daily']; ?></div>
          <div class="stat-label">Avg/Day</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
              <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
              <path d="M4 22h16"></path>
              <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
              <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
              <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
            </svg>
          </div>
          <div class="stat-value"><?php echo $stats['highest_day']['protein_grams']; ?></div>
          <div class="stat-label">Best Day</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="8" y1="6" x2="21" y2="6"></line>
              <line x1="8" y1="12" x2="21" y2="12"></line>
              <line x1="8" y1="18" x2="21" y2="18"></line>
              <line x1="3" y1="6" x2="3.01" y2="6"></line>
              <line x1="3" y1="12" x2="3.01" y2="12"></line>
              <line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
          </div>
          <div class="stat-value"><?php echo $stats['total_entries']; ?></div>
          <div class="stat-label">Entries</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2c.8 0 1.7.157 2.5.465a6 6 0 0 1 4.221 5.516c.013.096.02.192.02.288A5.6 5.6 0 0 1 16.5 14L12 22l-4.5-8a5.6 5.6 0 0 1-2.241-5.731c0-.096.007-.192.02-.288a6 6 0 0 1 4.221-5.516A7.5 7.5 0 0 1 12 2z"></path>
            </svg>
          </div>
          <div class="stat-value"><?php echo $stats['streak']; ?></div>
          <div class="stat-label">Streak</div>
        </div>
      </div>
    </section>

    <!-- Community Posts -->
    <section class="community-container">
      <div class="community-header">
        <div class="community-title">Community Posts</div>
        <a href="/feed" class="view-all">View All</a>
      </div>
      
      <?php if(empty($latest_posts)): ?>
        <div class="post-card" style="text-align: center; padding: 30px 15px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 15px;">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          <p style="margin-bottom: 15px;">No posts yet. Be the first to share!</p>
          <a href="/feed" style="color: var(--primary); font-weight: 500;">Create a Post</a>
        </div>
      <?php else: ?>
        <?php 
        // Show only the latest 3 posts
        $display_posts = array_slice($latest_posts, 0, 3);
        foreach($display_posts as $post_item): 
        ?>
          <div class="post-card">
            <div class="post-header">
              <div class="post-avatar">
                <?php if($post_item['profile_image']): ?>
                  <img src="<?php echo $post_item['profile_image']; ?>" alt="<?php echo $post_item['username']; ?>">
                <?php else: ?>
                  <?php echo strtoupper(substr($post_item['username'], 0, 1)); ?>
                <?php endif; ?>
              </div>
              <div>
                <div class="post-user"><?php echo $post_item['username']; ?></div>
                <div class="post-date"><?php echo date('M d', strtotime($post_item['created_at'])); ?></div>
              </div>
            </div>
            
            <div class="post-content">
              <p><?php echo nl2br(substr($post_item['content'], 0, 100)); ?><?php echo strlen($post_item['content']) > 100 ? '...' : ''; ?></p>
            </div>
            
            <?php if($post_item['image']): ?>
              <img src="<?php echo $post_item['image']; ?>" alt="Post image" class="post-image">
            <?php endif; ?>
            
            <div class="post-actions">
              <div style="display: flex; gap: 15px;">
                <button class="action-button like-button <?php echo $post_item['is_liked'] ? 'active' : ''; ?>" data-post-id="<?php echo $post_item['id']; ?>" data-liked="<?php echo $post_item['is_liked'] ? 'true' : 'false'; ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="<?php echo $post_item['is_liked'] ? '#ef4444' : 'none'; ?>" stroke="<?php echo $post_item['is_liked'] ? '#ef4444' : 'currentColor'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"></path>
                  </svg>
                  <span class="like-count"><?php echo $post_item['like_count']; ?></span>
                </button>
                
                <a href="/post/<?php echo $post_item['id']; ?>" class="action-button">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z"></path>
                  </svg>
                  <span><?php echo $post_item['comment_count']; ?></span>
                </a>
              </div>
              
              <a href="/post/<?php echo $post_item['id']; ?>" class="action-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178a1.015 1.015 0 0 1 0-.639Z"></path>
                  <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                </svg>
                <span>View</span>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>
</div>

<div id="alert-container" class="alert-container"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Like/unlike post
  const likeButtons = document.querySelectorAll('.like-button');
  likeButtons.forEach(button => {
    button.addEventListener('click', function() {
      const postId = this.getAttribute('data-post-id');
      const isLiked = this.getAttribute('data-liked') === 'true';
      const likeCount = this.querySelector('.like-count');
      const heartIcon = this.querySelector('svg');
      
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
            this.classList.remove('active');
            heartIcon.setAttribute('fill', 'none');
            heartIcon.setAttribute('stroke', 'currentColor');
          } else {
            this.setAttribute('data-liked', 'true');
            this.classList.add('active');
            heartIcon.setAttribute('fill', '#ef4444');
            heartIcon.setAttribute('stroke', '#ef4444');
          }
        } else {
          showAlert('error', 'Error', data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error', 'An error occurred. Please try again.');
      });
    });
  });
  
  // Alert function
  function showAlert(type, title, message) {
    // Create alert container if it doesn't exist
    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
      alertContainer = document.createElement('div');
      alertContainer.id = 'alert-container';
      alertContainer.className = 'alert-container';
      document.body.appendChild(alertContainer);
    }
    
    // Create alert element
    const alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type}`;
    
    // Alert content based on type
    if (type === 'success') {
      alertElement.innerHTML = `
        <div class="alert-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="alert-content">
          <div class="alert-title">${title}</div>
          <div class="alert-message">${message}</div>
        </div>
      `;
    } else {
      alertElement.innerHTML = `
        <div class="alert-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
        </div>
        <div class="alert-content">
          <div class="alert-title">${title}</div>
          <div class="alert-message">${message}</div>
        </div>
      `;
    }
    
    // Add to container
    alertContainer.appendChild(alertElement);
    
    // Animate in
    setTimeout(() => {
      alertElement.classList.add('show');
    }, 10);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      alertElement.classList.remove('show');
      setTimeout(() => {
        alertElement.remove();
      }, 300);
    }, 5000);
    
    // Click to dismiss
    alertElement.addEventListener('click', () => {
      alertElement.classList.remove('show');
      setTimeout(() => {
        alertElement.remove();
      }, 300);
    });
  }
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>