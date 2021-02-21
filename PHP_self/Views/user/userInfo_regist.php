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
  elseif(isset($_SESSION['post_value'])) {
    $post_value = $_SESSION['post_value'];
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
<link rel="stylesheet" type="text/css" href="/css/userInfo_regist.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-userInfoRegist" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-userInfoRegist-head">
        <h2 id="p-userInfoRegist-head__title">ユーザー情報登録</h2>
      </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['err_msg'])): ?>
        <ul id="p-userInfoRegist-errList">
          <?php foreach ($err['err_msg'] as $key => $value): ?>
            <?php if($key == 'class_name_msg' || $key == 'name_parent_cnt_msg' || $key == 'name_parent_msg' ||
                      $key == 'name_child_cnt_msg' || $key == 'falt_msg' || $key == 'name_child_msg' ||
                      $key == 'mail_msg' || $key == 'mail_match_msg'): ?>
              <li class="p-userInfoRegist-errList__item c-font__red"><?php echo $value; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form action="userInfo_registConfirm.php" method="POST">
        <div id="p-userInfoRegist-form-head__container">
          <h3 class="p-userInfoRegist-form__head" class="c-font-size1-2">※教職員の登録をする場合、保護者と園児の名前の欄に教職員の名前を記入してください。</h3>
        </div>
        <table id="p-userInfoRegist-form-table">
          <tr>
            <th>クラス名</th>
            <td>
              <select name="class_name">
                <option value="">選択して下さい</option>
                <!-- クラス名の配列をループで表示 -->
                <?php foreach ($class_arr as $value): ?>
                <option value="<?php echo $value; ?>" <?php if((isset($err['err_msg']) && $err['err_msg']['class_name_value'] == $value)): ?><?php echo 'selected' ?><?php endif; ?>><?php echo $value; ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <tr>
            <th>名前(保護者)</th>
            <td><input type="text" name="name_parents" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['name_parent_value']?><?php elseif(isset($post_value)) : ?><?php echo $post_value['name_parents']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>名前(園児名)</th>
            <td><input type="text" name="name_child" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['name_child_value']?><?php elseif(isset($post_value)) : ?><?php echo $post_value['name_child']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>メールアドレス</th>
            <td><input type="text" name="mail" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['mail_value']?><?php elseif(isset($post_value)) : ?><?php echo $post_value['mail']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>備考</th>
            <td><textarea type="text" name="notes" id="p-userInfoRegist-form-textBox__input"><?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['note_value']?><?php elseif(isset($post_value)) : ?><?php echo $post_value['notes']; ?><?php endif; ?></textarea></td>
          </tr>
        </table>
        <div id="p-userInfoRegist-form-buttonBox" class="c-marginTop2">
          <input type="submit" value="完&nbsp;了" style="cursor:pointer" id="p-userInfoRegist-form-buttonBox__button">
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
