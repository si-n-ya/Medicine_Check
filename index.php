<?php
session_start();
require_once(__DIR__ . '/lib/functions.php');
require_once(__DIR__ . '/lib/Controller/Medicine.php');
require_once(__DIR__ . '/config/config.php');

$today = new DateTime('today');
$today = $today->format('Y-m-d');
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>メニュー</title>
    <link rel="stylesheet" href="asset/css/medicine.css">
</head>

<body>
    <!-- header読み込み -->
    <?php include(__DIR__ . '/_inc/header.php'); ?>
    <div class="top_container">
        <main class="container">
            <h1 class="btn_center main_menu_title">メインメニュー</h1>
            <ul class="list_container">
                <li class="btn_center">
                    <a href="regist_form.php" class="btn btn_shape">お薬登録</a>
                </li>
                <li class="btn_center">
                    <a href="medicine_check.php?id=<?= h($today); ?>" class="btn btn_shape">お薬服用チェック</a>
                </li>
                <li class="btn_center">
                    <a href="list.php" class="btn btn_shape">登録リスト</a>
                </li>
                <li class="btn_center">
                    <a href="history.php" class="btn btn_shape">お薬服用履歴</a>
                </li>
            </ul>
        </main>
        <div>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
            <script src="asset/js/stock.js"></script>
</body>

</html>