<?php
require_once(ROOT_PATH .'/Models/Db.php');

class login extends Db {
  public function __construct($dbh=null) {
    parent::__construct($dbh);
  }

  public function validation($arr_post) {
    //エラーメッセージを入れる配列
    $err                    = [];
    //バリデーションのチェックがNGだった場合、入力された値をログインフォームに表示させるためにerr配列に格納
    $err['user_name_value'] = $arr_post['user_name'];
    $err['password_value']  = $arr_post['password'];
    $csrf_token             = $arr_post['csrf_token'];

    //ユーザー名とパスワードが入力されているかの判定
    if(empty($arr_post['user_name'])) {
      $err['user_name_msg'] = 'ユーザー名を入力してください。';
    }
    if(empty($arr_post['password'])) {
      $err['password_msg'] = 'パスワードを入力してください。';
    }
    //ユーザー名とパスワードが一致しているか判定
    if(empty($err['user_name_msg']) && empty($err['password_msg'])) {
      $result = $this->check_username_pass($arr_post['user_name'], $arr_post['password']);
      if(!$result) {
        $err['falt_msg'] = 'ユーザー名もしくはパスワードが違います。';
      }
      //ユーザー情報取得
      else {
        $getUserInfoByName = $this->getByUsername($arr_post['user_name']);
      }
    }
    //CSRF対策の判定
    if(empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
      exit('不正リクエスト');
    }
    unset($_SESSION['csrf_token']); //トークンを削除

    //エラーがあった場合、err配列の中身をセッションに格納し、ログインフォームに遷移
    if(count($err) > 2) {
      $_SESSION['err_msg'] = $err;
      header('Location: /login/login.php');
      exit;
    }
    //ログイン成功の場合、セッションidを再生成し、ユーザー名とそれに付随するuser_dataテーブルから取得した値をセッションに格納。
    else {
      session_regenerate_id(true);
      $_SESSION['id']               = session_id();
      $_SESSION['login_user']       = $arr_post['user_name'];
      $_SESSION['login_userInfo']   = $getUserInfoByName[0];
    }
  }

  public function validation_reset($arr_post) {
    //エラーメッセージを入れる配列
    $err                    = [];
    //バリデーションのチェックがNGだった場合、入力された値をログインフォームに表示させるためにerr配列に格納
    $err['pass_value']      = $arr_post['pass'];
    $err['pass_conf_value'] = $arr_post['pass_conf'];
    $err['name_id_value']   = $arr_post['name_id'];
    $csrf_token             = $arr_post['csrf_token'];

    //フォームが入力されているかの判定
    if(empty($arr_post['pass'])) {
      $err['pass_msg'] = '新しいパスワードを入力してください。';
    }
    if(empty($arr_post['pass_conf'])) {
      $err['pass_conf_msg'] = '確認用のパスワードを入力してください。';
    }
    //新しいものと確認用が一致しているか判定
    if(($arr_post['pass'] != $arr_post['pass_conf']) && !empty($arr_post['pass_conf']) && !empty($arr_post['pass'])) {
      $err['match_msg'] = '確認用のパスワードには新しいパスワードと同じものを入力してください。';
    }
    if(!preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,15}+\z/',$arr_post['pass']) && !empty($arr_post['pass'])) {
      $err['pass_match_msg'] = 'パスワードは半角英数字大文字小文字を含めて8文字以上15文字以内で設定してください。';
    }
    //CSRF対策の判定
    if(empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
      exit('不正リクエスト');
    }
    unset($_SESSION['csrf_token']); //トークンを削除

