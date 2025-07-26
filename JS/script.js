document.addEventListener('DOMContentLoaded', () => {
    // Comment submission
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(commentForm);
            const response = await fetch('comment.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.text();
            if (result.includes('success')) {
                const commentSection = document.getElementById('comment-section');
                const newComment = document.createElement('div');
                newComment.className = 'comment';
                newComment.innerHTML = `<p><strong>${formData.get('content')}</strong></p><p>Likes: 0</p>`;
                commentSection.appendChild(newComment);
                commentForm.reset();
            } else {
                alert(result);
            }
        });
    }

    // Like/Unlike submission
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const form = e.target.closest('form');
            const formData = new FormData(form);
            const response = await fetch('like.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                const likeCountElement = form.previousElementSibling;
                let currentCount = parseInt(likeCountElement.textContent.split(': ')[1]);
                if (result.action === 'like') {
                    // Increment count and set button to Unlike
                    likeCountElement.textContent = `Likes: ${currentCount + 1}`;
                    button.textContent = 'Unlike';
                    button.dataset.liked = 'true';
                } else if (result.action === 'unlike') {
                    // Decrement count and set button to Like
                    likeCountElement.textContent = `Likes: ${currentCount - 1}`;
                    button.textContent = 'Like';
                    button.dataset.liked = 'false';
                }
            } else {
                alert(result.message);
            }
        });
    });
});