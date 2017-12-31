$(function() {
    //クリックしたときのファンクションをまとめて指定
    $('.main_center .tab').click(function() {
        //.index()を使いクリックされたタブが何番目かを調べ、
        //indexという変数に代入します。
        var index = $('.main_center .tab').index(this);

        //コンテンツを一度すべて非表示にし、
        $('.index_contents').css('display','none');

        //クリックされたタブと同じ順番のコンテンツを表示します。
        $('.index_contents').eq(index).css('display','block');

        //一度タブについているクラスselectを消し、
        $('.main_center .tab').removeClass('tab_on');

        //クリックされたタブのみにクラスselectをつけます。
        $(this).addClass('.main_center tab_on')
    });
});