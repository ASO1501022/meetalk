//読み込み時に一番下まで（最新まで）スクロールする
window.onload = function(){
    // 一番下までスクロールする
    $('body').delay(100).animate({
      scrollTop: $(document).height()
    },100);
}
function pageScrollBottom(){
    $(function(){
        $('body').scrollTop($("#wrap-chat")[0].scrollHeight);
    });
}

// メッセージボックスの下追従固定用JS
var ua = navigator.userAgent;
var isiPhoneMb = (ua.indexOf('iPhone') > -1 && ua.indexOf('iPad') == -1) || ua.indexOf('iPod') > -1;
var isAndroidMb = (ua.indexOf('Android') > -1 && ua.indexOf('Mobile') > -1);
var isSp = (isiPhoneMb || isAndroidMb);
 
$(function() {
    // 固定する要素の指定
    var fixElement = $('#send-message');
 
    // 閲覧端末がスマホのときの処理
    if(isSp) {
        $(window).on('load', function() {
            // 固定する要素を表示・position指定
            fixElement.show().css({
                position: 'absolute'
            });
        });
        // ページアクセス・スクロール・リサイズ時に固定位置を指定する
        $(window).on('load scroll resize', function() {
            elementFix();
        });
 
        // 要素を固定する処理
        function elementFix() {
            // 固定する要素の高さ
            var fixHeight = fixElement.outerHeight();
            // 現在のスクロール位置
            var scTop = $(window).scrollTop();
            // 表示領域の高さ
            var winHeight = window.innerHeight;

            var scrollHeight = $(document).height();
            var scrollPosition = $(window).height() + $(window).scrollTop();

            //スクロールが一番下まで行ったら#send-messageの位置調整をしない
            if ((scrollHeight - scrollPosition) / scrollHeight >= 0) {
                // when scroll to bottom of the page
                // 固定する要素の位置を指定
                fixElement.css({
                    top: scTop + winHeight - fixHeight
                });
                //固定する要素でフッターが隠れないように余白を作成
                $('body').css({
                    paddingBottom: fixHeight
                });
            }
            
        
        }
    }
});
