<?php
session_start();
require_once(__DIR__ . '/lib/functions.php');
require_once(__DIR__ . '/lib/Controller/Medicine.php');
require_once(__DIR__ . '/config/config.php');

// 登録した薬の編集の時
if (isset($_REQUEST['id'])) {
    $medi = new \MyApp\Medicine();
    $conf = $medi->update_conf();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Validation = new \MyApp\Validation();
    $error = [];
    // 名前のバリデーション
    $error['name'] = $Validation->validateName();
    // 曜日のバリデーション
    $error['week'] = $Validation->validateWeek();
    // 時間のバリデーション
    $error['time'] = $Validation->validateTime();
    // 日付のバリデーション
    $error['get_date'] = $Validation->validateGetDate();
    // 服用する数のバリデーション
    $error['use_num'] = $Validation->validateUseNum();
    // 在庫数のバリデーション
    $error['stock_num'] = $Validation->validateStockNum();
    // エラーがない時
    if (empty($error['name']) && empty($error['week']) && empty($error['time']) && empty($error['get_date']) && empty($error['use_num']) && empty($error['stock_num'])) {
        $_SESSION['post'] = $_POST;
        // 登録した薬の編集の時
        if (isset($_REQUEST['id'])) {
            header('Location: regist.php?id=' . $_REQUEST['id']);
            exit;
        // 薬の登録の時
        } else {
            header('Location: regist.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php if (isset($_REQUEST['id'])): ?>
    <title>編集確認</title>
    <?php else: ?>
    <title>お薬登録</title>
    <?php endif; ?>
    <link rel="stylesheet" href="asset/css/medicine.css">
</head>

<body>
    <?php include(__DIR__ . '/_inc/header.php'); ?>
    <main class="container form_container margin_top">
        <?php if (isset($_REQUEST['id'])): ?>
        <div class="flex_container">
            <h1 class="title_shape conf_title_black">登録した薬の編集</h1>
            <p class="back_box btn_right">
                <a href="list.php" class="btn back">登録リストへ戻る</a>
            </p>
        </div> <!-- .flex_container -->
        <?php else: ?>
        <div class="flex_container">
            <h1 class="title_shape regist_title_black">お薬登録</h1>
            <p class="back_box btn_right">
                <a href="index.php" class="btn back">メニューへ戻る</a>
            </p>
        </div> <!-- .flex_container -->
        <?php endif; ?>
        <form action="" method="post" class="form margin_top">
            <!-- 薬の名前 -->
            <div class="d_container flexiblebox">
                <dt class="dt <?= isset($_REQUEST['id'])? 'conf_dt_bg': 'regist_dt_bg'; ?>"><label
                        for="name">薬の名前</label></dt>
                <dd class="dd">
                    <input type="text" name="name" id="name" class="input_text"
                        value="<?= isset($_REQUEST['id']) ? h($conf['name']): h($_POST['name']); ?>">
                    <?php if ($error['name'] === 'blank'): ?>
                    <p class="error">※名前を入力してください</p>
                    <?php endif; ?>
                </dd>
            </div> <!-- .flexiblebox -->
            <!-- 服用する曜日 -->
            <div class="d_container flexiblebox">
                <dt class="dt <?= isset($_REQUEST['id'])? 'conf_dt_bg': 'regist_dt_bg'; ?>">服用する曜日</dt>
                <dd class="dd week_dd">
                    <!-- 薬の編集 -->
                    <?php if (isset($_REQUEST['id'])): ?>
                    <?php
                     $get_week = $conf['weeks'];
                     $week = explode(',', $get_week);
                     $week_array = ['日', '月', '火', '水', '木', '金', '土'];
                    ?>
                    <?php for ($i=0; $i < 7; $i++): ?>
                    <div class="check_layout">
                        <input type="checkbox" name="week[]" id="<?= $week_array[$i]; ?>" class="regist_week_check"
                            value="<?= $i; ?>" <?php foreach ($week as $week_one) {
                        if ($week_one == $i) {
                            echo 'checked';
                        }
                    } ?>>
                        <label for="<?= $week_array[$i]; ?>">
                            <?= $week_array[$i]; ?>曜
                        </label>
                    </div> <!-- .check_layout -->
                    <?php endfor; ?>
                    <!-- 薬の登録 -->
                    <?php else: ?>
                    <?php
                     $week = ['日', '月', '火', '水', '木', '金', '土'];
                    ?>
                    <?php for ($i=0; $i < 7; $i++): ?>
                    <div class="check_layout">
                        <input type="checkbox" name="week[]" id="<?= $week[$i]; ?>" class="regist_week_check"
                            value="<?= $i; ?>" <?php foreach ($_POST['week'] as $week_value) {
                        if ($week_value == $i) {
                            echo 'checked';
                        }
                    } ?>>
                        <label for="<?= $week[$i]; ?>"><?= $week[$i]; ?>曜</label>
                    </div> <!-- .check_layout -->
                    <?php endfor; ?>
                    <?php endif; ?>
                    <div class="all_check_box">
                        <input type="checkbox" id="all_check" class="all_check">
                        <label for="all_check">全ての曜日</label>
                    </div> <!-- .all_check_box -->
                    <?php if ($error['week'] === 'blank'): ?>
                    <p class="error">※服用する曜日を選択してください</p>
                    <?php endif; ?>
                </dd>
            </div> <!-- .flexiblebox -->
            <!-- 服用する時刻 -->
            <div class="d_container flexiblebox">
                <dt class="dt <?= isset($_REQUEST['id'])? 'conf_dt_bg': 'regist_dt_bg'; ?>">服用する時刻</dt>
                <dd class="dd">
                    <!-- 薬の編集 -->
                    <?php if (isset($_REQUEST['id'])): ?>
                    <?php
                    $get_time = $conf['time'];
                    $time = explode(',', $get_time);
                    ?>
                    <?php for ($i=0; $i < 24; $i++): ?>
                    <div class="check_layout">
                        <input type="checkbox" name="time[]" id="<?= $i; ?>" value="<?= $i; ?>" <?php foreach ($time as $time_one) {
                        if ($time_one == $i) {
                            echo 'checked';
                        }
                    } ?>>
                        <label for="<?= $i; ?>"><?= $i; ?>:00</label>
                    </div> <!-- .check_layout -->
                    <?php endfor; ?>
                    <!-- 薬の登録 -->
                    <?php else: ?>
                    <?php for ($i=0; $i < 24; $i++): ?>
                    <div class="check_layout">
                        <input type="checkbox" name="time[]" id="<?= $i; ?>" value="<?= $i; ?>" <?php foreach ($_POST['time'] as $time_value) {
                        if ($time_value == $i) {
                            echo 'checked';
                        }
                    } ?>>
                        <label for="<?= $i; ?>"><?= $i; ?>:00</label>
                    </div> <!-- .check_layout -->
                    <?php endfor; ?>
                    <?php endif; ?>
                    <?php if ($error['time']): ?>
                    <p class="error">※服用する時刻を選択してください</p>
                    <?php endif; ?>
                </dd>
            </div> <!-- .flexiblebox -->
            <!-- 薬を使い始める日付 -->
            <div class="d_container flexiblebox">
                <dt class="dt <?= isset($_REQUEST['id'])? 'conf_dt_bg': 'regist_dt_bg'; ?>"><label
                        for="get_date">薬を使い始める日付</label>
                </dt>
                <dd class="dd">
                    <input type="date" id="get_date" class="input_text" name="get_date"
                        value="<?= isset($_REQUEST['id']) ? h($conf['get_date']): h($_POST['get_date']); ?>">
                    <?php if ($error['get_date'] === 'blank'): ?>
                    <p class="error">※薬を処方された日付を入力してください</p>
                    <?php elseif ($error['get_date'] === 'not_format'): ?>
                    <p class="error">※YYYY-mm-dd の形式で入力してください</p>
                    <?php endif; ?>
                </dd>
            </div> <!-- .flexiblebox -->
            <!-- 一回に服用する量 -->
            <div class="d_container flexiblebox">
                <dt class="dt <?= isset($_REQUEST['id'])? 'conf_dt_bg': 'regist_dt'; ?>"><label
                        for="use_num">一回に服用する量</label></dt>
                <dd class="dd">
                    <!-- 薬の編集 -->
                    <?php if (isset($_REQUEST['id'])): ?>
                    <input type="text" name="use_num" class="input_text" value="<?= h($conf['use_num']); ?>">
                    <select name="unit" id="unit" class="select">
                        <option value="錠" <?= $conf['unit'] === '錠' ? 'selected': ''; ?>>錠</option>
                        <option value="g" <?= $conf['unit'] === 'g' ? 'selected': ''; ?>>g</option>
                        <option value="ml" <?= $conf['unit'] === 'ml' ? 'selected': ''; ?>>ml</option>
                        <option value="包" <?= $conf['unit'] === '包' ? 'selected': ''; ?>>包</option>
                    </select>
                    <!-- 薬の登録 -->
                    <?php else: ?>
                    <input type="text" name="use_num" class="input_text" value="<?= h($_POST['use_num']); ?>">
                    <select name="unit" class="select">
                        <option value="錠" <?= $_POST['unit'] == '錠'? 'selected': ''; ?>>錠</option>
                        <option value="g" <?= $_POST['unit'] == 'g'? 'selected': ''; ?>>g</option>
                        <option value="ml" <?= $_POST['unit'] == 'ml'? 'selected': ''; ?>>ml</option>
                        <option value="包" <?= $_POST['unit'] == '包'? 'selected': ''; ?>>包</option>
                    </select>
                    <?php endif; ?>
                    <?php if ($error['use_num'] === 'blank'): ?>
                    <p class="error">※一回に服用する量を入力してください</p>
                    <?php elseif ($error['use_num'] === 'not_num'): ?>
                    <p class="error">*数字で入力してください</p>
                    <?php endif; ?>
                </dd>
            </div> <!-- .flexiblebox -->
            <!-- 在庫数 -->
            <div class="flexiblebox">
                <dt class="dt <?= isset($_REQUEST['id'])? 'conf_dt_bg': 'regist_dt_bg'; ?>"><label
                        for="stock_num">在庫数</label></dt>
                <dd class="dd">
                    <input type="text" name="stock_num" id="stock" class="input_text" placeholder="(例) 60"
                        value="<?= isset($_REQUEST['id']) ? h($conf['stock_num']): h($_POST['stock_num']); ?>">
                    <?php if ($error['stock_num'] === 'blank'): ?>
                    <p class="error">※在庫数を入力してください</p>
                    <?php elseif ($error['stock_num'] === 'not_num'): ?>
                    <p class="error">※数字で入力してください</p>
                    <?php endif; ?>
                </dd>
            </div> <!-- .flexiblebox -->
            <!-- 薬の登録 -->
            <?php if (isset($_REQUEST['id'])): ?>
            <p class="btn_center"><input type="submit" class="submit  regist_sub_design update_submit_color" value="更新">
            </p>
            <!-- 薬の編集 -->
            <?php else: ?>
            <p class="btn_center"><input type="submit" class="submit  regist_sub_design regist_submit_color" value="登録">
            </p>
            <?php endif; ?>
            <!-- CSRF対策 -->
            <input type="hidden" name="token" class="token" value="<?= h($_SESSION['token']); ?>">
        </form>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="asset/js/stock.js"></script>
</body>

</html>