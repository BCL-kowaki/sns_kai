<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$comment_content = $_POST['comment_content'];
$post_id = $_POST['post_id']; // 投稿IDを取得
$timestamp = date("Y-m-d H:i:s");

$filepath = 'data/comments.csv';

$write_data = "{$post_id},{$comment_content},{$timestamp},{$user[0]}";

$file = fopen($filepath, 'a');
flock($file, LOCK_EX);

fwrite($file, $write_data . "\n");

flock($file, LOCK_UN);
fclose($file);

header("Location: timeline.php");
?>
