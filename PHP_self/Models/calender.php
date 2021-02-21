<?php
require_once(ROOT_PATH .'/Models/Db.php');

class calender extends Db {
  public function __construct($dbh=null) {
    parent::__construct($dbh);
  }

  public function get($date_YM_now) {
    date_default_timezone_set('Asia/Tokyo');
    //月初め
    $startDate = date('Y-m-d', strtotime('first day of ' . $date_YM_now));
    //月終わり
    $endDate  = date('Y-m-d', strtotime('last day of ' . $date_YM_now));
    $sql    = 'SELECT * FROM event_data WHERE date >= :start_date AND date <= :end_date';
    $stmt   = $this->dbh->prepare($sql);
    $params = array(
      ':start_date' => $startDate,
      ':end_date' => $endDate
    );
    $stmt->execute($params);
    $resultEvent = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(!empty($resultEvent)) {
      foreach($resultEvent as $key => $value) {
        $getEvent[$key] = $value;
      }
      return $getEvent;
    }
  }
}