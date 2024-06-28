<?php

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

$sql = 'SELECT user_id, password, profile_img, name, kana, tel, mail, zipcode, address1, address2, address3, created_at, updated_at FROM sns_regist_table ORDER BY created_at DESC;';
$stmt = $pdo->prepare($sql);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$output = "";
foreach ($result as $record) {
  $output .= "
    <tr>
      <td>{$record["user_id"]}</td>
      <td>{$record["password"]}</td>
      <td><img src='images/profiles/{$record["profile_img"]}'></td>
      <td>{$record["name"]}</td>
      <td>{$record["kana"]}</td>
      <td>{$record["tel"]}</td>
      <td>{$record["mail,"]}</td>
      <td>{$record["zipcode"]}</td>
      <td>{$record["address1"]}</td>
      <td>{$record["address2"]}</td>
      <td>{$record["address3"]}</td>
      <td>{$record["created_at"]}</td>
      <td>{$record["updated_at"]}</td>
    </tr>
  ";
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/confirm.css">
  <title>登録一覧</title>
</head>
<body>
<section id="wrapper">
<section class="header">
    <div class=img><img src="images/logo.png"></div>
    <div class="text"><p>登録者一覧</p></div>
    </section>
<section class="box">
    <div class="innner">
    <table>
        <thead>
            <tr>
                <th>会員ID</th>
                <th>パスワード</th>
                <th>アイコン</th>
                <th>名前</th>
                <th>かな</th>
                <th>電話番号</th>
                <th>メアド</th>
                <th>郵便番号</th>
                <th>都道府県</th>
                <th>住所1</th>
                <th>住所2</th>
                <th>最終更新日</th>
                <th>登録日</th>
            </tr>
        </thead>
        <tbody>
        <?= $output ?>
        </tbody>
    </table>
    <div class="button_area">
    <button id="export">エクスポート</button>
    <input type="file" id="importFile" accept=".csv" style="display: none;">
    <button id="import">インポート</button>
    <div class="login"><a href="login.php">ログインページへ</a></div>
    </div>  

    </div>
    </section>
    </section>   

    <script>
        // CSVをエクスポートする関数
        function exportCSV() {
            var data = getTableData();
            var csvContent = "data:text/csv;charset=utf-8,";

            data.forEach(function(rowArray, index) {
                if (index === 0) {
                    return; // 最初の行はスキップ
                }
                let row = rowArray.join(",");
                csvContent += row + "\r\n";
            });

            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "data.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // テーブルデータを取得する関数
        function getTableData() {
            var table = document.querySelector("table");
            var rows = table.querySelectorAll("tr");
            var data = [];

            rows.forEach(function(row) {
                var rowData = [];
                var cells = row.querySelectorAll("td, th");

                cells.forEach(function(cell) {
                    rowData.push(cell.textContent);
                });

                data.push(rowData);
            });

            return data;
        }

        // CSVをインポートする関数
        function handleFileSelect(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var contents = e.target.result;
                    var rows = contents.split("\n");
                    var table = document.querySelector("tbody");
                    table.innerHTML = ''; // テーブルの内容をクリア
                    rows.forEach(function(row, index) {
                        if (index === 0) {
                            return; // 最初の行はスキップ
                        }
                        var cols = row.split(",");
                        if (cols.length > 1 && row.trim() !== "") { // 空行を無視
                            var newRow = table.insertRow();
                            for (var i = 0; i < cols.length; i++) {
                                var newCell = newRow.insertCell();
                                newCell.textContent = cols[i].trim(); // 余分な空白を削除
                            }
                        }
                    });

                    // 新しいデータをCSVファイルに保存
                    saveCSV(contents);
                };
                reader.readAsText(file);
            }
        }

        // 新しいデータをCSVファイルに保存する関数
        function saveCSV(contents) {
            fetch('save_csv.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ data: contents })
            }).then(response => response.text()).then(data => {
                console.log(data);
            }).catch(error => console.error('Error:', error));
        }

        // インポートボタンにイベントリスナーを追加
        document.getElementById("import").addEventListener("click", function() {
            document.getElementById("importFile").click();
        });

        // ファイル選択時にhandleFileSelect関数を呼び出す
        document.getElementById("importFile").addEventListener("change", handleFileSelect);
    </script>
</body>
</html>
