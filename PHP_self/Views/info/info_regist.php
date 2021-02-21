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
  if(isset($_SESSION['err_msg'])) {
    //バリデーションのエラー内容と入力されていた内容をセッションからerr配列に格納
    $err = $_SESSION;
    //前回のバリデーションのチェックで使用した内容を削除
    unset($err['csrf_token']);
    unset($_SESSION['err_msg']);
  }
  //ポストされた情報を格納。確認画面から戻るボタンが押された時にも記入内容を反映できるようにするため
  elseif(isset($_SESSION['arr_post']) && $_SESSION['arr_post']['vali_type'] == 'info_regist') {
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
<link rel="stylesheet" type="text/css" href="/css/info_regist.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-infoRegist" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-infoRegist-head">
        <h2 id="p-infoRegist-head__title">お知らせ登録</h2>
      </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['err_msg'])): ?>
        <ul id="p-infoRegist-errList">
          <?php foreach ($err['err_msg'] as $key => $value): ?>
            <?php if($key == 'title_msg' || $key == 'title_cnt_msg' || $key == 'info_value_msg'): ?>
              <li class="p-infoRegist-errList__item c-font__red"><?php echo $value; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form action="info_registConfirm.php" method="POST">
        <table id="p-infoRegist-form-table">
          <tr>
            <th>タイトル</th>
            <td><input type="text" name="title" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['title_value']?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>内容</th>
            <td><textarea type="text" name="Info" id="p-infoRegist-form-textBox__input"><?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['info_value']?><?php endif; ?></textarea></td>
          </tr>
        </table>
        <div id="p-infoRegist-form-buttonBox" class="c-marginTop2">
          <input type="submit" value="完&nbsp;了" style="cursor:pointer" id="p-infoRegist-form-buttonBox__button">
        </div>
        <input type="hidden" name="vali_type" value= "info_regist">
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
