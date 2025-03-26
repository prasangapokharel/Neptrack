<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
?>

<div class="max-w-md mx-auto">
    <!-- Create Post -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
        <div class="p-6">
            <form id="create-post-form" enctype="multipart/form-data">
                <div class="mb-4">
                    <textarea id="post-content" name="content" placeholder="What's on your mind?" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition duration-200 resize-none" rows="3"></textarea>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="cursor-pointer flex items-center text-gray-600 hover:text-primary transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                        <span>Add Photo</span>
                        <input type="file" name="image" id="post-image" class="hidden" accept="image/*">
                    </label>
                    
                    <button type="submit" id="submit-post" class="bg-primary hover:bg-green-600 text-white px-4 py-2 rounded-xl transition duration-200 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                        </svg>
                        Post
                    </button>
                </div>
                
                <div id="image-preview" class="mt-4 hidden">
                    <div class="relative">
                        <img id="preview-image" src="/placeholder.svg" alt="Preview" class="w-full h-auto rounded-xl">
                        <button type="button" id="remove-image" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Posts Feed -->
    <div id="posts-container">
        <?php if(empty($posts)): ?>
            <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6 p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                <p class="text-gray-500 mb-4">No posts in your feed yet.</p>
                <p class="text-gray-500">Follow other users to see their posts here!</p>
                <a href="/users" class="inline-block mt-4 bg-primary hover:bg-green-600 text-white px-6 py-2 rounded-xl transition duration-200">Find Users</a>
            </div>
        <?php else: ?>
            <?php foreach($posts as $post): ?>
                <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6 post-item" data-post-id="<?php echo $post['id']; ?>">
                    <div class="p-6">
                        <div class="flex items-start space-x-3 mb-3">
                            <a href="/profile/<?php echo $post['user_id']; ?>">
                                <?php if($post['profile_image']): ?>
                                    <img src="<?php echo $post['profile_image']; ?>" alt="<?php echo $post['username']; ?>" class="w-10 h-10 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                                        <?php echo strtoupper(substr($post['username'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div>
                                <a href="/profile/<?php echo $post['user_id']; ?>" class="font-medium text-gray-800 hover:text-primary transition duration-200"><?php echo $post['username']; ?></a>
                                <div class="text-xs text-gray-500"><?php echo date('M d, Y \a\t h:i A', strtotime($post['created_at'])); ?></div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-gray-800"><?php echo nl2br($post['content']); ?></p>
                        </div>
                        
                        <?php if($post['image']): ?>
                            <div class="mb-4">
                                <img src="<?php echo $post['image']; ?>" alt="Post image" class="rounded-xl w-full h-auto">
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-4">
                                <button class="like-button flex items-center <?php echo $post['is_liked'] ? 'text-primary' : 'text-gray-500 hover:text-primary'; ?> transition duration-200" data-post-id="<?php echo $post['id']; ?>" data-liked="<?php echo $post['is_liked'] ? 'true' : 'false'; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="<?php echo $post['is_liked'] ? 'currentColor' : 'none'; ?>" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span class="like-count"><?php echo $post['like_count']; ?></span>
                                </button>
                                
                                <a href="/post/<?php echo $post['id']; ?>" class="flex items-center text-gray-500 hover:text-primary transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span><?php echo $post['comment_count']; ?></span>
                                </a>
                            </div>
                            
                            <?php if($post['user_id'] == $_SESSION['user_id']): ?>
                                <button class="delete-post text-red-500 hover:text-red-700 transition duration-200" data-post-id="<?php echo $post['id']; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const postImage = document.getElementById('post-image');
    const imagePreview = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    const removeImage = document.getElementById('remove-image');
    
    postImage.addEventListener('change', function() {
        if(this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.classList.remove('hidden');
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    removeImage.addEventListener('click', function() {
        postImage.value = '';
        imagePreview.classList.add('hidden');
    });
    
    // Create post
    const createPostForm = document.getElementById('create-post-form');
    const postsContainer = document.getElementById('posts-container');
    
    createPostForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = document.getElementById('post-content').value;
        
        if(!content.trim()) {
            alert('Post content cannot be empty');
            return;
        }
        
        const formData = new FormData(this);
        
        fetch('/api/post/create', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Reset form
                createPostForm.reset();
                imagePreview.classList.add('hidden');
                
                // Create new post HTML
                const post = data.post;
                const profileImage = post.profile_image ? 
                    `<img src="${post.profile_image}" alt="${post.username}" class="w-10 h-10 rounded-full object-cover">` : 
                    `<div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">${post.username.charAt(0).toUpperCase()}</div>`;
                
                const postImage = post.image ? 
                    `<div class="mb-4"><img src="${post.image}" alt="Post image" class="rounded-xl w-full h-auto"></div>` : '';
                
                const postHTML = `
                <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6 post-item" data-post-id="${post.id}">
                    <div class="p-6">
                        <div class="flex items-start space-x-3 mb-3">
                            <a href="/profile/${post.user_id}">
                                ${profileImage}
                            </a>
                            <div>
                                <a href="/profile/${post.user_id}" class="font-medium text-gray-800 hover:text-primary transition duration-200">${post.username}</a>
                                <div class="text-xs text-gray-500">Just now</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-gray-800">${post.content.replace(/\n/g, '<br>')}</p>
                        </div>
                        
                        ${postImage}
                        
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-4">
                                <button class="like-button flex items-center text-gray-500 hover:text-primary transition duration-200" data-post-id="${post.id}" data-liked="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span class="like-count">0</span>
                                </button>
                                
                                <a href="/post/${post.id}" class="flex items-center text-gray-500 hover:text-primary transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span>0</span>
                                </a>
                            </div>
                            
                            <button class="delete-post text-red-500 hover:text-red-700 transition duration-200" data-post-id="${post.id}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                `;
                
                // Add new post to the top of the feed
                if(postsContainer.querySelector('.post-item')) {
                    postsContainer.insertAdjacentHTML('afterbegin', postHTML);
                } else {
                    // If no posts, replace the empty state
                    postsContainer.innerHTML = postHTML;
                }
                
                // Add event listeners to new post
                addPostEventListeners();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    
    // Like/unlike post
    function addPostEventListeners() {
        const likeButtons = document.querySelectorAll('.like-button');
        likeButtons.forEach(button => {
            button.addEventListener('click', likeHandler);
        });
        
        const deleteButtons = document.querySelectorAll('.delete-post');
        deleteButtons.forEach(button => {
            button.addEventListener('click', deleteHandler);
        });
    }
    
    function likeHandler() {
        const postId = this.getAttribute('data-post-id');
        const isLiked = this.getAttribute('data-liked') === 'true';
        const likeCount = this.querySelector('.like-count');
        const likeIcon = this.querySelector('svg');
        
        const endpoint = isLiked ? '/api/post/unlike' : '/api/post/like';
        
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Update like count
                likeCount.textContent = data.like_count;
                
                // Toggle liked state
                if(isLiked) {
                    this.setAttribute('data-liked', 'false');
                    this.classList.remove('text-primary');
                    this.classList.add('text-gray-500', 'hover:text-primary');
                    likeIcon.setAttribute('fill', 'none');
                } else {
                    this.setAttribute('data-liked', 'true');
                    this.classList.remove('text-gray-500', 'hover:text-primary');
                    this.classList.add('text-primary');
                    likeIcon.setAttribute('fill', 'currentColor');
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    function deleteHandler() {
        if(!confirm('Are you sure you want to delete this post?')) {
            return;
        }
        
        const postId = this.getAttribute('data-post-id');
        const postElement = this.closest('.post-item');
        
        fetch('/api/post/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Remove post from DOM
                postElement.remove();
                
                // If no posts left, show empty state
                if(!document.querySelector('.post-item')) {
                    postsContainer.innerHTML = `
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6 p-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <p class="text-gray-500 mb-4">No posts in your feed yet.</p>
                        <p class="text-gray-500">Follow other users to see their posts here!</p>
                        <a href="/users" class="inline-block mt-4 bg-primary hover:bg-green-600 text-white px-6 py-2 rounded-xl transition duration-200">Find Users</a>
                    </div>
                    `;
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Initialize event listeners
    addPostEventListeners();
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>


