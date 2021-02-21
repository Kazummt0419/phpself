<?php
require_once(ROOT_PATH. '/Models/info.php');

class infoController {
  private $Info;

  public function __construct() {
    //モデルオブジェクトの生成
    $this->Info = new Info();
  }

  //バリデーション
  public function validation($arr_post) {
    $this->Info->validation($arr_post);
  }

  //お知らせの追加
  public function add($arr_post) {
    $this->Info->add($arr_post);
  }

  //お知らせの検索
  public function serch() {
    //GET送信されていない場合、(=検索フォームから遷移してきた場合)1ページ目とする
    if(!isset($_GET['page_id'])) {
      $page = 0;
    }
    //GET送信によって、移動先のページが送られた場合
    else{
      $page = $_GET['page_id'];
    }
    //検索結果の総数を調べるメソッドをよび出す。
    $info_count = $this->Info->count();
    //検索条件を元にデータを取得するメソッドを呼び出す。
    $info_serch = $this->Info->serch($info_count, $page);
    //10件1ページごとに表示させるためにページ総数、現在のページ、検索結果をparamsに格納し返す。
    $params         = [
      'all_pages'      => ceil($info_count / 3),
      'page'           => $page,
      'info_serch'     => $info_serch
    ];
    return $params;
  }

  //お知らせの取得
  public function get($info_id) {
    $getInfo = $this->Info->get($info_id);
    return $getInfo;
  }

  //お知らせの編集
  public function edit($arr_post) {
    $getInfo = $this->Info->edit($arr_post);
    return $getInfo;
  }
}