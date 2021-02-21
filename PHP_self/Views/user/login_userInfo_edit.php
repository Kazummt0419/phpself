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
  //セッションのログインユーザー情報をuserInfo変数に格納
  $userInfo = $_SESSION['login_userInfo'];
  if(isset($_SESSION['post_value'])) {
    $post_value = $_SESSION['post_value'];
  }
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
<link rel="stylesheet" type="text/css" href="/css/login_userInfo_edit.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-userInfoEdti" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-userInfoEdti-head">
        <h2 id="p-userInfoEdti-head__title">ログインユーザー情報編集</h2>
      </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['err_msg'])): ?>
        <ul id="p-userInfoEdti-errList">
          <?php foreach ($err['err_msg'] as $key => $value): ?>
            <?php if($key == 'user_name_msg' || $key == 'user_name_cnt_msg' || $key == 'mail_msg' ||
                      $key == 'mail_match_msg' || $key == 'mail_match_msg' || $key == 'user_name_already_msg' ||
                      $key == 'pass_cnt_msg'): ?>
              <li class="p-userInfoEdti-errList__item c-font__red"><?php echo $value; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form action="login_userInfo_editConfirm.php" method="POST">
        <table id="p-userInfoEdti-form-table">
          <tr>
            <th>名前</th>
            <td><?php echo $userInfo['name_parents']; ?></td>
          </tr>
          <?php if($_SESSION['login_userInfo']['class'] != '教職員'): ?>
            <tr>
              <th>名前(園児名)</th>
              <td><?php echo $userInfo['name_child']; ?></td>
            </tr>
          <?php endif; ?>
            <tr>
            <th>ユーザー名</th>
            <td><input type="text" name="user_name" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['user_name_value']; ?><?php elseif(isset($post_value)) : ?><?php echo $post_value['user_name']; ?><?php else : ?><?php echo $userInfo['user']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>クラス名</th>
            <td><?php echo $userInfo['class']; ?></td>
          </tr>
          <tr>
            <th>メールアドレス</th>
            <td><input type="text" name="mail" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['mail_value']; ?><?php elseif(isset($post_value)) : ?><?php echo $post_value['mail']; ?><?php else : ?><?php echo $userInfo['mail']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>パスワード</th>
            <td><input type="text" name="pass" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['pass_value']; ?><?php elseif(isset($post_value)) : ?><?php echo $post_value['pass']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>備考</th>
            <td><textarea type="text" name="note" id="p-userInfoEdti-form-textBox__input"><?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['note_value']; ?><?php elseif(isset($post_value)) : ?><?php echo $post_value['note']; ?><?php else : ?><?php echo $userInfo['notes']; ?><?php endif; ?></textarea></td>
          </tr>
        </table>
        <div id="p-userInfoEdti-form-buttonBox" class="c-marginTop2">
          <input type="submit" value="完&nbsp;了" style="cursor:pointer" id="p-userInfoEdti-form-buttonBox__button">
        </div>
        <input type="hidden" name="vali_type" value= "edit">
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