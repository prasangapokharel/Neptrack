<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
  header('Location: /login');
  exit;
}

// Make sure $user is defined
$database = new Database();
$db = $database->connect();
$user = new User($db);
$user->id = $_SESSION['user_id'];
$user->read_single();
?>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
        <div class="bg-primary-light px-6 py-4">
            <h2 class="text-xl font-bold text-gray-800">Edit Profile</h2>
        </div>
        
        <div class="p-6">
            <form action="/edit-profile" method="POST" enctype="multipart/form-data">
                <!-- Profile Image -->
                <div class="mb-6 text-center">
                    <div class="mb-4">
                        <?php if($user->profile_image): ?>
                            <img src="<?php echo $user->profile_image; ?>" alt="<?php echo $user->username; ?>" class="w-24 h-24 rounded-full object-cover mx-auto border-4 border-white shadow-md">
                        <?php else: ?>
                            <div class="w-24 h-24 rounded-full bg-primary flex items-center justify-center text-white text-3xl font-bold mx-auto">
                                <?php echo strtoupper(substr($user->username, 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
                    <div class="flex items-center justify-center">
                        <label class="cursor-pointer bg-white border border-gray-300 rounded-md py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                            <span>Change Photo</span>
                            <input type="file" name="profile_image" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>
                
                <!-- Username -->
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo $user->username; ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200">
                </div>
                
                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $user->email; ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200">
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end">
                    <a href="/profile" class="mr-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-xl transition duration-200">Cancel</a>
                    <button type="submit" class="bg-primary hover:bg-green-600 text-white px-6 py-2 rounded-xl transition duration-200">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>


