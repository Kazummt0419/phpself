<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/eventController.php');

$security = new security();
$event    = new EventController();
//ログインされている場合、trueを返す
$result   = $security->check_login();

//編集フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  $event->validation($arr_post);
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
<link rel="stylesheet" type="text/css" href="/css/event_registConfirm.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-eventEdtiConfirm" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-eventEdtiConfirm-head">
        <h2 id="p-eventEdtiConfirm-head__title">イベント登録/編集確認</h2>
      </div>
      <h3 class="p-eventEdtiConfirm-form__head c-font__bold">以下の内容で問題ないでしょうか？</h3>
      <table id="p-eventEdtiConfirm-form-table">
        <tr>
          <th>イベント名</th>
          <td><?php echo $_POST['event']?></td>
        </tr>
        <tr>
          <th>日にち</th>
          <td><?php echo $_POST['date']?></td>
        </tr>
        <tr>
          <th>開始時間</th>
          <td><?php echo date("H:i", strtotime($_POST['start_time']))?></td>
        </tr>
        <tr>
          <th>終了時間</th>
          <td><?php echo date("H:i", strtotime($_POST['finish_time']))?></td>
        </tr>
      </table>
      <form class="c-marginTop5" action="event_registComp.php" method="POST">
        <input type="hidden" name="event" value="<?php echo $_POST['event']?>">
        <input type="hidden" name="date" value="<?php echo $_POST['date']?>">
        <input type="hidden" name="start_time" value="<?php echo $_POST['start_time']?>">
        <input type="hidden" name="finish_time" value="<?php echo $_POST['finish_time']?>">
        <input type="hidden" name="name_id" value="<?php echo $_SESSION['login_userInfo']['name_id']?>">
        <input type="hidden" name="author" value="<?php echo $_SESSION['login_userInfo']['name_parents']?>">
        <input type="hidden" name="time" value="<?php echo date("Y/m/d H:i:s")?>">
        <div class="p-eventEdtiConfirm-form-buttonBox c-marginTop2">
          <input type="submit" value="OK" style="cursor:pointer" class="p-eventEdtiConfirm-form-buttonBox__button">
        </div>
        <div class="p-eventEdtiConfirm-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-eventEdtiConfirm-form-buttonBox__button">
        </div>
      </form>
    </section>
  </main>
  <?php require(ROOT_PATH. '/Views/footer.php'); ?>
</div>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/index.js"></script>
</body>
</html>
