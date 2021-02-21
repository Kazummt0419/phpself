<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/userInfoController.php');
$security   = new security();
$userInfo   = new UserInfoController();

//ログインされている場合、trueを返す
$result     = $security->check_login();

//ログインユーザーが教職員以外の時、ページに入れないようにする
if($_SESSION['login_userInfo']['class'] != '教職員') {
  header('Location: /login/top.php');
  exit;
}

//確認フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $name_id = $_POST['name_id'];
  //ユーザー情報を登録するメソッドの呼び出し
  $userInfo->delete($name_id);
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
<link rel="stylesheet" type="text/css" href="/css/serchUser_deleteComp.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-serchUserDeleteComp" class="c-marginTop5 c-marginBottom5">
    <div id="p-serchUserDeleteComp-message-box">
      <h3 class="p-serchUserDeleteComp-message-box__text">ユーザー情報の削除が完了しました！</h3>
      <h3 class="p-serchUserDeleteComp-message-box__text c-marginTop1"><a href="serchUser_result.php" class="c-font-black c-border-bottom">検索結果画面に戻る</a></h3>
      <h3 class="p-serchUserDeleteComp-message-box__text c-marginTop1"><a href="/login/top.php" class="c-font-black c-border-bottom">TOPに戻る</a></h3>
    </div>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
