<?php
require_once(ROOT_PATH. '/Models/attend.php');

class attendController {
  private $attend;

  public function __construct() {
    //モデルオブジェクトの生成
    $this->attend = new attend();
  }

  public function validation($arr_post, $today) {
    $this->attend->validation($arr_post, $today);
  }

  public function validation_serch($arr_post) {
    $result_vali = $this->attend->validation_serch($arr_post);
    return $result_vali;
  }

  public function validation_edit($arr_post) {
    $this->attend->validation_edit($arr_post);
  }

  public function serch($arr_post) {
    if(!isset($_GET['page_id'])) {
      $page = 0;
    }else{
      $page = $_GET['page_id'];
    }
    $attend_count     = $this->attend->count($arr_post);
    $attendInfo_serch = $this->attend->serch($arr_post, $attend_count, $page);
    $params         = [
      'all_pages'      => ceil($attend_count / 10),
      'page'           => $page,
      'attend_serch'   => $attendInfo_serch
    ];
    return $params;
  }

  public function add($arr_post) {
    $this->attend->add($arr_post);
  }

  public function getByName() {
    $this->attend->getByName();
  }

  public function get($name_id) {
    $getAttendInfo = $this->attend->get($name_id);
    return $getAttendInfo;
  }

  public function edit($arr_post) {
    $this->attend->edit($arr_post);
  }
}