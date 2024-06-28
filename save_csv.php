<?php
$directory = 'data/';
$filename = 'data.csv';
$filepath = $directory . $filename;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true)['data'];
    
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    file_put_contents($filepath, $data);
    echo "ファイルを保存しました";
} else {
    echo "無効なリクエストです";
}
?>
