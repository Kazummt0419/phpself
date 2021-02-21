<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/attendController.php');

$security = new security();
$attend   = new attendController();
//ログインされている場合、trueを返す
$result   = $security->check_login();

//ログインユーザーが教職員以外の時、ページに入れないようにする
if($_SESSION['login_userInfo']['class'] != '教職員') {
  header('Location: /login/top.php');
  exit;
}

//確認フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  $attend->edit($arr_post);
  //更新する登園記録の情報を削除
  unset($_SESSION['attendInfo']);
}
//URLを直接記入しページ遷移された時
elseif($_SERVER["REQUEST_METHOD"] != "POST") {
  //更新する登園記録の情報を削除
  unset($_SESSION['attendInfo']);
  header('Location: /login/top.php');
  exit;
}
//ログインされていない場合
elseif(!$result) {
  //更新する登園記録の情報を削除
  unset($_SESSION['attendInfo']);
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
<link rel="stylesheet" type="text/css" href="/css/serchRecord_editComp.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-resrchRecordEdtiComp" class="c-marginTop5 c-marginBottom5">
    <div id="p-resrchRecordEdtiComp-message-box">
      <h3 class="p-resrchRecordEdtiComp-message-box__text">登園記録の更新が完了しました！</h3>
      <h3 class="p-resrchRecordEdtiComp-message-box__text c-marginTop1"><a href="serchRecord_result.php" class="c-font-black c-border-bottom">検索結果画面に戻る</a></h3>
      <h3 class="p-resrchRecordEdtiComp-message-box__text c-marginTop1"><a href="/login/top.php" class="c-font-black c-border-bottom">TOPに戻る</a></h3>
    </div>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
