<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
$security   = new security();
//トークンの発行
$csrf_token = $security->token();
//ログインされている場合、trueを返す
$result     = $security->check_login();

//ログインされていない場合
if(!$result) {
  header('Location: /login/login.php');
  exit;
}
//ログインユーザーが教員の時、ページに入れないようにする
elseif($_SESSION['login_userInfo']['class'] == '教職員') {
  header('Location: /login/top.php');
  exit;
}
//ログインされている場合
else {
  //セッションのログインユーザー情報をuserInfo変数に格納
  $userInfo = $_SESSION['login_userInfo'];

  //バリデーションのチェックにNGがあった場合の処理
  if(isset($_SESSION['err_msg'])) {
    //バリデーションのエラー内容と入力されていた内容をセッションからerr配列に格納
    $err = $_SESSION;
    //前回のバリデーションのチェックで使用した内容を削除
    unset($err['csrf_token']);
    unset($_SESSION['err_msg']);
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
<link rel="stylesheet" type="text/css" href="/css/attendance_regist.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-attendanceRegist" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-attendanceRegist-head">
        <h2 id="p-attendanceRegist-head__title">登園記録登録</h2>
      </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['err_msg'])): ?>
        <ul id="p-attendanceRegist-errList">
          <?php foreach ($err['err_msg'] as $key => $value): ?>
            <?php if($key == 'temp_msg' || $key == 'temp_match_msg' || $key == 'overlapping_msg'): ?>
              <li class="p-attendanceRegist-errList__item c-font__red"><?php echo $value; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form action="attendance_registConfirm.php" method="POST">
        <table id="p-attendanceRegist-form-table">
          <tr>
            <th>日付</th>
            <td><?php echo date("Y/m/d"); ?></td>
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
            <td><input type="text" name="temp">&nbsp;&#8451;</td>
          </tr>
          <tr>
            <th>連絡事項</th>
            <td><textarea type="text" name="msg" id="p-attendanceRegist-form-textBox__input"></textarea></td>
          </tr>
        </table>
        <div id="p-attendanceRegist-form-buttonBox" class="c-marginTop2">
          <input type="submit" value="登&nbsp;録" style="cursor:pointer" id="p-attendanceRegist-form-buttonBox__button">
        </div>
        <input type="hidden" name="vali_type" value= "regist">
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
