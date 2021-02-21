$(function () {
  calender();
})

function calender() {
  //矢印をクリックしたとき
  $(document).on('click', '.c-prev, .c-next', function(event) {
    event.preventDefault();
    var day = 1;

    //aタグの独自属性に指定された値を変数にセットする
    if ($(this).attr('year') != undefined && $(this).attr('month') != undefined) {
      var year  = $(this).attr('year');
      var month = $(this).attr('month');
    }
    //aタグに属性が存在しなければ、今月の値を取得する
    else {
      var date_now = new Date();
      var year     = date_now.getFullYear;
      //ゼロ埋め
      var month = ("00" + (date_now.getMonth() + 1)).slice(-2);
    }
    //ゼロ埋め
    month = ("00" + month).slice(-2);
    day = ("00" + day).slice(-2);

    //ポストする日付を作成
    var date = year + '-' + month + '-' + day;

    jQuery.ajax({
      type : 'POST',
      dataType : 'text',
      data : {
        'date': date
      },
      cache : false,
      url : "../calender.php"
    }).done(function (date) {
      $('.c-ajax').html(date);
    }).error(function (XMLHttpRequest, textStatus, errorThrown) {
      alert('error!!!');
      console.log("XMLHttpRequest : " + XMLHttpRequest.status);
      console.log("textStatus     : " + textStatus);
      console.log("errorThrown    : " + errorThrown.message);
    });
  });
}

