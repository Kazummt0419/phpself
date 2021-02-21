<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/loginController.php');

$security = new security();
$login    = new loginController();
//トークンの発行
$csrf_token = $security->token();

//name_idが1の番号かどうかのチェック
$name_id = 1;
$result = $login->get($name_id);
if(!$result) {
  header('Location: /login/login.php');
  exit;
}
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
<link rel="stylesheet" type="text/css" href="/css/user_regist.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header_login.php'); ?>
  <main id="p-userRegist" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-userRegist-head">
        <h2 id="p-userRegist-head__title">ユーザー情報登録</h2>
      </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['err_msg'])): ?>
        <ul id="p-userRegist-errList">
          <?php foreach ($err['err_msg'] as $key => $value): ?>
            <?php if($key == 'name_msg' || $key == 'name_cnt_msg' || $key == 'user_name_msg' ||
                      $key == 'user_name_cnt_msg' || $key == 'mail_msg' || $key == 'mail_match_msg' ||
                      $key == 'pass_msg' || $key == 'pass_cnt_msg'): ?>
              <li class="p-userRegist-errList__item c-font__red"><?php echo $value; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form action="user_registConfirm.php" method="POST">
        <table id="p-userRegist-form-table">
          <tr>
            <th>名前</th>
            <td><input type="text" name="name" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['name_value']?><?php elseif(isset($userInfo)): ?><?php echo $userInfo['name']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>ユーザー名</th>
            <td><input type="text" name="user_name" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['user_name_value']?><?php elseif(isset($userInfo)): ?><?php echo $userInfo['user_name']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>メールアドレス</th>
            <td><input type="text" name="mail" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['mail_value']?><?php elseif(isset($userInfo)): ?><?php echo $userInfo['mail']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>パスワード</th>
            <td><input type="text" name="pass" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['pass_value']?><?php elseif(isset($userInfo)): ?><?php echo $userInfo['pass']; ?><?php endif; ?>"></td>
          </tr>
        </table>
        <div id="p-userRegist-form-buttonBox" class="c-marginTop2">
          <input type="submit" value="完&nbsp;了" style="cursor:pointer" id="p-userRegist-form-buttonBox__button">
        </div>
        <input type="hidden" name="vali_type" value= "regist">
        <input type="hidden" name="csrf_token" value= "<?php echo $csrf_token; ?>">
      </form>
    </section>
  </main>
  <?php require(ROOT_PATH. 'Views/footer_login.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
