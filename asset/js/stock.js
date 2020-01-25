$(function() {
  "use strict";

  /*
    header.php
  */
  // ドロワー開閉
  $(".btn_hamburger").click(function() {
    let logo = $(this).find(".hamburger_logo");
    if ($(logo).hasClass("is_cancel_logo")) {
      $(".global_nav").removeClass("is_drower_active");
      $(".header")
        .removeClass("is_header_fixed")
        .removeClass("is_header_full_open");
      $(logo).removeClass("is_cancel_logo");
    } else {
      $(".global_nav").addClass("is_drower_active");
      $(".header")
        .addClass("is_header_fixed")
        .addClass("is_header_full_open");
      $(logo).addClass("is_cancel_logo");
    }
  });

  /*
    regist_form.php
  */
  // formの'全ての曜日'をクリック時
  $(".week_dd").on("click", ".all_check", function() {
    if ($(".all_check").hasClass("all")) {
      $(".regist_week_check").prop("checked", "");
      $(".all_check").removeClass("all");
    } else {
      $(".regist_week_check").prop("checked", "checked");
      $(".all_check").addClass("all");
    }
  });

  // formの曜日をクリックした時に、'全ての曜日'のチェックが外れる
  $(".week_dd").on("click", ".regist_week_check", function() {
    $(".all_check").prop("checked", "");
    $(".all_check").removeClass("all");
  });

  /*
    medicine_check.php
  */
  // カレンダーの日付をクリック
  $(".date").on("click", ".link_date", function() {
    $(this).addClass("click_date");
    $(".today").removeClass("today");
  });

  // 薬服用のcheck時の処理
  $(".list_one").on("click", ".check", function() {
    let id = $(this)
      .parents(".list_one")
      .data("id");
    let time_id = $(this)
      .parents(".list_one")
      .data("time");
    let week_id = $(this)
      .parents(".list_one")
      .data("week");
    let date = $(this)
      .parents(".list_one")
      .data("date");
    let class_done = $(this).parents(".list_one");
    let token = $(".token").val();

    if ($(class_done).hasClass("done")) {
      $.post(
        "lib/ajax.php",
        {
          id: id,
          week_id: week_id,
          time_id: time_id,
          date: date,
          mode: "remove_check",
          token: token
        },
        function() {
          $(class_done).removeClass("done");
        }
      );
    } else {
      $.post(
        "lib/ajax.php",
        {
          id: id,
          week_id: week_id,
          time_id: time_id,
          date: date,
          mode: "add_check",
          token: token
        },
        function() {
          $(class_done).addClass("done");
        }
      );
    }
  });

  /*
    list.php
  */
  // 登録した薬の削除
  $(".medi_list").on("click", ".delete_btn", function() {
    let id = $(this)
      .parents(".medi_list")
      .data("id");
    let token = $(".token").val();
    if (confirm("本当に削除しますか？")) {
      $.post(
        "lib/ajax.php",
        {
          id: id,
          mode: "list_delete",
          token: token
        },
        function() {
          $(".medi_" + id).fadeOut();
        }
      );
    }
  });

  /*
    history.php
  */
  // 薬の履歴の削除
  $(".history_container").on("click", ".history_delete", function() {
    let token = $(".token").val();
    if (confirm("本当に削除しますか？")) {
      // 複数削除もあるため、each()でループ
      $(".check:checked").each(function() {
        let id = $(this).val();
        $.post(
          "lib/ajax.php",
          {
            id: id,
            mode: "history_delete",
            token: token
          },
          function() {
            $(".history_" + id).fadeOut();
          }
        );
      });
    }
  });

  // '全ての履歴'をクリック時
  $(".history_flex_box").on("click", ".all_check", function() {
    if ($(".all_check").hasClass("all")) {
      $(".history_check").prop("checked", "");
      $(".all_check").removeClass("all");
    } else {
      $(".history_check").prop("checked", "checked");
      $(".all_check").addClass("all");
    }
  });
  // 履歴をクリックした時に、'全ての履歴'のチェックが外れる
  $(".history_td").on("click", ".history_check", function() {
    $(".all_check").prop("checked", "");
    $(".all_check").removeClass("all");
  });
});
