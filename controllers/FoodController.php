<?php
class FoodController {
  private $database;
  private $db;
  private $food;
  
  public function __construct() {
      $this->database = new Database();
      $this->db = $this->database->connect();
      $this->food = new Food($this->db);
  }
  
  public function showFoodForm() {
      if(!isset($_SESSION['user_id'])) {
          header('Location: /login');
          exit;
      }
      
      $this->food->user_id = $_SESSION['user_id'];
      $this->food->date = date('Y-m-d');
      $food_entries = $this->food->getTodayEntries();
      
      // Get today's goal
      $goal = new Goal($this->db);
      $goal->user_id = $_SESSION['user_id'];
      $goal->date = date('Y-m-d');
      $today_goal = $goal->getTodayGoal();
      
      // Calculate total protein consumed today
      $total_consumed = 0;
      foreach($food_entries as $entry) {
          $total_consumed += $entry['protein_grams'];
      }
      
      // Calculate remaining protein
      $remaining = isset($today_goal['protein_goal']) ? $today_goal['protein_goal'] - $total_consumed : 0;
      
      include BASE_PATH . '/views/food.php';
  }
  
  public function addFood() {
      if(!isset($_SESSION['user_id'])) {
          $response = [
              'success' => false,
              'message' => 'You must be logged in to add food'
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
      
      if(!isset($_POST['food_name']) || empty($_POST['food_name']) || !isset($_POST['protein_grams']) || empty($_POST['protein_grams'])) {
          $response = [
              'success' => false,
              'message' => 'Food name and protein grams are required'
          ];
          echo json_encode($response);
          exit;
      }
      
      $this->food->user_id = $_SESSION['user_id'];
      $this->food->food_name = $_POST['food_name'];
      $this->food->protein_grams = $_POST['protein_grams'];
      $this->food->date = date('Y-m-d');
      
      if($this->food->create()) {
          $response = [
              'success' => true,
              'message' => 'Food added successfully'
          ];
      } else {
          $response = [
              'success' => false,
              'message' => 'Failed to add food'
          ];
      }
      
      echo json_encode($response);
  }
  
  public function deleteFood() {
      if(!isset($_SESSION['user_id'])) {
          header('Location: /login');
          exit;
      }
      
      if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['food_id'])) {
          header('Location: /food');
          exit;
      }
      
      $this->food->id = $_POST['food_id'];
      $this->food->user_id = $_SESSION['user_id'];
      
      if($this->food->delete()) {
          $_SESSION['message'] = 'Food entry deleted successfully';
      } else {
          $_SESSION['error'] = 'Failed to delete food entry';
      }
      
      header('Location: /food');
      exit;
  }
}
?>
