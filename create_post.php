<?php
include 'config/db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$post = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $user_id]);
    $post = $stmt->fetch();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim(strip_tags($_POST['title'] ?? ''));
    $content = trim(strip_tags($_POST['content'] ?? ''));
    $status = trim(strip_tags($_POST['status'] ?? ''));
    if ($post) {
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, status = ? WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $status, $_GET['id'], $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $content, $status]);
    }
    header("Location: dashboard.php");
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<h2><?php echo $post ? 'Edit Post' : 'Create Post'; ?></h2>
<form method="POST">
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" value="<?php echo $post ? htmlspecialchars($post['title']) : ''; ?>" required>
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Content</label>
        <textarea class="form-control" id="content" name="content" rows="5" required><?php echo $post ? htmlspecialchars($post['content']) : ''; ?></textarea>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status">
            <option value="published" <?php echo $post && $post['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
            <option value="draft" <?php echo $post && $post['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary"><?php echo $post ? 'Update' : 'Create'; ?></button>
</form>
<?php include 'includes/footer.php'; ?>