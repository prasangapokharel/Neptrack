<?php
class WorkoutPlan {
    private $conn;
    public $id;
    public $user_id;
    public $name;
    public $description;
    public $is_public;

    public function __construct($db = null) {
        if ($db) {
            $this->conn = $db;
        } else {
            $database = new Database();
            $this->conn = $database->connect();
        }
    }

    public function create() {
        $query = "INSERT INTO user_workout_plans (user_id, name, description, is_public, created_at) 
                  VALUES (:user_id, :name, :description, :is_public, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->is_public = htmlspecialchars(strip_tags($this->is_public));
        
        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':is_public', $this->is_public);
        
        // Execute query
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    public function getUserWorkoutPlans($userId) {
        $query = "SELECT wp.*, 
                 (SELECT COUNT(*) FROM user_workout_exercises WHERE plan_id = wp.id) as exercise_count
                 FROM user_workout_plans wp
                 WHERE wp.user_id = :user_id
                 ORDER BY wp.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get exercises for each plan
        foreach ($plans as &$plan) {
            $plan['exercises'] = $this->getPlanExercises($plan['id']);
        }
        
        return $plans;
    }

    public function getPopularWorkoutPlans($limit = 6) {
        // For now, let's just get all public plans
        $query = "SELECT wp.*, u.username, u.profile_image as user_avatar,
                 (SELECT COUNT(*) FROM user_workout_exercises WHERE plan_id = wp.id) as exercise_count,
                 (SELECT COUNT(*) FROM workout_likes WHERE workout_id = wp.id) as like_count,
                 (SELECT COUNT(*) > 0 FROM workout_likes WHERE workout_id = wp.id AND user_id = :user_id) as user_liked
                 FROM user_workout_plans wp
                 JOIN users u ON wp.user_id = u.id
                 WHERE wp.is_public = 1 AND wp.user_id != :user_id
                 ORDER BY wp.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get exercises for each plan
        foreach ($plans as &$plan) {
            $plan['exercises'] = $this->getPlanExercises($plan['id']);
        }
        
        return $plans;
    }

    public function getPlanExercises($planId) {
        $query = "SELECT wpe.*, w.name as workout_name, w.description as workout_description,
                 w.category_id, w.difficulty_level
                 FROM user_workout_exercises wpe
                 JOIN workouts w ON wpe.workout_id = w.id
                 WHERE wpe.plan_id = :plan_id
                 ORDER BY wpe.id ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':plan_id', $planId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verifyOwnership() {
        $query = "SELECT id FROM user_workout_plans WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function addExercise($planId, $workoutId, $sets, $reps, $weight = null, $notes = null) {
        $query = "INSERT INTO user_workout_exercises (plan_id, workout_id, sets, reps, weight, notes) 
                  VALUES (:plan_id, :workout_id, :sets, :reps, :weight, :notes)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':plan_id', $planId);
        $stmt->bindParam(':workout_id', $workoutId);
        $stmt->bindParam(':sets', $sets);
        $stmt->bindParam(':reps', $reps);
        $stmt->bindParam(':weight', $weight);
        $stmt->bindParam(':notes', $notes);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    public function removeExercise($exerciseId) {
        $query = "DELETE FROM user_workout_exercises 
                  WHERE id = :exercise_id AND plan_id = :plan_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':exercise_id', $exerciseId);
        $stmt->bindParam(':plan_id', $this->id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function getExercises($planId) {
        return $this->getPlanExercises($planId);
    }

    public function logWorkout($userId, $planId, $notes = null) {
        $query = "INSERT INTO workout_logs (user_id, workout_id, notes, completed_at) 
                  VALUES (:user_id, :workout_id, :notes, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':workout_id', $planId);
        $stmt->bindParam(':notes', $notes);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    public function toggleLike($userId, $planId) {
        // Check if user already liked this plan
        $query = "SELECT id FROM workout_likes 
                  WHERE user_id = :user_id AND workout_id = :workout_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':workout_id', $planId);
        $stmt->execute();
        $existingLike = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingLike) {
            // Unlike
            $query = "DELETE FROM workout_likes 
                      WHERE user_id = :user_id AND workout_id = :workout_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':workout_id', $planId);
            $stmt->execute();
            
            return 'unliked';
        } else {
            // Like
            $query = "INSERT INTO workout_likes (user_id, workout_id) 
                      VALUES (:user_id, :workout_id)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':workout_id', $planId);
            $stmt->execute();
            
            return 'liked';
        }
    }
}
?>