<?php
include 'config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $user_id]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim(strip_tags($_POST['title'] ?? ''));
    $content = trim(strip_tags($_POST['content'] ?? ''));
    $status = trim(strip_tags($_POST['status'] ?? 'draft'));
    
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, status = ? WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$title, $content, $status, $post_id, $user_id]);
    
    header("Location: dashboard.php");
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<h2>Edit Post</h2>
<form method="POST">
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Content</label>
        <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status">
            <option value="published" <?php echo $post['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
            <option value="draft" <?php echo $post['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update Post</button>
    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include 'includes/footer.php'; ?>
