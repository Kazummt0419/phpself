<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/eventController.php');

$security = new security();
$event    = new EventController();
//トークンの発行
$csrf_token = $security->token();
//ログインされている場合、trueを返す
$result     = $security->check_login();

//ログインされていない場合、ログインフォームに遷移させる
if(!$result) {
  header('Location: /login/login.php');
  exit;
}
//ログインユーザーが教職員以外の時、ページに入れないようにする
elseif($_SESSION['login_userInfo']['class'] != '教職員') {
  header('Location: /login/top.php');
  exit;
}
//ログインされている場合
else {
  //GET送信されてきた情報があるかチェック
  if(isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    //GET送信されたevent_idをkeyに取得
    $eventInfo = $event->get($event_id);
  }

  //バリデーションのチェックにNGがあった場合の処理
  if(isset($_SESSION['err_msg'])) {
    //バリデーションのエラー内容と入力されていた内容をセッションからerr配列に格納
    $err = $_SESSION;
    $event_id = $err['event_id_value'];
    //前回のバリデーションのチェックで使用した内容を削除
    unset($err['csrf_token']);
    unset($_SESSION['err_msg']);
  }
  //ポストした情報を格納。確認画面から戻るボタンが押された時にも記入内容を反映できるようにするため
  elseif(isset($_SESSION['arr_post']) && $_SESSION['arr_post']['vali_type'] == 'regist') {
    $userInfo = $_SESSION['arr_post'];
    unset($_SESSION['arr_post']);
    unset($_SESSION['result_vali']);
  }
  else {
    unset($_SESSION['arr_post']);
    unset($_SESSION['result_vali']);
  }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/event_regist.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-eventEdit" class="c-marginTop5 c-marginBottom5">
    <div id="p-eventEdit-head">
      <h2 id="p-eventEdit-head__title">イベントの編集・削除</h2>
    </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['err_msg'])): ?>
        <ul id="p-eventEdit-errList">
          <?php foreach ($err['err_msg'] as $key => $value): ?>
            <?php if($key == 'event_msg' || $key == 'event_cnt_msg' || $key == 'date_msg' ||
                      $key == 'start_time_msg' || $key == 'finish_time_msg' || $key == 'time_msg'): ?>
              <li class="p-eventEdit-errList__item c-font__red"><?php echo $value; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    <form action="?" method="POST" id="p-eventEdit-form">
      <table id="p-eventEdit-form-table">
        <tr>
          <th>イベント名</th>
          <td><input type="text" name="event" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['event_value']?><?php elseif(isset($eventInfo)): ?><?php echo $eventInfo['eventInfo']['event']; ?><?php endif; ?>"></td>
        </tr>
        <tr>
          <th>日にち</th>
          <td><input type="date" name="date" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['date_value']?><?php elseif(isset($eventInfo)): ?><?php echo $eventInfo['eventInfo']['date']; ?><?php endif; ?>"></td>
        </tr>
        <tr>
          <th>開始時間</th>
          <td><input type="time" name="start_time" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['start_time_value']?><?php elseif(isset($eventInfo)): ?><?php echo $eventInfo['eventInfo']['start_time']; ?><?php endif; ?>"></td>
        </tr>
        <tr>
          <th>終了時間</th>
          <td><input type="time" name="finish_time" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['finish_time_value']?><?php elseif(isset($eventInfo)): ?><?php echo $eventInfo['eventInfo']['finish_time']; ?><?php endif; ?>"></td>
        </tr>
      </table>
      <div class="p-eventEdit-form-buttonBox c-marginTop2">
        <input type="submit" value="編&nbsp;集" method="POST" formaction="/event/event_editConfirm.php" style="cursor:pointer" class="p-eventEdit-form-buttonBox__button">
      </div>
      <div class="p-eventEdit-form-buttonBox c-marginTop2">
        <input type="submit" value="削&nbsp;除" style="cursor:pointer" method="POST" formaction="/event/event_delete.php" class="p-eventEdit-form-buttonBox__button">
      </div>
      <input type="hidden" name="vali_type" value= "edit">
      <input type="hidden" name="event_id" value= "<?php echo $event_id; ?>">
      <input type="hidden" name="csrf_token" value= "<?php echo $csrf_token; ?>">
    </form>
  </main>
  <?php require(ROOT_PATH. '/Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
