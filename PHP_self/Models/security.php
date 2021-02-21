<?php
class security {
  public function token() {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
    return $csrf_token;
  }

  public function check_login() {
    $result = false;
    if(isset($_SESSION['login_user']) && isset($_SESSION['id'])) {
      $result = true;
    }
    return $result;
  }
}