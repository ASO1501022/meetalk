window.onload = function(){
    // ページ読み込み時に実行したい処理
    var client_w = document.getElementById('big-menu-content').clientWidth;
    document.getElementById('big-menu-content').style.height=client_w+"px";
    var client_w = document.getElementById('small-menu1-content').clientWidth;
    document.getElementById('small-menu1-content').style.height=client_w+"px";
    var client_w = document.getElementById('small-menu2-content').clientWidth;
    document.getElementById('small-menu2-content').style.height=client_w+"px";
}


$(window).on('orientationchange resize', function() {
    if (Math.abs(window.orientation) === 90) {
        // ここに回転させた時の処理
        var client_w = document.getElementById('big-menu-content').clientWidth;
        document.getElementById('big-menu-content').style.height=client_w+"px";
        var client_w = document.getElementById('small-menu1-content').clientWidth;
    document.getElementById('small-menu1-content').style.height=client_w+"px";
    var client_w = document.getElementById('small-menu2-content').clientWidth;
    document.getElementById('small-menu2-content').style.height=client_w+"px";
    } else {
        // ここに元に戻した時の処理
        var client_w = document.getElementById('big-menu-content').clientWidth;
        document.getElementById('big-menu-content').style.height=client_w+"px";
        var client_w = document.getElementById('small-menu1-content').clientWidth;
    document.getElementById('small-menu1-content').style.height=client_w+"px";
    var client_w = document.getElementById('small-menu2-content').clientWidth;
    document.getElementById('small-menu2-content').style.height=client_w+"px";
    }
});