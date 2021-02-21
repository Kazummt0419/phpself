<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/attendController.php');
$attend = new attendController();
$security   = new security();

//トークンの発行
$csrf_token = $security->token();
//ログインされている場合、trueを返す
$result     = $security->check_login();

//ログインされていない場合、ログインフォームに遷移させる
if(!$result) {
  header('Location: /login/login.php');
  exit;
}
//ログインされている場合
else {
  //編集リンクを押さずにページ遷移していきた場合はTOPページに遷移させる
  if($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: /login/top.php');
    exit;
  }
  //ログインユーザーが教職員以外の時、ページに入れないようにする
  elseif($_SESSION['login_userInfo']['class'] != '教職員') {
    header('Location: /login/top.php');
    exit;
  }
  //バリデーションでNGで戻ってきた場合
  else {
    //GETで受け取ったname_idをセッションに格納
    if(isset($_GET['name_id'])) {
      $name_id = $_GET['name_id'];
      //GET送信で取得したname_idをkeyにユーザー情報を取得
      $getAttendInfo = $attend->get($_GET['name_id']);
      //getメソッドで取得した情報をuserInfo変数に格納
      $attendInfo             = $getAttendInfo['attendInfo'];
      //name_idから取得した情報をセッションに格納。編集完了になるまで保持
      $_SESSION['attendInfo'] = $attendInfo;
    }
    //バリデーションでNGで戻ってきた場合
    elseif (isset($_SESSION['err_msg'])) {
      //バリデーションのエラー内容と入力されていた内容をセッションからerr配列に格納
      $err = $_SESSION;
      //前回GETで受け取ったname_idを再度POST送信するため。
      $name_id    = $err['err_msg']['name_id'];
      $attendInfo = $_SESSION['attendInfo'];
      //前回のバリデーションのチェックで使用した内容を削除
      unset($err['csrf_token']);
      unset($_SESSION['err_msg']);
    }
    //編集確認画面から戻るボタンが押された時
    else {
      $attendInfo = $_SESSION['attendInfo'];
      $name_id    = $attendInfo['name_id'];
    }
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
<link rel="stylesheet" type="text/css" href="/css/serchRecord_edit.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-serchRecordEdti" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-serchRecordEdti-head">
        <h2 id="p-serchRecordEdti-head__title">登園記録編集</h2>
      </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['err_msg'])): ?>
        <ul id="p-serchRecordEdti-errList">
          <?php foreach ($err['err_msg'] as $key => $value): ?>
            <?php if($key == 'review_msg'): ?>
              <li class="p-serchRecordEdti-errList__item c-font__red"><?php echo $value; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form action="serchRecord_editConfirm.php" method="POST">
        <h3 id="p-serchRecordEdti-form__head">本日の振り返りを記入し、更新ボタンを押してください。</h3>
        <table id="p-serchRecordEdti-form-table">
          <tr>
            <th>日付</th>
            <td><?php echo $attendInfo['date']; ?></td>
          </tr>
          <tr>
            <th>クラス名</th>
            <td><?php echo $attendInfo['class']; ?></td>
          </tr>
          <tr>
            <th>名前(園児名)</th>
            <td><?php echo $attendInfo['name_child']; ?></td>
          </tr>
          <tr>
            <th>本日の体温</th>
            <td><?php echo $attendInfo['temperture']; ?></td>
          </tr>
          <tr>
            <th>連絡事項</th>
            <td><?php echo $attendInfo['message']; ?></td>
          </tr>
          <tr>
            <th>本日の振り返り</th>
            <td><textarea type="text" name="review" id="p-serchRecordEdti-form-textBox__input"><?php if(isset($err['err_msg']['review_msg'])): ?><?php echo $err['err_msg']['review_value']?><?php else: ?><?php echo $attendInfo['review']; ?><?php endif; ?></textarea></td>
          </tr>
        </table>
        <div id="p-serchRecordEdti-form-buttonBox" class="c-marginTop2">
          <input type="submit" value="更&nbsp;新" style="cursor:pointer" id="p-serchRecordEdti-form-buttonBox__button">
        </div>
        <input type="hidden" name="name_id" value= "<?php echo $name_id; ?>">
        <input type="hidden" name="csrf_token" value= "<?php echo $csrf_token; ?>">
      </form>
    </section>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
