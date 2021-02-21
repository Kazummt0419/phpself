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

//ログインフォームから値がPOSTされている場合、ログインフォームで入力された値をバリデーションチェック
if(isset($_POST['vali_type']) && $_POST['vali_type'] == 'login') {
  $arr_post = $_POST;
  //validationメソッドの中で、user_dataテーブルからログインユーザーの情報を取得しセッションに格納（userInfo）
  $login->validation($arr_post);
  header('Location: /login/top.php');
}
//ログインフォームから値がPOSTされていない場合、ログインフォームに強制的に移動させる
elseif(!$result) {
  header('Location: /login/login.php');
  exit;
}

//ログイン成功の場合、セッションのuserInfoの値をuserInfo変数に格納し、name_idをkeyにしてログインユーザー情報を取得
if(isset($_SESSION['login_userInfo'])) {
  //ログインユーザー情報の編集をした際、編集された最新の内容を表示させるため。
  $getUserInfo = $user->get($_SESSION['login_userInfo']['name_id']);
  //getメソッドでセッションに格納された情報をuserInfo変数に格納
  $userInfo                   = $getUserInfo['userInfo'];
  $_SESSION['login_userInfo'] = $getUserInfo['userInfo'];
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/top.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome-animation/0.0.10/font-awesome-animation.css" type="text/css" media="all" />
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-top" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-top-userInfo-head">
        <h2 id="p-top-userInfo-head__title">ログインユーザー情報</h2>
        <p><a href="/user/login_userInfo_edit.php" id="p-top-userInfo-edit__link" class="c-border-bottom">ログインユーザー情報の編集</a></p>
      </div>
      <table id="p-top-userInfo-table">
        <tr>
          <th>名前</th>
          <td><?php echo $userInfo['name_parents']; ?></td>
        </tr>
        <?php if($_SESSION['login_userInfo']['class'] != '教職員'): ?>
          <tr>
            <th>名前(園児名)</th>
            <td><?php echo $userInfo['name_child']; ?></td>
          </tr>
        <?php endif; ?>
        <tr>
          <th>ユーザー名</th>
          <td><?php echo $userInfo['user']; ?></td>
        </tr>
        <tr>
          <th>クラス名</th>
          <td><?php echo $userInfo['class']; ?></td>
        </tr>
        <tr>
          <th>メールアドレス</th>
          <td><?php echo $userInfo['mail']; ?></td>
        </tr>
        <tr>
          <th>備考</th>
          <td class="c-note"><p><?php echo $userInfo['notes']; ?></td>
        </tr>
      </table>
    </section>
    <?php if($_SESSION['login_userInfo']['class'] !== '卒園済'): ?>
      <div class="c-ajax">
        <?php require(ROOT_PATH. '/Views/calender.php'); ?>
      </div>
    <?php endif; ?>
  </main>
  <?php require(ROOT_PATH. '/Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/calender.js"></script>
</body>
</html>