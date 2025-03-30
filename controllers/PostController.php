<?php
class PostController {
    private $database;
    private $db;
    private $post;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->connect();
        $this->post = new Post($this->db);
    }
    
    public function showFeed() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $posts = $this->post->getTimelinePosts($_SESSION['user_id']);
        
        // Check if each post is liked by the current user
        $like = new Like($this->db);
        $like->user_id = $_SESSION['user_id'];
        
        foreach($posts as &$post) {
            $like->post_id = $post['id'];
            $post['is_liked'] = $like->isLiked();
        }
        
        include BASE_PATH . '/views/feed.php';
    }
    
    public function createPost() {
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to create a post'
            ];
            echo json_encode($response);
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response = [
                'success' => false,
                'message' => 'Invalid request method'
            ];
            echo json_encode($response);
            exit;
        }
        
        if(empty($_POST['content'])) {
            $response = [
                'success' => false,
                'message' => 'Post content cannot be empty'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->post->user_id = $_SESSION['user_id'];
        $this->post->content = $_POST['content'];
        $this->post->image = null;
        
        // Handle image upload
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = BASE_PATH . '/uploads/posts/';
            
            // Create directory if it doesn't exist
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . $_FILES['image']['name'];
            $file_path = $upload_dir . $file_name;
            
            // Move uploaded file
            if(move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $this->post->image = '/uploads/posts/' . $file_name;
            }
        }
        
        $post_id = $this->post->create();
        
        if($post_id) {
            // Get the newly created post with user info
            $this->post->id = $post_id;
            $post_data = $this->post->read_single();
            
            $response = [
                'success' => true,
                'message' => 'Post created successfully',
                'post' => $post_data
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to create post'
            ];
        }
        
        echo json_encode($response);
    }
    
    public function deletePost() {
        if(!isset($_SESSION['user_id'])) {
            $response = [
                'success' => false,
                'message' => 'You must be logged in to delete a post'
            ];
            echo json_encode($response);
            exit;
        }
        
        if(!isset($_POST['post_id'])) {
            $response = [
                'success' => false,
                'message' => 'Missing post_id parameter'
            ];
            echo json_encode($response);
            exit;
        }
        
        $this->post->id = $_POST['post_id'];
        $this->post->user_id = $_SESSION['user_id'];
        
        if($this->post->delete()) {
            $response = [
                'success' => true,
                'message' => 'Post deleted successfully'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to delete post'
            ];
        }
        
        echo json_encode($response);
    }
    
    public function showPost($post_id) {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $this->post->id = $post_id;
        $post = $this->post->read_single();
        
        if(!$post) {
            header('Location: /feed');
            exit;
        }
        
        // Get like count
        $like = new Like($this->db);
        $like->post_id = $post_id;
        $post['like_count'] = $like->getLikeCount();

        // Check if post is liked by current user
        $like->user_id = $_SESSION['user_id'];
        $like->post_id = $post_id;
        $post['is_liked'] = $like->isLiked();
        
        // Get comments
        $comment = new Comment($this->db);
        $comment->post_id = $post_id;
        $comments = $comment->getComments();
        
        include BASE_PATH . '/views/post.php';
    }
}
?>

