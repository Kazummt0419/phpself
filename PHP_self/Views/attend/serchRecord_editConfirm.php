<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/attendController.php');

$security = new security();
$attend   = new attendController();
//ログインされている場合、trueを返す
$result   = $security->check_login();

//ログインユーザーが教職員以外の時、ページに入れないようにする
if($_SESSION['login_userInfo']['class'] != '教職員') {
  header('Location: /login/top.php');
  exit;
}

//更新フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  $attend->validation_edit($arr_post);
  $serchAttend = $_SESSION['attendInfo'];
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
<link rel="stylesheet" type="text/css" href="/css/serchRecord_editConfirm.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-serchRecordEdtiConfirm" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-serchRecordEdtiConfirm-head">
        <h2 id="p-serchRecordEdtiConfirm-head__title">登園記録編集確認</h2>
      </div>
      <h3 id="p-serchRecordEdtiConfirm-form__head" class="c-font__bold">以下の内容で問題ないでしょうか？</h3>
      <table id="p-serchRecordEdtiConfirm-form-table">
        <tr>
          <th>日付</th>
          <td><?php echo $serchAttend['date']; ?></td>
        </tr>
        <tr>
          <th>クラス名</th>
          <td><?php echo $serchAttend['class']; ?></td>
        </tr>
        <tr>
          <th>名前(園児名)</th>
          <td><?php echo $serchAttend['name_child']; ?></td>
        </tr>
        <tr>
          <th>本日の体温</th>
          <td><?php echo $serchAttend['temperture']; ?></td>
        </tr>
        <tr>
          <th>連絡事項</th>
          <td><?php echo $serchAttend['message']; ?></td>
        </tr>
        <tr>
          <th>本日の振り返り</th>
          <td><?php echo nl2br(htmlspecialchars($_POST['review'], ENT_QUOTES, 'UTF-8'))?></td>
        </tr>
      </table>
      <form class="c-marginTop5" action="serchRecord_editComp.php" method="POST">
        <input type="hidden" name="name_id" value="<?php echo $serchAttend['name_id']?>">
        <input type="hidden" name="date" value="<?php echo $serchAttend['date']?>">
        <input type="hidden" name="review" value="<?php echo htmlspecialchars($_POST['review'], ENT_QUOTES, 'UTF-8')?>">
        <input type="hidden" name="time" value="<?php echo date("Y/m/d H:i:s")?>">
        <div class="p-serchRecordEdtiConfirm-form-buttonBox c-marginTop2">
          <input type="submit" value="OK" style="cursor:pointer" class="p-serchRecordEdtiConfirm-form-buttonBox__button">
        </div>
        <div class="p-serchRecordEdtiConfirm-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-serchRecordEdtiConfirm-form-buttonBox__button">
        </div>
        <input type="hidden" name="name_id" value= "<?php echo $serchAttend['name_id']; ?>">
      </form>
    </section>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
