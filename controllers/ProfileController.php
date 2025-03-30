<?php
class ProfileController {
private $database;
private $db;
private $user;

public function __construct() {
    $this->database = new Database();
    $this->db = $this->database->connect();
    $this->user = new User($this->db);
}

public function showProfile($user_id = null) {
    // If no user_id is provided, show current user's profile
    if(!$user_id) {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $user_id = $_SESSION['user_id'];
    }
    
    $this->user->id = $user_id;
    if(!$this->user->read_single()) {
        header('Location: /');
        exit;
    }
    
    // Get user's posts
    $post = new Post($this->db);
    $posts = $post->getUserPosts($user_id);
    
    // Get followers and following counts
    $followers_count = $this->user->getFollowersCount();
    $following_count = $this->user->getFollowingCount();
    
    // Check if current user is following this user
    $is_following = false;
    if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id) {
        $current_user = new User($this->db);
        $current_user->id = $_SESSION['user_id'];
        $is_following = $current_user->isFollowing($user_id);
    }
    
    // Make these variables available to the view
    $user = $this->user;
    $db = $this->db;
    
    include BASE_PATH . '/views/profile.php';
}

public function showYearlyView($user_id = null) {
    // If no user_id is provided, show current user's yearly view
    if(!$user_id) {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $user_id = $_SESSION['user_id'];
    }
    
    $this->user->id = $user_id;
    if(!$this->user->read_single()) {
        header('Location: /');
        exit;
    }
    
    // Make these variables available to the view
    $user = $this->user;
    $db = $this->db;
    
    include BASE_PATH . '/views/profile/yearly-view.php';
}

public function updateProfile() {
    if(!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    $this->user->id = $_SESSION['user_id'];
    $this->user->read_single();
    
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->user->username = $_POST['username'];
        $this->user->email = $_POST['email'];
        
        // Handle profile image upload
        if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = BASE_PATH . '/uploads/profile/';
            
            // Create directory if it doesn't exist
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . $_FILES['profile_image']['name'];
            $file_path = $upload_dir . $file_name;
            
            // Move uploaded file
            if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $file_path)) {
                $this->user->profile_image = '/uploads/profile/' . $file_name;
            }
        }
        
        if($this->user->update()) {
            $_SESSION['message'] = 'Profile updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update profile';
        }
        
        header('Location: /profile');
        exit;
    }
    
    // Make sure these variables are available in the view
    $user = $this->user;
    $db = $this->db;
    
    include BASE_PATH . '/views/edit_profile.php';
}

public function showUsers() {
    if(!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    $this->user->id = $_SESSION['user_id'];
    $users = $this->user->getAllUsers();
    
    // Make these variables available to the view
    $db = $this->db;
    
    include BASE_PATH . '/views/users.php';
}

public function showFollowers($user_id = null) {
    if(!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    if(!$user_id) {
        $user_id = $_SESSION['user_id'];
    }
    
    $this->user->id = $user_id;
    $this->user->read_single();
    
    $followers = $this->user->getFollowers();
    
    // Make sure these variables are available in the view
    $user = $this->user;
    $db = $this->db;
    
    include BASE_PATH . '/views/followers.php';
}

public function showFollowing($user_id = null) {
    if(!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    
    if(!$user_id) {
        $user_id = $_SESSION['user_id'];
    }
    
    $this->user->id = $user_id;
    $this->user->read_single();
    
    $following = $this->user->getFollowing();
    
    // Make sure these variables are available in the view
    $user = $this->user;
    $db = $this->db;
    
    include BASE_PATH . '/views/following.php';
}
}
?>

