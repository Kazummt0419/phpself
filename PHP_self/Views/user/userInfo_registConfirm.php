<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/userInfoController.php');

$security = new security();
$user     = new UserInfoController();
//ログインされている場合、trueを返す
$result   = $security->check_login();

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
//ログインユーザーが教職員以外の時、ページに入れないようにする
elseif($_SESSION['login_userInfo']['class'] != '教職員') {
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
<link rel="stylesheet" type="text/css" href="/css/userInfo_registConfirm.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-userInfoRegistConfirm" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-userInfoRegistConfirm-head">
        <h2 id="p-userInfoRegistConfirm-head__title">ユーザー情報登録確認</h2>
      </div>
      <h3 id="p-userInfoRegistConfirm-form__head" class="c-font__bold c-marginTop5">以下の内容で問題ないでしょうか？</h3>
      <table id="p-userInfoRegistConfirm-form-table">
        <tr>
          <th>クラス名</th>
          <td><?php echo $_POST['class_name']?></td>
        </tr>
        <tr>
          <th>名前</th>
          <td><?php echo $_POST['name_parents']?></td>
        </tr>
        <tr>
          <th>名前(園児名)</th>
          <td><?php echo $_POST['name_child']?></td>
        </tr>
        <tr>
          <th>メールアドレス</th>
          <td><?php echo $_POST['mail']?></td>
        </tr>
        <tr>
          <th>備考</th>
          <td class="c-note"><?php echo nl2br(htmlspecialchars($_POST['notes'], ENT_QUOTES, 'UTF-8'))?></td>
        </tr>
      </table>
      <form action="userInfo_registComp.php" method="POST">
        <input type="hidden" name="class_name" value="<?php echo $_POST['class_name']?>">
        <input type="hidden" name="name_parent" value="<?php echo $_POST['name_parents']?>">
        <input type="hidden" name="name_child" value="<?php echo $_POST['name_child']?>">
        <input type="hidden" name="user_name" value="<?php echo $_POST['mail']?>">
        <input type="hidden" name="mail" value="<?php echo $_POST['mail']?>">
        <input type="hidden" name="pass" value="<?php echo 'Password55'?>">
        <input type="hidden" name="note" value="<?php echo htmlspecialchars($_POST['notes'], ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="time" value="<?php echo date("Y/m/d H:i:s")?>">
        <div class="p-userInfoRegistConfirm-form-buttonBox c-marginTop2">
          <input type="submit" value="OK" style="cursor:pointer" class="p-userInfoRegistConfirm-form-buttonBox__button">
        </div>
        <div class="p-userInfoRegistConfirm-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-userInfoRegistConfirm-form-buttonBox__button">
        </div>
      </form>
    </section>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
