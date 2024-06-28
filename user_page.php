<?php
session_start();

// DB接続情報
$dbn ='mysql:dbname=gs_d15_06;charset=utf8mb4;port=3306;host=localhost';
$user = 'root';
$pwd = '';

// データベース接続
try {
    $pdo = new PDO($dbn, $user, $pwd);
} catch (PDOException $e) {
    echo json_encode(["db error" => "{$e->getMessage()}"]);
    exit();
}

// ログイン済みのユーザーIDをセッションから取得
if (!isset($_SESSION['user_id'])) {
    echo 'ユーザーがログインしていません。';
    exit();
}
$user_id = $_SESSION['user_id'];

// ユーザー情報を取得するSQLクエリ
$sql = 'SELECT user_id, password, profile_img, name, kana, tel, mail, zipcode, address1, address2, address3 FROM sns_regist_table WHERE user_id = :user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR); // ここで動的にユーザーIDを指定
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('ユーザーが見つかりません。');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/user.css">
    <title>ユーザーページ</title>
</head>
<body>
<section id="wrapper">
<section class="header">
    <div class="img"><img src="images/logo.png"></div>
</section>
<section class="box">
    <div class="inner">
        <h1>基本データ</h1>
        <section class="profiles">
        <div class="profilesimg"><img src="data/profiles/<?php echo htmlspecialchars($user['profile_img']); ?>" required></div>
        <form action="update_user.php" method="post" enctype="multipart/form-data">
            <dl class="profilesimg">
                <dd><label for="profile_img"></label>
                <input type="file" id="profile_img" name="profile_img"></dd>
            </dl> 
            <dl>
                <dt><label for="name">会員ID</label></dt>
                <dd><input type="text" id="newId" name="newId" value="<?php echo htmlspecialchars($user['user_id']); ?>" required readonly></dd>
            </dl>  
            <dl>
                <dt><label for="password">パスワード</label></dt>
                <dd><input type="password" id="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" required></dd>
            </dl>  
            <dl>
                <dt><label for="name">名前</label></dt>
                <dd><input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required></dd>
            </dl>  
            <dl>
                <dt><label for="kana">かな</label></dt>
                <dd><input type="text" id="kana" name="kana" value="<?php echo htmlspecialchars($user['kana']); ?>" required></dd>
            </dl>  
            <dl>
                <dt><label for="tel">電話番号</label></dt>
                <dd><input type="text" id="tel" name="tel" value="<?php echo htmlspecialchars($user['tel']); ?>" required></dd>
            </dl>  
            <dl>
                <dt><label for="mail">メールアドレス</label></dt>
                <dd><input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($user['mail']); ?>" required></dd>
            </dl>  
            <dl>
                <dt><label for="zipcode">郵便番号</label></dt>
                <dd><input type="text" id="zipcode" name="zipcode" value="<?php echo htmlspecialchars($user['zipcode']); ?>" required></dd>
            </dl>  
            <dl>
                <dt><label for="address1">都道府県</label></dt>
                <dd><input type="text" id="address1" name="address1" value="<?php echo htmlspecialchars($user['address1']); ?>" required></dd>
            </dl>  
            <dl>
                <dt><label for="address2">市区町村</label></dt>
                <dd><input type="text" id="address2" name="address2" value="<?php echo htmlspecialchars($user['address2']); ?>" required></dd>
            </dl>  
            <dl>
                <dt><label for="address3">番地</label></dt>
                <dd><input type="text" id="address3" name="address3" value="<?php echo htmlspecialchars($user['address3']); ?>"></dd>
            </dl>  
            <input type="submit" value="更新">
        </form>
        </section>
        <div class="logout"><a href="logout.php">ログアウト</a></div>
        <div class="timeline"><a href="timeline.php">タイムラインを見る</a></div>
    </div>
</section>
</body>
</html>
