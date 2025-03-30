<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
  header('Location: /login');
  exit;
}
?>

<style>
:root {
  --primary: #3B82F6;    /* Vibrant blue */
  --dark: #1E293B;       /* Dark slate */
  --light: #F1F5F9;      /* Light gray */
  --font-main: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

body {
  font-family: var(--font-main);
  background-color: var(--light);
  color: var(--dark);
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}

.container {
  max-width: 480px;
  margin: 0 auto;
  padding: 0;
  position: relative;
  min-height: 100vh;
  background-color: white;
}

.header {
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
}

.content-wrapper {
  padding: 0 20px 20px;
}

.card {
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin-bottom: 20px;
  overflow: hidden;
}

.card-header {
  padding: 15px 20px;
  border-bottom: 1px solid var(--light);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-title {
  font-size: 18px;
  font-weight: 600;
  display: flex;
  align-items: center;
}

.card-title-icon {
  margin-right: 8px;
  color: var(--primary);
}

.card-body {
  padding: 20px;
}

.post-item {
  padding: 15px 20px;
  border-bottom: 1px solid var(--light);
}

.post-header {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.post-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  margin-right: 10px;
  background-color: var(--light);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
  font-weight: 600;
  font-size: 14px;
  overflow: hidden;
}

.post-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.post-user {
  font-weight: 500;
  margin-bottom: 2px;
}

.post-date {
  font-size: 12px;
  color: var(--dark);
  opacity: 0.6;
}

.post-content {
  margin-bottom: 12px;
  font-size: 14px;
}

.post-image {
  width: 100%;
  border-radius: 8px;
  margin-bottom: 12px;
}

.post-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.post-stats {
  display: flex;
  gap: 15px;
}

.post-stat {
  display: flex;
  align-items: center;
  font-size: 12px;
  color: var(--dark);
  opacity: 0.7;
}

.post-stat-icon {
  margin-right: 4px;
}

.post-stat-icon.liked {
  color: #ef4444;
  fill: #ef4444;
}

.post-view {
  font-size: 12px;
  font-weight: 500;
  color: var(--primary);
  text-decoration: none;
}

.post-view:hover {
  text-decoration: underline;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  text-decoration: none;
}

.btn-primary {
  background-color: var(--primary);
  color: white;
}

.btn-primary:hover {
  background-color: #2563eb;
}

.btn-outline {
  background-color: transparent;
  border: 1px solid var(--light);
  color: var(--dark);
}

.btn-outline:hover {
  background-color: var(--light);
}

.btn-danger {
  color: #ef4444;
}

.btn-danger:hover {
  color: #dc2626;
}

.btn-icon {
  margin-right: 6px;
}

.comment-form {
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-control {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--light);
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s ease;
  resize: none;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.comment-list {
  margin-top: 20px;
}

.comment-item {
  display: flex;
  margin-bottom: 15px;
}

.comment-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  margin-right: 10px;
  background-color: var(--light);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
  font-weight: 600;
  font-size: 12px;
  overflow: hidden;
  flex-shrink: 0;
}

.comment-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.comment-content {
  flex: 1;
  background-color: var(--light);
  border-radius: 12px;
  padding: 10px 12px;
  position: relative;
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 4px;
}

.comment-user {
  font-weight: 500;
  font-size: 14px;
}

.comment-text {
  font-size: 14px;
}

.comment-date {
  font-size: 11px;
  color: var(--dark);
  opacity: 0.6;
  margin-top: 4px;
}

.empty-state {
  text-align: center;
  padding: 30px 0;
  color: var(--dark);
  opacity: 0.7;
}

.delete-btn {
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
  color: #ef4444;
  opacity: 0.7;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  opacity: 1;
}
</style>

<div class="container">
  <header class="header">
    <h1 class="page-title">Post</h1>
    <a href="/feed">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6"></path>
      </svg>
    </a>
  </header>

  <div class="content-wrapper">
    <!-- Post Card -->
    <div class="card">
      <div class="post-item">
        <div class="post-header">
          <a href="/profile/<?php echo $post['user_id']; ?>" class="post-avatar">
            <?php if($post['profile_image']): ?>
              <img src="<?php echo $post['profile_image']; ?>" alt="<?php echo $post['username']; ?>">
            <?php else: ?>
              <?php echo strtoupper(substr($post['username'], 0, 1)); ?>
            <?php endif; ?>
          </a>
          <div>
            <a href="/profile/<?php echo $post['user_id']; ?>" class="post-user"><?php echo $post['username']; ?></a>
            <div class="post-date"><?php echo date('M d, Y \a\t h:i A', strtotime($post['created_at'])); ?></div>
          </div>
        </div>
        
        <div class="post-content">
          <p><?php echo nl2br($post['content']); ?></p>
        </div>
        
        <?php if($post['image']): ?>
          <img src="<?php echo $post['image']; ?>" alt="Post image" class="post-image">
        <?php endif; ?>
        
        <div class="post-actions">
          <div class="post-stats">
            <button id="like-button" class="post-stat" data-post-id="<?php echo $post['id']; ?>" data-liked="<?php echo $post['is_liked'] ? 'true' : 'false'; ?>">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="<?php echo $post['is_liked'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="post-stat-icon <?php echo $post['is_liked'] ? 'liked' : ''; ?>">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
              <span id="like-count"><?php echo $post['like_count']; ?></span>
            </button>
            
            <div class="post-stat">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="post-stat-icon">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
              <span id="comment-count"><?php echo count($comments); ?></span>
            </div>
          </div>
          
          <?php if($post['user_id'] == $_SESSION['user_id']): ?>
            <button id="delete-post" class="btn-danger" data-post-id="<?php echo $post['id']; ?>">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18"></path>
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
              </svg>
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <!-- Comments Card -->
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="card-title-icon">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          </svg>
          Comments
        </h2>
      </div>
      
      <div class="card-body">
        <form id="comment-form" class="comment-form">
          <input type="hidden" id="post-id" value="<?php echo $post['id']; ?>">
          <div class="form-group">
            <textarea id="comment-content" class="form-control" rows="3" placeholder="Write a comment..."></textarea>
          </div>
          <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="btn-icon">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
              </svg>
              Post Comment
            </button>
          </div>
        </form>
        
        <div id="comments-container" class="comment-list">
          <?php if(empty($comments)): ?>
            <div id="no-comments" class="empty-state">
              <p>No comments yet. Be the first to comment!</p>
            </div>
          <?php else: ?>
            <?php foreach($comments as $comment): ?>
              <div class="comment-item" data-comment-id="<?php echo $comment['id']; ?>">
                <a href="/profile/<?php echo $comment['user_id']; ?>" class="comment-avatar">
                  <?php if($comment['profile_image']): ?>
                    <img src="<?php echo $comment['profile_image']; ?>" alt="<?php echo $comment['username']; ?>">
                  <?php else: ?>
                    <?php echo strtoupper(substr($comment['username'], 0, 1)); ?>
                  <?php endif; ?>
                </a>
                <div>
                  <div class="comment-content">
                    <div class="comment-header">
                      <a href="/profile/<?php echo $comment['user_id']; ?>" class="comment-user"><?php echo $comment['username']; ?></a>
                      <?php if($comment['user_id'] == $_SESSION['user_id']): ?>
                        <button class="delete-comment delete-btn" data-comment-id="<?php echo $comment['id']; ?>">
                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"></path>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                          </svg>
                        </button>
                      <?php endif; ?>
                    </div>
                    <div class="comment-text"><?php echo nl2br($comment['content']); ?></div>
                  </div>
                  <div class="comment-date"><?php echo date('M d, Y \a\t h:i A', strtotime($comment['created_at'])); ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Like/unlike post
  const likeButton = document.getElementById('like-button');
  const likeCount = document.getElementById('like-count');
  const likeIcon = likeButton.querySelector('svg');
  
  likeButton.addEventListener('click', function() {
    const postId = this.getAttribute('data-post-id');
    const isLiked = this.getAttribute('data-liked') === 'true';
    
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
          likeIcon.setAttribute('fill', 'none');
          likeIcon.classList.remove('liked');
        } else {
          this.setAttribute('data-liked', 'true');
          likeIcon.setAttribute('fill', 'currentColor');
          likeIcon.classList.add('liked');
        }
      } else {
        alert(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });
  
  // Delete post
  const deletePost = document.getElementById('delete-post');
  if(deletePost) {
    deletePost.addEventListener('click', function() {
      if(!confirm('Are you sure you want to delete this post?')) {
        return;
      }
      
      const postId = this.getAttribute('data-post-id');
      
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
          window.location.href = '/feed';
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    });
  }
  
  // Add comment
  const commentForm = document.getElementById('comment-form');
  const commentsContainer = document.getElementById('comments-container');
  const commentCount = document.getElementById('comment-count');
  
  commentForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const content = document.getElementById('comment-content').value;
    const postId = document.getElementById('post-id').value;
    
    if(!content.trim()) {
      alert('Comment cannot be empty');
      return;
    }
    
    fetch('/api/comment/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'post_id=' + postId + '&content=' + encodeURIComponent(content)
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        // Reset form
        commentForm.reset();
        
        // Remove no comments message if it exists
        const noComments = document.getElementById('no-comments');
        if(noComments) {
          noComments.remove();
        }
        
        // Create new comment HTML
        const comment = data.comment;
        const profileImage = comment.profile_image ? 
          `<img src="${comment.profile_image}" alt="${comment.username}">` : 
          `${comment.username.charAt(0).toUpperCase()}`;
        
        const commentHTML = `
        <div class="comment-item" data-comment-id="${comment.id}">
          <a href="/profile/${comment.user_id}" class="comment-avatar">
            ${profileImage}
          </a>
          <div>
            <div class="comment-content">
              <div class="comment-header">
                <a href="/profile/${comment.user_id}" class="comment-user">${comment.username}</a>
                <button class="delete-comment delete-btn" data-comment-id="${comment.id}">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"></path>
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                  </svg>
                </button>
              </div>
              <div class="comment-text">${comment.content.replace(/\n/g, '<br>')}</div>
            </div>
            <div class="comment-date">Just now</div>
          </div>
        </div>
        `;
        
        // Add new comment to the container
        commentsContainer.insertAdjacentHTML('beforeend', commentHTML);
        
        // Update comment count
        commentCount.textContent = parseInt(commentCount.textContent) + 1;
        
        // Add event listener to new delete button
        const newDeleteButton = commentsContainer.querySelector(`.delete-comment[data-comment-id="${comment.id}"]`);
        if(newDeleteButton) {
          newDeleteButton.addEventListener('click', deleteCommentHandler);
        }
      } else {
        alert(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });
  
  // Delete comment
  const deleteCommentButtons = document.querySelectorAll('.delete-comment');
  deleteCommentButtons.forEach(button => {
    button.addEventListener('click', deleteCommentHandler);
  });
  
  function deleteCommentHandler() {
    if(!confirm('Are you sure you want to delete this comment?')) {
      return;
    }
    
    const commentId = this.getAttribute('data-comment-id');
    const commentElement = this.closest('.comment-item');
    
    fetch('/api/comment/delete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'comment_id=' + commentId
    })
    .then(response => response.json())
    .then(data => {
      if(data.success) {
        // Remove comment from DOM
        commentElement.remove();
        
        // Update comment count
        commentCount.textContent = parseInt(commentCount.textContent) - 1;
        
        // If no comments left, show empty state
        if(commentsContainer.children.length === 0) {
          commentsContainer.innerHTML = `
          <div id="no-comments" class="empty-state">
            <p>No comments yet. Be the first to comment!</p>
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
});
</script>

<?php include BASE_PATH . '/views/templates/footer.php'; ?>