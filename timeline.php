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

// コメントデータを読み込む関数
function getComments($postId, $pdo) {
    $sql = 'SELECT * FROM sns_timeline_table WHERE post_id = :post_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/timeline.css">
    <title>FREInet</title>
</head>
<body>
<section id="wrapper">
<section class="header">
    <div class=img><img src="images/logo.png"></div>
</section>
<section class="timelinebox">
    <div class="inner">
    <form action="post_timeline.php" method="post" enctype="multipart/form-data">
       <dl> 
        <dd>
            <div class="flex">
            <div class="postimg">

        <?php
            // ログインしているユーザーのアイコンを表示
            if (!empty($user['profile_img'])) {
                echo "<img src='data/profiles/" . htmlspecialchars($user['profile_img'], ENT_QUOTES, 'UTF-8') . "' alt='プロフィール画像' width='50' style='vertical-align: middle;'>";
            } else {
                echo "<img src='images/default_icon.png' alt='デフォルトアイコン' width='50' style='vertical-align: middle;'>";
            }
            ?>  
            </div>             
        <textarea name="post_content" rows="4" cols="50" placeholder="今の気持ちを投稿しよう" required></textarea></div><dd>
        <dd>
            <ul>
                <li>
            <input type="file" id="post_img" name="post_img"></li>
            <li><input type="submit" value="投稿"></li>
</dd>
    </form>
    </div>
</section>    

    <div class="timeline">
    <?php
// 投稿データを取得するSQLクエリ
$sql = 'SELECT * FROM sns_timeline_table ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 逆順にした行を表示する
foreach ($posts as $post) {
    echo "
    <section class='box'>
    <div class='post'>";
    if ($post['profile_img']) {
        echo "<dl><dt><img src='data/profiles/" . htmlspecialchars($post['profile_img'], ENT_QUOTES, 'UTF-8') . "' alt='プロフィール画像' width='50'></dt>";
    }
    echo "<dd><ul><li><a href='profile.php?id=" . htmlspecialchars($post['user_id'], ENT_QUOTES, 'UTF-8') . "'><strong>" . htmlspecialchars($post['name'], ENT_QUOTES, 'UTF-8') . "</strong></a></li>";
    echo "<li class='timestamp'>" . htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') . "</li></ul></dd></dl>";
    echo "<div class='postarea'><p>" . htmlspecialchars($post['post'], ENT_QUOTES, 'UTF-8') . "</p></div>";
    if ($post['post_img']) {
        echo "<img src='data/uploads/" . htmlspecialchars($post['post_img'], ENT_QUOTES, 'UTF-8') . "' alt='投稿画像'>";
    }
    echo "</div></section>";

    // // コメントを表示
    // $comments = getComments($post['id'], $pdo);
    // echo "<div class='comments'>";
    // foreach ($comments as $comment) {
    //     echo "<div class='comment'>";
    //     echo "<p><strong>" . htmlspecialchars($comment['user_name'], ENT_QUOTES, 'UTF-8') . "</strong>: " . htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8') . "</p>";
    //     echo "<p class='timestamp'>" . htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8') . "</p>";
    //     echo "</div>";
    // }
    // echo "</div>";

    // // コメント投稿フォーム
    // echo "
    // <form action='post_comment.php' method='post'>
    //     <input type='hidden' name='post_id' value='" . htmlspecialchars($post['id'], ENT_QUOTES, 'UTF-8') . "'>
    //     <textarea name='comment_content' rows='2' cols='50' placeholder='コメントを追加' required></textarea>
    //     <button type='submit'>コメント</button>
    // </form>";
}
?>
    </div>
    </div>
</section>


</section>    
</body>
</html>
