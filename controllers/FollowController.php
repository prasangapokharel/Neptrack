<?php
class FollowController {
    private $database;
    private $db;
    private $follow;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->connect();
        $this->follow = new Follow($this->db);
    }
    
    public function follow() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to follow users'
            ];
            echo json_encode($response);
            exit;
        }
        
        // Check if following_id is provided
        if(!isset($_POST['following_id'])) {
            $response = [
                'success' => false,
                'message' => 'Missing following_id parameter'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->follow->follower_id = $_SESSION['user_id'];
        $this->follow->following_id = $_POST['following_id'];
        
        // Cannot follow yourself
        if($this->follow->follower_id == $this->follow->following_id) {
            $response = [
                'success' => false,
                'message' => 'You cannot follow yourself'
            ];
            echo json_encode($response);
            exit;
        }
        
        if($this->follow->follow()) {
            $response = [
                'success' => true,
                'message' => 'User followed successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to follow user'
            ];
        }
        
        echo json_encode($response);
    }
    
    public function unfollow() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to unfollow users'
            ];
            echo json_encode($response);
            exit;
        }
        
        // Check if following_id is provided
        if(!isset($_POST['following_id'])) {
            $response = [
                'success' => false,
                'message' => 'Missing following_id parameter'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->follow->follower_id = $_SESSION['user_id'];
        $this->follow->following_id = $_POST['following_id'];
        
        if($this->follow->unfollow()) {
            $response = [
                'success' => true,
                'message' => 'User unfollowed successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to unfollow user'
            ];
        }
        
        echo json_encode($response);
    }
}
?>

