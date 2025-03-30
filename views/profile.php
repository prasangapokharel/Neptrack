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

// Initialize total_completions variable
$total_completions = 0;

// Get protein goal completion data for the last year
$food = new Food($db);
$food->user_id = $user->id;
$goal = new Goal($db);
$goal->user_id = $user->id;

// Get data for the last 365 days
$contribution_data = [];
$max_intensity = 0;

// Get current month and year for pagination
$current_month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('n'));
$current_year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

// Validate month and year
if ($current_month < 1 || $current_month > 12) {
  $current_month = intval(date('n'));
}
if ($current_year < 2020 || $current_year > intval(date('Y'))) {
  $current_year = intval(date('Y'));
}

// Calculate previous and next month/year for pagination
$prev_month = $current_month - 1;
$prev_year = $current_year;
if ($prev_month < 1) {
  $prev_month = 12;
  $prev_year--;
}

$next_month = $current_month + 1;
$next_year = $current_year;
if ($next_month > 12) {
  $next_month = 1;
  $next_year++;
}

// Get first and last day of the current month
$first_day_of_month = new DateTime("$current_year-$current_month-01");
$last_day_of_month = new DateTime($first_day_of_month->format('Y-m-t'));

// Get the first day of the week for the first day of the month
$first_day_of_week = clone $first_day_of_month;
$first_day_of_week->modify('last sunday');
if ($first_day_of_week->format('Y-m-d') != $first_day_of_month->format('Y-m-d')) {
  $first_day_of_week->modify('+1 day');
}

// Get the last day of the week for the last day of the month
$last_day_of_week = clone $last_day_of_month;
$last_day_of_week->modify('next saturday');

// Process all days in the year for the total count
for ($i = 365; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  
  // Get goal for this date
  $goal->date = $date;
  $day_goal = $goal->getGoalByDate();
  
  // Get food entries for this date
  $food->date = $date;
  $day_entries = $food->getEntriesByDate();
  
  // Calculate total protein consumed
  $total_consumed = 0;
  foreach ($day_entries as $entry) {
    $total_consumed += $entry['protein_grams'];
  }
  
  // Check if goal was met
  $intensity = 0;
  if (!empty($day_goal) && isset($day_goal['protein_goal']) && $day_goal['protein_goal'] > 0) {
    $percentage = min(($total_consumed / $day_goal['protein_goal']) * 100, 200);
    
    if ($percentage >= 100) {
      $total_completions++;
      
      // Set intensity based on percentage of goal
      if ($percentage >= 175) {
        $intensity = 4; // Super achievement (175%+)
      } elseif ($percentage >= 150) {
        $intensity = 3; // Great achievement (150-174%)
      } elseif ($percentage >= 125) {
        $intensity = 2; // Good achievement (125-149%)
      } else {
        $intensity = 1; // Goal met (100-124%)
      }
    }
  }
  
  $contribution_data[$date] = $intensity;
  $max_intensity = max($max_intensity, $intensity);
}

// Create calendar data for the current month view
$calendar_data = [];
$current = clone $first_day_of_week;
while ($current <= $last_day_of_week) {
  $date_str = $current->format('Y-m-d');
  $day_num = $current->format('j');
  $is_current_month = $current->format('m') == $current_month;
  
  $intensity = isset($contribution_data[$date_str]) ? $contribution_data[$date_str] : 0;
  
  $calendar_data[] = [
    'date' => $date_str,
    'day' => $day_num,
    'is_current_month' => $is_current_month,
    'intensity' => $intensity
  ];
  
  $current->modify('+1 day');
}

// Group calendar data by weeks
$weeks = array_chunk($calendar_data, 7);

// Month names for display
$month_names = [
  1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
  5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
  9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];
?>

