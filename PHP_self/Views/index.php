<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
  header('Location: login/login.php');
  exit;
}