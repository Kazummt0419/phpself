<?php
require_once(ROOT_PATH. '/Models/event.php');

class eventController {
  private $Event;

  public function __construct() {
    //モデルオブジェクトの生成
    $this->Event = new Event();
  }

  //バリデーション
  public function validation($arr_post) {
    if($arr_post['vali_type'] == 'regist') {
      $this->Event->validation_regist($arr_post);
    }
    if($arr_post['vali_type'] == 'edit') {
      $this->Event->validation_edit($arr_post);
    }
  }

  //イベントの登録
  public function add($arr_post) {
    $this->Event->add($arr_post);
  }

  //イベント情報の取得
  public function get($event_id) {
    $getEventInfo = $this->Event->get($event_id);
    return $getEventInfo;
  }

  //イベントの編集
  public function edit($arr_post) {
    $this->Event->edit($arr_post);
  }

  //イベントの削除
  public function delete($event_id) {
    $this->Event->delete($event_id);
  }
}