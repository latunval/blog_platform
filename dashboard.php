<?php
include 'config/db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Total posts
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM posts WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_posts = $stmt->fetch()['total'];

// Total comments
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM comments WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_comments = $stmt->fetch()['total'];

// Total post likes
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM likes WHERE post_id IN (SELECT post_id FROM posts WHERE user_id = ?)");
$stmt->execute([$user_id]);
$total_post_likes = $stmt->fetch()['total'];

// Total comment likes
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM likes WHERE comment_id IN (SELECT comment_id FROM comments WHERE user_id = ?)");
$stmt->execute([$user_id]);
$total_comment_likes = $stmt->fetch()['total'];

// User's posts
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>
<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Posts</h5>
                <p class="card-text"><?php echo $total_posts; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Comments</h5>
                <p class="card-text"><?php echo $total_comments; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Post Likes</h5>
                <p class="card-text"><?php echo $total_post_likes; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Comment Likes</h5>
                <p class="card-text"><?php echo $total_comment_likes; ?></p>
            </div>
        </div>
    </div>
</div>
<h3 class="mt-4">Your Posts</h3>
<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?php echo htmlspecialchars($post['title']); ?></td>
                <td><?php echo $post['status']; ?></td>
                <td>
                    <a href="view_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-primary">View</a>
                    <a href="edit.php?id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include 'includes/footer.php'; ?>