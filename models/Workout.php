<?php
class Workout {
  private $conn;
  public $id;
  public $category_id;
  public $name;
  public $description;
  public $image;
  public $difficulty_level;
  public $created_by;
  public $created_at;

  public function __construct($db) {
      $this->conn = $db;
  }

  // Create workout
  public function create() {
      $query = "INSERT INTO workouts (category_id, name, description, image, difficulty_level, created_by) 
                VALUES (:category_id, :name, :description, :image, :difficulty_level, :created_by)";
      $stmt = $this->conn->prepare($query);
      
      // Clean data
      $this->category_id = htmlspecialchars(strip_tags($this->category_id));
      $this->name = htmlspecialchars(strip_tags($this->name));
      $this->description = htmlspecialchars(strip_tags($this->description));
      $this->difficulty_level = htmlspecialchars(strip_tags($this->difficulty_level));
      $this->created_by = htmlspecialchars(strip_tags($this->created_by));
      
      // Bind data
      $stmt->bindParam(':category_id', $this->category_id);
      $stmt->bindParam(':name', $this->name);
      $stmt->bindParam(':description', $this->description);
      $stmt->bindParam(':image', $this->image);
      $stmt->bindParam(':difficulty_level', $this->difficulty_level);
      $stmt->bindParam(':created_by', $this->created_by);
      
      // Execute query
      if($stmt->execute()) {
          return $this->conn->lastInsertId();
      }
      
      return false;
  }

  // Read single workout
  public function read_single() {
      $query = "SELECT w.*, c.name as category_name 
                FROM workouts w
                LEFT JOIN workout_categories c ON w.category_id = c.id
                WHERE w.id = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();
      
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if($row) {
          return $row;
      }
      return false;
  }

  // Read all workouts
  public function read_all() {
      $query = "SELECT w.*, c.name as category_name 
                FROM workouts w
                LEFT JOIN workout_categories c ON w.category_id = c.id
                ORDER BY w.created_at DESC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Read workouts by category
  public function read_by_category($category_id) {
      $query = "SELECT w.*, c.name as category_name 
                FROM workouts w
                LEFT JOIN workout_categories c ON w.category_id = c.id
                WHERE w.category_id = :category_id
                ORDER BY w.created_at DESC";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':category_id', $category_id);
      $stmt->execute();
      
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Update workout
  public function update() {
      $query = "UPDATE workouts 
                SET category_id = :category_id, name = :name, description = :description, 
                    difficulty_level = :difficulty_level";
      
      // Add image to update if it's set
      if($this->image) {
          $query .= ", image = :image";
      }
      
      $query .= " WHERE id = :id";
      
      $stmt = $this->conn->prepare($query);
      
      // Clean data
      $this->id = htmlspecialchars(strip_tags($this->id));
      $this->category_id = htmlspecialchars(strip_tags($this->category_id));
      $this->name = htmlspecialchars(strip_tags($this->name));
      $this->description = htmlspecialchars(strip_tags($this->description));
      $this->difficulty_level = htmlspecialchars(strip_tags($this->difficulty_level));
      
      // Bind data
      $stmt->bindParam(':id', $this->id);
      $stmt->bindParam(':category_id', $this->category_id);
      $stmt->bindParam(':name', $this->name);
      $stmt->bindParam(':description', $this->description);
      $stmt->bindParam(':difficulty_level', $this->difficulty_level);
      
      // Bind image if it's set
      if($this->image) {
          $stmt->bindParam(':image', $this->image);
      }
      
      // Execute query
      if($stmt->execute()) {
          return true;
      }
      
      return false;
  }

  // Delete workout
  public function delete() {
      $query = "DELETE FROM workouts WHERE id = :id";
      $stmt = $this->conn->prepare($query);
      
      // Clean data
      $this->id = htmlspecialchars(strip_tags($this->id));
      
      // Bind data
      $stmt->bindParam(':id', $this->id);
      
      // Execute query
      if($stmt->execute()) {
          return true;
      }
      
      return false;
  }

  // Get workout categories
  public function getCategories() {
      $query = "SELECT * FROM workout_categories ORDER BY name";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Get like count for a workout
  public function getLikeCount() {
      $query = "SELECT COUNT(*) as count FROM workout_likes WHERE workout_id = :workout_id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':workout_id', $this->id);
      $stmt->execute();
      
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row['count'];
  }

  // Check if user liked a workout
  public function isLikedByUser($user_id) {
      $query = "SELECT * FROM workout_likes WHERE user_id = :user_id AND workout_id = :workout_id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':user_id', $user_id);
      $stmt->bindParam(':workout_id', $this->id);
      $stmt->execute();
      
      return $stmt->rowCount() > 0;
  }

  // Like a workout
  public function like($user_id) {
      // Check if already liked
      if($this->isLikedByUser($user_id)) {
          return true;
      }
      
      $query = "INSERT INTO workout_likes (user_id, workout_id) VALUES (:user_id, :workout_id)";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':user_id', $user_id);
      $stmt->bindParam(':workout_id', $this->id);
      
      return $stmt->execute();
  }

  // Unlike a workout
  public function unlike($user_id) {
      $query = "DELETE FROM workout_likes WHERE user_id = :user_id AND workout_id = :workout_id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':user_id', $user_id);
      $stmt->bindParam(':workout_id', $this->id);
      
      return $stmt->execute();
  }

  // Search workouts
  public function search($keyword) {
      $query = "SELECT w.*, c.name as category_name 
                FROM workouts w
                LEFT JOIN workout_categories c ON w.category_id = c.id
                WHERE w.name LIKE :keyword OR w.description LIKE :keyword OR c.name LIKE :keyword
                ORDER BY w.created_at DESC";
      $stmt = $this->conn->prepare($query);
      
      $keyword = "%{$keyword}%";
      $stmt->bindParam(':keyword', $keyword);
      $stmt->execute();
      
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
?>

