<?php
session_start();
require_once(__DIR__ . '/lib/functions.php');
require_once(__DIR__ . '/lib/Controller/Medicine.php');
require_once(__DIR__ . '/config/config.php');

if (isset($_SESSION['post']) && isset($_SESSION['week']) && isset($_SESSION['use_time']) && isset($_SESSION['token'])) {
    $medi = new \MyApp\Medicine();
    if (isset($_REQUEST['id'])) {
        $result = $medi->update();
    } else {
        $result = $medi->stock_regist();
    }
} else {
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>在庫登録</title>
    <link rel="stylesheet" href="asset/css/medicine.css">
</head>

<body>
    <?php include(__DIR__ . '/_inc/header.php'); ?>
    <p class="finish_text"><?= h($result); ?></p>
    <p class="btn_box btn_center"><a href="index.php" class="btn back">メニューへ戻る</a></p>
</body>

</html>