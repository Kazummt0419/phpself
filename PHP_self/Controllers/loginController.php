<?php
require_once(ROOT_PATH. '/Models/login.php');

class loginController {
  private $login;

  public function __construct() {
    //モデルオブジェクトの生成
    $this->login = new login();
  }

  //バリデーション
  public function validation($arr_post) {
    if($arr_post['vali_type'] == 'reset') {
      $result = $this->login->validation_reset($arr_post);
      return $result;
    }elseif($arr_post['vali_type'] == 'login') {
      $this->login->validation($arr_post);
    }elseif($arr_post['vali_type'] == 'regist') {
      $this->login->validation_regist($arr_post);
    }
  }

  //ログインチェック。ログインされている場合、TRUEを返す。
  public function check_login() {
    $result = false;
    $result = $this->login->check_login();
    return $result;
  }

  //入力されたメールが登録されているかチェック。登録されている場合、メールを送信し、トークンと発行時間をテーブルに格納
  public function check_mail($arr_post) {
    $count = $this->login->check_mail($arr_post);
    //メールが登録されていた場合
    if($count > 0) {
      //パスワードのリセット用にトークンを発行
      $passResetToken = md5(uniqid(rand(),true));
      //トークンが発行された時間を発行 (10分以内に対応してもらうため)
      $timeToken = date("Y-m-d H:i:s");
      //メール送信用の変数
      $to      = $_POST['mail'];
      $subject = '【パスワードリセット用ページの送信】10分以内に対応お願いします。';
      $message = "パスワードリセット用のページを送信しました。"."\r\n".
                "10分以内に下記リンクより対応よろしくお願い致します。"."\r\n".
                "http://localhost/login/change_pass.php?passReset=". $passResetToken
                ;
      $header  = "From: kazummt0419@gmail.com " . "\r\n";
      //メールを送信する
      $resultSend = mb_send_mail($to, $subject, $message, $header);
      //メールが送信されなかったら
      if(!$resultSend) {
        header('Location: /login/login.php');
        exit;
      }
      //トークンと発行時間をuser_dataテーブルに登録
      $this->edit($to, $passResetToken, $timeToken);
    }
  }

  //トークンと発行時間を格納
  public function edit($to, $passResetToken, $timeToken) {
    $this->login->edit($to, $passResetToken, $timeToken);
  }

  public function edit_pass($arr_post) {
    $this->login->edit_pass($arr_post);
  }

  public function add($arr_post) {
    $this->login->add($arr_post);
  }

  public function get($name_id) {
    $result = false;
    $count = $this->login->get($name_id);
    if($count == "0") {
      $result = true;
    }
    return $result;
  }

  //メールのURLを踏んできたトークンと発行時間のチェック
  public function check_token($token) {
    //DBにトークンと発行時間があるかをチェック
    $result = $this->login->serch_token($token);
    if(isset($result)) {
      //URLを踏んだ時間
      $now  = date("Y-m-d H:i:s");
      $diff = date_diff(new DateTime($result['issue_at']), new DateTime($now))->format('%H:%I:%S');
      //10分以上経過していた場合
      if(strtotime($diff) > strtotime('00:10:00')) {
        header('Location: /login/login.php');
        exit;
      }
    }
    //一致するトークンがなかった場合
    else {
      header('Location: /login/login.php');
    }
    return $result;
  }
}