<style>
  :root {
    --primary: #3B82F6;    /* Vibrant blue */
    --dark: #1E293B;       /* Dark slate */
    --light: #F1F5F9;      /* Light gray */
    --font-main: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    
    /* GitHub-style contribution colors */
    --level-0: #ebedf0;
    --level-1: #9be9a8;
    --level-2: #40c463;
    --level-3: #30a14e;
    --level-4: #216e39;
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

  .page-title {
    font-size: 24px;
    font-weight: 600;
  }

  .content-wrapper {
    padding: 0 20px 20px;
  }

  .alert-container {
    margin-bottom: 16px;
    display: none;
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
  }

  .alert-success {
    border-left: 4px solid #10b981;
  }

  .alert-error {
    border-left: 4px solid #ef4444;
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

  .card {
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
    overflow: hidden;
  }

  .card-header {
    padding: 15px 20px;
    border-bottom: 1px solid var(--light);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .card-title {
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
  }

  .card-title-icon {
    margin-right: 8px;
    color: var(--primary);
  }

  .card-body {
    padding: 20px;
  }

  .profile-header {
    position: relative;
    height: 120px;
    background-color: var(--primary);
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
  }

  .profile-avatar {
    position: absolute;
    bottom: -40px;
    left: 20px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 4px solid white;
    background-color: var(--light);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 600;
    color: var(--primary);
    overflow: hidden;
  }

  .profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .profile-action {
    position: absolute;
    bottom: 10px;
    right: 20px;
  }

  .profile-info {
    padding-top: 50px;
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 20px;
  }

  .profile-name {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 4px;
  }

  .profile-email {
    font-size: 14px;
    color: var(--dark);
    opacity: 0.7;
    margin-bottom: 15px;
  }

  .profile-stats {
    display: flex;
    gap: 20px;
    padding-top: 15px;
    border-top: 1px solid var(--light);
  }

  .stat-item {
    text-align: center;
    text-decoration: none;
  }

  .stat-value {
    font-size: 18px;
    font-weight: 600;
    color: var(--primary);
  }

  .stat-label {
    font-size: 12px;
    color: var(--dark);
    opacity: 0.7;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
  }

  .btn-primary {
    background-color: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background-color: #2563eb;
  }

  .btn-outline {
    background-color: transparent;
    border: 1px solid var(--light);
    color: var(--dark);
  }

  .btn-outline:hover {
    background-color: var(--light);
  }

  .btn-icon {
    margin-right: 6px;
  }

  /* New Calendar Styles */
  .goal-calendar {
    width: 100%;
  }

  .calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }

  .calendar-title {
    font-size: 18px;
    font-weight: 500;
  }

  .calendar-nav {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .calendar-nav-btn {
    display: flex;
    align-items: center;
    color: var(--dark);
    opacity: 0.7;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .calendar-nav-btn:hover {
    color: var(--primary);
    opacity: 1;
  }

  .calendar-grid-container {
    display: flex;
    margin-bottom: 10px;
  }

  .calendar-weekdays {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-right: 10px;
  }

  .weekday {
    font-size: 14px;
    color: var(--dark);
    height: 24px;
    display: flex;
    align-items: center;
  }

  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-template-rows: repeat(4, 1fr);
    gap: 4px;
    flex-grow: 1;
  }

  .calendar-day {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    background-color: var(--level-0);
  }

  .calendar-day-other-month {
    opacity: 0.4;
  }

  .intensity-0 {
    background-color: var(--level-0);
  }

  .intensity-1 {
    background-color: var(--level-1);
  }

  .intensity-2 {
    background-color: var(--level-2);
  }

  .intensity-3 {
    background-color: var(--level-3);
  }

  .intensity-4 {
    background-color: var(--level-4);
  }

  .calendar-legend {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 4px;
    margin-top: 10px;
  }

  .legend-label {
    font-size: 12px;
    color: var(--dark);
    opacity: 0.7;
  }

  .legend-item {
    width: 12px;
    height: 12px;
    border-radius: 2px;
  }

  .tooltip {
    position: absolute;
    background-color: #24292e;
    color: white;
    padding: 8px 10px;
    border-radius: 6px;
    font-size: 12px;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s;
    z-index: 10;
    white-space: nowrap;
  }

  .menu-list {
    border-top: 1px solid var(--light);
  }

  .menu-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    border-bottom: 1px solid var(--light);
    text-decoration: none;
    color: var(--dark);
    transition: background-color 0.2s ease;
  }

  .menu-item:hover {
    background-color: var(--light);
  }

  .menu-item-left {
    display: flex;
    align-items: center;
  }

  .menu-item-icon {
    width: 24px;
    height: 24px;
    margin-right: 12px;
    color: var(--primary);
  }

  .menu-item-text {
    font-weight: 500;
  }

  .menu-item-right {
    color: var(--dark);
    opacity: 0.5;
  }

  .danger-text {
    color: #ef4444;
  }

  .danger-icon {
    color: #ef4444;
  }

  .post-list {
    border-top: 1px solid var(--light);
  }

  .post-item {
    padding: 15px 20px;
    border-bottom: 1px solid var(--light);
  }

  .post-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
  }

  .post-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    margin-right: 10px;
    background-color: var(--light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-weight: 600;
    font-size: 14px;
    overflow: hidden;
  }

  .post-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .post-user {
    font-weight: 500;
    margin-bottom: 2px;
  }

  .post-date {
    font-size: 12px;
    color: var(--dark);
    opacity: 0.6;
  }

  .post-content {
    margin-bottom: 12px;
    font-size: 14px;
  }

  .post-image {
    width: 100%;
    border-radius: 8px;
    margin-bottom: 12px;
  }

  .post-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .post-stats {
    display: flex;
    gap: 15px;
  }

  .post-stat {
    display: flex;
    align-items: center;
    font-size: 12px;
    color: var(--dark);
    opacity: 0.7;
  }

  .post-stat-icon {
    margin-right: 4px;
    color: var(--primary);
  }

  .post-view {
    font-size: 12px;
    font-weight: 500;
    color: var(--primary);
    text-decoration: none;
  }

  .post-view:hover {
    text-decoration: underline;
  }

  .empty-state {
    padding: 40px 20px;
    text-align: center;
  }

  .empty-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    color: var(--dark);
    opacity: 0.3;
  }

  .empty-title {
    font-weight: 600;
    margin-bottom: 8px;
  }

  .empty-text {
    font-size: 14px;
    color: var(--dark);
    opacity: 0.6;
    margin-bottom: 15px;
  }

  .action-bar {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 20px;
  }
