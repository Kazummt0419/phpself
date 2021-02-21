<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/attendController.php');

$security   = new security();
$attend     = new attendController();

//ログインされている場合、trueを返す
$result     = $security->check_login();

//ログインされていない場合
if(!$result) {
  header('Location: /login/login.php');
  exit;
}
//ログインユーザーが教員の時、ページに入れないようにする
elseif($_SESSION['login_userInfo']['class'] == '教員') {
  header('Location: /login/top.php');
  exit;
}
//ログインされている場合
else {
  $arr_post = $_POST;
  $attend->add($arr_post);
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/attendance_registComp.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-attendanceRegistComp" class="c-marginTop5 c-marginBottom5">
    <div id="p-attendanceRegistComp-message-box">
      <h3 class="p-attendanceRegistComp-message-box__text">登園記録の登録が完了しました！</h3>
      <h3 class="p-attendanceRegistComp-message-box__text c-marginTop1"><a href="/login/top.php" class="c-font-black c-border-bottom">TOPに戻る</a></h3>
    </div>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
