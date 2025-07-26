<?php
// File: edit_post.php
session_start();
include 'config/db_connect.php';


if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

if (!isset($_GET['id'])) {
  header("Location: dashboard.php");
  exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch post
$query = "SELECT * FROM posts WHERE id = $id AND user_id = $user_id";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) !== 1) {
  die("Post not found or not authorized.");
}
$post = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $content = mysqli_real_escape_string($conn, $_POST['content']);
  $update = "UPDATE posts SET title = '$title', content = '$content' WHERE id = $id AND user_id = $user_id";
  mysqli_query($conn, $update);
  header("Location: dashboard.php");
}
?>

<div class="container mt-4">
  <h2>Edit Post</h2>
  <form method="POST">
    <input type="text" name="title" class="form-control mb-3" value="<?= htmlspecialchars($post['title']) ?>" required>
    <textarea name="content" rows="5" class="form-control mb-3" required><?= htmlspecialchars($post['content']) ?></textarea>
    <button type="submit" name="update" class="btn btn-primary">Update</button>
  </form>
</div>

<?php
// File: delete_post.php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
  header("Location: login.php");
  exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$delete = "DELETE FROM posts WHERE id = $id AND user_id = $user_id";
mysqli_query($conn, $delete);
header("Location: dashboard.php");
?>

<?php
// File: like_post.php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

$check = mysqli_query($conn, "SELECT * FROM likes WHERE post_id = $post_id AND user_id = $user_id");
if (mysqli_num_rows($check) > 0) {
  // Unlike
  mysqli_query($conn, "DELETE FROM likes WHERE post_id = $post_id AND user_id = $user_id");
} else {
  // Like
  mysqli_query($conn, "INSERT INTO likes (post_id, user_id) VALUES ($post_id, $user_id)");
}
header("Location: view_post.php?id=$post_id");
?>
