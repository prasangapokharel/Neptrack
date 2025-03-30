<?php
// Start session
session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Include database configuration
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/http_client.php';

// Include models - using require_once to prevent duplicate class declarations
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Goal.php';
require_once BASE_PATH . '/models/Food.php';
require_once BASE_PATH . '/models/Follow.php';
require_once BASE_PATH . '/models/Post.php';
require_once BASE_PATH . '/models/Like.php';
require_once BASE_PATH . '/models/Comment.php';
// Add new models for workout functionality
require_once BASE_PATH . '/models/Admin.php';
require_once BASE_PATH . '/models/Workout.php';
require_once BASE_PATH . '/models/WorkoutPlan.php';

// Include controllers - using require_once to prevent duplicate class declarations
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/GoalController.php';
require_once BASE_PATH . '/controllers/FoodController.php';
require_once BASE_PATH . '/controllers/ProfileController.php';
require_once BASE_PATH . '/controllers/FollowController.php';
require_once BASE_PATH . '/controllers/PostController.php';
require_once BASE_PATH . '/controllers/LikeController.php';
require_once BASE_PATH . '/controllers/CommentController.php';
// Add new controllers for workout functionality
require_once BASE_PATH . '/controllers/AdminController.php';
require_once BASE_PATH . '/controllers/WorkoutController.php';

require_once 'controllers/WorkoutPlanController.php';


// Get the request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Remove query string
$request_uri = strtok($request_uri, '?');

// Remove trailing slash if it exists
$request_uri = rtrim($request_uri, '/');

// If empty, set to home
if (empty($request_uri)) {
  $request_uri = '/';
}

