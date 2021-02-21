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
<link rel="stylesheet" type="text/css" href="/css/serchRecord.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-serchRecord" class="c-marginTop5 c-marginBottom5">
    <div id="p-serchRecord-head">
      <h2 id="p-serchRecord-head__title">登園記録の検索</h2>
    </div>
    <!-- バリデーションエラーメッセージ -->
    <?php if(isset($err['err_msg'])): ?>
      <ul id="p-serchRecord-errList">
        <?php foreach ($err['err_msg'] as $key => $value): ?>
          <?php if($key == 'class_name_msg' || $key == 'date_msg' || $key == 'serch_msg' ||
                    $key == 'date_start_msg' || $key == 'date_end_msg') : ?>
            <li class="p-serchRecord-errList__item c-font__red"><?php echo $value; ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <form action="serchRecord_result.php" method="POST" id="p-serchRecord-form">
      <div id="p-serchRecord-form-head__container">
        <?php if($_SESSION['login_userInfo']['class'] == '教職員'): ?>
          <h3 class="p-serchRecord-form__head" class="c-font-size1-2">検索条件を選択の上、検索ボタンを押してください。</h3>
          <h3 class="p-serchRecord-form__head" class="c-font-size1-2"><span class="c-font__red">&#42;</span>は選択必須項目になります。</h3>
        <?php endif; ?>
      </div>
      <table id="p-serchRecord-form-table">
        <?php if($_SESSION['login_userInfo']['class'] == '教職員'): ?>
          <tr>
            <th><span class="c-font__red">&#42;</span>クラス名</th>
            <td>
              <select name="class_name">
                <option value="">選択して下さい</option>
                <?php foreach ($class_arr as $value): ?>
                  <option value="<?php echo $value; ?>" <?php if(isset($err['err_msg']) && $err['err_msg']['class_name_value'] == $value){echo 'selected';} ?>><?php echo $value; ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
        <?php endif; ?>
          <?php if($_SESSION['login_userInfo']['class'] == '教職員'): ?>
          <tr>
            <th>名前(園児名)</th>
            <td><input type="text" name="name_child" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['name_child_value']?><?php endif; ?>"></td>
          </tr>
          <tr>
        <?php endif; ?>
          <th>開始日</th>
          <td><input type="date" name="start_date" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['start_date_value']?><?php endif; ?>"></td>
        </tr>
        <tr>
          <th>終了日</th>
          <td><input type="date" name="end_date" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['end_date_value']?><?php endif; ?>"></td>
        </tr>
      </table>
      <div class="p-serchRecord-form-buttonBox c-marginTop2">
        <input type="submit" value="検&nbsp;索" style="cursor:pointer" class="p-serchRecord-form-buttonBox__button">
      </div>
      <?php if($_SESSION['login_userInfo']['class'] != '教職員'): ?>
        <input type="hidden" name="class_name" value= "<?php echo $_SESSION['login_userInfo']['class']; ?>">
        <input type="hidden" name="name_child" value= "<?php echo $_SESSION['login_userInfo']['name_child']; ?>">
      <?php endif; ?>
      <input type="hidden" name="csrf_token" value= "<?php echo $csrf_token; ?>">
      <input type="hidden" name="vali_type" value= "serch">
    </form>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
