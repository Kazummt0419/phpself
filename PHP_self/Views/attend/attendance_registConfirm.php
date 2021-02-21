<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/attendController.php');
$security = new security();
$attend   = new attendController();

//ログインされている場合、trueを返す
$result   = $security->check_login();
$today    = date("Y/m/d");
//セッションのログインユーザー情報をuserInfo変数に格納
$userInfo = $_SESSION['login_userInfo'];

//登録フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  $attend->validation($arr_post, $today);
}
//URLを直接記入しページ遷移された時もしくはログインユーザーが教員の時、ページに入れないようにする
elseif($_SERVER["REQUEST_METHOD"] != "POST" || $_SESSION['login_userInfo']['class'] == '教員') {
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
<link rel="stylesheet" type="text/css" href="/css/attendance_registConfirm.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-attendanceRegistConfirm" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-attendanceRegistConfirm-head">
        <h2 id="p-attendanceRegistConfirm-head__title">登園記録登録確認</h2>
      </div>
      <h3 id="p-attendanceRegistConfirm-form__head" class="c-font__bold">以下の内容で問題ないでしょうか？</h3>
      <table id="p-attendanceRegistConfirm-form-table">
        <tr>
          <th>日付</th>
          <td><?php echo $today ?></td>
        </tr>
        <tr>
          <th>クラス名</th>
          <td><?php echo $userInfo['class']; ?></td>
        </tr>
        <tr>
          <th>名前(園児名)</th>
          <td><?php echo $userInfo['name_child']; ?></td>
        </tr>
        <tr>
          <th>体温</th>
          <td><?php echo $_POST['temp']?></td>
        </tr>
        <tr>
          <th>連絡事項</th>
          <td><?php echo nl2br(htmlspecialchars($_POST['msg'], ENT_QUOTES, 'UTF-8'))?></td>
        </tr>
      </table>
      <form class="c-marginTop5" action="attendance_registComp.php" method="POST">
        <input type="hidden" name="date" value="<?php echo $today ?>">
        <input type="hidden" name="class" value="<?php echo $userInfo['class']?>">
        <input type="hidden" name="name_child" value="<?php echo $userInfo['name_child']?>">
        <input type="hidden" name="temp" value="<?php echo $_POST['temp']?>">
        <input type="hidden" name="msg" value="<?php echo htmlspecialchars($_POST['msg'], ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="time" value="<?php echo date("Y/m/d H:i:s")?>">
        <div class="p-userInfoRegistConfirm-form-buttonBox c-marginTop2">
        <div class="p-attendanceRegistConfirm-form-buttonBox c-marginTop2">
          <input type="submit" value="OK" style="cursor:pointer" class="p-attendanceRegistConfirm-form-buttonBox__button">
        </div>
        <div class="p-attendanceRegistConfirm-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-attendanceRegistConfirm-form-buttonBox__button">
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
