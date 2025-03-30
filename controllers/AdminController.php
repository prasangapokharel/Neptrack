<?php
class AdminController {
    private $database;
    private $db;
    private $admin;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->connect();
        $this->admin = new Admin($this->db);
    }

    public function showLogin() {
        // If admin is already logged in, redirect to dashboard
        if(isset($_SESSION['admin_id'])) {
            header('Location: /admin/dashboard');
            exit;
        }
        
        include BASE_PATH . '/views/admin/login.php';
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
        
        if($this->admin->authenticate($username, $password)) {
            // Set session variables
            $_SESSION['admin_id'] = $this->admin->id;
            $_SESSION['admin_username'] = $this->admin->username;
            
            $response = [
                'success' => true,
                'message' => 'Login successful'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Invalid username or password'
            ];
        }
        
        echo json_encode($response);
    }

    public function logout() {
        // Unset admin session variables
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        
        header('Location: /admin/login');
        exit;
    }

    public function showDashboard() {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
        
        // Get some stats for the dashboard
        $workout = new Workout($this->db);
        $workouts = $workout->read_all();
        $categories = $workout->getCategories();
        
        // Get user count
        $query = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get workout plan count
        $query = "SELECT COUNT(*) as count FROM user_workout_plans";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $plan_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get workout log count
        $query = "SELECT COUNT(*) as count FROM workout_logs";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $log_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        include BASE_PATH . '/views/admin/dashboard.php';
    }

    public function showWorkouts() {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
        
        $workout = new Workout($this->db);
        $workouts = $workout->read_all();
        $categories = $workout->getCategories();
        
        include BASE_PATH . '/views/admin/workouts.php';
    }

    public function showAddWorkout() {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
        
        $workout = new Workout($this->db);
        $categories = $workout->getCategories();
        
        include BASE_PATH . '/views/admin/add_workout.php';
    }

    public function addWorkout() {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in as admin to add workouts'
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
        
        if(!isset($_POST['name']) || !isset($_POST['category_id']) || !isset($_POST['description'])) {
            $response = [
                'success' => false,
                'message' => 'Name, category, and description are required'
            ];
            echo json_encode($response);
            exit;
        }
        
        $workout = new Workout($this->db);
        $workout->name = $_POST['name'];
        $workout->category_id = $_POST['category_id'];
        $workout->description = $_POST['description'];
        $workout->difficulty_level = $_POST['difficulty_level'] ?? 'intermediate';
        $workout->created_by = $_SESSION['admin_id'];
        $workout->image = null;
        
        // Handle image upload
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = BASE_PATH . '/uploads/workouts/';
            
            // Create directory if it doesn't exist
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . $_FILES['image']['name'];
            $file_path = $upload_dir . $file_name;
            
            // Move uploaded file
            if(move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $workout->image = '/uploads/workouts/' . $file_name;
            }
        }
        
        $workout_id = $workout->create();
        
        if($workout_id) {
            $response = [
                'success' => true,
                'message' => 'Workout added successfully',
                'workout_id' => $workout_id
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to add workout'
            ];
        }
        
        echo json_encode($response);
    }
    public function showEditWorkout($workout_id) {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
        
        $workout = new Workout($this->db);
        $workout->id = $workout_id;
        $workout_data = $workout->read_single();
        
        if(!$workout_data) {
            header('Location: /admin/workouts');
            exit;
        }
        
        $categories = $workout->getCategories();
        
        include BASE_PATH . '/views/admin/edit_workout.php';
    }

    public function updateWorkout() {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in as admin to update workouts'
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
        
        if(!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['category_id']) || !isset($_POST['description'])) {
            $response = [
                'success' => false,
                'message' => 'ID, name, category, and description are required'
            ];
            echo json_encode($response);
            exit;
        }
        
        $workout = new Workout($this->db);
        $workout->id = $_POST['id'];
        $workout->name = $_POST['name'];
        $workout->category_id = $_POST['category_id'];
        $workout->description = $_POST['description'];
        $workout->difficulty_level = $_POST['difficulty_level'] ?? 'intermediate';
        
        // Handle image upload
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = BASE_PATH . '/uploads/workouts/';
            
            // Create directory if it doesn't exist
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . $_FILES['image']['name'];
            $file_path = $upload_dir . $file_name;
            
            // Move uploaded file
            if(move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $workout->image = '/uploads/workouts/' . $file_name;
            }
        }
        
        if($workout->update()) {
            $response = [
                'success' => true,
                'message' => 'Workout updated successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to update workout'
            ];
        }
        
        echo json_encode($response);
    }

    public function deleteWorkout() {
        // Check if admin is logged in
        if(!isset($_SESSION['admin_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in as admin to delete workouts'
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
        
        if(!isset($_POST['id'])) {
            $response = [
                'success' => false,
                'message' => 'Workout ID is required'
            ];
            echo json_encode($response);
            exit;
        }
        
        $workout = new Workout($this->db);
        $workout->id = $_POST['id'];
        
        if($workout->delete()) {
            $response = [
                'success' => true,
                'message' => 'Workout deleted successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to delete workout'
            ];
        }
        
        echo json_encode($response);
    }

    public function showRegister() {
        // If admin is already logged in, redirect to dashboard
        if(isset($_SESSION['admin_id'])) {
            header('Location: /admin/dashboard');
            exit;
        }
        
        include BASE_PATH . '/views/admin/register.php';
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
        $query = "SELECT * FROM admins WHERE username = :username";
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
        $query = "SELECT * FROM admins WHERE email = :email";
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
        
        // Create admin user
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $query = "INSERT INTO admins (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        
        if($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Admin account created successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to create admin account'
            ];
        }
        
        echo json_encode($response);
    }
}
?>