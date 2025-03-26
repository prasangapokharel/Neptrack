<?php
class Post {
    private $conn;
    public $id;
    public $user_id;
    public $content;
    public $image;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create post
    public function create() {
        $query = "INSERT INTO posts (user_id, content, image) VALUES (:user_id, :content, :image)";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->content = htmlspecialchars(strip_tags($this->content));
        if($this->image) {
            $this->image = htmlspecialchars(strip_tags($this->image));
        }
        
        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':image', $this->image);
        
        // Execute query
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    // Read single post
    public function read_single() {
        $query = "SELECT p.*, u.username, u.profile_image 
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            return $row;
        }
        return false;
    }

    // Get all posts
    public function read() {
        $query = "SELECT p.*, u.username, u.profile_image,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get posts from followed users and self
    public function getTimelinePosts($user_id) {
        $query = "SELECT p.*, u.username, u.profile_image,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.user_id = :user_id OR p.user_id IN 
                  (SELECT following_id FROM follows WHERE follower_id = :user_id)
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete post
    public function delete() {
        $query = "DELETE FROM posts WHERE id = :id AND user_id = :user_id";
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

    // Get user posts
    public function getUserPosts($user_id) {
        $query = "SELECT p.*, u.username, u.profile_image,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                  (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.user_id = :user_id
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
