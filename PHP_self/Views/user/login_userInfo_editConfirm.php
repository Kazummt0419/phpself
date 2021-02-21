<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/userInfoController.php');

$security = new security();
$user     = new UserInfoController();
//ログインされている場合、trueを返す
$result   = $security->check_login();
//セッションのログインユーザー情報をuserInfo変数に格納
$userInfo = $_SESSION['login_userInfo'];

//編集フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  $_SESSION['post_value'] = $_POST;
  $user->validation($arr_post);
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
<link rel="stylesheet" type="text/css" href="/css/login_userInfo_editConfirm.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-userInfoEdtiConfirm" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-userInfoEdtiConfirm-head">
        <h2 id="p-userInfoEdtiConfirm-head__title">ログインユーザー情報編集確認</h2>
      </div>
      <h3 id="p-userInfoEdtiConfirm-form__head" class="c-font__bold">以下の内容で問題ないでしょうか？</h3>
      <table id="p-userInfoEdtiConfirm-form-table">
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
          <td><?php echo $_POST['user_name']?></td>
        </tr>
        <tr>
          <th>クラス名</th>
          <td><?php echo $userInfo['class']; ?></td>
        </tr>
        <tr>
          <th>メールアドレス</th>
          <td><?php echo $_POST['mail']?></td>
        </tr>
        <tr>
          <th>パスワード</th>
          <td><?php echo $_POST['pass']?></td>
        </tr>
        <tr>
          <th>備考</th>
          <td class="c-note"><?php echo nl2br(htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8'))?></td>
        </tr>
      </table>
      <form class="c-marginTop5" action="login_userInfo_editComp.php" method="POST">
        <input type="hidden" name="user_name" value="<?php echo $_POST['user_name']?>">
        <input type="hidden" name="mail" value="<?php echo $_POST['mail']?>">
        <input type="hidden" name="note" value="<?php echo htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="time" value="<?php echo date("Y/m/d H:i:s")?>">
        <input type="hidden" name="edit_type" value="loginUser">
        <div class="p-userInfoEdtiConfirm-form-buttonBox c-marginTop2">
          <input type="submit" value="OK" style="cursor:pointer" class="p-userInfoEdtiConfirm-form-buttonBox__button">
        </div>
        <div class="p-userInfoEdtiConfirm-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-userInfoEdtiConfirm-form-buttonBox__button">
        </div>
      </form>
    </section>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/index.js"></script>
</body>
</html>