    //エラーがあった場合、err配列の中身をセッションに格納し、ログインフォームに遷移
    if(count($err) > 3) {
      $_SESSION['err_msg'] = $err;
      header('Location: /login/change_pass.php');
      exit;
    }
    else {
      $result = true;
      return $result;
    }
  }

  public function validation_regist($arr_post) {
    //エラーメッセージを入れる配列
    $err                      = [];
     //バリデーションのチェックがNGだった場合、入力された値を編集フォームに表示させるためにerr配列に格納
    $err['name_value']  = $arr_post['name'];
    $err['user_name_value'] = $arr_post['user_name'];
    $err['mail_value']        = $arr_post['mail'];
    $err['pass_value']  = $arr_post['pass'];
    $csrf_token               = $arr_post['csrf_token'];

    //エラー判定
    if(empty($arr_post['name'])) {
      $err['name_msg'] = '名前を記入してください。';
    }
    if(mb_strlen($arr_post['name']) > 9) {
      $err['name_cnt_msg'] = '名前の文字数が超過しています。8文字以内でお願いします。';
    }
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
    if(empty($arr_post['pass'])) {
      $err['pass_msg'] = 'パスワードは必須項目です。';
    }
    if(!preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,15}+\z/',$arr_post['pass']) && !empty($arr_post['pass'])) {
      $err['pass_cnt_msg'] = 'パスワードは半角英数字大文字小文字を含めて8文字以上15文字以内で設定してください。';
    }
    //CSRF対策の判定
    $this->csrf($csrf_token);

    //エラーがあった場合、err配列の中身をセッションに格納し、編集フォームに遷移
    if(count($err) > 4) {
      $_SESSION['err_msg'] = $err;
      //ログインユーザー情報登録フォームからの場合
      header('Location: /login/user_regist.php');
    }
    //エラーがなかった場合、バリデーションチェックで一度NGだった場合を考慮して、セッションのerr_msgの中身を削除
    else {
      unset($_SESSION['err_msg']);
      //POSTの内容をセッションに格納
      $_SESSION['arr_post'] = $arr_post;
    }
  }

  public function check_username_pass($user_name, $password) {
    $sql    = 'SELECT * FROM user_data WHERE user = :user';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':user' => $user_name,
    );
    $stmt->execute($params);
    $getUserInfoByPassName = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($getUserInfoByPassName) == 1) {
      $result = password_verify($password, $getUserInfoByPassName[0]['password']);
      return $result;
    }
  }

  public function getByUsername($user_name) {
    $sql    = 'SELECT * FROM user_data WHERE user = :user';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':user' => $user_name,
    );
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  public function check_mail($arr_post) {
    $sql    = 'SELECT count(*) FROM user_data WHERE mail = :mail';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':mail' => $arr_post['mail']
    );
    $stmt->execute($params);
    $count  = $stmt->fetchColumn();
    return $count;
  }

  public function edit($to, $passResetToken, $timeToken) {
    $sql    = "UPDATE user_data SET reset_token = :reset_token, issue_at = :issue_at WHERE mail = :mail";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':reset_token' => $passResetToken,
      ':issue_at'    => $timeToken,
      ':mail'        => $to
    );
    $stmt->execute($params);
  }

  public function edit_pass($arr_post) {
    $sql    = "UPDATE user_data SET password = :password WHERE name_id = :name_id";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':password' => password_hash($arr_post['pass'], PASSWORD_DEFAULT),
      ':name_id'  => $arr_post['name_id']
    );
    $stmt->execute($params);
  }

  public function serch_token($token) {
    $sql    = 'SELECT issue_at, name_id FROM user_data WHERE reset_token = :reset_token';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':reset_token' => $token
    );
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result[0];
  }

  public function get($name_id) {
    $sql    = 'SELECT count(*) FROM user_data WHERE name_id = :name_id';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':name_id' => $name_id
    );
    $stmt->execute($params);
    $count = $stmt->fetchColumn();
    return $count;
  }

  public function add($arr_post) {
    $sql    = "INSERT INTO user_data(name_parents, name_child, user, class, mail, password, created_at) VALUES(:name_parents, :name_child, :user, :class, :mail, :password, :created_at)";
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':name_parents' => $arr_post['name'],
      ':name_child'   => $arr_post['name'],
      ':user'         => $arr_post['user_name'],
      ':class'        => '教職員',
      ':mail'         => $arr_post['mail'],
      ':password'     => password_hash($arr_post['pass'], PASSWORD_DEFAULT),
      ':created_at'   => $arr_post['time']
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