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
}

// Initialize total_completions variable
$total_completions = 0;

// Get protein goal completion data for the last year
$food = new Food($db);
$food->user_id = $user->id;
$goal = new Goal($db);
$goal->user_id = $user->id;

// Get current year for display
$current_year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

// Validate year
if ($current_year < 2020 || $current_year > intval(date('Y'))) {
  $current_year = intval(date('Y'));
}

// Calculate previous and next year for pagination
$prev_year = $current_year - 1;
$next_year = $current_year + 1;
if ($next_year > intval(date('Y'))) {
  $next_year = intval(date('Y'));
}

// Get data for the last 365 days
$contribution_data = [];
$max_intensity = 0;

// Get first and last day of the year
$first_day = new DateTime("$current_year-01-01");
$last_day = new DateTime("$current_year-12-31");

// Process all days in the year
$current = clone $first_day;
while ($current <= $last_day) {
  $date = $current->format('Y-m-d');
  
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
  
  $current->modify('+1 day');
}

// Group data by months
$months_data = [];
for ($month = 1; $month <= 12; $month++) {
  $month_name = date('F', mktime(0, 0, 0, $month, 1, $current_year));
  $first_day_of_month = new DateTime("$current_year-$month-01");
  $last_day_of_month = new DateTime($first_day_of_month->format('Y-m-t'));
  
  // Get the first day of the week for the first day of the month
  $first_day_of_week = clone $first_day_of_month;
  $day_of_week = intval($first_day_of_week->format('w')); // 0 (Sunday) to 6 (Saturday)
  $first_day_of_week->modify('-' . $day_of_week . ' days');
  
  // Get the last day of the week for the last day of the month
  $last_day_of_week = clone $last_day_of_month;
  $day_of_week = intval($last_day_of_week->format('w')); // 0 (Sunday) to 6 (Saturday)
  $last_day_of_week->modify('+' . (6 - $day_of_week) . ' days');
  
  // Create calendar data for the month
  $calendar_data = [];
  $current = clone $first_day_of_week;
  while ($current <= $last_day_of_week) {
    $date_str = $current->format('Y-m-d');
    $day_num = $current->format('j');
    $is_current_month = $current->format('m') == $month;
    
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
  
  $months_data[$month] = [
    'name' => $month_name,
    'weeks' => $weeks
  ];
}
?>

<div class="max-w-4xl mx-auto pb-20">
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="p-6 border-b border-white/10">
      <div class="flex items-center justify-between mb-6">
        <a href="/profile/<?php echo $user->id; ?>" class="flex items-center text-white/80 hover:text-white transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
          </svg>
          Back to Profile
        </a>
        
        <h3 class="text-xl font-medium text-white flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />
          </svg>
          Protein Goal Completions
        </h3>
        
        <div class="text-sm text-white/80 font-light"><?php echo $total_completions; ?> in <?php echo $current_year; ?></div>
      </div>
      
      <!-- Year Navigation -->
      <div class="flex items-center justify-center mb-6">
        <a href="?year=<?php echo $prev_year; ?>" class="flex items-center text-white/80 hover:text-white transition-colors mr-4">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
          </svg>
          <?php echo $prev_year; ?>
        </a>
        <h4 class="text-white font-medium text-xl"><?php echo $current_year; ?></h4>
        <?php if($next_year <= intval(date('Y'))): ?>
          <a href="?year=<?php echo $next_year; ?>" class="flex items-center text-white/80 hover:text-white transition-colors ml-4">
            <?php echo $next_year; ?>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-1">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </a>
        <?php endif; ?>
      </div>
      
      <!-- Year View -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($months_data as $month_num => $month): ?>
          <div class="glass-dark rounded-xl p-4">
            <h5 class="text-white font-medium mb-2"><?php echo $month['name']; ?></h5>
            
            <!-- Calendar grid -->
            <div class="contribution-calendar w-full">
              <!-- Day headers -->
              <div class="grid grid-cols-7 gap-1 mb-1">
                <?php 
                $days = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                foreach ($days as $day): 
                ?>
                  <div class="text-[10px] text-white/60 text-center"><?php echo $day; ?></div>
                <?php endforeach; ?>
              </div>
              
              <!-- Calendar grid -->
              <div class="grid grid-cols-7 gap-1">
                <?php foreach ($month['weeks'] as $week): ?>
                  <?php foreach ($week as $day): ?>
                    <?php
                    // Determine color class based on intensity
                    switch ($day['intensity']) {
                      case 1:
                        $color_class = 'bg-green-200/80';
                        break;
                      case 2:
                        $color_class = 'bg-green-300/80';
                        break;
                      case 3:
                        $color_class = 'bg-green-400/80';
                        break;
                      case 4:
                        $color_class = 'bg-green-500/80';
                        break;
                      default:
                        $color_class = 'bg-white/10';
                    }
                    
                    // Add opacity for days not in current month
                    if (!$day['is_current_month']) {
                      $color_class .= ' opacity-30';
                    }
                    ?>
                    <div 
                      class="aspect-square rounded-sm <?php echo $color_class; ?> flex items-center justify-center text-[8px] text-white/80 relative" 
                      data-date="<?php echo $day['date']; ?>" 
                      data-intensity="<?php echo $day['intensity']; ?>"
                    >
                      <?php if ($day['is_current_month']): ?>
                        <span><?php echo $day['day']; ?></span>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      
      <!-- Legend -->
      <div class="flex items-center justify-center mt-6 text-xs text-white/60">
        <span class="mr-1">Less</span>
        <div class="h-3 w-3 rounded-sm bg-white/10 mr-0.5"></div>
        <div class="h-3 w-3 rounded-sm bg-green-200/80 mr-0.5"></div>
        <div class="h-3 w-3 rounded-sm bg-green-300/80 mr-0.5"></div>
        <div class="h-3 w-3 rounded-sm bg-green-400/80 mr-0.5"></div>
        <div class="h-3 w-3 rounded-sm bg-green-500/80 mr-1"></div>
        <span>More</span>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Tooltip for contribution cells
  const contributionCells = document.querySelectorAll('[data-date]');
  contributionCells.forEach(cell => {
    cell.addEventListener('mouseenter', function(e) {
      const date = this.getAttribute('data-date');
      const intensity = this.getAttribute('data-intensity');
      
      // Format date
      const formattedDate = new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
      
      // Create tooltip
      const tooltip = document.createElement('div');
      tooltip.className = 'fixed z-50 bg-black/90 text-white text-xs rounded-md px-2 py-1 pointer-events-none';
      tooltip.style.left = (e.pageX + 10) + 'px';
      tooltip.style.top = (e.pageY + 10) + 'px';
      
      // Set tooltip content based on intensity
      let statusText = 'No goal set';
      if (intensity > 0) {
        if (intensity == 1) statusText = 'Goal completed';
        else if (intensity == 2) statusText = 'Goal exceeded (125%+)';
        else if (intensity == 3) statusText = 'Goal exceeded (150%+)';
        else if (intensity == 4) statusText = 'Goal exceeded (175%+)';
      } else {
        statusText = 'Goal not met';
      }
      
      tooltip.textContent = `${formattedDate}: ${statusText}`;
      document.body.appendChild(tooltip);
      
      // Remove tooltip on mouseleave
      this.addEventListener('mouseleave', function() {
        document.body.removeChild(tooltip);
      }, { once: true });
    });
  });
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>

