<?php
class Like {
    private $conn;
    public $id;
    public $user_id;
    public $post_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Like a post
    public function like() {
        // Check if already liked
        $check_query = "SELECT * FROM likes WHERE user_id = :user_id AND post_id = :post_id";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':user_id', $this->user_id);
        $check_stmt->bindParam(':post_id', $this->post_id);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            return true; // Already liked
        }
        
        $query = "INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->post_id = htmlspecialchars(strip_tags($this->post_id));
        
        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':post_id', $this->post_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Unlike a post
    public function unlike() {
        $query = "DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->post_id = htmlspecialchars(strip_tags($this->post_id));
        
        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':post_id', $this->post_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Check if user liked a post
    public function isLiked() {
        $query = "SELECT * FROM likes WHERE user_id = :user_id AND post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        
        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':post_id', $this->post_id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Get like count for a post
    public function getLikeCount() {
        $query = "SELECT COUNT(*) as count FROM likes WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
}
?>
