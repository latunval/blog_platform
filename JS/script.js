document.addEventListener('DOMContentLoaded', () => {
    // Comment submission
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(commentForm);
            const commentCountElement = document.getElementById('comment-count');
            if (!commentCountElement) {
                console.error('Comment count element not found');
                alert('Error: Comment counter not available.');
                return;
            }
            try {
                const response = await fetch('interact.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log('Response:', result); // Debug response
                if (result.status === 'success' && result.type === 'comment') {
                    const newCount = parseInt(result.comment_count) || 0;
                    commentCountElement.textContent = `Comments: ${newCount}`;
                    commentForm.reset();
                    // Reload the page to show the new comment
                    location.reload();
                } else if (result.message === 'Please log in to interact') {
                    alert('Please log in to comment on this post.');
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Error submitting comment: ' + error.message);
            }
        });
    }

    // Like/Unlike submission
    document.querySelectorAll('.like-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const button = form.querySelector('.like-btn');
            
            try {
                const response = await fetch('interact.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log('Response:', result); // Debug response
                
                if (result.status === 'success' && result.type === 'like') {
                    // Update like count
                    if (form.closest('.post-likes-section')) {
                        // This is a post like
                        const likeCountElement = document.getElementById('post-likes-count');
                        if (likeCountElement) {
                            const newCount = parseInt(result.like_count) || 0;
                            likeCountElement.textContent = `Likes: ${newCount}`;
                        }
                    } else {
                        // This is a comment like
                        const commentSection = form.closest('.comment-likes-section');
                        if (commentSection) {
                            const likeCountElement = commentSection.querySelector('.comment-likes-count');
                            if (likeCountElement) {
                                const newCount = parseInt(result.like_count) || 0;
                                likeCountElement.textContent = `Likes: ${newCount}`;
                            }
                        }
                    }
                    
                    // Update button text
                    if (result.action === 'like') {
                        button.textContent = 'Unlike';
                        button.dataset.liked = 'true';
                    } else if (result.action === 'unlike') {
                        button.textContent = 'Like';
                        button.dataset.liked = 'false';
                    }
                } else if (result.message === 'Please log in to interact') {
                    alert('Please log in to like or unlike this content.');
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error processing like/unlike:', error);
                alert('Error processing like/unlike: ' + error.message);
            }
        });
    });
});