<?php
// Start session
session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Include database configuration
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/http_client.php';

// Include models
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Goal.php';
require_once BASE_PATH . '/models/Food.php';
require_once BASE_PATH . '/models/Follow.php';
require_once BASE_PATH . '/models/Post.php';
require_once BASE_PATH . '/models/Like.php';
require_once BASE_PATH . '/models/Comment.php';

// Include controllers
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/GoalController.php';
require_once BASE_PATH . '/controllers/FoodController.php';
require_once BASE_PATH . '/controllers/ProfileController.php';
require_once BASE_PATH . '/controllers/FollowController.php';
require_once BASE_PATH . '/controllers/PostController.php';
require_once BASE_PATH . '/controllers/LikeController.php';
require_once BASE_PATH . '/controllers/CommentController.php';

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
  '/edit-profile' => ['controller' => 'ProfileController', 'action' => 'updateProfile'],
  '/users' => ['controller' => 'ProfileController', 'action' => 'showUsers'],
  '/followers' => ['controller' => 'ProfileController', 'action' => 'showFollowers'],
  '/following' => ['controller' => 'ProfileController', 'action' => 'showFollowing'],
  
  // Social features
  '/feed' => ['controller' => 'PostController', 'action' => 'showFeed'],
  
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

