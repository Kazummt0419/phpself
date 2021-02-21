<?php
session_start();

if(!empty($_POST['logout'])) {
  $_SESSION = array();
  session_destroy();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/logout.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header_login.php'); ?>
  <main id="p-logout" class="c-marginTop5 c-marginBottom5">
    <div id="p-logout-message-box">
      <h3 class="p-logout-message-box__text">ログアウトしました！</h3>
      <h3 class="p-logout-message-box__text c-marginTop1"><a href="/login/login.php" class="c-font-black c-border-bottom">ログインページへ</a></h3>
    </div>
  </main>
  <?php require(ROOT_PATH. 'Views/footer_login.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
