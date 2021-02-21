<?php
require_once(ROOT_PATH. 'Models/calender.php');

if(!isset($_SESSION['login_userInfo']['class'])) {
  session_start();
}

$calender = new calender();

date_default_timezone_set('Asia/Tokyo');
//ajax通信によってPOST情報が送られてきた場合
if(isset($_POST['date'])) {
  $date = $_POST['date'];
}
//送られてこなかった場合、今月の情報を取得
else {
  $date = date("Y/m/d H:i:s");
}
//202102の形に整形
$date_ym_now = date("Ym", strtotime($date));
//2021年02月の形に整形←この値を表示させる
$date_ym_head = date("Y年m月", strtotime($date));
$year = substr($date_ym_now, 0, 4);
$month = substr($date_ym_now, 4, 2);

//カレンダー上部のナビに入れる文字を作成
for($i = 1; $i <= 12; $i++) {
  //0が入っているものは消す
  if(substr($month, 0, 1) == 0) {
    $month_cal = substr($month, 1, 1);
  }
  else {
    $month_cal = $month;
  }
  //次月と前月を求める
  if($i == $month_cal) {
    $nextMonth = $month + 1;
    $nextMonth = sprintf('%02d', $nextMonth);
    $lastMonth = $month - 1;
    $nextYear  = $year;
    if($nextMonth == 13 && isset($nextMonth)) {
      $nextMonth = 1;
      $nextYear = $year + 1;
    }
  }
}

//データベースから表示する月のスケージュールを取得
$date_YM_now  = date("Y-m", strtotime($date));
$getEvent = $calender->get($date_YM_now);
//スケジュールが登録されているときのみ、日にちの整型処理を行う
if(!empty($getEvent)) {
  $getEvent_date = array_column($getEvent, 'date');
  foreach($getEvent_date as $key => $value) {
    $getEvent_date[$key] = date("d", strtotime($value));
  }
}
else {
  $getEvent_date = array();
}

$DOW_1st   = date('w', mktime(0, 0, 0, $month, 1, $year));
$DOW_final = date('w', mktime(0, 0, 0, $month + 1, 0, $year));
$day = 1;
?>

