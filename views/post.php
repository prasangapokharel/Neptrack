<?php 
include BASE_PATH . '/views/templates/header.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
  header('Location: /login');
  exit;
}
?>

<div class="max-w-md mx-auto pb-20">
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="p-6">
      <div class="flex items-start space-x-3 mb-3">
        <a href="/profile/<?php echo $post['user_id']; ?>">
          <?php if($post['profile_image']): ?>
            <img src="<?php echo $post['profile_image']; ?>" alt="<?php echo $post['username']; ?>" class="w-10 h-10 rounded-full object-cover">
          <?php else: ?>
            <div class="w-10 h-10 rounded-full glass-dark flex items-center justify-center text-white font-medium">
              <?php echo strtoupper(substr($post['username'], 0, 1)); ?>
            </div>
          <?php endif; ?>
        </a>
        <div>
          <a href="/profile/<?php echo $post['user_id']; ?>" class="font-medium text-white hover:text-white/80 transition duration-200"><?php echo $post['username']; ?></a>
          <div class="text-xs text-white/70 font-light"><?php echo date('M d, Y \a\t h:i A', strtotime($post['created_at'])); ?></div>
        </div>
      </div>
      
      <div class="mb-4">
        <p class="text-white font-light"><?php echo nl2br($post['content']); ?></p>
      </div>
      
      <?php if($post['image']): ?>
        <div class="mb-4">
          <img src="<?php echo $post['image']; ?>" alt="Post image" class="rounded-xl w-full h-auto">
        </div>
      <?php endif; ?>
      
      <div class="flex items-center justify-between text-sm">
        <div class="flex items-center space-x-4">
          <button id="like-button" class="flex items-center <?php echo $post['is_liked'] ? 'text-white' : 'text-white/70 hover:text-white'; ?> transition duration-200" data-post-id="<?php echo $post['id']; ?>" data-liked="<?php echo $post['is_liked'] ? 'true' : 'false'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="<?php echo $post['is_liked'] ? 'currentColor' : 'none'; ?>" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span id="like-count"><?php echo $post['like_count']; ?></span>
          </button>
          
          <div class="flex items-center text-white/70">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span id="comment-count"><?php echo count($comments); ?></span>
          </div>
        </div>
        
        <?php if($post['user_id'] == $_SESSION['user_id']): ?>
          <button id="delete-post" class="text-red-300 hover:text-red-200 transition duration-200" data-post-id="<?php echo $post['id']; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Comments Section -->
  <div class="glass rounded-2xl shadow-lg overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-white/10">
      <h3 class="text-lg font-medium text-white">Comments</h3>
    </div>
    
    <div class="p-6">
      <form id="comment-form" class="mb-6">
        <input type="hidden" id="post-id" value="<?php echo $post['id']; ?>">
        <div class="mb-3">
          <textarea id="comment-content" placeholder="Write a comment..." class="w-full px-4 py-2 rounded-xl focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200 resize-none" rows="2"></textarea>
        </div>
        <div class="flex justify-end">
          <button type="submit" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl transition duration-200">
            Post Comment
          </button>
        </div>
      </form>
      
      <div id="comments-container" class="space-y-4">
        <?php if(empty($comments)): ?>
          <div id="no-comments" class="text-center text-white/70 py-4 font-light">
            No comments yet. Be the first to comment!
          </div>
        <?php else: ?>
          <?php foreach($comments as $comment): ?>
            <div class="comment-item flex space-x-3" data-comment-id="<?php echo $comment['id']; ?>">
              <a href="/profile/<?php echo $comment['user_id']; ?>">
                <?php if($comment['profile_image']): ?>
                  <img src="<?php echo $comment['profile_image']; ?>" alt="<?php echo $comment['username']; ?>" class="w-8 h-8 rounded-full object-cover">
                <?php else: ?>
                  <div class="w-8 h-8 rounded-full glass-dark flex items-center justify-center text-white font-medium text-sm">
                    <?php echo strtoupper(substr($comment['username'], 0, 1)); ?>
                  </div>
                <?php endif; ?>
              </a>
              <div class="flex-1">
                <div class="glass-dark rounded-xl p-3">
                  <div class="flex justify-between items-start">
                    <a href="/profile/<?php echo $comment['user_id']; ?>" class="font-medium text-white hover:text-white/80 transition duration-200"><?php echo $comment['username']; ?></a>
                    <?php if($comment['user_id'] == $_SESSION['user_id']): ?>
                      <button class="delete-comment text-red-300 hover:text-red-200 transition duration-200" data-comment-id="<?php echo $comment['id']; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    <?php endif; ?>
                  </div>
                  <p class="text-white text-sm font-light mt-1"><?php echo nl2br($comment['content']); ?></p>
                </div>
                <div class="text-xs text-white/70 font-light mt-1 ml-2"><?php echo date('M d, Y \a\t h:i A', strtotime($comment['created_at'])); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
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
          this.classList.remove('text-white');
          this.classList.add('text-white/70', 'hover:text-white');
          likeIcon.setAttribute('fill', 'none');
        } else {
          this.setAttribute('data-liked', 'true');
          this.classList.remove('text-white/70', 'hover:text-white');
          this.classList.add('text-white');
          likeIcon.setAttribute('fill', 'currentColor');
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
          `<img src="${comment.profile_image}" alt="${comment.username}" class="w-8 h-8 rounded-full object-cover">` : 
          `<div class="w-8 h-8 rounded-full glass-dark flex items-center justify-center text-white font-medium text-sm">${comment.username.charAt(0).toUpperCase()}</div>`;
        
        const commentHTML = `
        <div class="comment-item flex space-x-3" data-comment-id="${comment.id}">
          <a href="/profile/${comment.user_id}">
            ${profileImage}
          </a>
          <div class="flex-1">
            <div class="glass-dark rounded-xl p-3">
              <div class="flex justify-between items-start">
                <a href="/profile/${comment.user_id}" class="font-medium text-white hover:text-white/80 transition duration-200">${comment.username}</a>
                <button class="delete-comment text-red-300 hover:text-red-200 transition duration-200" data-comment-id="${comment.id}">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
              <p class="text-white text-sm font-light mt-1">${comment.content.replace(/\n/g, '<br>')}</p>
            </div>
            <div class="text-xs text-white/70 font-light mt-1 ml-2">Just now</div>
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
          <div id="no-comments" class="text-center text-white/70 py-4 font-light">
            No comments yet. Be the first to comment!
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

