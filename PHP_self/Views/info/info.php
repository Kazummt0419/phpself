<?php
session_cache_limiter('none');
session_start();

require_once(ROOT_PATH. 'Models/security.php');
require_once(ROOT_PATH. 'Controllers/infoController.php');

$info     = new infoController();
$security = new security();
//ログインされている場合、trueを返す
$result   = $security->check_login();

//ログインされていない場合
if(!$result) {
  header('Location: /login/login.php');
  exit;
}
//ログインユーザーが卒園済の時、ページに入れないようにする
elseif($_SESSION['login_userInfo']['class'] == '卒園済') {
  header('Location: /login/top.php');
  exit;
}
//ログインされている場合
else {
  $params = $info->serch();
  //検索結果が0件だった場合、エラーメッセージを表示
  if(isset($_SESSION['err_msg'])) {
    $err = $_SESSION['err_msg'];
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
<link rel="stylesheet" type="text/css" href="/css/info.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header.php'); ?>
  <main id="p-info" class="c-marginTop5 c-marginBottom5">
    <section>
      <div id="p-info-head">
        <h2 id="p-info-head__title">お知らせ</h2>
        <?php if($_SESSION['login_userInfo']['class'] == '教職員'): ?>
          <p><a href="/info/info_regist.php" id="p-info-regist__link" class="c-border-bottom">新しいお知らせを登録</a></p>
        <?php endif; ?>
      </div>
      <!-- バリデーションエラーメッセージ -->
      <?php if(isset($err['serch_msg'])): ?>
        <ul id="p-info-errList">
          <li class="p-info-errList__item c-font__red"><?php echo $err['serch_msg']; ?></li>
        </ul>
      <?php endif; ?>
      <?php if(!isset($err['serch_msg'])): ?>
        <div id="p-info-table-container">
          <?php foreach ($params['info_serch'] as $value): ?>
            <table>
              <tr>
                <th>日付</th>
                <td><?php echo $value['date']; ?></td>
                <td></td>
              </tr>
              <tr>
                <th>タイトル</th>
                <td><?php echo $value['title']; ?></td>
                <td></td>
              </tr>
              <tr>
                <th>内容</th>
                <td class="c-note"><?php echo $value['info']; ?></td>
                <td>
                  <?php if($_SESSION['login_userInfo']['class'] == '教職員'): ?>
                    <a href="/info/info_edit.php?info_id=<?php echo $value['info_id']?>" class="c-border-bottom c-font-black">編集</a>
                  <?php endif; ?>
                </td>
              </tr>
            </table>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
    <?php if(!isset($err['serch_msg'])): ?>
      <div id="p-info-table-navi" class="c-marginTop2">
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
    <?php endif; ?>
  </main>
  <?php require(ROOT_PATH. 'Views/footer.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
