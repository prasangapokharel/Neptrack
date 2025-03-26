<?php
class User {
    private $conn;
    public $id;
    public $username;
    public $email;
    public $password;
    public $profile_image;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create user
    public function create() {
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind data
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        // Execute query
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Read single user
    public function read_single() {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->profile_image = $row['profile_image'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Update user
    public function update() {
        $query = "UPDATE users SET 
                  username = :username, 
                  email = :email";
        
        // Add profile_image to update if it's set
        if($this->profile_image) {
            $query .= ", profile_image = :profile_image";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind data
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);
        
        // Bind profile_image if it's set
        if($this->profile_image) {
            $stmt->bindParam(':profile_image', $this->profile_image);
        }

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get followers count
    public function getFollowersCount() {
        $query = "SELECT COUNT(*) as count FROM follows WHERE following_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Get following count
    public function getFollowingCount() {
        $query = "SELECT COUNT(*) as count FROM follows WHERE follower_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Check if user is following another user
    public function isFollowing($following_id) {
        $query = "SELECT * FROM follows WHERE follower_id = :follower_id AND following_id = :following_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':follower_id', $this->id);
        $stmt->bindParam(':following_id', $following_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Get all users except current user
    public function getAllUsers() {
        $query = "SELECT id, username, profile_image FROM users WHERE id != :id ORDER BY username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get followers
    public function getFollowers() {
        $query = "SELECT u.id, u.username, u.profile_image 
                  FROM follows f 
                  JOIN users u ON f.follower_id = u.id 
                  WHERE f.following_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get following
    public function getFollowing() {
        $query = "SELECT u.id, u.username, u.profile_image 
                  FROM follows f 
                  JOIN users u ON f.following_id = u.id 
                  WHERE f.follower_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
