<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/loginController.php');
$login    = new loginController();
$security = new security();

//トークンの発行
$csrf_token = $security->token();

//メールで送られたURLを踏んだ場合
if(isset($_GET['passReset'])) {
  //GETされたトークンがDBにあるか。発行時間から10分過ぎていないチェック
  $resultMail = $login->check_token($_GET['passReset']);
  //10分過ぎていない場合、セッションにname_idを格納
  if(isset($resultMail)) {
    $name_id = $resultMail['name_id'];
  }
}
//バリデーションエラーがあった場合
elseif(isset($_SESSION['err_msg'])) {
  //バリデーションのエラー内容と入力されていた内容をセッションからerr配列に格納
  $err = $_SESSION;
  $name_id = $err['err_msg']['name_id_value'];
  //前回のバリデーションのチェックで使用したトークンを削除
  unset($err['csrf_token']);
  unset($_SESSION['err_msg']);
}
//メールで送られたURLを踏んでいない場合、ログインフォームに強制的に移動させる
else {
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
<link rel="stylesheet" type="text/css" href="/css/change_pass.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header_login.php'); ?>
  <main id="p-changePass" class="c-marginTop5 c-marginBottom5">
    <!-- バリデーションエラーメッセージ -->
    <?php if(isset($err['err_msg'])): ?>
      <ul id="p-changePass-errList">
        <?php foreach ($err['err_msg'] as $key => $value): ?>
          <?php if($key == 'pass_msg' || $key == 'pass_conf_msg' || $key == 'match_msg' || $key == 'pass_match_msg'): ?>
            <li class="p-changePass-errList__item c-font__red"><?php echo $value; ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <form action="change_pass_comp.php" method="POST" id="p-resetPass-form">
      <h3 class="p-changePass-form__head">新しいパスワードを設定してください。</h3>
      <table id="p-changePass-form-table">
        <tr>
          <th>新しいパスワード</th>
          <td><input type="text" name="pass" value= "<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['pass_value']?><?php endif; ?>"></td>
        </tr>
        <tr>
          <th>確認用</th>
          <td><input type="text" name="pass_conf" value= "<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['pass_conf_value']?><?php endif; ?>"></td>
        </tr>
      </table>
      <div id="p-changePass-form-buttonBox" class="c-marginTop2">
        <input type="submit" value="完&nbsp;了" style="cursor:pointer" id="p-changePass-form-buttonBox__button">
      </div>
      <input type="hidden" name="vali_type" value= "reset">
      <input type="hidden" name="name_id" value= "<?php echo $name_id; ?>">
      <input type="hidden" name="csrf_token" value= "<?php echo $csrf_token; ?>">
    </form>
  </main>
  <?php require(ROOT_PATH. 'Views/footer_login.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
