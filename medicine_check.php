<?php
session_start();
require_once(__DIR__ . "/lib/Controller/Medicine.php");
require_once(__DIR__ . "/lib/functions.php");
require_once(__DIR__ . "/config/config.php");

if (isset($_REQUEST['id']) || isset($_REQUEST['t'])) {
    $medi = new \MyApp\Medicine();
    $today = new DateTime('today');
    $today = $today->format('Y-m-d');
    function func_explode($value)
    {
        return explode(',', $value);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>スケジュール帳</title>
    <link rel="stylesheet" href="asset/css/medicine.css">
</head>

<body>
    <!-- ヘッダーの読み込み -->
    <?php include(__DIR__ . '/_inc/header.php'); ?>
    <div class="bg_image_check bg_help">
        <div class="bg_mask">
            <main class="container check_container margin_top">
                <div class="flex_container">
                    <h1 class="title_shape title_white">お薬服用チェック</h1>
                    <p class="back_box btn_center">
                        <a href="index.php" class="btn back">メニューへ戻る</a>
                    </p>
                </div> <!-- .flex_container -->
                <!-- カレンダー -->
                <table border="1" class="table">
                    <thead>
                        <tr>
                            <th class="calender_head"><a href="medicine_check.php?t=<?= h($medi->prev); ?>">&laquo</a>
                            </th>
                            <th colspan="5" class="calender_head"><?= h($medi->yearMonth); ?></th>
                            <th class="calender_head"><a href="medicine_check.php?t=<?= h($medi->next); ?>">&raquo</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="week sun">Sun</td>
                            <td class="week">Mon</td>
                            <td class="week">Tue</td>
                            <td class="week">Wed</td>
                            <td class="week">Thu</td>
                            <td class="week">Fri</td>
                            <td class="week sat">Sat</td>
                        </tr>
                        <?php $medi->show(); ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="calender_foot"><a
                                    href="medicine_check.php?id=<?= $today; ?>">Today</a></th>
                        </tr>
                    </tfoot>
                </table>
                <?php if (isset($_REQUEST['id'])): ?>
                <?php
                $check_get_all = $medi->check_get_all();
                ?>
                <ul class="list_all">
                    <?php foreach ($check_get_all as $get_one): ?>
                    <!-- 在庫数(stock_num)が０より大きい時に表示 -->
                    <?php if ($get_one['stock_num'] > 0): ?>
                    <!-- 一行目の曜日毎 -->
                    <?php
                    $week = func_explode($get_one['weeks']);
                    $get_date = new DateTime($get_one['get_date']);
                    $request_date = new DateTime($_REQUEST['id']);
                    ?>

                    <!-- 登録した曜日の数だけループ -->
                    <?php foreach ($week as $week_one):?>
                    <!-- GETパラメータのidの日付の曜日(format('w'))が登録している曜日($week_one)と等しい時かつ、idの日付が登録した日付よりも大きい時 -->
                    <?php if ($request_date->format('w') == $week_one && $request_date >= $get_date): ?>
                    <?php
                    $time = func_explode($get_one['time']);
                    ?>
                    <!-- 登録した時間の数だけループ -->
                    <?php foreach ($time as $time_one): ?>
                    <?php
                    // stateテーブルのtime_stateをexplodeするため、$get_oneでforeachされているitem_idを使って取得
                    $_SESSION['item_id'] = $get_one['item_id'];
                    $get_time_states = $medi->get_time_states();
                    // 登録した時間と服用した時間が一致すれば、$checkedと$doneに値を代入
                        $time_states = func_explode($get_time_states['time_state']);
                        $checked = '';
                        $done = '';
                        foreach ($time_states as $time_state) {
                            if ($time_state == $time_one) {
                                $checked = 'checked';
                                $done = 'done';
                            }
                        }
                    ?>
                    <label for="check_<?= h($get_one['item_id']); ?>_<?= h($time_one); ?>" class="name_check">
                        <li class="list_one hover <?= $done === 'done' ? 'done': ''; ?>"
                            data-id="<?= h($get_one['item_id']); ?>" data-time="<?= h($time_one); ?>"
                            data-week="<?= h($week_one); ?>" data-date="<?= h($_REQUEST['id']); ?>">
                            <input type="checkbox" class="check"
                                id="check_<?= h($get_one['item_id']); ?>_<?= h($time_one); ?>"
                                <?= $checked === 'checked' ? 'checked': ''; ?>>
                            <span class="list name_list">
                                <?= h($get_one['name']); ?>
                            </span>
                            <span class="list num_list">
                                <?= h($get_one['use_num']); ?>
                                <?= h($get_one['unit']); ?>
                            </span>
                            <span class="list time_list">
                                <?= h($time_one); ?>:00
                            </span>
                        </li>
                    </label>
                    <?php endforeach; ?>
                    <?php break; ?>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <input type="hidden" class="token" name="token" value="<?= h($_SESSION['token']); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="asset/js/stock.js"></script>
</body>

</html>