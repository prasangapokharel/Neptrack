<?php
class LikeController {
    private $database;
    private $db;
    private $like;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->connect();
        $this->like = new Like($this->db);
    }
    
    public function like() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to like posts'
            ];
            echo json_encode($response);
            exit;
        }
        
        // Check if post_id is provided
        if(!isset($_POST['post_id'])) {
            $response = [
                'success' => false,
                'message' => 'Missing post_id parameter'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->like->user_id = $_SESSION['user_id'];
        $this->like->post_id = $_POST['post_id'];
        
        if($this->like->like()) {
            // Get updated like count
            $like_count = $this->like->getLikeCount();
            
            $response = [
                'success' => true,
                'message' => 'Post liked successfully',
                'like_count' => $like_count
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to like post'
            ];
        }
        
        echo json_encode($response);
    }
    
    public function unlike() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to unlike posts'
            ];
            echo json_encode($response);
            exit;
        }
        
        // Check if post_id is provided
        if(!isset($_POST['post_id'])) {
            $response = [
                'success' => false,
                'message' => 'Missing post_id parameter'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->like->user_id = $_SESSION['user_id'];
        $this->like->post_id = $_POST['post_id'];
        
        if($this->like->unlike()) {
            // Get updated like count
            $like_count = $this->like->getLikeCount();
            
            $response = [
                'success' => true,
                'message' => 'Post unliked successfully',
                'like_count' => $like_count
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to unlike post'
            ];
        }
        
        echo json_encode($response);
    }
}
?>
