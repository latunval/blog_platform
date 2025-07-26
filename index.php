<?php
include 'config/db_connect.php';
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.user_id WHERE posts.status = 'published' ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>
<h2>All Posts</h2>
<div class="row">
    <?php foreach ($posts as $post): ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                    <p class="card-text"><?php echo substr(htmlspecialchars($post['content']), 0, 100); ?>...</p>
                    <p>By: <?php echo htmlspecialchars($post['username']); ?></p>
                    <a href="view_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-primary">Read More</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include 'includes/footer.php'; ?>