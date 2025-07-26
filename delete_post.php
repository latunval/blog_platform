<?php
include 'config/db_connect.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}
$stmt = $pdo->prepare("DELETE FROM posts WHERE post_id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
header("Location: dashboard.php");
exit;
?>