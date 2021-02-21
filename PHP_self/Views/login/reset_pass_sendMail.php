<?php
require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/loginController.php');

$security = new security();
$login    = new loginController();
//ログインされている場合、trueを返す
$result   = $security->check_login();

//ログインフォームから値がPOSTされている場合、ログインフォームで入力された値をバリデーションチェック
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  //POSTされたメールアドレスがuser_dataテーブルにあるかどうかのチェック
  $resultMail = $login->check_mail($arr_post);
}
//ログインフォームから値がPOSTされていない場合、ログインフォームに強制的に移動させる
elseif(!$result) {
  header('Location: /login/login.php');
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/reset_pass_sendMail.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header_login.php'); ?>
  <main id="p-resetPassMail" class="c-marginTop5 c-marginBottom5">
    <div id="p-resetPassMail-message-box">
      <h3 class="p-resetPassMail-message-box__text">登録されているメールアドレスに</h3>
      <h3 class="p-resetPassMail-message-box__text c-marginTop1">パスワードリセット用のメールが送信されました。</h3>
      <h3 class="p-resetPassMail-message-box__text c-marginTop1 c-font__bold">10分以内に対応してください。</h3>
    </div>
  </main>
  <?php require(ROOT_PATH. 'Views/footer_login.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
