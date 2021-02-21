<?php
require_once(ROOT_PATH .'/Models/Db.php');

class attend extends Db {
  public function __construct($dbh=null) {
    parent::__construct($dbh);
  }

  public function validation($arr_post, $today) {
    //エラーメッセージを入れる配列
    $err               = [];
    $err['temp_value'] = $arr_post['temp'];
    $err['msg_value']  = htmlspecialchars($arr_post['msg'], ENT_QUOTES, 'UTF-8');
    $csrf_token        = $arr_post['csrf_token'];

    //2重で登園記録が登録されないか判定
    $result = $this->getByName($today);
    if(!$result) {
      $err['overlapping_msg'] = '本日の登園記録は登録済みです。';
    }

    //値が入力されているかの判定
    if(!isset($err['overlapping_msg'])) {
      if(empty($arr_post['temp'])) {
        $err['temp_msg'] = '体温を入力してください。';
      }
      if(!preg_match('/^[0-9]{2}.[0-9]$/',$arr_post['temp']) && !empty($arr_post['temp'])) {
        $err['temp_match_msg'] = '体温は○○.○の形式で入力してください。';
      }
    }

    //CSRF対策の判定
    if(empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
      exit('不正リクエスト');
    }
    unset($_SESSION['csrf_token']);

    //エラーがあった場合の処理
    if(count($err) > 2) {
      $_SESSION['err_msg'] = $err;
      header('Location: /attend/attendance_regist.php');
      exit;
    }
    //エラーがなかった場合
    else {
      unset($_SESSION['err_msg']);
    }
  }

  public function validation_serch($arr_post) {
    //エラーメッセージを入れる配列
    $err                     = [];
    $err['class_name_value'] = $arr_post['class_name'];
    $err['name_child_value'] = $arr_post['name_child'];
    $err['start_date_value'] = $arr_post['start_date'];
    $err['end_date_value']   = $arr_post['end_date'];
    $csrf_token              = $arr_post['csrf_token'];

    //エラー判定
    if(empty($arr_post['class_name'])) {
      $err['class_name_msg'] = 'クラス名を選択して下さい。';
    }
    if(!empty($arr_post['start_date']) || !empty($arr_post['end_date'])) {
      if(empty($arr_post['start_date'])) {
        $err['date_start_msg'] = '開始日を選択してください';
      }
      elseif(empty($arr_post['end_date'])) {
        $err['date_end_msg'] = '終了日を選択してください';
      }
      elseif($arr_post['start_date'] > $arr_post['end_date']) {
        $err['date_msg'] = '開始日が終了日より後になっています。選択し直してください。';
      }
    }
    //CSRF対策の判定
    $this->csrf($csrf_token);

    //エラーがあった場合の処理
    if(count($err) > 4) {
      $_SESSION['err_msg'] = $err;
      header('Location: /attend/serchRecord.php');
    }
    //エラーがなかった場合の処理
    else {
      //検索条件をセッションのcriteriaに格納と同時に前回格納された内容を削除
      unset($_SESSION['criteria']);
      $_SESSION['criteria']['class_name'] = $arr_post['class_name'];
      $_SESSION['criteria']['name_child'] = $arr_post['name_child'];
      $_SESSION['criteria']['start_date'] = $arr_post['start_date'];
      $_SESSION['criteria']['end_date']   = $arr_post['end_date'];
      //バリデーションチェックで一度NGだった場合を考慮して、セッションのerr_msgの中身を削除
      unset($_SESSION['err_msg']);
    }
  }

  public function validation_edit($arr_post) {
    //エラーメッセージを入れる配列
    $err                 = [];
    $err['review_value'] = $arr_post['review'];
    $csrf_token          = $arr_post['csrf_token'];

    //エラー判定
    if(empty($arr_post['review'])) {
      $err['review_msg'] = '本日の振り返りを入力してください。';
    }
    //CSRF対策の判定
    $this->csrf($csrf_token);

    //エラーがあった場合の処理
    if(count($err) > 1) {
      $_SESSION['err_msg'] = $err;
      //NGだった場合、GETで受け取ったname_idを再度POST送信するのに必要なため
      $_SESSION['err_msg']['name_id'] = $arr_post['name_id'];
      header('Location: /attend/serchRecord_edit.php');
      exit;
    }
    //エラーがなかった場合の処理
    else {
      unset($_SESSION['err_msg']);
    }
  }

