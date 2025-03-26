<?php
class Goal {
  private $conn;
  public $id;
  public $user_id;
  public $protein_goal;
  public $date;

  public function __construct($db) {
      $this->conn = $db;
  }

  // Set goal
  public function setGoal() {
      // Check if goal already exists for this date
      $check_query = "SELECT * FROM goals WHERE user_id = :user_id AND date = :date";
      $check_stmt = $this->conn->prepare($check_query);
      $check_stmt->bindParam(':user_id', $this->user_id);
      $check_stmt->bindParam(':date', $this->date);
      $check_stmt->execute();
      
      if($check_stmt->rowCount() > 0) {
          // Update existing goal
          $query = "UPDATE goals SET protein_goal = :protein_goal WHERE user_id = :user_id AND date = :date";
      } else {
          // Create new goal
          $query = "INSERT INTO goals (user_id, protein_goal, date) VALUES (:user_id, :protein_goal, :date)";
      }
      
      $stmt = $this->conn->prepare($query);
      
      // Clean data
      $this->user_id = htmlspecialchars(strip_tags($this->user_id));
      $this->protein_goal = htmlspecialchars(strip_tags($this->protein_goal));
      $this->date = htmlspecialchars(strip_tags($this->date));
      
      // Bind data
      $stmt->bindParam(':user_id', $this->user_id);
      $stmt->bindParam(':protein_goal', $this->protein_goal);
      $stmt->bindParam(':date', $this->date);
      
      // Execute query
      if($stmt->execute()) {
          return true;
      }
      
      return false;
  }

  // Get today's goal
  public function getTodayGoal() {
      $query = "SELECT * FROM goals WHERE user_id = :user_id AND date = :date";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':user_id', $this->user_id);
      $stmt->bindParam(':date', $this->date);
      $stmt->execute();
      
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row ? $row : [];
  }

  // Get goal by date
  public function getGoalByDate() {
      $query = "SELECT * FROM goals WHERE user_id = :user_id AND date = :date";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':user_id', $this->user_id);
      $stmt->bindParam(':date', $this->date);
      $stmt->execute();
      
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row ? $row : [];
  }

  // Get current streak
  public function getCurrentStreak() {
      $streak = 0;
      $current_date = date('Y-m-d');
      
      while(true) {
          $check_date = date('Y-m-d', strtotime("-$streak days"));
          
          $query = "SELECT * FROM goals WHERE user_id = :user_id AND date = :date";
          $stmt = $this->conn->prepare($query);
          $stmt->bindParam(':user_id', $this->user_id);
          $stmt->bindParam(':date', $check_date);
          $stmt->execute();
          
          if($stmt->rowCount() > 0) {
              $streak++;
          } else {
              break;
          }
      }
      
      return $streak;
  }
}
?>
