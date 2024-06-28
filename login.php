<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/form.css">
    <title>ログインページ</title>
</head>
<body>
<section id="wrapper">
<section class="header">
    <div class=img><img src="images/logo.png"></div>
    <div class="text"><p>FREInetを使うと、起業家、経営者、会社員、同じ志を持つ仲間たちとつながりを深められます。ケータイ、スマートフォンからもアクセスできます。</p></div>
</section>
<section class="box">
    <div class="innner">
    <form action="login_process.php" method="post">
        <dl>
            <dd><input type="text" id="identifier" name="identifier" placeholder="メールアドレスまたは会員ID" required></dd>
            <dd><input type="password" id="password" name="password" placeholder="パスワード" required></dd>
        </dl>
       <button type="submit" name="login">ログイン</button>
       <div class="reset"><a href="#">パスワードを忘れた場合</a></div>
       <div style="background:#eee; height: 1px; margin: 10px 0 20px 0;"></div>
       <div class="regist"><a href="regist.php">新しいアカウント作成</a></div>
    </form>
    </div>
    </section>
    </section>
    <footer>

    </footer>
</body>
</html>
