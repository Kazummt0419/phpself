<?php
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/infoController.php');

$security = new security();
$info     = new infoController();
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
  //直接リンクを踏んで来ていない場合
  if($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: /login/top.php');
    exit;
  }
  //GET送信されたinfo_idをkeyにお知らせ情報を取得
  if(isset($_GET['info_id'])) {
    $info_id = $_GET['info_id'];
    //GET送信で取得したinfo_idをkeyにユーザー情報を取得
    $getInfo = $info->get($_GET['info_id']);
    //getメソッドで取得した情報をInfo変数に格納
    $Info = $getInfo['info'];

  }
  //バリデーションのチェックにNGがあった場合の処理
  if(isset($_SESSION['err_msg'])) {
    //バリデーションのエラー内容と入力されていた内容をセッションからerr配列に格納
    $err     = $_SESSION;
    $info_id = $err['err_msg']['info_value_msg'];
    //前回のバリデーションのチェックで使用した内容を削除
    unset($err['csrf_token']);
    unset($_SESSION['err_msg']);
  }
  //ポストされた情報を格納。確認画面から戻るボタンが押された時にも記入内容を反映できるようにするため
  elseif(isset($_SESSION['arr_post']) && $_SESSION['arr_post']['vali_type'] == 'info_edit') {
    $Info    = $_SESSION['arr_post'];
    $info_id = $Info['info_id'];
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
<link rel="stylesheet" type="text/css" href="/css/info_edit.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-infoEdit" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-infoEdit-head">
        <h2 id="p-infoEdit-head__title">お知らせ編集</h2>
      </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['err_msg'])): ?>
        <ul id="p-infoEdit-errList">
          <?php foreach ($err['err_msg'] as $key => $value): ?>
            <?php if($key == 'title_msg' || $key == 'title_cnt_msg' || $key == 'info_value_msg'): ?>
              <li class="p-infoEdit-errList__item c-font__red"><?php echo $value; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form action="info_editConfirm.php" method="post">
        <table id="p-infoEdit-form-table">
          <tr>
            <th>タイトル</th>
            <td><input type="text" name="title" value="<?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['title_value']?><?php else: ?><?php echo $Info['title']; ?><?php endif; ?>"></td>
          </tr>
          <tr>
            <th>内容</th>
            <td><textarea type="text" name="Info" id="p-infoEdit-form-textBox__input"><?php if(isset($err['err_msg'])): ?><?php echo $err['err_msg']['info_value']?><?php else: ?><?php echo $Info['Info']; ?><?php endif; ?></textarea></td>
          </tr>
        </table>
        <div id="p-infoEdit-form-buttonBox" class="c-marginTop2">
          <input type="submit" value="完&nbsp;了" style="cursor:pointer" id="p-infoEdit-form-buttonBox__button">
        </div>
        <input type="hidden" name="vali_type" value= "info_edit">
        <input type="hidden" name="info_id" value= "<?php echo $info_id; ?>">
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