</style>

<div class="container">
  <header class="header">
    <h1 class="page-title">Profile</h1>
    <a href="/dashboard">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6"></path>
      </svg>
    </a>
  </header>

  <div class="content-wrapper">
    <!-- Alert Container -->
    <div id="alert-container" class="alert-container"></div>
    
    <!-- Profile Card -->
    <div class="card">
      <div class="profile-header">
        <div class="profile-avatar">
          <?php if($user->profile_image): ?>
            <img src="<?php echo $user->profile_image; ?>" alt="<?php echo $user->username; ?>">
          <?php else: ?>
            <?php echo strtoupper(substr($user->username, 0, 1)); ?>
          <?php endif; ?>
        </div>
        
        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user->id): ?>
          <div class="profile-action">
            <?php if($is_following): ?>
              <button id="unfollow-btn" data-user-id="<?php echo $user->id; ?>" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-icon">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <line x1="16" x2="22" y1="11" y2="11"></line>
                </svg>
                Unfollow
              </button>
            <?php else: ?>
              <button id="follow-btn" data-user-id="<?php echo $user->id; ?>" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-icon">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <line x1="19" x2="19" y1="8" y2="14"></line>
                  <line x1="16" x2="22" y1="11" y2="11"></line>
                </svg>
                Follow
              </button>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      
      <div class="profile-info">
        <h2 class="profile-name"><?php echo $user->username; ?></h2>
        <p class="profile-email"><?php echo $user->email; ?></p>
        
        <?php if($_SESSION['user_id'] == $user->id): ?>
          <a href="/edit-profile" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-icon">
              <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3Z"></path>
            </svg>
            Edit Profile
          </a>
        <?php endif; ?>
        
        <div class="profile-stats">
          <a href="/followers/<?php echo $user->id; ?>" class="stat-item">
            <div class="stat-value"><?php echo $followers_count; ?></div>
            <div class="stat-label">Followers</div>
          </a>
          <a href="/following/<?php echo $user->id; ?>" class="stat-item">
            <div class="stat-value"><?php echo $following_count; ?></div>
            <div class="stat-label">Following</div>
          </a>
          <div class="stat-item">
            <div class="stat-value"><?php echo count($posts); ?></div>
            <div class="stat-label">Posts</div>
          </div>
          <div class="stat-item">
            <div class="stat-value"><?php echo $total_completions; ?></div>
            <div class="stat-label">Goals Met</div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Goal Completion Calendar -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="card-title-icon">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
            <line x1="16" x2="16" y1="2" y2="6"></line>
            <line x1="8" x2="8" y1="2" y2="6"></line>
            <line x1="3" x2="21" y1="10" y2="10"></line>
            <path d="m9 16 2 2 4-4"></path>
          </svg>
          Goal Completion
        </h2>
      </div>
      
      <div class="card-body">
        <div class="goal-calendar">
          <div class="calendar-header">
            <div class="calendar-title"><?php echo $month_names[$current_month] . ' ' . $current_year; ?></div>
            <div class="calendar-nav">
              <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="calendar-nav-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                  <path d="m15 18-6-6 6-6"></path>
                </svg>
                Prev
              </a>
              <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="calendar-nav-btn">
                Next
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;">
                  <path d="m9 18 6-6-6-6"></path>
                </svg>
              </a>
            </div>
          </div>
          
          <div class="calendar-grid-container">
            <div class="calendar-weekdays">
              <div class="weekday">Sun</div>
              <div class="weekday">Tue</div>
              <div class="weekday">Thu</div>
              <div class="weekday">Sat</div>
            </div>
            
            <div class="calendar-grid">
              <?php foreach ($weeks as $week): ?>
                <?php foreach ($week as $day): ?>
                  <?php
                  // Determine intensity class
                  $intensity_class = 'intensity-' . $day['intensity'];
                  $other_month_class = $day['is_current_month'] ? '' : 'calendar-day-other-month';
                  
                  // Format date for display
                  $date_obj = new DateTime($day['date']);
                  $formatted_date = $date_obj->format('M j, Y');
                  
                  // Get status text based on intensity
                  $statusText = '';
                  if ($day['intensity'] > 0) {
                    if ($day['intensity'] === 1) $statusText = 'Goal completed';
                    else if ($day['intensity'] === 2) $statusText = 'Goal exceeded (125%+)';
                    else if ($day['intensity'] === 3) $statusText = 'Goal exceeded (150%+)';
                    else if ($day['intensity'] === 4) $statusText = 'Goal exceeded (175%+)';
                  } else {
                    $statusText = 'Goal not met';
                  }
                  ?>
                  <div 
                    class="calendar-day <?php echo $intensity_class; ?> <?php echo $other_month_class; ?>" 
                    data-date="<?php echo $formatted_date; ?>" 
                    data-status="<?php echo $statusText; ?>"
                  ></div>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </div>
          </div>
          
          <div class="calendar-legend">
            <span class="legend-label">Less</span>
            <div class="legend-item intensity-0"></div>
            <div class="legend-item intensity-1"></div>
            <div class="legend-item intensity-2"></div>
            <div class="legend-item intensity-3"></div>
            <div class="legend-item intensity-4"></div>
            <span class="legend-label">More</span>
          </div>
          
          <div style="text-align: center; margin-top: 15px;">
            <a href="/profile/<?php echo $user->id; ?>/yearly-view" style="color: var(--primary); font-size: 14px; font-weight: 500; text-decoration: none;">
              View Full Year
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Account Settings (Only for own profile) -->
    <?php if($_SESSION['user_id'] == $user->id): ?>
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="card-title-icon">
            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
          Account
        </h2>
      </div>
      <div class="menu-list">
        <a href="/users" class="menu-item">
          <div class="menu-item-left">
            <div class="menu-item-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <span class="menu-item-text">Find Users</span>
          </div>
          <div class="menu-item-right">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m9 18 6-6-6-6"></path>
            </svg>
          </div>
        </a>
        
        <a href="/logout" class="menu-item">
          <div class="menu-item-left">
            <div class="menu-item-icon danger-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
            </div>
            <span class="menu-item-text danger-text">Logout</span>
          </div>
          <div class="menu-item-right">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m9 18 6-6-6-6"></path>
            </svg>
          </div>
        </a>
      </div>
    </div>
    <?php endif; ?>
    
    <!-- User Posts -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="card-title-icon">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          </svg>
          Posts
        </h2>
        <?php if($_SESSION['user_id'] == $user->id): ?>
          <a href="/feed" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-icon">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Post
          </a>
        <?php endif; ?>
      </div>
      
      <?php if(empty($posts)): ?>
        <div class="empty-state">
          <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
          </div>
          <h3 class="empty-title">No posts yet</h3>
          <p class="empty-text">Share your fitness journey with the community</p>
          <?php if($_SESSION['user_id'] == $user->id): ?>
            <a href="/feed" class="btn btn-primary">Create your first post</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="post-list">
          <?php foreach($posts as $post): ?>
            <div class="post-item">
              <div class="post-header">
                <div class="post-avatar">
                  <?php if($post['profile_image']): ?>
                    <img src="<?php echo $post['profile_image']; ?>" alt="<?php echo $post['username']; ?>">
                  <?php else: ?>
                    <?php echo strtoupper(substr($post['username'], 0, 1)); ?>
                  <?php endif; ?>
                </div>
                <div>
                  <div class="post-user"><?php echo $post['username']; ?></div>
                  <div class="post-date"><?php echo date('M d, Y \a\t h:i A', strtotime($post['created_at'])); ?></div>
                </div>
              </div>
              
              <div class="post-content">
                <p><?php echo nl2br($post['content']); ?></p>
              </div>
              
              <?php if($post['image']): ?>
                <img src="<?php echo $post['image']; ?>" alt="Post image" class="post-image">
              <?php endif; ?>
              
              <div class="post-actions">
                <div class="post-stats">
                  <div class="post-stat">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="post-stat-icon">
                      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <span><?php echo $post['like_count']; ?></span>
                  </div>
                  <div class="post-stat">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="post-stat-icon">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <span><?php echo $post['comment_count']; ?></span>
                  </div>
                </div>
                
                <a href="/post/<?php echo $post['id']; ?>" class="post-view">View Post</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- Quick Actions -->
    <div class="action-bar">
      <a href="/feed" class="btn btn-outline">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-icon">
          <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3Z"></path>
        </svg>
        Social Feed
      </a>
      
      <a href="/goal" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-icon">
          <path d="M12 2v4"></path>
          <path d="M12 18v4"></path>
          <path d="m4.93 4.93 2.83 2.83"></path>
          <path d="m16.24 16.24 2.83 2.83"></path>
          <path d="M2 12h4"></path>
          <path d="M18 12h4"></path>
          <path d="m4.93 19.07 2.83-2.83"></path>
          <path d="m16.24 7.76 2.83-2.83"></path>
        </svg>
        Set Goal
      </a>
    </div>
  </div>
