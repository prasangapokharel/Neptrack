<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
?>

<div class="max-w-[480px] mx-auto bg-[#F1F5F9] min-h-screen pb-20">
    <header class="bg-white p-5 flex items-center justify-between shadow-sm">
        <h1 class="text-xl font-semibold text-[#1E293B]">Feed</h1>
        <button class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-[#F1F5F9] transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#1E293B]">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </button>
    </header>

    <!-- Alert Container (initially hidden) -->
    <div id="alert-container" class="hidden fixed top-5 left-1/2 transform -translate-x-1/2 z-50 w-[90%] max-w-[400px]"></div>
    
    <!-- Create Post Card -->
    <div class="bg-white rounded-[16px] shadow-[0_2px_10px_rgba(0,0,0,0.05)] overflow-hidden m-5">
        <div class="p-5">
            <form id="create-post-form" enctype="multipart/form-data">
                <div class="mb-4">
                    <textarea 
                        id="post-content" 
                        name="content" 
                        placeholder="What's on your mind?" 
                        class="w-full p-[12px_15px] border border-[#F1F5F9] rounded-[12px] focus:outline-none focus:border-[#3B82F6] focus:shadow-[0_0_0_2px_rgba(59,130,246,0.2)] transition-all duration-200 resize-none"
                        rows="3"
                    ></textarea>
                </div>
                
                <div id="image-preview" class="mt-4 hidden">
                    <div class="relative">
                        <img id="preview-image" src="/placeholder.svg" alt="Preview" class="w-full h-auto rounded-[12px]">
                        <button 
                            type="button" 
                            id="remove-image" 
                            class="absolute top-2 right-2 bg-[#ef4444] text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-[#dc2626] transition-all duration-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-4">
                    <label class="cursor-pointer flex items-center text-[#1E293B]/70 hover:text-[#3B82F6] transition-all duration-200">
                        <div class="w-10 h-10 rounded-[10px] bg-[#F1F5F9] flex items-center justify-center mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3B82F6]">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                        </div>
                        <span class="font-medium">Add Photo</span>
                        <input type="file" name="image" id="post-image" class="hidden" accept="image/*">
                    </label>
                    
                    <button 
                        type="submit" 
                        id="submit-post" 
                        class="bg-[#3B82F6] hover:bg-[#2563EB] text-white px-5 py-[10px] rounded-[12px] font-medium transition-all duration-200 flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        Post
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Posts Feed -->
    <div id="posts-container">
        <?php if(empty($posts)): ?>
            <div class="bg-white rounded-[16px] shadow-[0_2px_10px_rgba(0,0,0,0.05)] overflow-hidden m-5 p-8 text-center">
                <div class="w-[60px] h-[60px] mx-auto mb-4 rounded-full bg-[#F1F5F9] flex items-center justify-center">

                
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#1E293B]/30">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-[#1E293B] font-semibold mb-2">No posts in your feed yet</h3>
                <p class="text-[#1E293B]/60 mb-6">Follow other users to see their posts here!</p>
                <a href="/users" class="bg-[#3B82F6] hover:bg-[#2563EB] text-white px-5 py-[10px] rounded-[12px] font-medium transition-all duration-200 inline-block">Find Users</a>
            </div>
        <?php else: ?>
            <?php foreach($posts as $post): ?>
                <div class="bg-white rounded-[16px] shadow-[0_2px_10px_rgba(0,0,0,0.05)] overflow-hidden m-5 post-item" data-post-id="<?php echo $post['id']; ?>">
                    <div class="p-5">
                        <div class="flex items-start gap-3 mb-4">
                            <a href="/profile/<?php echo $post['user_id']; ?>">
                                <?php if($post['profile_image']): ?>
                                    <img src="<?php echo $post['profile_image']; ?>" alt="<?php echo $post['username']; ?>" class="w-10 h-10 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-[#3B82F6] flex items-center justify-center text-white font-semibold">
                                        <?php echo strtoupper(substr($post['username'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div>
                                <a href="/profile/<?php echo $post['user_id']; ?>" class="font-medium text-[#1E293B] hover:text-[#3B82F6] transition-all duration-200"><?php echo $post['username']; ?></a>
                                <div class="text-xs text-[#1E293B]/60"><?php echo date('M d, Y \a\t h:i A', strtotime($post['created_at'])); ?></div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-[#1E293B] leading-[1.6]"><?php echo nl2br($post['content']); ?></p>
                        </div>
                        
                        <?php if($post['image']): ?>
                            <div class="mb-4">
                                <img src="<?php echo $post['image']; ?>" alt="Post image" class="rounded-[12px] w-full h-auto">
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex items-center justify-between border-t border-[#F1F5F9] pt-4 mt-2">
                            <div class="flex items-center gap-4">
                                <button 
                                    class="like-button flex items-center gap-1 <?php echo $post['is_liked'] ? 'text-[#3B82F6]' : 'text-[#1E293B]/60 hover:text-[#3B82F6]'; ?> transition-all duration-200" 
                                    data-post-id="<?php echo $post['id']; ?>" 
                                    data-liked="<?php echo $post['is_liked'] ? 'true' : 'false'; ?>"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="<?php echo $post['is_liked'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                    <span class="like-count"><?php echo $post['like_count']; ?></span>
                                </button>
                                
                                <a 
                                    href="/post/<?php echo $post['id']; ?>" 
                                    class="flex items-center gap-1 text-[#1E293B]/60 hover:text-[#3B82F6] transition-all duration-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                    <span><?php echo $post['comment_count']; ?></span>
                                </a>
                            </div>
                            
                            <?php if($post['user_id'] == $_SESSION['user_id']): ?>
                                <button 
                                    class="delete-post w-8 h-8 rounded-full flex items-center justify-center text-[#1E293B]/60 hover:bg-[#F1F5F9] transition-all duration-200" 
                                    data-post-id="<?php echo $post['id']; ?>"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Confirmation Modal (initially hidden) -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-[rgba(0,0,0,0.5)] flex items-center justify-center z-50">
        <div class="bg-white rounded-[16px] shadow-[0_10px_25px_rgba(0,0,0,0.1)] w-[90%] max-w-[400px] p-[25px]">
            <h3 class="text-[#1E293B] text-lg font-semibold mb-3">Delete Post</h3>
            <p class="text-[#1E293B]/60 mb-5">Are you sure you want to delete this post? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button id="cancel-delete" class="bg-transparent border border-[#F1F5F9] hover:border-[#3B82F6] text-[#1E293B] px-5 py-[10px] rounded-[12px] font-medium transition-all duration-200">
                    Cancel
                </button>
                <button id="confirm-delete" class="bg-[#ef4444] hover:bg-[#dc2626] text-white px-5 py-[10px] rounded-[12px] font-medium transition-all duration-200">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const postImage = document.getElementById('post-image');
    const imagePreview = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    const removeImage = document.getElementById('remove-image');
    
    // Modal elements
    const deleteModal = document.getElementById('delete-modal');
    const cancelDelete = document.getElementById('cancel-delete');
    const confirmDelete = document.getElementById('confirm-delete');
    let currentPostToDelete = null;
    
    // Alert container
    const alertContainer = document.getElementById('alert-container');
    
    // Show alert function
    function showAlert(message, type) {
        // Create alert element
        const alert = document.createElement('div');
        
        // Set alert styles based on type
        if (type === 'success') {
            alert.className = 'flex items-center p-[15px] rounded-[8px] border-l-4 border-[#10b981] bg-white shadow-[0_4px_12px_rgba(0,0,0,0.1)] mb-4';
            alert.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#10b981] mr-3">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span>${message}</span>
            `;
        } else {
            alert.className = 'flex items-center p-[15px] rounded-[8px] border-l-4 border-[#ef4444] bg-white shadow-[0_4px_12px_rgba(0,0,0,0.1)] mb-4';
            alert.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#ef4444] mr-3">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>${message}</span>
            `;
        }
        
        // Clear previous alerts
        alertContainer.innerHTML = '';
        
        // Add alert to container and show it
        alertContainer.appendChild(alert);
        alertContainer.classList.remove('hidden');
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertContainer.classList.add('hidden');
        }, 5000);
    }
    
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
            showAlert('Post content cannot be empty', 'error');
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
                
                // Show success message
                showAlert('Post created successfully!', 'success');
                
                // Create new post HTML
                const post = data.post;
                const profileImage = post.profile_image ? 
                    `<img src="${post.profile_image}" alt="${post.username}" class="w-10 h-10 rounded-full object-cover">` : 
                    `<div class="w-10 h-10 rounded-full bg-[#3B82F6] flex items-center justify-center text-white font-semibold">${post.username.charAt(0).toUpperCase()}</div>`;
                
                const postImage = post.image ? 
                    `<div class="mb-4"><img src="${post.image}" alt="Post image" class="rounded-[12px] w-full h-auto"></div>` : '';
                
                const postHTML = `
                <div class="bg-white rounded-[16px] shadow-[0_2px_10px_rgba(0,0,0,0.05)] overflow-hidden m-5 post-item" data-post-id="${post.id}">
                    <div class="p-5">
                        <div class="flex items-start gap-3 mb-4">
                            <a href="/profile/${post.user_id}">
                                ${profileImage}
                            </a>
                            <div>
                                <a href="/profile/${post.user_id}" class="font-medium text-[#1E293B] hover:text-[#3B82F6] transition-all duration-200">${post.username}</a>
                                <div class="text-xs text-[#1E293B]/60">Just now</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-[#1E293B] leading-[1.6]">${post.content.replace(/\n/g, '<br>')}</p>
                        </div>
                        
                        ${postImage}
                        
                        <div class="flex items-center justify-between border-t border-[#F1F5F9] pt-4 mt-2">
                            <div class="flex items-center gap-4">
                                <button class="like-button flex items-center gap-1 text-[#1E293B]/60 hover:text-[#3B82F6] transition-all duration-200" data-post-id="${post.id}" data-liked="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                    <span class="like-count">0</span>
                                </button>
                                
                                <a href="/post/${post.id}" class="flex items-center gap-1 text-[#1E293B]/60 hover:text-[#3B82F6] transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                    <span>0</span>
                                </a>
                            </div>
                            
                            <button class="delete-post w-8 h-8 rounded-full flex items-center justify-center text-[#1E293B]/60 hover:bg-[#F1F5F9] transition-all duration-200" data-post-id="${post.id}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
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
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'error');
        });
    });
    
    // Like/unlike post
    function addPostEventListeners() {
        const likeButtons = document.querySelectorAll('.like-button');
        likeButtons.forEach(button => {
            button.removeEventListener('click', likeHandler);
            button.addEventListener('click', likeHandler);
        });
        
        const deleteButtons = document.querySelectorAll('.delete-post');
        deleteButtons.forEach(button => {
            button.removeEventListener('click', deleteHandler);
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
                    this.classList.remove('text-[#3B82F6]');
                    this.classList.add('text-[#1E293B]/60', 'hover:text-[#3B82F6]');
                    likeIcon.setAttribute('fill', 'none');
                } else {
                    this.setAttribute('data-liked', 'true');
                    this.classList.remove('text-[#1E293B]/60', 'hover:text-[#3B82F6]');
                    this.classList.add('text-[#3B82F6]');
                    likeIcon.setAttribute('fill', 'currentColor');
                }
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'error');
        });
    }
    
    function deleteHandler() {
        const postId = this.getAttribute('data-post-id');
        currentPostToDelete = this.closest('.post-item');
        
        // Show delete confirmation modal
        deleteModal.classList.remove('hidden');
    }
    
    // Modal handlers
    cancelDelete.addEventListener('click', function() {
        deleteModal.classList.add('hidden');
        currentPostToDelete = null;
    });
    
    // Close modal when clicking outside
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
            currentPostToDelete = null;
        }
    });
    
    confirmDelete.addEventListener('click', function() {
        if (!currentPostToDelete) return;
        
        const postId = currentPostToDelete.getAttribute('data-post-id');
        
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
                // Hide modal
                deleteModal.classList.add('hidden');
                
                // Show success message
                showAlert('Post deleted successfully', 'success');
                
                // Remove post from DOM
                currentPostToDelete.remove();
                currentPostToDelete = null;
                
                // If no posts left, show empty state
                if(!document.querySelector('.post-item')) {
                    postsContainer.innerHTML = `
                    <div class="bg-white rounded-[16px] shadow-[0_2px_10px_rgba(0,0,0,0.05)] overflow-hidden m-5 p-8 text-center">
                        <div class="w-  overflow-hidden m-5 p-8 text-center">
                        <div class="w-[60px] h-[60px] mx-auto mb-4 rounded-full bg-[#F1F5F9] flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#1E293B]/30">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-[#1E293B] font-semibold mb-2">No posts in your feed yet</h3>
                        <p class="text-[#1E293B]/60 mb-6">Follow other users to see their posts here!</p>
                        <a href="/users" class="bg-[#3B82F6] hover:bg-[#2563EB] text-white px-5 py-[10px] rounded-[12px] font-medium transition-all duration-200 inline-block">Find Users</a>
                    </div>
                    `;
                }
            } else {
                deleteModal.classList.add('hidden');
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            deleteModal.classList.add('hidden');
            showAlert('An error occurred. Please try again.', 'error');
        });
    });
    
    // Initialize event listeners
    addPostEventListeners();
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>