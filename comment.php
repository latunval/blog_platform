<?php
include 'config/db_connect.php';
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "Unauthorized.";
    exit;
}
$user_id = $_SESSION['user_id'];
$post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
$content = trim(htmlspecialchars($_POST['content'] ?? '', ENT_QUOTES, 'UTF-8'));

// Validate content length (minimum 1 character, after trimming)
if ($post_id && strlen($content) > 0) {
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $content]);
    echo "success";
} else {
    echo "Invalid input.";
}
?>