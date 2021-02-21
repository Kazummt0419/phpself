<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/userInfoController.php');

$security   = new security();
$user        = new UserInfoController();
//トークンの発行
$csrf_token = $security->token();
//ログインされている場合、trueを返す
$result     = $security->check_login();

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
  //編集リンクを押さずにページ遷移していきた場合はTOPページに遷移させる
  if($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: /login/top.php');
    exit;
  }
  //バリデーションでNGで戻ってきた場合
  else {
    //GETで受け取ったname_idをセッションに格納
    if(isset($_GET['name_id'])) {
      $name_id = $_GET['name_id'];
      //GET送信で取得したname_idをkeyにユーザー情報を取得
      $getUserInfo = $user->get($_GET['name_id']);
      //getメソッドで取得した情報をuserInfo変数に格納
      $userInfo = $getUserInfo['userInfo'];
    }
    //バリデーションでNGで戻ってきた場合
    elseif(isset($_SESSION['err_msg'])) {
      //バリデーションのエラー内容と入力されていた内容をセッションからerr配列に格納
      $err     = $_SESSION;
      //前回GETで受け取ったname_idを再度POST送信するため。
      $name_id = $err['err_msg']['name_id'];
      //前回のバリデーションのチェックで使用した内容を削除
      unset($err['csrf_token']);
      unset($_SESSION['err_msg']);
    }
    //ポストされた情報を格納。確認画面から戻るボタンが押された時にも記入内容を変更できるようにするため
    elseif(isset($_SESSION['arr_post']) && $_SESSION['arr_post']['vali_type'] == 'edit_serch') {
      $userInfo = $_SESSION['arr_post'];
      $name_id  = $userInfo['name_id'];
      unset($_SESSION['arr_post']);
      unset($_SESSION['result_vali']);
    }
    //編集確認画面から戻るボタンが押された時
    else {
      $name_id  = $userInfo['name_id'];
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
<link rel="stylesheet" type="text/css" href="/css/serchUser_edit.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-serchUserEdit" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-serchUserEdit-head">
        <h2 id="p-serchUserEdit-head__title">ユーザー情報編集</h2>
      </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['err_msg'])): ?>
        <ul id="p-serchUserEdit-errList">
          <?php foreach ($err['err_msg'] as $key => $value): ?>
            <?php if($key == 'class_name_msg' || $key == 'name_parent_cnt_msg' || $key == 'name_parent_msg' ||
                      $key == 'name_child_cnt_msg' || $key == 'falt_msg' || $key == 'name_child_msg' ||
                      $key == 'mail_msg' || $key == 'mail_match_msg'): ?>
              <li class="p-serchUserEdit-errList__item c-font__red"><?php echo $value; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form action="serchUser_editConfirm.php" method="POST">
        <table id="p-serchUserEdit-form-table">
          <tr>
            <th>クラス名</th>
            <td>
              <select name="class_name">
                  <option value="">選択して下さい</option>
                  <?php foreach ($class_arr as $value): ?>
                    <option value="<?php echo $value; ?>" <?php if((isset($err['err_msg']) && $err['err_msg']['class_name_value'] == $value) || (isset($userInfo['class']) && $userInfo['class'] == $value)): ?><?php echo 'selected' ?><?php endif; ?>><?php echo $value; ?></option>
                  <?php endforeach; ?>
                </select>
            </td>
          </tr>
          <tr>
            <th>名前(保護者)</th>
            <td><input type="text" name="name_parents" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['name_parent_value']?><?php else: ?><?php echo $userInfo['name_parents']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>名前(園児名)</th>
            <td><input type="text" name="name_child" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['name_child_value']?><?php else: ?><?php echo $userInfo['name_child']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>メールアドレス</th>
            <td><input type="text" name="mail" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['mail_value']?><?php else: ?><?php echo $userInfo['mail']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>備考</th>
            <td><textarea type="text" name="notes" id="p-serchUserEdit-form-textBox__input"><?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['note_value']?><?php else: ?><?php echo $userInfo['notes']; ?><?php endif; ?></textarea></td>
          </tr>
        </table>
        <div class="p-serchUserEdit-form-buttonBox c-marginTop2">
          <input type="submit" value="完&nbsp;了" style="cursor:pointer" class="p-serchUserEdit-form-buttonBox__button">
        </div>
        <div class="p-serchUserEdit-form-buttonBox c-marginTop2">
          <input type="button" value="キャンセル" style="cursor:pointer" onclick="history.back()" class="p-serchUserEdit-form-buttonBox__button">
        </div>
        <input type="hidden" name="vali_type" value= "edit_serch">
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
