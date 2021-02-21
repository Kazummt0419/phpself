<?php
session_cache_limiter('none');
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/userInfoController.php');

$user = new UserInfoController();
$security = new security();
//ログインされている場合、trueを返す
$result   = $security->check_login();

//ログインユーザーが教職員以外の時、ページに入れないようにする
if($_SESSION['login_userInfo']['class'] != '教職員') {
  header('Location: /login/top.php');
  exit;
}

//検索フォームから値がPOSTされた時
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $arr_post = $_POST;
  $user->validation($arr_post);
  //バリデーションのエラーがなかった場合
  if(!isset($_SESSION['err_msg'])) {
    //検索条件を元に検索された結果をparamasに返す
    $params = $user->serch($arr_post);
  }
}
//ページネーションの矢印がクリックされた時かつ編集ページから戻るボタンを押しページ遷移された時
elseif(isset($_GET['page_id']) || isset($_SESSION['criteria'])) {
  //ページネーションの矢印がクリック前の検索結果を削除
  unset($params);
  //ページネーションの矢印がクリックの検索条件をcriteria_arr配列に格納
  $criteria_arr['class_name'] = $_SESSION['criteria']['class_name'];
  $criteria_arr['name_child'] = $_SESSION['criteria']['name_child'];
  $params = $user->serch($criteria_arr);
}
//URLを直接記入しページ遷移された時
elseif($_SERVER["REQUEST_METHOD"] != "POST") {
  header('Location: /login/top.php');
  exit;
}
//ログインされていない場合
elseif(!$result) {
  header('Location: /login/login.php');
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/serchUser_result.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-serchUserResult" class="c-marginTop5 c-marginBottom5">
    <div id="p-serchUserResult-head">
      <h2 id="p-serchUserResult-head__title">ユーザー情報検索結果</h2>
    </div>
    <table id="p-serchUserResult-form-table">
      <tr>
        <th>クラス名</th>
        <th>名前(保護者)</th>
        <th>名前(園児名)</th>
        <th>メールアドレス</th>
        <th>備考</th>
        <th></th>
        <th></th>
      </tr>
      <?php foreach ($params['userinfo_serch'] as $value): ?>
        <tr>
          <td><?php echo $value['class']; ?></td>
          <td><?php echo $value['name_parents']; ?></td>
          <td><?php echo $value['name_child']; ?></td>
          <td><?php echo $value['mail']; ?></td>
          <td class="c-note"><?php echo $value['notes']; ?></td>
          <td><a href="serchUser_edit.php?name_id=<?php echo $value['name_id']?>">編集</a></td>
          <td><a href="serchUser_delete.php?name_id=<?php echo $value['name_id']?>">削除</a></td>
        </tr>
      <?php endforeach; ?>
    </table>
    <div id="p-serchUserResult-table-navi" class="c-marginTop2">
      <h3 class="c-font-size1">
        <?php if($params['page'] != 0): ?>
          <a href="?page_id=<?php echo $params['page']-1?>" class="c-font-black">&lt;</a>
        <?php endif; ?>
        &nbsp;&nbsp;<?php echo $params['page']+1; ?>
        /
        <?php echo $params['all_pages']; ?>&nbsp;&nbsp;
        <?php if($params['all_pages']-1 != $params['page']): ?>
          <a href="?page_id=<?php echo $params['page']+1?>" class="c-font-black">&gt;</a>
        <?php endif; ?>
      </h3>
    </div>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
