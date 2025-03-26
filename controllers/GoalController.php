<?php
class GoalController {
  private $database;
  private $db;
  private $goal;
  
  public function __construct() {
      $this->database = new Database();
      $this->db = $this->database->connect();
      $this->goal = new Goal($this->db);
  }
  
  // Add the missing index method
  public function index() {
      if(!isset($_SESSION['user_id'])) {
          header('Location: /login');
          exit;
      }
      
      // Redirect to dashboard
      include BASE_PATH . '/views/dashboard.php';
  }
  
  // Add the missing showGoalForm method
  public function showGoalForm() {
      if(!isset($_SESSION['user_id'])) {
          header('Location: /login');
          exit;
      }
      
      $this->goal->user_id = $_SESSION['user_id'];
      $this->goal->date = date('Y-m-d');
      $today_goal = $this->goal->getTodayGoal();
      
      include BASE_PATH . '/views/goal.php';
  }
  
  public function setGoal() {
      if(!isset($_SESSION['user_id'])) {
          $response = [
              'success' => false,
              'message' => 'You must be logged in to set a goal'
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
      
      if(!isset($_POST['protein_goal']) || empty($_POST['protein_goal'])) {
          $response = [
              'success' => false,
              'message' => 'Protein goal is required'
          ];
          echo json_encode($response);
          exit;
      }
      
      $this->goal->user_id = $_SESSION['user_id'];
      $this->goal->protein_goal = $_POST['protein_goal'];
      $this->goal->date = date('Y-m-d');
      
      if($this->goal->setGoal()) {
          $response = [
              'success' => true,
              'message' => 'Goal set successfully'
          ];
      } else {
          $response = [
              'success' => false,
              'message' => 'Failed to set goal'
          ];
      }
      
      echo json_encode($response);
  }
}
?>
