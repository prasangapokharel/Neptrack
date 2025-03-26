<?php
class AuthController {
  private $database;
  private $db;
  private $user;
  
  public function __construct() {
      $this->database = new Database();
      $this->db = $this->database->connect();
      $this->user = new User($this->db);
  }
  
  // Add the missing showLogin method
  public function showLogin() {
      // If user is already logged in, redirect to dashboard
      if(isset($_SESSION['user_id'])) {
          header('Location: /');
          exit;
      }
      
      include BASE_PATH . '/views/auth/login.php';
  }
  
  // Add the missing showRegister method
  public function showRegister() {
      // If user is already logged in, redirect to dashboard
      if(isset($_SESSION['user_id'])) {
          header('Location: /');
          exit;
      }
      
      include BASE_PATH . '/views/auth/register.php';
  }
  
  public function login() {
      if($_SERVER['REQUEST_METHOD'] !== 'POST') {
          $response = [
              'success' => false,
              'message' => 'Invalid request method'
          ];
          echo json_encode($response);
          exit;
      }
      
      if(!isset($_POST['username']) || !isset($_POST['password'])) {
          $response = [
              'success' => false,
              'message' => 'Username and password are required'
          ];
          echo json_encode($response);
          exit;
      }
      
      $username = $_POST['username'];
      $password = $_POST['password'];
      
      // Get user by username
      $query = "SELECT * FROM users WHERE username = :username";
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':username', $username);
      $stmt->execute();
      
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if(!$user) {
          $response = [
              'success' => false,
              'message' => 'Invalid username or password'
          ];
          echo json_encode($response);
          exit;
      }
      
      // Verify password
      if(!password_verify($password, $user['password'])) {
          $response = [
              'success' => false,
              'message' => 'Invalid username or password'
          ];
          echo json_encode($response);
          exit;
      }
      
      // Set session variables
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      
      $response = [
          'success' => true,
          'message' => 'Login successful'
      ];
      echo json_encode($response);
  }
  
  public function register() {
      if($_SERVER['REQUEST_METHOD'] !== 'POST') {
          $response = [
              'success' => false,
              'message' => 'Invalid request method'
          ];
          echo json_encode($response);
          exit;
      }
      
      if(!isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['confirm_password'])) {
          $response = [
              'success' => false,
              'message' => 'All fields are required'
          ];
          echo json_encode($response);
          exit;
      }
      
      if($_POST['password'] !== $_POST['confirm_password']) {
          $response = [
              'success' => false,
              'message' => 'Passwords do not match'
          ];
          echo json_encode($response);
          exit;
      }
      
      // Check if username already exists
      $query = "SELECT * FROM users WHERE username = :username";
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':username', $_POST['username']);
      $stmt->execute();
      
      if($stmt->rowCount() > 0) {
          $response = [
              'success' => false,
              'message' => 'Username already exists'
          ];
          echo json_encode($response);
          exit;
      }
      
      // Check if email already exists
      $query = "SELECT * FROM users WHERE email = :email";
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':email', $_POST['email']);
      $stmt->execute();
      
      if($stmt->rowCount() > 0) {
          $response = [
              'success' => false,
              'message' => 'Email already exists'
          ];
          echo json_encode($response);
          exit;
      }
      
      // Create user
      $this->user->username = $_POST['username'];
      $this->user->email = $_POST['email'];
      $this->user->password = $_POST['password'];
      
      $user_id = $this->user->create();
      
      if($user_id) {
          // Set session variables
          $_SESSION['user_id'] = $user_id;
          $_SESSION['username'] = $_POST['username'];
          
          $response = [
              'success' => true,
              'message' => 'Registration successful'
          ];
      } else {
          $response = [
              'success' => false,
              'message' => 'Failed to register user'
          ];
      }
      
      echo json_encode($response);
  }
  
  public function logout() {
      // Unset all session variables
      $_SESSION = [];
      
      // Destroy the session
      session_destroy();
      
      header('Location: /login');
      exit;
  }
}
?>
