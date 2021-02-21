<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/loginController.php');
require_once(ROOT_PATH. 'Controllers/userInfoController.php');

$security = new security();
$login    = new loginController();
$user     = new UserInfoController();
//ログインされている場合、trueを返す
$result    = $security->check_login();

//パスワードリセットフォームから値がPOSTされている場合、ログインフォームで入力された値をバリデーションチェック
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  //validationメソッドの中で、user_dataテーブルからログインユーザーの情報を取得
  $result = $login->validation($arr_post);
  if($result) {
    $login->edit_pass($arr_post);
  }
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
<link rel="stylesheet" type="text/css" href="/css/change_pass_comp.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header_login.php'); ?>
  <main id="p-changePassComp" class="c-marginTop5 c-marginBottom5">
    <div id="p-changePassComp-message-box">
      <h3 class="p-changePassComp-message-box__text">パスワードの変更が完了しました！</h3>
      <h3 class="p-changePassComp-message-box__text c-marginTop1"><a href="login.php" class="c-font-black c-border-bottom">ログイン画面に戻る</a></h3>
    </div>
  </main>
  <?php require(ROOT_PATH. 'Views/footer_login.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
