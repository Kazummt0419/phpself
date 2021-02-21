<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/loginController.php');
$security = new security();
$login    = new loginController();

//トークンの発行
$csrf_token = $security->token();
//ログインされている場合、trueを返す
$result     = $security->check_login();

$result_adminUser = $login->get("1");

//ログインされている状態の時、TOPページに遷移
if($result) {
  header('Location: /login/top.php');
  exit;
}
//ログインされていない時
else {
  //バリデーションのエラー内容と入力されていた内容をセッションからerr配列に格納
  $err = $_SESSION;
  //前回のバリデーションのチェックで使用した内容を削除
  unset($err['csrf_token']);
  foreach($_SESSION as $key => $value) {
    if($key == 'err_msg') {
      unset($_SESSION['err_msg']);
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/login.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header_login.php'); ?>
  <!-- バリデーションエラーメッセージ -->
  <?php if(isset($err['err_msg'])): ?>
    <ul id="p-login-errList">
      <?php foreach ($err['err_msg'] as $key => $value): ?>
        <?php if($key == 'user_name_msg' || $key == 'password_msg' || $key == 'falt_msg'): ?>
          <li class="p-login-errList__item c-font__red"><?php echo $value; ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  <main id="p-login" class="c-marginTop4 c-marginBottom5">
    <form action="top.php" method="POST" id="p-login-form">
      <h3 id="p-login-form__head">ユーザー名とパスワードを入力し、ログインボタンを押してください。</h3>
      <table id="p-login-form-table">
        <tr>
          <th>ユーザー名</th>
          <td><input type="text" name="user_name" value= "<?php if(isset($err['err_msg']['user_name_value'])): ?><?php echo $err['err_msg']['user_name_value']?><?php endif; ?>"></td>
        </tr>
        <tr>
          <th>パスワード</th>
          <td><input type="text" name="password" value= "<?php if(isset($err['err_msg']['password_value'])): ?><?php echo $err['err_msg']['password_value']?><?php endif; ?>"></td>
        </tr>
      </table>
      <div id="p-login-form-buttonBox" class="c-marginTop2">
        <input type="submit" value="ログイン" style="cursor:pointer" id="p-login-form-buttonBox__button">
      </div>
      <input type="hidden" name="csrf_token" value= "<?php echo $csrf_token; ?>">
      <input type="hidden" name="vali_type" value= "login">
    </form>
    <p id="p-login-passReset__text" class="c-marginTop2">パスワードをお忘れの方は<a href="/login/reset_pass.php" class="c-font-black c-border-bottom">こちら</a></p>
    <?php if($result_adminUser): ?>
      <p id="p-login-passReset__text" class="c-marginTop2">新規登録画面は<a href="/login/user_regist.php" class="c-font-black c-border-bottom">こちら</a><br>※このアプリへの新規登録は初めて登録されたユーザーのみ許可</p>
    <?php endif; ?>
  </main>
  <?php require(ROOT_PATH. 'Views/footer_login.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>