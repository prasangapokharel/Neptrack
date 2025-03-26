<?php
class Follow {
    private $conn;
    public $id;
    public $follower_id;
    public $following_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Follow a user
    public function follow() {
        // Check if already following
        $check_query = "SELECT * FROM follows WHERE follower_id = :follower_id AND following_id = :following_id";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':follower_id', $this->follower_id);
        $check_stmt->bindParam(':following_id', $this->following_id);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            return true; // Already following
        }
        
        $query = "INSERT INTO follows (follower_id, following_id) VALUES (:follower_id, :following_id)";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->follower_id = htmlspecialchars(strip_tags($this->follower_id));
        $this->following_id = htmlspecialchars(strip_tags($this->following_id));
        
        // Bind data
        $stmt->bindParam(':follower_id', $this->follower_id);
        $stmt->bindParam(':following_id', $this->following_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Unfollow a user
    public function unfollow() {
        $query = "DELETE FROM follows WHERE follower_id = :follower_id AND following_id = :following_id";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->follower_id = htmlspecialchars(strip_tags($this->follower_id));
        $this->following_id = htmlspecialchars(strip_tags($this->following_id));
        
        // Bind data
        $stmt->bindParam(':follower_id', $this->follower_id);
        $stmt->bindParam(':following_id', $this->following_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
