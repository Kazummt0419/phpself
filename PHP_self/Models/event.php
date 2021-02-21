<?php
require_once(ROOT_PATH .'/Models/Db.php');

class Event extends Db {
  public function __construct($dbh=null) {
    parent::__construct($dbh);
  }

  public function validation_regist($arr_post) {
    //エラーメッセージを入れる配列
    $err                      = [];
     //バリデーションのチェックがNGだった場合、入力された値を編集フォームに表示させるためにerr配列に格納
    $err['event_value']       = $arr_post['event'];
    $err['date_value']        = $arr_post['date'];
    $err['start_time_value']  = $arr_post['start_time'];
    $err['finish_time_value'] = $arr_post['finish_time'];
    $csrf_token               = $arr_post['csrf_token'];

    //エラー判定
    if(empty($arr_post['event'])) {
      $err['event_msg'] = 'イベント名を記入してください。';
    }
    if(mb_strlen($arr_post['event']) > 16) {
      $err['event_cnt_msg'] = 'タイトルの文字数が超過しています。15文字以内でお願いします。';
    }
    if(empty($arr_post['date'])) {
      $err['date_msg'] = '日にちを記入してください。';
    }
    if(empty($arr_post['start_time'])) {
      $err['start_time_msg'] = '開始時間を記入してください。';
    }
    if(empty($arr_post['finish_time'])) {
      $err['finish_time_msg'] = '終了時間を記入してください。';
    }
    if($arr_post['start_time'] > $arr_post['finish_time']) {
      $err['time_msg'] = '開始時間が終了時間よりも後になっています。';
    }
    //CSRF対策の判定
    $this->csrf($csrf_token);

    //エラーがあった場合、err配列の中身をセッションに格納し、編集フォームに遷移
    if(count($err) > 4) {
      $_SESSION['err_msg'] = $err;
      //POST元によって戻るページの判定
      header('Location: /event/event_regist.php');
    }
    //エラーがなかった場合、バリデーションチェックで一度NGだった場合を考慮して、セッションのerr_msgの中身を削除
    else {
      unset($_SESSION['err_msg']);
      //POSTの内容をセッションに格納
      $_SESSION['arr_post'] = $arr_post;
    }
  }

  public function validation_edit($arr_post) {
    //エラーメッセージを入れる配列
    $err                      = [];
     //バリデーションのチェックがNGだった場合、入力された値を編集フォームに表示させるためにerr配列に格納
    $err['event_value']       = $arr_post['event'];
    $err['date_value']        = $arr_post['date'];
    $err['start_time_value']  = $arr_post['start_time'];
    $err['finish_time_value'] = $arr_post['finish_time'];
    $err['event_id_value']    = $arr_post['event_id'];
    $csrf_token               = $arr_post['csrf_token'];

    //エラー判定
    if(empty($arr_post['event'])) {
      $err['event_msg'] = 'イベント名を記入してください。';
    }
    if(mb_strlen($arr_post['event']) > 16) {
      $err['event_cnt_msg'] = 'タイトルの文字数が超過しています。15文字以内でお願いします。';
    }
    if(empty($arr_post['date'])) {
      $err['date_msg'] = '日にちを記入してください。';
    }
    if(empty($arr_post['start_time'])) {
      $err['start_time_msg'] = '開始時間を記入してください。';
    }
    if(empty($arr_post['finish_time'])) {
      $err['finish_time_msg'] = '終了時間を記入してください。';
    }
    if($arr_post['start_time'] > $arr_post['finish_time']) {
      $err['time_msg'] = '開始時間が終了時間よりも後になっています。';
    }
    //CSRF対策の判定
    $this->csrf($csrf_token);

    //エラーがあった場合、err配列の中身をセッションに格納し、編集フォームに遷移
    if(count($err) > 5) {
      $_SESSION['err_msg'] = $err;
      //POST元によって戻るページの判定
      header('Location: /event/event_edit.php');
    }
    //エラーがなかった場合、バリデーションチェックで一度NGだった場合を考慮して、セッションのerr_msgの中身を削除
    else {
      unset($_SESSION['err_msg']);
      //POSTの内容をセッションに格納
      $_SESSION['arr_post'] = $arr_post;
    }
  }

  public function add($arr_post) {
    $sql    = "INSERT INTO event_data(date, event, start_time, finish_time, name_id, event_author, created_at) VALUES(:date, :event, :start_time, :finish_time, :name_id, :event_author, :created_at)";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':date'         => $arr_post['date'],
      ':event'        => $arr_post['event'],
      ':start_time'   => $arr_post['start_time'],
      ':finish_time'  => $arr_post['finish_time'],
      ':name_id'      => $arr_post['name_id'],
      ':event_author' => $arr_post['author'],
      ':created_at'   => $arr_post['time']
    );
    $stmt->execute($params);
  }

  public function get($event_id) {
    $sql    = 'SELECT * FROM event_data WHERE event_id = :event_id';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':event_id' => $event_id
    );
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //取得したログインユーザー情報をセッションのuserInfoに格納
    //TOPページに遷移する度に常に新しいログインユーザー情報を取得
    //ユーザー検索画面の編集リンクを押した時も同じロジックでユーザー情報を取得
    foreach($result[0] as $key => $value) {
      $getEventInfo['eventInfo'][$key] = $value;
    }
    return $getEventInfo;
  }

  public function edit($arr_post) {
    $sql    = "UPDATE event_data SET date = :date, event = :event, start_time = :start_time, finish_time = :finish_time, updated_at = :updated_at WHERE event_id = :event_id";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':date'        => $arr_post['date'],
      ':event'       => $arr_post['event'],
      ':start_time'  => $arr_post['start_time'],
      ':finish_time' => $arr_post['finish_time'],
      ':event_id'    => $arr_post['event_id'],
      ':updated_at'  => $arr_post['time']
    );
    $stmt->execute($params);
  }

  public function delete($event_id) {
    if(isset($event_id)) {
      $sql    = "DELETE FROM event_data WHERE event_id = :event_id";
      $stmt   = $this->dbh->prepare($sql);
      $params = array(':event_id' => (int)$event_id);
      $stmt->execute($params);
    }
  }

  public function csrf($csrf_token) {
    if(empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
      echo '指定されたパラメーターが不正です。このページは表示できません。';
      exit;
    }
    unset($_SESSION['csrf_token']);
  }
}