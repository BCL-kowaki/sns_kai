<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/form.css">
<title>登録フォーム</title>
</head>

<body>
<!-- formにはaction, method, nameを設定！ -->
<section id="wrapper">
<section class="header">
    <div class=img><img src="images/logo.png"></div>
    <div class="text"><p>FREInetを使うと、起業家、経営者、会社員、同じ志を持つ仲間たちとつながりを深められます。ケータイ、スマートフォンからもアクセスできます。</p></div>
</section>
<section class="box">
    <div class="innner">
  <form action="regist_create.php" enctype="multipart/form-data" method="POST">
    <dl> 
          <dd><input type="text" name="name" id="name" value="" placeholder="名前（フルネーム）"></dd>
          <dd><input type="text" name="kana" id="kana" value="" placeholder="ふりがな"></dd>
          <dd><input id="tel" type="number" name="tel" value="" placeholder="電話番号"></dd>
          <dd><input id="mail" type="text" name="mail" value="" placeholder="メールアドレス"></dd>
          <dd>
            <ul>
            <li><input id="zipcode" type="number" name="zipcode" placeholder="郵便番号" value="">
            <button id="search" type="button">住所検索</button>
        <p id="error"></p></li>
        <li><input id="address1" type="text" name="address1" placeholder="都道府県" value=""></li>
        <li> <input id="address2" type="text" name="address2" placeholder="市区町村" value=""></li>
        <li><input id="address3" type="text" name="address3" placeholder="番地" value=""></li>
      </ul>
      </dd>
        <dd>
            <input type="file" id="profile_img" name="profile_img" placeholder="アイコン画像" >
        </dd>
    </dl>
    <button id="regist">アカウント登録</button>
  </form>
  </div>
</section>


</section>
<footer>

</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script> 
<script src="js/autoKana.js" defer></script>
<script src="js/script.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/fetch-jsonp@1.1.3/build/fetch-jsonp.min.js"></script> 
<script>
let search = document.getElementById('search');
search.addEventListener('click', ()=>{
    
    let api = 'https://zipcloud.ibsnet.co.jp/api/search?zipcode=';
    let error = document.getElementById('error');
    let input = document.getElementById('zipcode');
    let address1 = document.getElementById('address1');
    let address2 = document.getElementById('address2');
    let address3 = document.getElementById('address3');
    let param = input.value.replace("-",""); //入力された郵便番号から「-」を削除
    let url = api + param;
    
    fetchJsonp(url, {
        timeout: 10000, //タイムアウト時間
    })
    .then((response)=>{
        error.textContent = ''; //HTML側のエラーメッセージ初期化
        return response.json();  
    })
    .then((data)=>{
        if(data.status === 400){ //エラー時
            error.textContent = data.message;
        }else if(data.results === null){
            error.textContent = '郵便番号から住所が見つかりませんでした。';
        } else {
            address1.value = data.results[0].address1;
            address2.value = data.results[0].address2;
            address3.value = data.results[0].address3;
        }
    })
    .catch((ex)=>{ //例外処理
        console.log(ex);
    });
}, false);

</script>
</body>
</html>