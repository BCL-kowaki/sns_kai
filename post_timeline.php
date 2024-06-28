<?php
session_start();

// エラーレポートを有効にする
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB接続情報
$dbn = 'mysql:dbname=gs_d15_06;charset=utf8mb4;port=3306;host=localhost';
$user = 'root';
$pwd = '';

// データベース接続
try {
    $pdo = new PDO($dbn, $user, $pwd);
} catch (PDOException $e) {
    echo json_encode(["db error" => "{$e->getMessage()}"]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ユーザー情報を取得するSQLクエリ
$sql = 'SELECT * FROM sns_regist_table WHERE user_id = :user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('ユーザーが見つかりません。');
}

$post_content = $_POST['post_content'];
$timestamp = date("Y-m-d H:i:s");
$post_img = '';

// 投稿画像のアップロード処理
if (!empty($_FILES['post_img']['name'])) {
    $target_dir = "data/uploads/";
    $target_file = $target_dir . basename($_FILES['post_img']['name']);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $new_file_name = uniqid() . '.' . $fileType;
    $target_file = $target_dir . $new_file_name;
    move_uploaded_file($_FILES['post_img']['tmp_name'], $target_file);
    $post_img = $new_file_name;
}

// データベースに投稿を追加するSQLクエリ
$sql = 'INSERT INTO sns_timeline_table (post_id, user_id, name, post, created_at, post_img, profile_img) VALUES (NULL, :user_id, :name, :post, now(), :post_img, :profile_img)';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user['user_id'], PDO::PARAM_STR);
$stmt->bindValue(':name', $user['name'], PDO::PARAM_STR);
$stmt->bindValue(':post', $post_content, PDO::PARAM_STR);
$stmt->bindValue(':post_img', $post_img, PDO::PARAM_STR);
$stmt->bindValue(':profile_img', $user['profile_img'], PDO::PARAM_STR);
if ($stmt->execute()) {
    header("Location: timeline.php");
}
?>
