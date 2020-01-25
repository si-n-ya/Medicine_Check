<?php
namespace MyApp;

class Medicine
{
    public $prev;
    public $next;
    public $yearMonth;
    private $_db;
    private $_thisMonth;
    private $_calender_date;

    public function __construct()
    {
        // トークンの作成
        $this->_createToken();
        // データベース接続
        try {
            define('DSN', 'mysql:host=localhost;dbname=medicine_check');
            define('DB_USERNAME', 'root');
            define('DB_PASSWORD', 'root');
            $this->_db = new \PDO(DSN, DB_USERNAME, DB_PASSWORD);
            $this->_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "データベース接続エラー" . $e->getMessage();
            exit;
        }

        /*
          カレンダー(medicine_check.php)
        */
        try {
            if (isset($_REQUEST['id'])) {
                $this->_thisMonth = new \DateTime($_REQUEST['id']);
            } elseif (!isset($_GET['t']) || !preg_match('/\A\d{4}-\d{2}\z/', $_GET['t'])) {
                throw new \Exception();
            } else {
                $this->_thisMonth = new \DateTime($_GET['t']);
            }
        } catch (\Exception $e) {
            // 今月の最初の日付(1日)を取得
            $this->_thisMonth = new \DateTime('first day of this month');
        }

        $this->prev = $this->_createPrevLink();
        $this->next = $this->_createNextLink();
        // 今月を、英語の月と西暦で取得
        $this->yearMonth = $this->_thisMonth->format('F Y');
    }

