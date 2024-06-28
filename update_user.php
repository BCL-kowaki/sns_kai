<?php
session_start();

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
$sql = 'SELECT user_id, password, profile_img, name, kana, tel, mail, zipcode, address1, address2, address3 FROM sns_regist_table WHERE user_id = :user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('ユーザーが見つかりません。');
}

$name = $_POST['name'];
$kana = $_POST['kana'];
$tel = $_POST['tel'];
$mail = $_POST['mail'];
$zipcode = $_POST['zipcode'];
$address1 = $_POST['address1'];
$address2 = $_POST['address2'];
$address3 = $_POST['address3'];
$password = $_POST['password'];
$profile_img = $user['profile_img'];

// プロフィール画像のアップロード処理
if (!empty($_FILES['profile_img']['name'])) {
    $target_dir = "data/profiles/";
    $target_file = $target_dir . basename($_FILES['profile_img']['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $new_file_name = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $new_file_name;
    move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_file);
    $profile_img = $new_file_name;
}

// データベースの更新
$sql = 'UPDATE sns_regist_table SET password = :password, profile_img = :profile_img, name = :name, kana = :kana, tel = :tel, mail = :mail, zipcode = :zipcode, address1 = :address1, address2 = :address2, address3 = :address3 WHERE user_id = :user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':password', $password, PDO::PARAM_STR);
$stmt->bindValue(':profile_img', $profile_img, PDO::PARAM_STR);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':kana', $kana, PDO::PARAM_STR);
$stmt->bindValue(':tel', $tel, PDO::PARAM_STR);
$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
$stmt->bindValue(':zipcode', $zipcode, PDO::PARAM_STR);
$stmt->bindValue(':address1', $address1, PDO::PARAM_STR);
$stmt->bindValue(':address2', $address2, PDO::PARAM_STR);
$stmt->bindValue(':address3', $address3, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);

if ($stmt->execute()) {
    // セッションのユーザー情報を更新
    $_SESSION['user'] = [
        'user_id' => $user_id,
        'password' => $password,
        'profile_img' => $profile_img,
        'name' => $name,
        'kana' => $kana,
        'tel' => $tel,
        'mail' => $mail,
        'zipcode' => $zipcode,
        'address1' => $address1,
        'address2' => $address2,
        'address3' => $address3
    ];

    header("Location: profile.php?id=" . $user_id);
} else {
    echo "データの更新に失敗しました。";
}
?>
