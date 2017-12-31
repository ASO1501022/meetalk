$(function(){
    $('.get-location').click(function(){
      if (navigator.geolocation) {
          /* geolocation is available */
          navigator.geolocation.getCurrentPosition(function(position) {
              // alert("緯度:"+position.coords.latitude+",経度"+position.coords.longitude);
              location.href='./search.php?search_genre=restaurant&latitude=' + position.coords.latitude + '&longitude=' + position.coords.longitude;
          },
          // 取得失敗した場合
          function(error) {
              switch(error.code) {
                  case 1: //PERMISSION_DENIED
                      alert("位置情報の利用が許可されていません");
                      break;
                  case 2: //POSITION_UNAVAILABLE
                      alert("位置情報が取得できませんでした");
                      break;
                  case 3: //TIMEOUT
                      alert("タイムアウトになりました");
                      break;
                  default:
                      alert("その他のエラー(エラーコード:"+error.code+")");
                      break;
              }
          
          });
      } else {
          /* geolocation IS NOT available */
          alert("GPSの使用を許可してください");
      }
  });
});