</div>

<div id="tooltip" class="tooltip"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Alert function
  function showAlert(type, title, message) {
    // Create alert container if it doesn't exist
    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
      alertContainer = document.createElement('div');
      alertContainer.id = 'alert-container';
      alertContainer.className = 'alert-container';
      document.querySelector('.content-wrapper').prepend(alertContainer);
    }
    
    // Create alert element
    const alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type}`;
    
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
    alertContainer.style.display = 'block';
    
    // Auto hide after 5 seconds
    setTimeout(() => {
      alertElement.remove();
      if (alertContainer.children.length === 0) {
        alertContainer.style.display = 'none';
      }
    }, 5000);
    
    // Click to dismiss
    alertElement.addEventListener('click', () => {
      alertElement.remove();
      if (alertContainer.children.length === 0) {
        alertContainer.style.display = 'none';
      }
    });
  }

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
          showAlert('success', 'Success', 'You are now following this user');
          // Reload page after a short delay
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        } else {
          showAlert('error', 'Error', data.message || 'An error occurred');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error', 'An error occurred. Please try again.');
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
          showAlert('success', 'Success', 'You have unfollowed this user');
          // Reload page after a short delay
          setTimeout(() => {
            window.location.reload();
          }, 1500);
        } else {
          showAlert('error', 'Error', data.message || 'An error occurred');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error', 'An error occurred. Please try again.');
      });
    });
  }
  
  // Calendar day tooltips
  const tooltip = document.getElementById('tooltip');
  const days = document.querySelectorAll('.calendar-day');
  
  days.forEach(day => {
    day.addEventListener('mouseenter', function(e) {
      const date = this.getAttribute('data-date');
      const status = this.getAttribute('data-status');
      
      tooltip.textContent = `${date}: ${status}`;
      tooltip.style.opacity = '1';
      
      // Position the tooltip
      const rect = this.getBoundingClientRect();
      const tooltipHeight = tooltip.offsetHeight;
      
      tooltip.style.left = `${rect.left + (rect.width / 2)}px`;
      tooltip.style.top = `${rect.top - tooltipHeight - 5}px`;
      tooltip.style.transform = 'translateX(-50%)';
    });
    
    day.addEventListener('mouseleave', function() {
      tooltip.style.opacity = '0';
    });
  });
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>