// Define routes
$routes = [
  // Main pages
  '/' => ['controller' => 'GoalController', 'action' => 'index'],
  '/login' => ['controller' => 'AuthController', 'action' => 'showLogin'],
  '/register' => ['controller' => 'AuthController', 'action' => 'showRegister'],
  '/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
  '/goal' => ['controller' => 'GoalController', 'action' => 'showGoalForm'],
  '/food' => ['controller' => 'FoodController', 'action' => 'showFoodForm'],
  
  // Profile routes
  '/profile' => ['controller' => 'ProfileController', 'action' => 'showProfile'],
  '/profile/monthly' => ['controller' => 'ProfileController', 'action' => 'showMonthlyView'],
  '/profile/yearly' => ['controller' => 'ProfileController', 'action' => 'showYearlyView'],
  '/edit-profile' => ['controller' => 'ProfileController', 'action' => 'updateProfile'],
  '/users' => ['controller' => 'ProfileController', 'action' => 'showUsers'],
  '/followers' => ['controller' => 'ProfileController', 'action' => 'showFollowers'],
  '/following' => ['controller' => 'ProfileController', 'action' => 'showFollowing'],
  
  // Social features
  '/feed' => ['controller' => 'PostController', 'action' => 'showFeed'],
  
  // Admin routes
  '/admin' => ['controller' => 'AdminController', 'action' => 'showLogin'],
  '/admin/login' => ['controller' => 'AdminController', 'action' => 'showLogin'],
  '/admin/register' => ['controller' => 'AdminController', 'action' => 'showRegister'],
  '/admin/dashboard' => ['controller' => 'AdminController', 'action' => 'showDashboard'],
  '/admin/add-workout' => ['controller' => 'AdminController', 'action' => 'showAddWorkout'],
  
  // Workout routes
  '/workouts' => ['controller' => 'WorkoutController', 'action' => 'showWorkouts'],
  '/workout-plans' => ['controller' => 'WorkoutPlanController', 'action' => 'showWorkoutPlans'],
  '/workout-plans/create' => ['controller' => 'WorkoutPlanController', 'action' => 'showCreateWorkoutPlan'],
  
  // API endpoints
  '/api/auth/login' => ['controller' => 'AuthController', 'action' => 'login'],
  '/api/auth/register' => ['controller' => 'AuthController', 'action' => 'register'],
  '/api/goal/set' => ['controller' => 'GoalController', 'action' => 'setGoal'],
  '/api/food/add' => ['controller' => 'FoodController', 'action' => 'addFood'],
  '/api/food/delete' => ['controller' => 'FoodController', 'action' => 'deleteFood'],
  '/api/follow' => ['controller' => 'FollowController', 'action' => 'follow'],
  '/api/unfollow' => ['controller' => 'FollowController', 'action' => 'unfollow'],
  '/api/post/create' => ['controller' => 'PostController', 'action' => 'createPost'],
  '/api/post/delete' => ['controller' => 'PostController', 'action' => 'deletePost'],
  '/api/post/like' => ['controller' => 'LikeController', 'action' => 'like'],
  '/api/post/unlike' => ['controller' => 'LikeController', 'action' => 'unlike'],
  '/api/comment/create' => ['controller' => 'CommentController', 'action' => 'createComment'],
  '/api/comment/delete' => ['controller' => 'CommentController', 'action' => 'deleteComment'],
  '/api/comment/get' => ['controller' => 'CommentController', 'action' => 'getComments'],
  
  // Admin API endpoints
  '/api/admin/login' => ['controller' => 'AdminController', 'action' => 'login'],
  '/api/admin/register' => ['controller' => 'AdminController', 'action' => 'register'],
  '/api/admin/add-workout' => ['controller' => 'AdminController', 'action' => 'addWorkout'],
  
  // Workout API endpoints
  '/api/workout-plan/create' => ['controller' => 'WorkoutPlanController', 'action' => 'createWorkoutPlan'],
  '/api/workout-plan/add-exercise' => ['controller' => 'WorkoutPlanController', 'action' => 'addExerciseToWorkoutPlan'],
  '/api/workout-plan/remove-exercise' => ['controller' => 'WorkoutPlanController', 'action' => 'removeExerciseFromWorkoutPlan'],
  '/api/workout-plan/log' => ['controller' => 'WorkoutPlanController', 'action' => 'logWorkout'],
  '/api/workout-plan/like' => ['controller' => 'WorkoutPlanController', 'action' => 'toggleLike'],
  '/api/workout-plan/exercises' => ['controller' => 'WorkoutPlanController', 'action' => 'getExercises'],
  '/api/workout-plan/clone' => ['controller' => 'WorkoutPlanController', 'action' => 'cloneWorkoutPlan'],
];

// Check for dynamic routes
if (preg_match('#^/profile/(\d+)$#', $request_uri, $matches)) {
  $user_id = $matches[1];
  $controller = new ProfileController();
  $controller->showProfile($user_id);
  exit;
}

if (preg_match('#^/followers/(\d+)$#', $request_uri, $matches)) {
  $user_id = $matches[1];
  $controller = new ProfileController();
  $controller->showFollowers($user_id);
  exit;
}

if (preg_match('#^/following/(\d+)$#', $request_uri, $matches)) {
  $user_id = $matches[1];
  $controller = new ProfileController();
  $controller->showFollowing($user_id);
  exit;
}

if (preg_match('#^/post/(\d+)$#', $request_uri, $matches)) {
  $post_id = $matches[1];
  $controller = new PostController();
  $controller->showPost($post_id);
  exit;
}

// Add dynamic route for workout plan editing
if (preg_match('#^/workout-plans/edit/(\d+)$#', $request_uri, $matches)) {
  $plan_id = $matches[1];
  $controller = new WorkoutPlanController();
  $controller->showEditWorkoutPlan($plan_id);
  exit;
}

// Check if route exists
if (isset($routes[$request_uri])) {
  $route = $routes[$request_uri];
  $controller_name = $route['controller'];
  $action = $route['action'];
  
  // Create controller instance
  $controller = new $controller_name();
  
  // Call action
  $controller->$action();
} else {
  // Route not found
  header('HTTP/1.0 404 Not Found');
  echo '404 Not Found';
}
?>