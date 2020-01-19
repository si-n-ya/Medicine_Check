<?php
session_start();

require_once(__DIR__ . "/lib/Controller/Medicine.php");
require_once(__DIR__ . "/lib/functions.php");
require_once(__DIR__ . "/config/config.php");

$medicine = new \MyApp\Medicine();

$get_all = $medicine->get_all();
$page =  $medicine->page();
$max_page = $medicine->max_page();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登録したお薬一覧</title>
    <link rel="stylesheet" href="asset/css/medicine.css">
</head>
<body>
<?php include(__DIR__ . '/_inc/header.php'); ?>
    <div class="bg_image_list bg_help">
    <div class="bg_mask">
    <main class="container list_box">
    <div class="flex_container">
    <h1 class="title_shape title_white">登録リスト</h1>
    <p class="back_box btn_center">
    <a href="index.php" class="btn back">メニューへ戻る</a>
    </p>
</div> <!-- .flex_container -->
    <dl>
    <?php foreach ($get_all as $get_one): ?>
    <div class="medi_list medi_<?= h($get_one['item_id']); ?>" data-id="<?= h($get_one['item_id']); ?>">
    <a href="regist_form.php?id=<?= h($get_one['item_id']); ?>">
    <dt class="list_dt">
    <?= h($get_one['name']); ?>
    </dt>
    <!-- <div class="listbox_center"> -->
        <dd class="list_dd">
    <p>残り数： <?= h($get_one['stock_num']); ?>
    <?= h($get_one['unit']); ?></p>
    <p>1回 <?= h($get_one['use_num']); ?> <?= h($get_one['unit']); ?></p>
    </dd>
    </a>
    <p class="delete_box">
    <button type="button" class="btn delete_btn">削除</button>
    </p>
<!-- </div> -->
    </div>
    <?php endforeach; ?>
    </dl>
    <ul class="page_list_box">
    <?php if ($page > 1): ?>
      <li class="page_list">
          <a href="list.php?page=<?= h($page - 1); ?>" class="page_link">前のページ</a>
      </li>
    <?php endif; ?>

    <?php if ($page < $max_page): ?>
      <li class="page_list">
          <a href="list.php?page=<?= h($page + 1); ?>" class="page_link">次のページ</a>
      </li>
    <?php endif; ?>
    </ul>
    </main>
    </div>
    </div>
    <input type="hidden" class="token" name="token" value="<?= h($_SESSION['token']); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="asset/js/stock.js"></script>
</body>
</html>