  public function add($arr_post) {
    $sql    = "INSERT INTO attend_data(date, class, name_id, name_child, temperture, message, created_at) VALUES(:date, :class, :name_id, :name_child, :temperture, :message, :created_at)";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':date'       => $arr_post['date'],
      ':class'      => $arr_post['class'],
      ':name_id'    => (int)$_SESSION['login_userInfo']['name_id'],
      ':name_child' => $arr_post['name_child'],
      ':temperture' => $arr_post['temp'],
      ':message'    => htmlspecialchars($arr_post['msg'], ENT_QUOTES, 'UTF-8'),
      ':created_at' => $arr_post['time']
    );
    $stmt->execute($params);
  }

  public function getByName($today) {
    $overlapping = true;
    $sql         = 'SELECT * FROM attend_data WHERE name_id = :name_id AND date = :date';
    $stmt        = $this->dbh->prepare($sql);
    $params = array(
      ':name_id' => (int)$_SESSION['login_userInfo']['name_id'],
      ':date'    => $today
    );
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($result) > 0) {
      $overlapping = false;
      return $overlapping;
    }
    return $overlapping;
  }

  public function serch($arr_post, $attend_count, $page) {
    //クラス名＋園児名＋開始日と終了日
    if(!empty($arr_post['name_child']) && !empty($arr_post['start_date']) && !empty($arr_post['end_date'])) {
      $sql    = 'SELECT date, class, name_child, temperture, message, review, name_id FROM attend_data WHERE class = :class AND name_child = :name_child AND date >= :start_date AND date <= :end_date';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'      => $arr_post['class_name'],
        ':name_child' => $arr_post['name_child'],
        ':start_date' => $arr_post['start_date'],
        ':end_date'   => $arr_post['end_date']
      );
    }
    //クラス名＋開始日と終了日
    elseif(empty($arr_post['name_child']) && !empty($arr_post['start_date']) && !empty($arr_post['end_date'])) {
      $sql    = 'SELECT date, class, name_child, temperture, message, review, name_id FROM attend_data WHERE class = :class AND date >= :start_date AND date <= :end_date';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'      => $arr_post['class_name'],
        ':start_date' => $arr_post['start_date'],
        ':end_date'   => $arr_post['end_date']
      );
    }
    //クラス名＋園児名
    elseif(!empty($arr_post['name_child']) && empty($arr_post['start_date']) && empty($arr_post['end_date'])) {
      $sql    = 'SELECT date, class, name_child, temperture, message, review, name_id FROM attend_data WHERE class = :class AND name_child = :name_child';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'      => $arr_post['class_name'],
        ':name_child' => $arr_post['name_child']
      );
    }
    //クラス名
    else {
      $sql    = 'SELECT date, class, name_child, temperture, message, review, name_id FROM attend_data WHERE class = :class LIMIT 10 OFFSET ' .(10 * $page);
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class' => $arr_post['class_name']
      );
    }
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //検索結果が0件だった場合
    if(count($result) == 0) {
      $err['serch_msg'] = '検索結果が0件でした。';
      //検索フォームに記入された値をerr配列に格納
      $err['class_name_value'] = $arr_post['class_name'];
      $err['name_child_value'] = $arr_post['name_child'];
      $err['start_date_value'] = $arr_post['start_date'];
      $err['end_date_value']   = $arr_post['end_date'];
      $_SESSION['err_msg']     = $err;
      header('Location: /attend/serchRecord.php');
      exit;
    }
    //検索結果が1件以上あった場合
    foreach($result as $key => $value) {
      $attendInfo_serch[$key] = $value;
    }
    return $attendInfo_serch;
  }

  public function count($arr_post):Int {
    //クラス名＋園児名＋開始日と終了日
    if(!empty($arr_post['name_child']) && !empty($arr_post['start_date']) && !empty($arr_post['end_date'])) {
      $sql    = 'SELECT count(*) FROM attend_data WHERE class = :class AND name_child = :name_child AND date >= :start_date AND date <= :end_date';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'      => $arr_post['class_name'],
        ':name_child' => $arr_post['name_child'],
        ':start_date' => $arr_post['start_date'],
        ':end_date'   => $arr_post['end_date']
      );
    }
    //クラス名＋開始日と終了日
    elseif(empty($arr_post['name_child']) && !empty($arr_post['start_date']) && !empty($arr_post['end_date'])) {
      $sql    = 'SELECT count(*) FROM attend_data WHERE class = :class AND date >= :start_date AND date <= :end_date';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'      => $arr_post['class_name'],
        ':start_date' => $arr_post['start_date'],
        ':end_date'   => $arr_post['end_date']
      );
    }
    //クラス名＋園児名
    elseif(!empty($arr_post['name_child']) && empty($arr_post['start_date']) && empty($arr_post['end_date'])) {
      $sql    = 'SELECT count(*) FROM attend_data WHERE class = :class AND name_child = :name_child';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'      => $arr_post['class_name'],
        ':name_child' => $arr_post['name_child']
      );
    }
    //クラス名
    else {
      $sql    = 'SELECT count(*) FROM attend_data WHERE class = :class';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'=> $arr_post['class_name']
      );
    }
    $stmt->execute($params);
    $count = $stmt->fetchColumn();
    return $count;
  }

  public function get($name_id) {
    $sql    = 'SELECT * FROM attend_data WHERE name_id = :name_id';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':name_id' => $name_id
    );
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //取得したログインユーザー情報をセッションのuserInfoに格納
    //TOPページに遷移する度に常に新しいログインユーザー情報を取得
    //ユーザー検索画面の編集リンクを押した時も同じロジックでユーザー情報を取得
    foreach($result[0] as $key => $value) {
      $getAttendInfo['attendInfo'][$key] = $value;
    }
    return $getAttendInfo;
  }

  public function edit($arr_post) {
    $sql    = "UPDATE attend_data SET review = :review, updated_at = :updated_at WHERE name_id = :name_id AND date = :date";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':name_id'    => (int)$arr_post['name_id'],
      ':date'       => $arr_post['date'],
      ':review'     => htmlspecialchars($arr_post['review'], ENT_QUOTES, 'UTF-8'),
      ':updated_at' => $arr_post['time']
    );
    $stmt->execute($params);
  }

  public function csrf($csrf_token) {
    if(empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
      echo '指定されたパラメーターが不正です。このページは表示できません。';
      exit;
    }
    unset($_SESSION['csrf_token']);
  }
}