<?php
require_once(ROOT_PATH. '/Models/userInfo.php');

class UserInfoController {
  private $UserInfo;

  public function __construct() {
    //モデルオブジェクトの生成
    $this->UserInfo = new UserInfo();
  }

  //バリデーション
  public function validation($arr_post) {
    //ログインユーザー情報編集フォームのバリデーション
    if($arr_post['vali_type'] == 'edit') {
      $this->UserInfo->validation_edit($arr_post);
    }
    //ログインユーザー情報登録フォームもしくは検索結果の編集フォームのバリデーション
    elseif($arr_post['vali_type'] == 'regist' || $arr_post['vali_type'] == 'edit_serch') {
      $result = $this->UserInfo->validation_regist($arr_post);
    }
    //ログインユーザー情報検索フォームのバリデーション
    elseif($arr_post['vali_type'] == 'serch') {
      $this->UserInfo->validation_serch($arr_post);
    }
  }

  //ユーザー情報の取得
  public function get($name_id) {
    $getUserInfo = $this->UserInfo->get($name_id);
    return $getUserInfo;
  }

  //ユーザー情報の追加
  public function add($arr_post) {
    $this->UserInfo->add($arr_post);
  }

  //ユーザー情報の編集
  public function edit($arr_post) {
    if($arr_post['edit_type'] == 'loginUser') {
      $this->UserInfo->edit_login($arr_post);
    } elseif($arr_post['edit_type'] == 'serchUser') {
      $this->UserInfo->edit($arr_post);
    }
  }

  public function delete($name_id) {
    $this->UserInfo->delete($name_id);
  }

  //ユーザー情報の検索
  public function serch($arr_post) {
    //GET送信されていない場合、(=検索フォームから遷移してきた場合)1ページ目とする
    if(!isset($_GET['page_id'])) {
      $page = 0;
    }
    //GET送信によって、移動先のページが送られた場合
    else{
      $page = $_GET['page_id'];
    }
    //検索結果の総数を調べるメソッドをよび出す。
    $userinfo_count = $this->UserInfo->count($arr_post);
    //検索条件を元にデータを取得するメソッドを呼び出す。
    $userinfo_serch = $this->UserInfo->serch($arr_post, $userinfo_count, $page);
    //10件1ページごとに表示させるためにページ総数、現在のページ、検索結果をparamsに格納し返す。
    $params         = [
      'all_pages'      => ceil($userinfo_count / 10),
      'page'           => $page,
      'userinfo_serch' => $userinfo_serch
    ];
    return $params;
  }
}