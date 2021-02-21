<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');

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
//ログインユーザーが教職員以外の時、ページに入れないようにする
elseif($_SESSION['login_userInfo']['class'] != '教職員') {
  header('Location: /login/top.php');
  exit;
}
//ログインされている場合
else {
  //バリデーションのチェックにNGがあった場合の処理
  //バリデーションのエラー内容と入力されていた内容をセッションからerr配列に格納
  $err = $_SESSION;
  //前回のバリデーションのチェックで使用した内容を削除
  unset($err['csrf_token']);
  foreach($_SESSION as $key => $value) {
    if($key == 'err_msg') {
      unset($_SESSION['err_msg']);
    }
  }
}
//クラス名を格納する配列
$class_arr = ['あか組', 'あお組', 'きいろ組', 'きみどり組', 'むらさき組', 'もも組', 'オレンジ組', 'みどり組', 'みずいろ組', '卒園済', '教職員'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/serchUser.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-serchUser" class="c-marginTop5 c-marginBottom5">
    <div id="p-serchUser-head">
      <h2 id="p-serchUser-head__title">ユーザー情報の検索</h2>
    </div>
    <!-- バリデーションエラーメッセージ -->
    <?php if(isset($err['err_msg'])): ?>
      <ul id="p-serchUser-errList">
        <?php foreach ($err['err_msg'] as $key => $value): ?>
          <?php if($key == 'class_name_msg' || $key == 'serch_msg') : ?>
            <li class="p-serchUser-errList__item c-font__red"><?php echo $value; ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <form action="serchUser_result.php" method="POST" id="p-serchUser-form">
      <div id="p-serchUser-form-head__container">
        <h3 class="p-serchUser-form__head" class="c-font-size1-2">検索条件を選択の上、検索ボタンを押してください。</h3>
        <h3 class="p-serchUser-form__head" class="c-font-size1-2"><span class="c-font__red">&#42;</span>は選択必須項目になります。</h3>
      </div>
      <table id="p-serchUser-form-table">
        <tr>
          <th><span class="c-font__red">&#42;</span>クラス名</th>
          <td>
            <select name="class_name">
              <option value="">選択して下さい</option>
              <!-- バリデーションエラーメッセージ -->
              <?php foreach ($class_arr as $value): ?>
                <option value="<?php echo $value; ?>" <?php if(isset($err['err_msg']) && $err['err_msg']['class_name_value'] == $value){echo 'selected';} ?>><?php echo $value; ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th>名前(園児名)</th>
          <td><input type="text" name="name_child" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['name_child_value']?><?php endif; ?>"></td>
        </tr>
      </table>
      <div class="p-serchUser-form-buttonBox c-marginTop2">
        <input type="submit" value="検&nbsp;索" style="cursor:pointer" class="p-serchUser-form-buttonBox__button">
      </div>
      <input type="hidden" name="vali_type" value= "serch">
      <input type="hidden" name="csrf_token" value= "<?php echo $csrf_token; ?>">
    </form>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>