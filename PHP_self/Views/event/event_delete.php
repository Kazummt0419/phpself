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
<link rel="stylesheet" type="text/css" href="/css/event_delete.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-eventDelete" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-eventDelete-head">
        <h2 id="p-eventDelete-head__title">イベント削除確認</h2>
      </div>
      <h3 class="p-eventDelete-form__head c-font__bold">以下の内容を削除してもよろしいでしょうか？</h3>
      <table id="p-eventDelete-form-table">
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
      <form class="c-marginTop5" action="event_deleteComp.php" method="POST">
        <input type="hidden" name="event_id" value="<?php echo $_POST['event_id']?>">
        <div class="p-eventDelete-form-buttonBox c-marginTop2">
          <input type="submit" value="OK" style="cursor:pointer" class="p-eventDelete-form-buttonBox__button">
        </div>
        <div class="p-eventDelete-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-eventDelete-form-buttonBox__button">
        </div>
      </form>
    </section>
  </main>
  <?php require(ROOT_PATH. '/Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
