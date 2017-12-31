window.onload = function(){
    // ページ読み込み時に実行したい処理
    var client_w = document.getElementById('thumbnail').clientWidth;
    client_w = client_w * (2/3);
    document.getElementById('thumbnail').style.height=client_w+"px";
}
