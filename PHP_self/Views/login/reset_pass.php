<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description">
<meta name="keywords">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/reset_pass.css">
<link rel="icon" type="image/x-icon" href="/img/favicon.png">
<title>登園管理システム</title>
</head>
<body>
<div id="l-wrap">
  <?php require(ROOT_PATH. 'Views/header_login.php'); ?>
  <main id="p-resetPass" class="c-marginTop5 c-marginBottom5">
    <form action="reset_pass_sendMail.php" method="POST" id="p-resetPass-form">
      <h3 id="p-resetPass-form__head">登録したメールアドレスを入力し、送信ボタンを押してください。</h3>
      <table id="p-resetPass-form-table">
        <tr>
          <th>メールアドレス</th>
          <td><input type="text" name="mail"></td>
        </tr>
      </table>
      <div id="p-resetPass-form-buttonBox" class="c-marginTop2">
        <input type="submit" value="送&nbsp;信" style="cursor:pointer" id="p-resetPass-form-buttonBox__button">
      </div>
    </form>
  </main>
  <?php require(ROOT_PATH. 'Views/footer_login.php'); ?>
</div>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/index.js"></script>
</body>
</html>
