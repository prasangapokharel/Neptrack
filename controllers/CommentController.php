<?php
class CommentController {
    private $database;
    private $db;
    private $comment;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->connect();
        $this->comment = new Comment($this->db);
    }
    
    public function createComment() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to comment'
            ];
            echo json_encode($response);
            exit;
        }
        
        // Check if post_id and content are provided
        if(!isset($_POST['post_id']) || !isset($_POST['content']) || empty($_POST['content'])) {
            $response = [
                'success' => false,
                'message' => 'Missing required parameters'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->comment->user_id = $_SESSION['user_id'];
        $this->comment->post_id = $_POST['post_id'];
        $this->comment->content = $_POST['content'];
        
        $comment_id = $this->comment->create();
        
        if($comment_id) {
            // Get user info for the new comment
            $user = new User($this->db);
            $user->id = $_SESSION['user_id'];
            $user->read_single();
            
            $response = [
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => [
                    'id' => $comment_id,
                    'content' => $this->comment->content,
                    'user_id' => $this->comment->user_id,
                    'post_id' => $this->comment->post_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'username' => $user->username,
                    'profile_image' => $user->profile_image
                ]
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to add comment'
            ];
        }
        
        echo json_encode($response);
    }
    
    public function getComments() {
        // Check if post_id is provided
        if(!isset($_GET['post_id'])) {
            $response = [
                'success' => false,
                'message' => 'Missing post_id parameter'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->comment->post_id = $_GET['post_id'];
        $comments = $this->comment->getComments();
        
        $response = [
            'success' => true,
            'comments' => $comments
        ];
        
        echo json_encode($response);
    }
    
    public function deleteComment() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to delete comments'
            ];
            echo json_encode($response);
            exit;
        }
        
        // Check if comment_id is provided
        if(!isset($_POST['comment_id'])) {
            $response = [
                'success' => false,
                'message' => 'Missing comment_id parameter'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->comment->id = $_POST['comment_id'];
        $this->comment->user_id = $_SESSION['user_id'];
        
        if($this->comment->delete()) {
            $response = [
                'success' => true,
                'message' => 'Comment deleted successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to delete comment'
            ];
        }
        
        echo json_encode($response);
    }
}
?>
