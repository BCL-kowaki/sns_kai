<?php

// POSTデータ確認
if (
    !isset($_POST['name']) || $_POST['name'] === '' ||
    !isset($_POST['kana']) || $_POST['kana'] === ''||
    !isset($_POST['tel']) || $_POST['tel'] === '' ||
    !isset($_POST['mail']) || $_POST['mail'] === '' ||
    !isset($_POST['zipcode']) || $_POST['zipcode'] === ''||
    !isset($_POST['address1']) || $_POST['address1'] === '' ||
    !isset($_POST['address2']) || $_POST['address2'] === ''||
    !isset($_POST['address3']) || $_POST['address3'] === '' 
  ) {
    exit('データが足りません');
  }

// データの受け取り
$name = $_POST['name'];
$kana = $_POST['kana'];
$tel = $_POST['tel'];
// $mail = $_POST['mail'];
$mail = filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL);
// var_dump($mail);
// exit();
$zipcode = $_POST['zipcode'];
$address1 = $_POST['address1'];
$address2 = $_POST['address2'];
$address3 = $_POST['address3'];
$profile_img = ''; // 初期値を空に設定

$errors = [];
if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email is empty or invalid';

// DB接続
// 各種項目設定
$dbn ='mysql:dbname=gs_d15_06;charset=utf8mb4;port=3306;host=localhost';
$user = 'root';
$pwd = '';

// SQL作成&実行
try {
    $pdo = new PDO($dbn, $user, $pwd);
  } catch (PDOException $e) {
    echo json_encode(["db error" => "{$e->getMessage()}"]);
    exit();
  }

// プロフィール画像のアップロード処理
if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "data/profiles/";

    // ディレクトリが存在しない場合は作成
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // 元のファイル名を使用する
    $original_file_name = basename($_FILES['profile_img']['name']);
    $target_file = $target_dir . $original_file_name;

    // ファイル名の衝突を避けるために、同名ファイルが存在する場合はファイル名を変更する
    $file_counter = 1;
    while (file_exists($target_file)) {
        $file_info = pathinfo($original_file_name);
        $file_name = $file_info['filename'] . '_' . $file_counter . '.' . $file_info['extension'];
        $target_file = $target_dir . $file_name;
        $file_counter++;
    }

    // ファイルを指定のディレクトリに移動
    if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_file)) {
        $profile_img = basename($target_file);
    } else {
        echo "ファイルのアップロードに失敗しました。";
        exit();
    }
} else {
    echo "ファイルのアップロードに失敗しました。エラーコード: " . $_FILES['profile_img']['error'];
    exit();
}

// ディレクトリとファイルのパスを定義
$directory = 'data/';
$filename = 'data.csv';
$filepath = $directory . $filename;

// ディレクトリが存在しない場合は作成
if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}

// 最新のIDを取得
function getLatestId($filepath) {
    if (!file_exists($filepath)) {
        return 'ID000000'; // 初期値
    }

    $file = fopen($filepath, 'r');
    flock($file, LOCK_SH);

    $latestId = 'ID000000';
    while ($line = fgetcsv($file)) {
        if (isset($line[0]) && preg_match('/^ID\d+$/', $line[0])) {
            $latestId = $line[0];
        }
    }

    flock($file, LOCK_UN);
    fclose($file);

    return $latestId;
}

// 新しいIDを生成
function generateNewId($latestId) {
    $number = (int)substr($latestId, 2) + 1;
    return 'ID' . str_pad($number, 6, '0', STR_PAD_LEFT);
}

// 6桁のランダムパスワードを生成
function generatePassword() {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < 6; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

$newId = generateNewId(getLatestId($filepath));
$password = generatePassword();

// データの書き込み
$sql = 'INSERT INTO sns_regist_table(user_id, password, profile_img, name, kana, tel, mail, zipcode, address1, address2, address3, created_at, updated_at) 
        VALUES (:user_id, :password, :profile_img, :name, :kana, :tel, :mail, :zipcode, :address1, :address2, :address3, now(), now())';
$stmt = $pdo->prepare($sql);

  // バインド編集を設定
  $stmt->bindValue(':user_id', $newId, PDO::PARAM_STR);
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
  
  // SQL実行（実行に失敗すると `sql error ...` が出力される）
  try {
    $status = $stmt->execute();
  } catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
  }


$to = $_POST['mail'];
$title = "ご登録ありがとうございます。";
$headers = 'From: takuyakowaki0412@icloud.com' . "\r\n" .
           'Reply-To: takuyakowaki0412@icloud.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion() . "\r\n" .
           'Content-Type: text/plain; charset=UTF-8'; // 文字エンコードの指定
$message = "あなたの会員ID: " . $newId . "\nパスワード: " . $password . "です。";
mail($to, $title, $message, $headers);
 // SQL実行の処理
 header("Location:login.php");
 exit();
?>
