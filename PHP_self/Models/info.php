<?php
require_once(ROOT_PATH .'/Models/Db.php');

class Info extends Db {
  public function __construct($dbh=null) {
    parent::__construct($dbh);
  }

  public function validation($arr_post) {
    //エラーメッセージを入れる配列
    $err                      = [];
     //バリデーションのチェックがNGだった場合、入力された値を編集フォームに表示させるためにerr配列に格納
    $err['title_value'] = $arr_post['title'];
    $err['info_value']  = htmlspecialchars($arr_post['Info'], ENT_QUOTES, 'UTF-8');
    $csrf_token         = $arr_post['csrf_token'];

    //エラー判定
    if(empty($arr_post['title'])) {
      $err['title_msg'] = 'タイトルを記入してください。';
    }
    if(mb_strlen($arr_post['title']) > 31) {
      $err['title_cnt_msg'] = 'タイトルの文字数が超過しています。30文字以内でお願いします。';
    }
    if(empty($arr_post['Info'])) {
      $err['info_value_msg'] = 'お知らせ内容記入してください。';
    }
    //CSRF対策の判定
    $this->csrf($csrf_token);

    //エラーがあった場合、err配列の中身をセッションに格納し、編集フォームに遷移
    if(count($err) > 2) {
      $_SESSION['err_msg'] = $err;
      //POST元によって戻るページの判定
      if($arr_post['vali_type'] == 'info_regist') {
        header('Location: /info/info_regist.php');
      }
      elseif($arr_post['vali_type'] == 'info_edit') {
        $err['info_id_value'] = $arr_post['info_id'];
        header('Location: /info/info_edit.php');
      }
    }
    //エラーがなかった場合、バリデーションチェックで一度NGだった場合を考慮して、セッションのerr_msgの中身を削除
    else {
      unset($_SESSION['err_msg']);
      //POSTの内容をセッションに格納
      $_SESSION['arr_post'] = $arr_post;
    }
  }

  public function add($arr_post) {
    $sql    = "INSERT INTO info_data(date, title, Info, name_id, info_author, created_at, updated_at) VALUES(:date, :title, :Info, :name_id, :info_author, :created_at, :updated_at)";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':date'        => $arr_post['time'],
      ':title'       => $arr_post['title'],
      ':Info'        => htmlspecialchars($arr_post['info'], ENT_QUOTES, 'UTF-8'),
      ':name_id'     => $arr_post['name_id'],
      ':info_author' => $arr_post['info_author'],
      ':created_at'  => $arr_post['created_at'],
      ':updated_at'  => $arr_post['created_at']
    );
    $stmt->execute($params);
  }

  public function count():Int {
    $sql    = 'SELECT count(*) FROM info_data';
    $stmt   = $this->dbh->prepare($sql);
    $stmt->execute();
    $count  = $stmt->fetchColumn();
    return $count;
  }

  public function serch($info_count, $page) {
    $sql    = 'SELECT info_id, date, title, info FROM info_data ORDER BY updated_at DESC LIMIT 3 OFFSET ' .(3 * $page);
    $stmt   = $this->dbh->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //検索結果が0件だった場合、入力された検索条件とエラーメッセージをerr配列に格納
    if(count($result) == 0) {
      $err['serch_msg']    = 'お知らせはありません。';
      //err配列の中身をセッションに格納し、検索画面に遷移
      $_SESSION['err_msg'] = $err;
    }
    //検索結果が1件以上あった場合、検索結果をセッションに格納
    else {
      foreach($result as $key => $value) {
        $info_serch[$key] = $value;
      }
      return $info_serch;
    }
  }

  public function get($info_id) {
    $sql    = 'SELECT * FROM info_data WHERE info_id = :info_id';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':info_id' => $info_id
    );
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //取得したログインユーザー情報をセッションのuserInfoに格納
    //TOPページに遷移する度に常に新しいログインユーザー情報を取得
    //ユーザー検索画面の編集リンクを押した時も同じロジックでユーザー情報を取得
    foreach($result[0] as $key => $value) {
      $getInfo['info'][$key] = $value;
    }
    return $getInfo;
  }

  public function edit($arr_post) {
    $sql    = "UPDATE info_data SET title = :title, Info = :Info, name_id = :name_id, info_author = :info_author, updated_at = :updated_at WHERE info_id = :info_id";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':info_id'     => (int)$arr_post['info_id'],
      ':title'       => $arr_post['title'],
      ':Info'        => htmlspecialchars($arr_post['Info'], ENT_QUOTES, 'UTF-8'),
      ':name_id'     => $arr_post['name_id'],
      ':info_author' => $arr_post['info_author'],
      ':updated_at'  => $arr_post['updated_at']
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