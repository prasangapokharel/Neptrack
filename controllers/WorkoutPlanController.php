<?php
class WorkoutPlanController {
    private $database;
    private $db;
    private $workoutPlan;
    private $workout;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->connect();
        $this->workoutPlan = new WorkoutPlan($this->db);
        $this->workout = new Workout($this->db);
    }

    public function showWorkoutPlans() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        
        // Get user's workout plans
        $user_plans = $this->workoutPlan->getUserWorkoutPlans($user_id);
        
        // Get popular workout plans (most liked)
        $popular_plans = $this->workoutPlan->getPopularWorkoutPlans(5);
        
        // Get workout categories for filtering
        $categories = $this->workout->getCategories();
        
        include BASE_PATH . '/views/workout-plans.php';
    }

    public function showCreateWorkoutPlan() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        
        // Get all workouts for selection
        $workouts = $this->workout->read_all();
        
        // Get workout categories for filtering
        $categories = $this->workout->getCategories();
        
        include BASE_PATH . '/views/create-workout-plan.php';
    }

    public function createWorkoutPlan() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to create a workout plan'
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
        
        if(!isset($_POST['name']) || !isset($_POST['description'])) {
            $response = [
                'success' => false,
                'message' => 'Name and description are required'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->workoutPlan->user_id = $_SESSION['user_id'];
        $this->workoutPlan->name = $_POST['name'];
        $this->workoutPlan->description = $_POST['description'];
        $this->workoutPlan->is_public = isset($_POST['is_public']) && $_POST['is_public'] == 1 ? 1 : 0;
        
        $plan_id = $this->workoutPlan->create();
        
        if($plan_id) {
            $response = [
                'success' => true,
                'message' => 'Workout plan created successfully',
                'plan_id' => $plan_id
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to create workout plan'
            ];
        }
        
        echo json_encode($response);
    }

    public function addExerciseToWorkoutPlan() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to add exercises'
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
        
        if(!isset($_POST['plan_id']) || !isset($_POST['workout_id']) || !isset($_POST['sets']) || !isset($_POST['reps'])) {
            $response = [
                'success' => false,
                'message' => 'Plan ID, workout ID, sets, and reps are required'
            ];
            echo json_encode($response);
            exit;
        }
        
        // Verify that the plan belongs to the user
        $this->workoutPlan->id = $_POST['plan_id'];
        $this->workoutPlan->user_id = $_SESSION['user_id'];
        
        if(!$this->workoutPlan->verifyOwnership()) {
            $response = [
                'success' => false,
                'message' => 'You do not have permission to modify this workout plan'
            ];
            echo json_encode($response);
            exit;
        }
        
        $result = $this->workoutPlan->addExercise(
            $_POST['plan_id'],
            $_POST['workout_id'],
            $_POST['sets'],
            $_POST['reps'],
            $_POST['weight'] ?? null,
            $_POST['notes'] ?? null
        );
        
        if($result) {
            $response = [
                'success' => true,
                'message' => 'Exercise added to workout plan successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to add exercise to workout plan'
            ];
        }
        
        echo json_encode($response);
    }

    public function removeExerciseFromWorkoutPlan() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to remove exercises'
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
        
        if(!isset($_POST['plan_id']) || !isset($_POST['exercise_id'])) {
            $response = [
                'success' => false,
                'message' => 'Plan ID and exercise ID are required'
            ];
            echo json_encode($response);
            exit;
        }
        
        // Verify that the plan belongs to the user
        $this->workoutPlan->id = $_POST['plan_id'];
        $this->workoutPlan->user_id = $_SESSION['user_id'];
        
        if(!$this->workoutPlan->verifyOwnership()) {
            $response = [
                'success' => false,
                'message' => 'You do not have permission to modify this workout plan'
            ];
            echo json_encode($response);
            exit;
        }
        
        $result = $this->workoutPlan->removeExercise($_POST['exercise_id']);
        
        if($result) {
            $response = [
                'success' => true,
                'message' => 'Exercise removed from workout plan successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to remove exercise from workout plan'
            ];
        }
        
        echo json_encode($response);
    }

    public function logWorkout() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to log workouts'
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
        
        if(!isset($_POST['plan_id'])) {
            $response = [
                'success' => false,
                'message' => 'Plan ID is required'
            ];
            echo json_encode($response);
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $plan_id = $_POST['plan_id'];
        $notes = $_POST['notes'] ?? null;
        
        $result = $this->workoutPlan->logWorkout($user_id, $plan_id, $notes);
        
        if($result) {
            $response = [
                'success' => true,
                'message' => 'Workout logged successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to log workout'
            ];
        }
        
        echo json_encode($response);
    }

    public function toggleLike() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to like workout plans'
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
        
        if(!isset($_POST['plan_id'])) {
            $response = [
                'success' => false,
                'message' => 'Plan ID is required'
            ];
            echo json_encode($response);
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $plan_id = $_POST['plan_id'];
        
        $result = $this->workoutPlan->toggleLike($user_id, $plan_id);
        
        if($result !== false) {
            $response = [
                'success' => true,
                'liked' => $result === 'liked',
                'message' => $result === 'liked' ? 'Workout plan liked successfully' : 'Workout plan unliked successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to toggle like status'
            ];
        }
        
        echo json_encode($response);
    }
    
    public function getExercises() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to view exercises'
            ];
            echo json_encode($response);
            exit;
        }
        
        if(!isset($_GET['plan_id'])) {
            $response = [
                'success' => false,
                'message' => 'Plan ID is required'
            ];
            echo json_encode($response);
            exit;
        }
        
        $plan_id = $_GET['plan_id'];
        
        // Verify that the plan belongs to the user
        $this->workoutPlan->id = $plan_id;
        $this->workoutPlan->user_id = $_SESSION['user_id'];
        
        if(!$this->workoutPlan->verifyOwnership()) {
            $response = [
                'success' => false,
                'message' => 'You do not have permission to view this workout plan'
            ];
            echo json_encode($response);
            exit;
        }
        
        $exercises = $this->workoutPlan->getExercises($plan_id);
        
        $response = [
            'success' => true,
            'exercises' => $exercises
        ];
        
        echo json_encode($response);
    }
}
?>