<section class="c-marginTop5">
  <div id="p-top-carender-head">
    <h2 id="p-top-carender-head__title">行事カレンダー</h2>
    <?php if($_SESSION['login_userInfo']['class'] == '教職員'): ?>
      <p><a href="/event/event_regist.php" id="p-top-carender-regist__link" class="c-border-bottom">イベントの登録</a></p>
    <?php endif; ?>
  </div>
  <div id="p-top-carender-head__current" class="c-marginTop2">
    <h3 class="c-font-size1-2"><a href="#" year="<?php echo $year; ?>" month="<?php echo $lastMonth; ?>" class="c-font-black c-prev">&lt;</a>&nbsp;&nbsp;<?php echo $date_ym_head; ?>&nbsp;&nbsp;<a href="#" year="<?php echo $nextYear; ?>" month="<?php echo $nextMonth; ?>" class="c-font-black c-next">&gt;</a></h3>
  </div>
  <table id="p-top-carender-table">
      <tr>
        <th>日</th>
        <th>月</th>
        <th>火</th>
        <th>水</th>
        <th>木</th>
        <th>金</th>
        <th>土</th>
      </tr>
      <!--カレンダー日付部分の作成-->
      <?php
      for ($i = 1; $i <= $DOW_1st; $i++) {
        echo "<td></td>";
      }
      while(checkdate($month, $day, $year)) {
        //日付のゼロ埋め
        $date_day = sprintf('%02d', $day);
        $date_output = $day;
        //土曜日の場合
        if(date('w', strtotime(date($year.$month.$date_day))) == 6){
          //日程が登録されているか確認
          if(array_search($date_day, $getEvent_date) !== FALSE) {
            $posi = array_keys($getEvent_date, $date_day);
            if($_SESSION['login_userInfo']['class'] == '教職員') {
              echo '<td class="c-sat">'.$date_output;
              foreach($posi as $key => $value) {
                echo '</br><i class="fas fa-child"></i> <a href="/event/event_edit.php?event_id='.$getEvent[$value]['event_id'].'" class="c-font-black">'.date("G:i" ,strtotime($getEvent[$value]['start_time'])).'~'.date("G:i" ,strtotime($getEvent[$value]['finish_time'])).'</br><span class="p-top-carender-event__link">'.$getEvent[$value]['event'].'</span></a>';
              }
              echo '</td>';
            }
            else {
              echo '<td class="c-sat">'.$date_output;
              foreach($posi as $key => $value) {
                echo '</br><i class="fas fa-child"></i> <span class="c-font-black">'.date("G:i" ,strtotime($getEvent[$value]['start_time'])).'~'.date("G:i" ,strtotime($getEvent[$value]['finish_time'])).'</br><span class="p-top-carender-event__link">'.$getEvent[$value]['event'].'</span></span>';
              }
              echo '</td>';
            }
          }
          else {
            echo '<td class="c-sat">'.$date_output.'</td>';
          }
          // 週を終了
          echo "</tr>";
          // 次の週がある場合は新たな行を準備
          if (checkdate($month, $day + 1, $year)) {
            echo "<tr>";
          }
        }
        //日曜日の場合
        elseif(date('w', strtotime(date($year.$month.$date_day))) == 0){
          if(array_search($date_day, $getEvent_date) !== FALSE) {
            $posi = array_keys($getEvent_date, $date_day);
            if($_SESSION['login_userInfo']['class'] == '教職員') {
              echo '<td class="c-sun">'.$date_output;
              foreach($posi as $key => $value) {
                echo '</br><i class="fas fa-child"></i> <a href="/event/event_edit.php?event_id='.$getEvent[$value]['event_id'].'" class="c-font-black">'.date("G:i" ,strtotime($getEvent[$value]['start_time'])).'~'.date("G:i" ,strtotime($getEvent[$value]['finish_time'])).'</br><span class="p-top-carender-event__link">'.$getEvent[$value]['event'].'</span></a>';
              }
              echo '</td>';
            }
            else {
              echo '<td class="c-sun">'.$date_output;
              foreach($posi as $key => $value) {
                echo '</br><i class="fas fa-child"></i> <span class="c-font-black">'.date("G:i" ,strtotime($getEvent[$value]['start_time'])).'~'.date("G:i" ,strtotime($getEvent[$value]['finish_time'])).'</br><span class="p-top-carender-event__link">'.$getEvent[$value]['event'].'</span></span>';
              }
              echo '</td>';
            }
          }
          else {
            echo '<td class="c-sun">'.$date_output.'</td>';
          }
        }
        //それ以外の場合
        else{
          if(array_search($date_day, $getEvent_date) !== FALSE) {
            $posi = array_keys($getEvent_date, $date_day);
            if($_SESSION['login_userInfo']['class'] == '教職員') {
              echo '<td>'.$date_output;
              foreach($posi as $key => $value) {
                echo '</br><i class="fas fa-child"></i> <a href="/event/event_edit.php?event_id='.$getEvent[$value]['event_id'].'" class="c-font-black">'.date("G:i" ,strtotime($getEvent[$value]['start_time'])).'~'.date("G:i" ,strtotime($getEvent[$value]['finish_time'])).'</br><span class="p-top-carender-event__link">'.$getEvent[$value]['event'].'</span></a>';
              }
              echo '</td>';
            }
            else {
              echo '<td>'.$date_output;
              foreach($posi as $key => $value) {
                echo '</br><i class="fas fa-child"></i> <span class="c-font-black">'.date("G:i" ,strtotime($getEvent[$value]['start_time'])).'~'.date("G:i" ,strtotime($getEvent[$value]['finish_time'])).'</br><span class="p-top-carender-event__link">'.$getEvent[$value]['event'].'</span></span>';
              }
              echo '</td>';
            }
          }
          else {
            echo '<td>'.$date_output.'</td>';
          }
        }
        $day++;
      }
      for ($i = 1; $i < 7 - $DOW_final; $i++) {
        echo '<td></td>';
      }
      ?>
  </table>
</section>