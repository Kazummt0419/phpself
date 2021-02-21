<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/userInfoController.php');

$security = new security();
$user     = new UserInfoController();
//ログインされている場合、trueを返す
$result   = $security->check_login();
$name_id  = $_GET['name_id'];

//ログインユーザーが教職員以外の時、ページに入れないようにする
if($_SESSION['login_userInfo']['class'] != '教職員') {
  header('Location: /login/top.php');
  exit;
}

//ログインされていない場合、ログインフォームに遷移させる
if(!$result) {
  header('Location: /login/login.php');
  exit;
}
//ログインされている場合
else {
  //削除リンクを押さずにページ遷移していきた場合はTOPページに遷移させる
  if($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: /login/top.php');
    exit;
  }
  //GET送信されてきた場合、name_idをkeyにしてユーザー情報を取得
  $getUserInfo = $user->get($name_id);
  //getメソッドで取得した情報をuserInfo変数に格納
  $userInfo = $getUserInfo['userInfo'];
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/serchUser_delete.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-serchUserDelete" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-serchUserDelete-head">
        <h2 id="p-serchUserDelete-head__title">ユーザー情報削除確認</h2>
      </div>
      <form class="c-marginTop5" action="serchUser_deleteComp.php" method="post">
        <h3 id="p-serchUserDelete-form__head" class="c-font__bold">以下の内容を削除してもよろしいでしょうか？</h3>
        <table id="p-serchUserDelete-form-table">
          <tr>
            <th>クラス名</th>
            <td><?php echo $userInfo['class']; ?></td>
          </tr>
          <tr>
            <th>名前(保護者)</th>
            <td><?php echo $userInfo['name_parents']; ?></td>
          </tr>
          <tr>
            <th>名前(園児名)</th>
            <td><?php echo $userInfo['name_child']; ?></td>
          </tr>
          <tr>
            <th>メールアドレス</th>
            <td><?php echo $userInfo['mail']; ?></td>
          </tr>
          <tr>
            <th>備考</th>
            <td><?php echo $userInfo['notes']; ?></td>
          </tr>
        </table>
        <div class="p-serchUserDelete-form-buttonBox c-marginTop2">
          <input type="submit" value="OK" style="cursor:pointer" class="p-serchUserDelete-form-buttonBox__button">
        </div>
        <div class="p-serchUserDelete-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-serchUserDelete-form-buttonBox__button">
        </div>
        <input type="hidden" name="name_id" value= "<?php echo $name_id; ?>">
      </form>
    </section>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
