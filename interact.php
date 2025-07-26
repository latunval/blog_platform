<?php
include 'config/db_connect.php';
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please log in to interact']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT) ?: null;
    $comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT) ?: null;
    $content = trim(strip_tags($_POST['content'] ?? ''));

    if (!$action || ($action !== 'comment' && $action !== 'like')) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }

    if ($action === 'comment') {
        if ($post_id && $content) {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
            if ($stmt->execute([$post_id, $user_id, $content])) {
                $stmt = $pdo->prepare("SELECT COUNT(*) as comment_count FROM comments WHERE post_id = ?");
                $stmt->execute([$post_id]);
                $comment_count = $stmt->fetch()['comment_count'];
                echo json_encode(['status' => 'success', 'type' => 'comment', 'comment_count' => $comment_count]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add comment']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid post ID or comment content']);
        }
    } elseif ($action === 'like') {
        if (($post_id === null && $comment_id === null) || ($post_id !== null && $comment_id !== null)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input: Exactly one of post_id or comment_id must be provided']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND (post_id = ? OR comment_id = ?)");
        $stmt->execute([$user_id, $post_id, $comment_id]);
        $existing_like = $stmt->fetch();

        if ($existing_like) {
            $stmt = $pdo->prepare("DELETE FROM likes WHERE like_id = ?");
            if ($stmt->execute([$existing_like['like_id']])) {
                $count_stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM likes WHERE " . ($post_id ? "post_id = ?" : "comment_id = ?"));
                $count_stmt->execute([$post_id ?: $comment_id]);
                $like_count = $count_stmt->fetch()['like_count'];
                echo json_encode(['status' => 'success', 'type' => 'like', 'action' => 'unlike', 'like_count' => $like_count]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to remove like']);
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id, comment_id) VALUES (?, ?, ?)");
            if ($stmt->execute([$user_id, $post_id, $comment_id])) {
                $count_stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM likes WHERE " . ($post_id ? "post_id = ?" : "comment_id = ?"));
                $count_stmt->execute([$post_id ?: $comment_id]);
                $like_count = $count_stmt->fetch()['like_count'];
                echo json_encode(['status' => 'success', 'type' => 'like', 'action' => 'like', 'like_count' => $like_count]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add like']);
            }
        }
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
}
?>