<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/loginController.php');

$security = new security();
$login    = new loginController();

//編集フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  $login->validation($arr_post);
}
//URLを直接記入しページ遷移された時
elseif($_SERVER["REQUEST_METHOD"] != "POST") {
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
<link rel="stylesheet" type="text/css" href="/css/user_registConfirm.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header_login.php'); ?>
  <main id="p-userRegistConfirm" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-userRegistConfirm-head">
        <h2 id="p-userRegistConfirm-head__title">ユーザー情報登録確認</h2>
      </div>
      <h3 id="p-userRegistConfirm-form__head" class="c-font__bold c-marginTop5">以下の内容で問題ないでしょうか？</h3>
      <table id="p-userRegistConfirm-form-table">
        <tr>
          <th>名前</th>
          <td><?php echo $_POST['name']?></td>
        </tr>
        <tr>
          <th>ユーザー名</th>
          <td><?php echo $_POST['user_name']?></td>
        </tr>
        <tr>
          <th>メールアドレス</th>
          <td><?php echo $_POST['mail']?></td>
        </tr>
        <tr>
          <th>パスワード</th>
          <td><?php echo $_POST['pass']?></td>
        </tr>
      </table>
      <form action="user_registComp.php" method="POST">
        <input type="hidden" name="name" value="<?php echo $_POST['name']?>">
        <input type="hidden" name="user_name" value="<?php echo $_POST['user_name']?>">
        <input type="hidden" name="mail" value="<?php echo $_POST['mail']?>">
        <input type="hidden" name="pass" value="<?php echo $_POST['pass']?>">
        <input type="hidden" name="time" value="<?php echo date("Y/m/d H:i:s")?>">
        <div class="p-userRegistConfirm-form-buttonBox c-marginTop2">
          <input type="submit" value="OK" style="cursor:pointer" class="p-userRegistConfirm-form-buttonBox__button">
        </div>
        <div class="p-userRegistConfirm-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-userRegistConfirm-form-buttonBox__button">
        </div>
      </form>
    </section>
  </main>
  <?php require(ROOT_PATH. 'Views/footer_login.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
