<?php
  $today = new DateTime('today');
  $today = $today->format('Y-m-d');
?>
<header class="header">
        <div class="header_container header_flex">
        <div class="header_inner container">
        <p>
        <a href="index.php" class="top_title">お薬管理アプリ</a>
        </p>
        <button class="btn_hamburger">
        <span class="hamburger_logo"></span>
        </button>
    </div> <!-- .header_inner -->
        <nav class="global_nav">
            <ul>
            <li class="nav_list">
            <a href="medicine_check.php?id=<?= h($today); ?>" class="nav_link">お薬服用チェック</a>
            </li>
            <li class="nav_list">
            <a href="regist_form.php" class="nav_link">お薬登録</a>
            </li>
            <li class="nav_list">
            <a href="list.php" class="nav_link">登録リスト</a>
            </li>
            <li class="nav_list">
            <a href="history.php" class="nav_link">薬の使用履歴</a>
            </li>
            </ul>
        </nav>
</div> <!-- .header_container -->
    </header>  