<?php
session_start();
require_once(__DIR__ . "/lib/Controller/Medicine.php");
require_once(__DIR__ . "/lib/functions.php");
require_once(__DIR__ . "/config/config.php");

$medi = new \MyApp\Medicine();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 検索時に値が空かどうかのチェック
    if ($_POST['search_name'] != '' || $_POST['search_date'] != '') {
        //検索結果を取得
        $search_result = $medi->searchResult();
    } else {
        header('Location: history.php');
    }
    // 検索しなければ全件表示
} else {
    $history = $medi->history();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>お薬服用履歴</title>
    <link rel="stylesheet" href="asset/css/medicine.css">
</head>

<body>
    <?php include(__DIR__ . '/_inc/header.php'); ?>
    <div class="container history_container">
        <main class="container">
            <div class="flex_container">
                <h1 class="title_shape conf_title_black">お薬服用履歴</h1>
                <p class="back_box btn_right">
                    <a href="index.php" class="btn back">メニューへ戻る</a>
                </p>
            </div> <!-- .flex_container -->
            <div class="history_form_container">
                <form action="" method="post" class="history_form">
                    <dl>
                        <dt class="history_dt">お薬の名前</dt>
                        <dd class="history_dd">
                            <input type="text" name="search_name" class="input_text input_history">
                        </dd>
                        <dt class="history_dt">服用した日付</dt>
                        <dd class="history_dd">
                            <input type="date" name="search_date" class="input_text input_history">
                        </dd>
                    </dl>
                    <p class="history_submit_box btn_center">
                        <input type="submit" name="submit" value="検索" class="submit history_submit">
                    </p>
                </form>
            </div>
            <!-- 検索結果が0の時 -->
            <?php if (count($search_result) < 1 && isset($search_result)): ?>
            <p class="error history_error">*検索結果はありません</p>
            <!-- 検索結果がある時 -->
            <?php elseif (isset($search_result)): ?>
            <table border="1" class="history_table">
                <thead>
                    <tr>
                        <th></th>
                        <th>日付</th>
                        <th>時間</th>
                        <th>名前</th>
                        <th>量</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_result as $search_result_one): ?>
                    <?php
                        $get_time = $search_result_one['time_state'];
                        $time = explode(',', $get_time);
                        ?>
                    <tr class="history_tr history_<?= h($search_result_one['id']); ?>">
                        <td class="history_td">
                            <input type="checkbox" class="check history_check"
                                value="<?= h($search_result_one['id']); ?>">
                        </td>
                        <td class="history_td">
                            <?= h($search_result_one['use_date']); ?>
                        </td>
                        <td class="history_td">
                            <?php foreach ($time as $time_one): ?>
                            <span><?= $time_one; ?>:00</span>
                            <?php endforeach; ?>
                        </td>
                        <td class="history_td">
                            <?= h($search_result_one['name']); ?>
                        </td>
                        <td class="history_td">
                            <?= h($search_result_one['use_num']) ?><?= h($search_result_one['unit']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="flex_container history_flex_box">
                <div class="all_check_box history_all_check_box">
                    <input type="checkbox" id="all_check" class="all_check check">
                    <label for="all_check">全ての履歴</label>
                </div> <!-- .all_check_box -->
                <p colspan="5" class="btn_box btn_center history_delete_box">
                    <button class="btn history_delete">削除</button>
                </p>
            </div> <!-- .history_flex_box -->
            <!-- 検索をしていない時 -->
            <?php elseif (!isset($_POST['submit'])) : ?>
            <table border="1" class="history_table">
                <thead>
                    <tr>
                        <th></th>

                        <th>日付</th>
                        <th>時間</th>
                        <th>名前</th>
                        <th>量</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $history_one): ?>
                    <?php
                      $get_time = $history_one['time_state'];
                      $time = explode(',', $get_time);
                    ?>
                    <tr class="history_tr history_<?= h($history_one['id']); ?>">
                        <td class="history_td">
                            <input type="checkbox" class="check history_check" value="<?= h($history_one['id']); ?>">
                        </td>
                        <td class="history_td">
                            <?= h($history_one['use_date']); ?>
                        </td>
                        <td class="history_td">
                            <?php foreach ($time as $time_one): ?>
                            <span><?= $time_one; ?>:00</span>
                            <?php endforeach; ?>
                        </td>
                        <td class="history_td">
                            <?= h($history_one['name']); ?>
                        </td>
                        <td class="history_td">
                            <?= h($history_one['use_num']) ?><?= h($history_one['unit']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="flex_container history_flex_box">
                <div class="all_check_box history_all_check_box">
                    <input type="checkbox" id="all_check" class="all_check check">
                    <label for="all_check">全ての履歴</label>
                </div> <!-- .all_check_box -->
                <p colspan="5" class="btn_box btn_center history_delete_box">
                    <button class="btn history_delete">削除</button>
                </p>
            </div> <!-- .history_flex_box -->
            <?php endif; ?>

        </main>
    </div> <!-- .history_container -->
    <input type="hidden" class="token" name="token" value="<?= h($_SESSION['token']); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="asset/js/stock.js"></script>
</body>

</html>