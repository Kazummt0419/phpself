<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/infoController.php');

$security = new security();
$info     = new infoController();
//ログインされている場合、trueを返す
$result   = $security->check_login();

//ログインユーザーが教職員以外の時、ページに入れないようにする
if($_SESSION['login_userInfo']['class'] != '教職員') {
  header('Location: /login/top.php');
  exit;
}

//編集フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  $info->validation($arr_post);
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
<link rel="stylesheet" type="text/css" href="/css/info_editConfirm.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-infoEditConfirm" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-infoEditConfirm-head">
        <h2 id="p-infoEditConfirm-head__title">お知らせ編集確認</h2>
      </div>
      <h3 id="p-infoEditConfirm-form__head" class="c-font__bold">以下の内容で問題ないでしょうか？</h3>
      <table id="p-infoEditConfirm-form-table">
        <tr>
          <th>タイトル</th>
          <td><?php echo $_POST['title']?></td>
        </tr>
        <tr>
          <th>内容</th>
          <td class="c-note"><?php echo htmlspecialchars($_POST['Info'], ENT_QUOTES, 'UTF-8')?></td>
        </tr>
      </table>
      <form class="c-marginTop5" action="info_editComp.php" method="POST">
        <input type="hidden" name="info_id" value="<?php echo $_POST['info_id']?>">
        <input type="hidden" name="title" value="<?php echo $_POST['title']?>">
        <input type="hidden" name="Info" value="<?php echo htmlspecialchars($_POST['Info'], ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="name_id" value="<?php echo $_SESSION['login_userInfo']['name_id']?>">
        <input type="hidden" name="info_author" value="<?php echo $_SESSION['login_userInfo']['name_parents']?>">
        <input type="hidden" name="updated_at" value="<?php echo date("Y/m/d H:i:s")?>">
        <div class="p-infoEditConfirm-form-buttonBox c-marginTop2">
          <input type="submit" value="OK" style="cursor:pointer" class="p-infoEditConfirm-form-buttonBox__button">
        </div>
        <div class="p-infoEditConfirm-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-infoEditConfirm-form-buttonBox__button">
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