    // トークンの作成
    private function _createToken()
    {
        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
        }
    }
    
    // 前月のカレンダーをgetパラメーターの't'で表す
    private function _createPrevLink()
    {
        // 今月の英語の月と西暦をコピー
        $dt = clone $this->_thisMonth;
        // $dtを前月の西暦と2桁の月に変える
        return $dt->modify('-1 month')->format('Y-m');
    }

    // 次の月のカレンダーをgetパラメーターの't'で表す
    private function _createNextLink()
    {
        // 今月の英語の月と西暦をコピー
        $dt = clone $this->_thisMonth;
        // $dtを前月の西暦と2桁の月に変える
        return $dt->modify('+1 month')->format('Y-m');
    }

    // カレンダーの日付部分を表示
    public function show()
    {
        $tail = $this->_getTail();
        $body = $this->_getBody();
        $head = $this->_getHead();
        // カレンダーの最初と最後に<tr>を置く(<td>は、while文で作っているため)
        $html = '<tr>' . $tail . $body . $head . '</tr>';
        echo $html;
    }

    // 前月のから土曜日まで、来月の日付を表示
    private function _getTail()
    {
        $tail = '';
        // 前月の最終日を取得
        $lastDayOfPrevMonth = new \DateTime('last day of ' . $this->yearMonth . ' -1 month');
        // format('w')が 0(日), 1(月), 2(火), 3(水), 4(木), 5(金) の間、前月最終日から1日ずつ引いた日付を日曜日から順に表示。6(土)になったら終わり。
        // 新しくできた日付(sprintf)の後に$tailを連結し、大きい日付を後ろに持ってくる。
        while ($lastDayOfPrevMonth->format('w') < 6) {
            $tail = sprintf('<td class="gray date">%d</td>', $lastDayOfPrevMonth->format('d')) . $tail;
            // 前月の最終日から1日づつ引いていく
            $lastDayOfPrevMonth->sub(new \DateInterval('P1D'));
        }
        return $tail;
    }

    private function _getBody()
    {
        $body = '';
        // 今月の最初の日(1日)から今月の最終日までを取得
        $period = new \DatePeriod(
            new \DateTime('first day of ' . $this->yearMonth),
            new \DateInterval('P1D'),
          // DatePeriodの期間の終わりは含まないので、今月の最終日を取得
          new \DateTime('first day of ' . $this->yearMonth . ' +1 month')
        );

        // 今日の日付と$_REQUEST['id']の日付を取得
        $today = new \DateTime('today');
        if (isset($_REQUEST['id'])) {
            $request_date = new \DateTime($_REQUEST['id']);
        }
        foreach ($period as $day) {
            // format('w')が0(日曜日)の時、</tr><tr> で一行を終え、新しい行にする
            if ($day->format('w') === '0') {
                $body .= "</tr><tr>";
            }
            // $day->format('Y-m-d')(カレンダーの日付) と $today->format('Y-m-d')(今日の)日付が一致すれば、$todayClassに'today'を代入
            // $_REQUEST['id']があれば、$click_dateに条件、$todayClassが''。なければ、$click_dateが''、$todayClassに条件。
            
            if (isset($_REQUEST['id'])) {
                $todayClass = '';
                $click_date = ($day->format('Y-n-j') === $request_date->format('Y-n-j')) ? 'click_date': '';
            } else {
                $click_date = '';
                $todayClass = ($day->format('Y-m-d') === $today->format('Y-m-d')) ? 'today' : '';
            }
            // 土曜日と日曜日の日付の色を変えるため、<td>に$day->format("w")の数字で曜日毎にclass名を変える

            $body .= '<td class="youbi_' . $day->format("w") . ' ' . $todayClass . ' ' . $click_date . ' date"><a href="medicine_check.php?id=' . $day->format('Y') . '-' . $day->format('m') . '-' . $day->format('d') . '" class="link_date">' . $day->format('j') . '</a></td>';
        }
        return $body;
    }

    // 今月の最終日から土曜日まで、来月の日付を表示
    private function _getHead()
    {
        $head = '';
        // 来月の最初の日(1日)を取得
        $firstDayOfNextMonth = new \DateTime('first day of ' . $this->yearMonth . ' +1 month');
        // format('w')が 1(月), 2(火), 3(水), 4(木), 5(金), 6(土)の間、来月の1日からの日付を表示。0(日曜日)になったら終わりで、show()での</tr>で一行にする。
        while ($firstDayOfNextMonth->format('w') > 0) {
            $head .= sprintf('<td class="gray date">%d</td>', $firstDayOfNextMonth->format('d'));
            // 来月の1日から1日ずつ足していく
            $firstDayOfNextMonth->add(new \DateInterval('P1D'));
        }
        return $head;
    }

    /*
      medicine_check.php
    */
    // 服用チェックする薬を取得
    public function check_get_all()
    {
        $sql = "SELECT * FROM when_use AS w JOIN items AS i ON w.item_id=i.id ORDER BY time DESC";
        return $this->_db->query($sql);
    }

    // stateテーブルを取得するため
    public function get_time_states()
    {
        $sql = "SELECT * FROM when_use AS w JOIN state AS s ON item_id=item__id WHERE use_date=? AND item__id=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $_REQUEST['id'],
            $_SESSION['item_id']
            ]);
        unset($_SESSION['item_id']);
        return $stmt->fetch();
    }
    /*
      list.php
    */
    // 登録した薬の取得
    public function get_all()
    {
        $start = $this->page();
        $start = ($start - 1) * 5;
        // 10個までを取り出す
        $sql = "SELECT * FROM when_use AS w JOIN items AS i ON w.item_id=i.id LIMIT ?, 5";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(1, $start, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // pageの取得
    public function page()
    {
        if (isset($_REQUEST['page'])) {
            $page = $_REQUEST['page'];
        } else {
            $page = 1;
        }

        $max_page = $this->max_page();
        // $pageと$maxpageの小さい値の方を返す
        return min($page, $max_page);
    }

    // 登録した薬の数を5で割った余りの切り上げた値を取得
    public function max_page()
    {
        $sql = "SELECT COUNT(*) AS cnt FROM when_use AS w JOIN items AS i ON w.item_id=i.id";
        $stmt = $this->_db->query($sql);
        $cnt  = $stmt->fetch();
        // cnt を5で割った余りを切り上げ
        return ceil($cnt['cnt'] / 5);
    }

    /*
      regist_form.php
    */
    // 登録した薬の編集
    public function update_conf()
    {
        $sql = "SELECT * FROM when_use AS w JOIN items AS i ON w.item_id=i.id WHERE item_id=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $_REQUEST['id']
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /*
      regist.php
    */
    // 薬をデータベースへ登録
    public function stock_regist()
    {
        try {
            $this->_db->beginTransaction();

            // itemsテーブルにINSERT
            $sql = "INSERT INTO items SET name=?, stock_num=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
             $_SESSION['post']['name'],
             $_SESSION['post']['stock_num'],
         ]);
            $last_id = $this->_db->lastInsertId();
 
            // when_useテーブルにINSERT
            $sql = "INSERT INTO when_use SET weeks=?, time=?, get_date=?, use_num=?, unit=?, item_id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
             $_SESSION['week'],
             $_SESSION['use_time'],
             $_SESSION['post']['get_date'],
             $_SESSION['post']['use_num'],
             $_SESSION['post']['unit'],
             $last_id
         ]);
         
            $this->_db->commit();
            unset($_SESSION['post']);
            unset($_SESSION['week']);
            unset($_SESSION['use_time']);
            return '登録完了しました';
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo '薬の登録エラー' . $e->getMessage();
            exit;
        }
    }

    // 登録した薬を更新
    public function update()
    {
        try {
            $this->_db->beginTransaction();

            $sql = "UPDATE items AS i JOIN when_use AS w ON i.id=w.item_id SET name=?, stock_num=?, weeks=?, time=?, get_date=?, use_num=?, unit=? WHERE item_id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
            $_SESSION['post']['name'],
            $_SESSION['post']['stock_num'],
            $_SESSION['week'],
            $_SESSION['use_time'],
            $_SESSION['post']['get_date'],
            $_SESSION['post']['use_num'],
            $_SESSION['post']['unit'],
            $_REQUEST['id']
        ]);

            unset($_SESSION['post']);
            unset($_SESSION['week']);
            unset($_SESSION['use_time']);
            $this->_db->commit();
            return "編集完了しました";
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo '登録した薬の更新エラー' . $e->getMessage();
            exit;
        }
    }

    /*
      history.php
    */
    // 薬の使用履歴
    public function history()
    {
        $sql = "SELECT i.*, w.*, s.* FROM items AS i JOIN when_use AS w ON i.id=w.item_id JOIN state AS s ON i.id=s.item__id ORDER BY use_date ASC, name ASC";
        $stmt = $this->_db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // 薬の使用履歴の検索
    public function searchResult()
    {
        // 名前と日付が両方検索されている時
        if ($_POST['search_name'] != '' && $_POST['search_date'] != '') {
            $sql = "SELECT i.*, w.*, s.* FROM items AS i JOIN when_use AS w ON i.id=w.item_id JOIN state AS s ON i.id=s.item__id WHERE use_date LIKE ? AND name LIKE ? ORDER BY use_date ASC, name ASC";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
            '%' . $_POST['search_date'] . '%',
            '%' . $_POST['search_name'] . '%'
        ]);
        // 名前のみ検索されている時
        } elseif ($_POST['search_name'] != '') {
            $sql = "SELECT i.*, w.*, s.* FROM items AS i JOIN when_use AS w ON i.id=w.item_id JOIN state AS s ON i.id=s.item__id WHERE name LIKE ? ORDER BY use_date ASC, name ASC";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
            '%' . $_POST['search_name'] . '%'
        ]);
        // 日付のみ検索されている時
        } elseif ($_POST['search_date'] != '') {
            $sql = "SELECT i.*, w.*, s.* FROM items AS i JOIN when_use AS w ON i.id=w.item_id JOIN state AS s ON i.id=s.item__id WHERE use_date LIKE ? ORDER BY use_date ASC, name ASC";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
            '%' . $_POST['search_date'] . '%'
        ]);
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /*
      ajax.php
    */
    public function post()
    {
        // トークンのバリデーション
        $this->_validateToken();
        if (!isset($_POST['mode'])) {
            throw new \Exception('$_POST["mode"]が存在しません');
        }

        switch ($_POST['mode']) {
                case 'add_check':
                    return $this->_check();
                case 'remove_check':
                    return $this->_check();
                case 'list_delete':
                    return $this->_list_delete();
                case 'history_delete':
                    return $this->_history_delete();
        }
    }

    protected function _validateToken()
    {
        if (!isset($_SESSION['token']) || !isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']) {
            throw new \Exception('不正な投稿です');
        }
    }

    // 薬を服用時(medicine.php)
    private function _check()
    {
        try {
            if (!isset($_POST['id'])) {
                throw new \Exception('[check]のidが存在しません');
            }
            if (!isset($_POST['week_id'])) {
                throw new \Exception('[check]のweek_idが存在しません');
            }
            if (!isset($_POST['time_id'])) {
                throw new \Exception('[check]のtime_idが存在しません');
            }

            $this->_db->beginTransaction();

            // stateテーブルに値が入っているかを調べる
            $sql = "SELECT i.*, w.*, s.* FROM items AS i JOIN when_use AS w ON i.id=item_id JOIN state AS s ON item_id=item__id WHERE use_date=? AND item__id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
            $_POST['date'],
            $_POST['id']
        ]);
            $result = $stmt->fetch();
            $get_name = $result['name'];
            $get_stock = $result['stock_num'];
            $get_week = $result['week_state'];
            $get_time = $result['time_state'];
            $use_date = $result['use_date'];
            $use_num = $result['use_num'];
            $get_item = $result['item__id'];

            // Ajax処理で'add_checkの時'
            if ($_POST['mode'] === 'add_check') {
                // stateテーブルが空の時
                if (empty($result)) {
                    // stateテーブルにINSERT
                    $sql = "INSERT INTO state SET week_state=?, time_state=?, use_date=?, item__id=?";
                    $stmt = $this->_db->prepare($sql);
                    $stmt->execute([
                    $_POST['week_id'],
                    $_POST['time_id'],
                    $_POST['date'],
                    $_POST['id']
                ]);

                    // 薬の残り数を引くために、使った薬の数を取得($use_numは、empty空のため、もう一度SELECT文で取得)
                    $sql = "SELECT * FROM when_use WHERE item_id=?";
                    $stmt = $this->_db->prepare($sql);
                    $stmt->execute([
                    $_POST['id']
                ]);
                    $result = $stmt->fetch();

                    // 薬の残り数を引く
                    $sql = "UPDATE items SET stock_num=stock_num-? WHERE id=?";
                    $stmt = $this->_db->prepare($sql);
                    $stmt->execute([
                    $result['use_num'],
                    $_POST['id']
                ]);

                // stateテーブルに値が入ってる時
                } else {

                // stateテーブルのtime_stateカラムの文字列が1以上の時(','とAjaxの$_POST['time_id]の値をプラスする)
                    if (mb_strlen($get_time, 'UTF-8') > 0) {
                        $time_state = $get_time . ',' . $_POST['time_id'];
                    } else {
                        $time_state = $_POST['time_id'];
                    }

                    // 薬の残り数を引き、使った時間を更新
                    $sql = "UPDATE items AS i JOIN when_use AS w ON i.id=w.item_id JOIN state AS s ON item_id=item__id SET stock_num=stock_num-?, time_state=? WHERE use_date=? AND item__id=?";
                    $stmt = $this->_db->prepare($sql);
                    $stmt->execute([
            $result['use_num'],
            $time_state,
            $_POST['date'],
            $_POST['id']
        ]);
                }
                // Ajax処理で'add_checkの時'
            } elseif ($_POST['mode'] === 'remove_check') {

            // stateテーブルのtime_stateの文字に','が含まれている時,'区切りでexplode
                if (strpos($get_time, ',') !== false) {
                    $get_time_state = explode(',', $get_time);
                    $count = 0;

                    // explodeした配列をforeach
                    foreach ($get_time_state as $time_state_one) {
                        // explodeしたtime_stateの値とAjaxの$_POST['time_id']が一致した時、前者をunsetで削除
                        if ($time_state_one == $_POST['time_id']) {
                            unset($time_state_one);
                            $count = 0;
                        }
                        // $countが0の時、explodeしたtime_stateの値を.=でつなげる
                        if ($count == 0) {
                            $time_state_new .= $time_state_one;
                        } else {
                            // $countが0以外の時、','とexplodeしたtime_stateの値でつなげる
                            $time_state_new .= ',' . $time_state_one;
                        }
                        $count++;
                    }
                    // 先頭と末尾の','を取り除く
                    $time_state = trim($time_state_new, ',');

                    // チェックを外すと薬の残り数を足して、時間を新しい値で更新する
                    $sql = "UPDATE items AS i JOIN when_use AS w ON i.id=item_id JOIN state AS s ON item_id=item__id SET stock_num=stock_num+?, time_state=? WHERE use_date=? AND item__id=?";
                    $stmt = $this->_db->prepare($sql);
                    $stmt->execute([
            $use_num,
            $time_state,
            $_POST['date'],
            $_POST['id']
        ]);
                // stateテーブルのtime_stateの文字に','が含まれていない時
                } else {
                    // stateテーブルを一行削除
                    $sql = "DELETE FROM state WHERE use_date=? AND item__id=?";
                    $stmt = $this->_db->prepare($sql);
                    $stmt->execute([
                    $use_date,
                    $get_item
                ]);
                    // 薬の残り数を元に戻す(itemsテーブルのstock_numをプラスする)
                    $sql = "UPDATE items AS i SET stock_num=stock_num+? WHERE id=?";
                    $stmt = $this->_db->prepare($sql);
                    $stmt->execute([
                    $use_num,
                    $get_item
                ]);
                }
            }

            $this->_db->commit();
            return [];
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo "薬を服用時の更新エラー" . $e->getMessage();
            exit;
        }
    }

    // 登録リストの削除(list.php)
    private function _list_delete()
    {
        if (!isset($_POST['id'])) {
            throw new \Exception('[list_delete]のidが存在しません');
        }
        try {
            $this->_db->beginTransaction();

            // 削除する薬のstateテーブルが空か調べるため
            $sql = "SELECT * FROM state WHERE item__id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                $_POST['id']
            ]);
            $result = $stmt->fetch();

            // 削除する薬のstateテーブルが空の時、itemテーブルとwhen_useテーブルを削除
            if (empty($result)) {
                $sql = "DELETE i.*, w.* FROM items AS i JOIN when_use AS w ON i.id=item_id WHERE item_id=?";
                $stmt = $this->_db->prepare($sql);
            // 削除する薬のstateテーブルが空ではない時、stateテーブルも含めて削除
            } else {
                $sql = "DELETE i.*, w.*, s.* FROM items AS i JOIN when_use AS w ON i.id=item_id JOIN state AS s ON w.item_id=s.item__id WHERE item_id=?";
                $stmt = $this->_db->prepare($sql);
            }
            $stmt->execute([
                $_POST['id']
            ]);

            $this->_db->commit();
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo '登録した薬の削除エラー' . $e->getMessage();
            exit;
        }
    }

    // 使用した薬の履歴の削除(history.php)
    private function _history_delete()
    {
        try {
            $this->_db->beginTransaction();

            if (!isset($_POST['id'])) {
                throw new \Exception('[history_delete]のidが存在しません');
            }

            $sql = "DELETE FROM state WHERE id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                $_POST['id']
            ]);
            $this->_db->commit();
            return [];
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo '使用した薬の履歴の削除エラー' . $e->getMessage();
            exit;
        }
    }
}

