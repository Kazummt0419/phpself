<header id="l-haader">
  <div id="l-header-logo">
    <a href="/login/top.php"><img src="/img/logo.png" id="l-header-logo__img" alt="ロゴ"></a>
  </div>
  <ul id="l-header-menu">
    <?php if( strpos($_SESSION['login_userInfo']['class'], '組') !== false): ?>
      <li><a href="/attend/attendance_regist.php" class="l-header-menu-items__link">登園記録の登録</a></li>
    <?php endif; ?>
    <li><a href="/attend/serchRecord.php" class="l-header-menu-items__link">登園記録の検索</a></li>
    <?php if($_SESSION['login_userInfo']['class'] == '教職員'): ?>
      <li><a href="/user/userInfo_regist.php" class="l-header-menu-items__link">ユーザー情報の登録</a></li>
    <?php endif; ?>
    <?php if($_SESSION['login_userInfo']['class'] == '教職員'): ?>
      <li><a href="/user/serchUser.php" class="l-header-menu-items__link">ユーザー情報の検索</a></li>
    <?php endif; ?>
    <?php if($_SESSION['login_userInfo']['class'] !== '卒園済'): ?>
      <li><a href="/info/info.php" class="l-header-menu-items__link">お知らせ</a></li>
    <?php endif; ?>
    <li><form action="/login/logout.php" method="POST"><input type="submit" name="logout" value="ログアウト" style="cursor:pointer"></form></li>
  </ul>
</header>