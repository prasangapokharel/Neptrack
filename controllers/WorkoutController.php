<?php
class WorkoutController {
  private $database;
  private $db;
  private $workout;
  
  public function __construct() {
      $this->database = new Database();
      $this->db = $this->database->connect();
      $this->workout = new Workout($this->db);
  }
  
  public function showWorkouts() {
      if(!isset($_SESSION['user_id'])) {
          header('Location: /login');
          exit;
      }
      
      $category_id = isset($_GET['category']) ? $_GET['category'] : null;
      
      if($category_id) {
          $workouts = $this->workout->read_by_category($category_id);
      } else {
          $workouts = $this->workout->read_all();
      }
      
      $categories = $this->workout->getCategories();
      
      // Check if each workout is liked by the current user
      foreach($workouts as &$workout) {
          $this->workout->id = $workout['id'];
          $workout['is_liked'] = $this->workout->isLikedByUser($_SESSION['user_id']);
          $workout['like_count'] = $this->workout->getLikeCount();
      }
      
      include BASE_PATH . '/views/workouts.php';
  }
  
  public function showWorkout($workout_id) {
      if(!isset($_SESSION['user_id'])) {
          header('Location: /login');
          exit;
      }
      
      $this->workout->id = $workout_id;
      $workout = $this->workout->read_single();
      
      if(!$workout) {
          header('Location: /workouts');
          exit;
      }
      
      // Check if workout is liked by current user
      $workout['is_liked'] = $this->workout->isLikedByUser($_SESSION['user_id']);
      $workout['like_count'] = $this->workout->getLikeCount();
      
      // Get user's workout plans for adding this workout
      $workoutPlan = new WorkoutPlan($this->db);
      $user_plans = $workoutPlan->getUserPlans($_SESSION['user_id']);
      
      include BASE_PATH . '/views/workout.php';
  }
  
  public function likeWorkout() {
      if(!isset($_SESSION['user_id'])) {
          $response = [
              'success' => false,
              'message' => 'You must be logged in to like workouts'
          ];
          echo json_encode($response);
          exit;
      }
      
      if($_SERVER['REQUEST_METHOD'] !== 'POST') {
          $response = [
              'success' => false,
              'message' => 'Invalid request method'
          ];
          echo json_encode($response);
          exit;
      }
      
      if(!isset($_POST['workout_id'])) {
          $response = [
              'success' => false,
              'message' => 'Workout ID is required'
          ];
          echo json_encode($response);
          exit;
      }
      
      $this->workout->id = $_POST['workout_id'];
      
      if($this->workout->like($_SESSION['user_id'])) {
          $like_count = $this->workout->getLikeCount();
          
          $response = [
              'success' => true,
              'message' => 'Workout liked successfully',
              'like_count' => $like_count
          ];
      } else {
          $response = [
              'success' => false,
              'message' => 'Failed to like workout'
          ];
      }
      
      echo json_encode($response);
  }
  
  public function unlikeWorkout() {
      if(!isset($_SESSION['user_id'])) {
          $response = [
              'success' => false,
              'message' => 'You must be logged in to unlike workouts'
          ];
          echo json_encode($response);
          exit;
      }
      
      if($_SERVER['REQUEST_METHOD'] !== 'POST') {
          $response = [
              'success' => false,
              'message' => 'Invalid request method'
          ];
          echo json_encode($response);
          exit;
      }
      
      if(!isset($_POST['workout_id'])) {
          $response = [
              'success' => false,
              'message' => 'Workout ID is required'
          ];
          echo json_encode($response);
          exit;
      }
      
      $this->workout->id = $_POST['workout_id'];
      
      if($this->workout->unlike($_SESSION['user_id'])) {
          $like_count = $this->workout->getLikeCount();
          
          $response = [
              'success' => true,
              'message' => 'Workout unliked successfully',
              'like_count' => $like_count
          ];
      } else {
          $response = [
              'success' => false,
              'message' => 'Failed to unlike workout'
          ];
      }
      
      echo json_encode($response);
  }
  
  public function searchWorkouts() {
      if(!isset($_SESSION['user_id'])) {
          header('Location: /login');
          exit;
      }
      
      $keyword = isset($_GET['q']) ? $_GET['q'] : '';
      
      if(empty($keyword)) {
          header('Location: /workouts');
          exit;
      }
      
      $workouts = $this->workout->search($keyword);
      $categories = $this->workout->getCategories();
      
      // Check if each workout is liked by the current user
      foreach($workouts as &$workout) {
          $this->workout->id = $workout['id'];
          $workout['is_liked'] = $this->workout->isLikedByUser($_SESSION['user_id']);
          $workout['like_count'] = $this->workout->getLikeCount();
      }
      
      include BASE_PATH . '/views/workouts.php';
  }
}
?>

