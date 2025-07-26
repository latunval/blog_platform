<?php
include 'config/db_connect.php';
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];
$post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT) ?: null;
$comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT) ?: null;

// Enforce exactly one of post_id or comment_id
if (($post_id === null && $comment_id === null) || ($post_id !== null && $comment_id !== null)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input: Exactly one of post_id or comment_id must be provided']);
    exit;
}

// Check if user already liked (fix: only check the relevant column)
if ($post_id !== null) {
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ? AND comment_id IS NULL");
    $stmt->execute([$user_id, $post_id]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND comment_id = ? AND post_id IS NULL");
    $stmt->execute([$user_id, $comment_id]);
}
$existing_like = $stmt->fetch();

if ($existing_like) {
    // Unlike: Remove the existing like
    $stmt = $pdo->prepare("DELETE FROM likes WHERE like_id = ?");
    if ($stmt->execute([$existing_like['like_id']])) {
        echo json_encode(['status' => 'success', 'action' => 'unlike']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove like']);
    }
} else {
    // Like: Insert new like
    $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id, comment_id) VALUES (?, ?, ?)");
    if ($stmt->execute([$user_id, $post_id, $comment_id])) {
        echo json_encode(['status' => 'success', 'action' => 'like']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add like']);
    }
}
?>