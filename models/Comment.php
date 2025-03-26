<?php
class Comment {
    private $conn;
    public $id;
    public $user_id;
    public $post_id;
    public $content;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create comment
    public function create() {
        $query = "INSERT INTO comments (user_id, post_id, content) VALUES (:user_id, :post_id, :content)";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->post_id = htmlspecialchars(strip_tags($this->post_id));
        $this->content = htmlspecialchars(strip_tags($this->content));
        
        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->bindParam(':content', $this->content);
        
        // Execute query
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    // Get comments for a post
    public function getComments() {
        $query = "SELECT c.*, u.username, u.profile_image 
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = :post_id
                  ORDER BY c.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete comment
    public function delete() {
        $query = "DELETE FROM comments WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind data
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
