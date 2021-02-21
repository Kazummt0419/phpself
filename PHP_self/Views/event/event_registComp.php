<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/eventController.php');

$security = new security();
$event    = new EventController();
//ログインされている場合、trueを返す
$result   = $security->check_login();

//確認フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  $event->add($arr_post);
}
//URLを直接記入しページ遷移された時
elseif($_SERVER["REQUEST_METHOD"] != "POST") {
  header('Location: /login/top.php');
  exit;
}
//ログインされていない場合
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
<link rel="stylesheet" type="text/css" href="/css/event_registComp.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-eventEdtiComp" class="c-marginTop5 c-marginBottom5">
    <div id="p-eventEdtiComp-message-box">
      <h3 class="p-eventEdtiComp-message-box__text">イベントの登録が完了しました！</h3>
      <h3 class="p-eventEdtiComp-message-box__text c-marginTop1"><a href="/login/top.php" class="c-font-black c-border-bottom">TOPに戻る</a></h3>
    </div>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
