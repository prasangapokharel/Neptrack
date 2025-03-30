<?php
class Admin {
  private $conn;
  public $id;
  public $username;
  public $email;
  public $password;
  public $created_at;

  public function __construct($db) {
      $this->conn = $db;
  }

  // Authenticate admin
  public function authenticate($username, $password) {
      $query = "SELECT * FROM admins WHERE username = :username";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':username', $username);
      $stmt->execute();
      
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if($row && password_verify($password, $row['password'])) {
          $this->id = $row['id'];
          $this->username = $row['username'];
          $this->email = $row['email'];
          $this->created_at = $row['created_at'];
          return true;
      }
      
      return false;
  }

  // Read single admin
  public function read_single() {
      $query = "SELECT * FROM admins WHERE id = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if($row) {
          $this->id = $row['id'];
          $this->username = $row['username'];
          $this->email = $row['email'];
          $this->created_at = $row['created_at'];
          return true;
      }
      return false;
  }

  // Update admin password
  public function updatePassword($current_password, $new_password) {
      // First verify current password
      $query = "SELECT password FROM admins WHERE id = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();
      
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if(!$row || !password_verify($current_password, $row['password'])) {
          return false;
      }
      
      // Update with new password
      $query = "UPDATE admins SET password = :password WHERE id = :id";
      $stmt = $this->conn->prepare($query);
      
      $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
      
      $stmt->bindParam(':password', $hashed_password);
      $stmt->bindParam(':id', $this->id);
      
      return $stmt->execute();
  }
}
?>