class Validation extends Medicine
{
    public function __construct()
    {
        // CSRFトークンのバリデーション
        $this->_validateToken();
    }

    /*
      regist_form.php
    */
    //  名前のバリデーション
    public function validateName()
    {
        if ($_POST['name'] === '') {
            return 'blank';
        } else {
            return [];
        }
    }

    // 曜日のバリデーション
    public function validateWeek()
    {
        if (!isset($_POST['week'])) {
            return 'blank';
        } else {
            $_SESSION['week'] = implode(',', $_POST['week']);
            return [];
        }
    }

    // 時刻のバリデーション
    public function validateTime()
    {
        if (!isset($_POST['time'])) {
            return 'blank';
        } else {
            $_SESSION['use_time'] = implode(',', $_POST['time']);
            return [];
        }
    }

    // 日付のバリデーション
    public function validateGetDate()
    {
        if ($_POST['get_date'] === '') {
            return 'blank';
        } elseif (!preg_match("/^[0-9]{4}-[0-1]{1}[0-9]{1}-[0-3]{1}[0-9]{1}$/", $_POST['get_date'])) {
            return 'not_format';
        } else {
            return [];
        }
    }

    // 一回に服用する量のバリデーション
    public function validateUseNum()
    {
        if ($_POST['use_num'] === '') {
            return 'blank';
        } elseif (!is_numeric($_POST['use_num'])) {
            return 'not_num';
        } else {
            return [];
        }
    }

    // 在庫数のバリデーション
    public function validateStockNum()
    {
        if ($_POST['stock_num'] === '') {
            return 'blank';
        } elseif (!is_numeric($_POST['stock_num'])) {
            return 'not_num';
        } else {
            return [];
        }
    }
}