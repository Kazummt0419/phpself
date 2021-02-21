<?php
require_once(ROOT_PATH .'/Models/Db.php');

class UserInfo extends Db {
  public function __construct($dbh=null) {
    parent::__construct($dbh);
  }

  public function validation_regist($arr_post) {
    //エラーメッセージを入れる配列
    $err                      = [];
     //バリデーションのチェックがNGだった場合、入力された値を編集フォームに表示させるためにerr配列に格納
    $err['class_name_value']  = $arr_post['class_name'];
    $err['name_parent_value'] = $arr_post['name_parents'];
    $err['name_child_value']  = $arr_post['name_child'];
    $err['mail_value']        = $arr_post['mail'];
    $err['note_value']        = htmlspecialchars($arr_post['notes'], ENT_QUOTES, 'UTF-8');
    $csrf_token               = $arr_post['csrf_token'];

    //エラー判定
    if(empty($arr_post['class_name'])) {
      $err['class_name_msg'] = 'クラス名を選択して下さい。';
    }
    if(mb_strlen($arr_post['name_parents']) > 9) {
      $err['name_parent_cnt_msg'] = '名前(保護者)の文字数が超過しています。8文字以内でお願いします。';
    }
    if(empty($arr_post['name_parents'])) {
      $err['name_parent_msg'] = '名前(保護者)は必須項目です。';
    }
    if(mb_strlen($arr_post['name_child']) > 9) {
      $err['name_child_cnt_msg'] = '名前(園児名)の文字数が超過しています。8文字以内でお願いします。';
    }
    if(empty($arr_post['name_child'])) {
      $err['name_child_msg'] = '名前(園児名)は必須項目です。';
    }
    if(empty($arr_post['mail'])) {
      $err['mail_msg'] = 'メールアドレスは必須項目です。';
    }
    if(!preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/',$arr_post['mail']) && !empty($arr_post['mail'])) {
      $err['mail_match_msg'] = 'メールアドレスを正しく入力してください。';
    }
    //CSRF対策の判定
    $this->csrf($csrf_token);

    //エラーがあった場合、err配列の中身をセッションに格納し、編集フォームに遷移
    if(count($err) > 5) {
      $_SESSION['err_msg'] = $err;
      //ログインユーザー情報登録フォームからの場合
      if($arr_post['vali_type'] == 'regist') {
        header('Location: /user/userInfo_regist.php');
      }
      //検索結果の編集フォームからの場合
      if($arr_post['vali_type'] == 'edit_serch') {
        //NGだった場合、GETで受け取ったname_idを再度POST送信するのに必要なため
        $_SESSION['err_msg']['name_id'] = $arr_post['name_id'];
        header('Location: /user/serchUser_edit.php');
      }
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
    $err                    = [];
    //バリデーションのチェックがNGだった場合、入力された値を編集フォームに表示させるためにerr配列に格納
    $err['user_name_value'] = $arr_post['user_name'];
    $err['mail_value']      = $arr_post['mail'];
    $err['pass_value']      = $arr_post['pass'];
    $err['note_value']      = htmlspecialchars($arr_post['note'], ENT_QUOTES, 'UTF-8');
    $csrf_token             = $arr_post['csrf_token'];

    //ユーザー名が被ってないかのチェック
    $count = $this->countByusername($arr_post['user_name']);
    $result = $this->getByUsername($arr_post['user_name']);
    if($count > 0 && $result[0]['name_id'] != $_SESSION['login_userInfo']['name_id']) {
      $err['user_name_already_msg'] = 'すでに登録されているユーザー名です。別のものを設定してください。';
    }
    //エラー判定
    if(empty($arr_post['user_name'])) {
      $err['user_name_msg'] = 'ユーザー名は必須項目です。';
    }
    if(!preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,15}+\z/',$arr_post['user_name']) && !empty($arr_post['user_name'])) {
      $err['user_name_cnt_msg'] = 'ユーザー名は半角英数字大文字小文字を含めて8文字以上15文字以内で設定してください。';
    }
    if(empty($arr_post['mail'])) {
      $err['mail_msg'] = 'メールアドレスは必須項目です。';
    }
    if(!preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/',$arr_post['mail']) && !empty($arr_post['mail'])) {
      $err['mail_match_msg'] = 'メールアドレスを正しく入力してください。';
    }
    if(!preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,15}+\z/',$arr_post['pass']) && !empty($arr_post['pass'])) {
      $err['pass_cnt_msg'] = 'パスワードは半角英数字大文字小文字を含めて8文字以上15文字以内で設定してください。';
    }
    //CSRF対策の判定
    $this->csrf($csrf_token);

