<?php
include 'config/db_connect.php';
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.user_id WHERE post_id = ?");
$stmt->execute([$_GET['id']]);
$post = $stmt->fetch();
if (!$post) {
    header("Location: index.php");
    exit;
}

// Get post likes
$stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?");
$stmt->execute([$post['post_id']]);
$post_likes = $stmt->fetch()['like_count'];

// Check if user liked the post
$user_liked_post = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$_SESSION['user_id'], $post['post_id']]);
    $user_liked_post = $stmt->fetch() !== false;
}

// Get comments
$stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.user_id WHERE post_id = ? ORDER BY created_at");
$stmt->execute([$post['post_id']]);
$comments = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>
<h2><?php echo htmlspecialchars($post['title']); ?></h2>
<p>By: <?php echo htmlspecialchars($post['username']); ?> | <?php echo $post['created_at']; ?></p>
<p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
<p>Likes: <?php echo $post_likes; ?></p>
<?php if (isset($_SESSION['user_id'])): ?>
    <form action="like.php" method="POST">
        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
        <button type="submit" class="btn btn-sm btn-primary like-btn" data-liked="<?php echo $user_liked_post ? 'true' : 'false'; ?>">
            <?php echo $user_liked_post ? 'Unlike' : 'Like'; ?>
        </button>
    </form>
    <h4 class="mt-4">Add Comment</h4>
    <form id="comment-form" method="POST">
        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
        <div class="mb-3">
            <textarea class="form-control" name="content" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Comment</button>
    </form>
<?php endif; ?>
<h4 class="mt-4">Comments</h4>
<div id="comment-section">
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['content']); ?></p>
            <p>
                <?php
                $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM likes WHERE comment_id = ?");
                $stmt->execute([$comment['comment_id']]);
                $comment_likes = $stmt->fetch()['like_count'];
                ?>
                Likes: <?php echo $comment_likes; ?>
            </p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND comment_id = ?");
                $stmt->execute([$_SESSION['user_id'], $comment['comment_id']]);
                $user_liked_comment = $stmt->fetch() !== false;
                ?>
                <form action="like.php" method="POST">
                    <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                    <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                    <button type="submit" class="btn btn-sm btn-primary like-btn" data-liked="<?php echo $user_liked_comment ? 'true' : 'false'; ?>">
                        <?php echo $user_liked_comment ? 'Unlike' : 'Like'; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php include 'includes/footer.php'; ?>