    //エラーがあった場合、err配列の中身をセッションに格納し、編集フォームに遷移
    if(count($err) > 4) {
      $_SESSION['err_msg'] = $err;
      header('Location: /user/login_userInfo_edit.php');
      exit;
    }
    //エラーがなかった場合、バリデーションチェックで一度NGだった場合を考慮して、セッションのerr_msgの中身を削除
    else {
      unset($_SESSION['err_msg']);
    }
  }

  public function validation_serch($arr_post) {
    //エラーメッセージを入れる配列
    $err                     = [];
    //バリデーションのチェックがNGだった場合、入力された値を編集フォームに表示させるためにerr配列に格納
    $err['class_name_value'] = $arr_post['class_name'];
    $err['name_child_value'] = $arr_post['name_child'];
    $csrf_token              = $arr_post['csrf_token'];

    //エラー判定
    if(empty($arr_post['class_name'])) {
      $err['class_name_msg'] = 'クラス名を選択して下さい。';
    }
    //CSRF対策の判定
    $this->csrf($csrf_token);

    //エラーがあった場合、err配列の中身をセッションに格納し、検索フォームに遷移
    if(count($err) > 2) {
      $_SESSION['err_msg'] = $err;
      header('Location: /user/serchUser.php');
    }
    //エラーがなかった場合
    else {
      //検索条件をセッションのcriteriaに格納と同時に前回格納された内容を削除
      unset($_SESSION['criteria']);
      $_SESSION['criteria']['class_name'] = $arr_post['class_name'];
      $_SESSION['criteria']['name_child'] = $arr_post['name_child'];
      //バリデーションチェックで一度NGだった場合を考慮して、セッションのerr_msgの中身を削除
      unset($_SESSION['err_msg']);
    }
  }

  public function get($name_id) {
    $sql    = 'SELECT * FROM user_data WHERE name_id = :name_id';
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
      $getUserInfo['userInfo'][$key] = $value;
    }
    return $getUserInfo;
  }

  public function getByUsername($username) {
    $sql    = 'SELECT name_id FROM user_data WHERE user = :user';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':user' => $username
    );
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  public function add($arr_post) {
    $sql    = "INSERT INTO user_data(name_parents, name_child, user, class, mail, password, notes, created_at) VALUES(:name_parents, :name_child, :user, :class, :mail, :password, :notes, :created_at)";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':name_parents' => $arr_post['name_parent'],
      ':name_child'   => $arr_post['name_child'],
      ':user'         => $arr_post['mail'],
      ':class'        => $arr_post['class_name'],
      ':mail'         => $arr_post['mail'],
      ':password'     => password_hash($arr_post['pass'], PASSWORD_DEFAULT),
      ':notes'        => htmlspecialchars($arr_post['note'], ENT_QUOTES, 'UTF-8'),
      ':created_at'   => $arr_post['time']
    );
    $stmt->execute($params);
  }

  public function edit_login($arr_post) {
    $sql    = "UPDATE user_data SET user = :user, mail = :mail, notes = :notes, updated_at = :updated_at WHERE name_id = :name_id";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':name_id'    => (int)$_SESSION['login_userInfo']['name_id'],
      ':user'       => $arr_post['user_name'],
      ':mail'       => $arr_post['mail'],
      ':notes'      => htmlspecialchars($arr_post['note'], ENT_QUOTES, 'UTF-8'),
      ':updated_at' => $arr_post['time']
    );
    $stmt->execute($params);
  }

  public function edit($arr_post) {
    $sql    = "UPDATE user_data SET class = :class, name_parents = :name_parents, name_child = :name_child, mail = :mail, notes = :notes, updated_at = :updated_at WHERE name_id = :name_id";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':name_id'      => (int)$arr_post['name_id'],
      ':class'        => $arr_post['class_name'],
      ':name_parents' => $arr_post['name_parent'],
      ':name_child'   => $arr_post['name_child'],
      ':mail'         => $arr_post['mail'],
      ':notes'        => htmlspecialchars($arr_post['note'], ENT_QUOTES, 'UTF-8'),
      ':updated_at'   => $arr_post['time']
    );
    $stmt->execute($params);
  }

  public function delete($name_id) {
    if(isset($name_id)) {
      $sql    = "DELETE FROM user_data WHERE name_id = :name_id";
      $stmt   = $this->dbh->prepare($sql);
      $params = array(':name_id' => (int)$name_id);
      $stmt->execute($params);
    }
  }

  public function serch($arr_post, $userinfo_count, $page) {
    //クラス名と園児名が検索条件にある場合
    if(!empty($arr_post['name_child'])) {
      $sql    = 'SELECT class, name_parents, name_child, mail, notes, name_id FROM user_data WHERE class = :class AND name_child = :name_child';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'      => $arr_post['class_name'],
        ':name_child' => $arr_post['name_child']
      );
    }
    //クラス名のみ検索条件にある場合
    else {
      $sql    = 'SELECT class, name_parents, name_child, mail, notes, name_id FROM user_data WHERE class = :class LIMIT 10 OFFSET ' .(10 * $page);
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'=> $arr_post['class_name']
      );
    }
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //検索結果が0件だった場合、入力された検索条件とエラーメッセージをerr配列に格納
    if(count($result) == 0) {
      $err['class_name_value'] = $arr_post['class_name'];
      $err['name_child_value'] = $arr_post['name_child'];
      $err['serch_msg']        = '検索結果が0件でした。';
      //err配列の中身をセッションに格納し、検索画面に遷移
      $_SESSION['err_msg']     = $err;
      header('Location: /user/serchUser.php');
      exit;
    }
    //検索結果が1件以上あった場合、検索結果をセッションに格納
    else {
      foreach($result as $key => $value) {
        $userinfo_serch[$key] = $value;
      }
      return $userinfo_serch;
    }
  }

  public function count($arr_post):Int {
    //クラス名と園児名が検索条件にある場合
    if(!empty($arr_post['name_child'])) {
      $sql    = 'SELECT count(*) FROM user_data WHERE class = :class AND name_child = :name_child';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'      => $arr_post['class_name'],
        ':name_child' => $arr_post['name_child']
      );
    }
    //クラス名のみ検索条件にある場合
    else {
      $sql    = 'SELECT count(*) FROM user_data WHERE class = :class';
      $stmt   = $this->dbh->prepare($sql);
      $params = array(
        ':class'=> $arr_post['class_name']
      );
    }
    $stmt->execute($params);
    $count = $stmt->fetchColumn();
    return $count;
  }

  public function countByusername($username):INT {
    $sql    = 'SELECT count(*) FROM user_data WHERE user = :user';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':user'=> $username
    );
    $stmt->execute($params);
    $count = $stmt->fetchColumn();
    return $count;
  }

  public function csrf($csrf_token) {
    if(empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
      echo '指定されたパラメーターが不正です。このページは表示できません。';
      exit;
    }
    unset($_SESSION['csrf_token']);
  }
}