$(function(){
    $(document).on('click', '.fav_delete', function() {
        if(!confirm('この店舗をお気に入りから削除しますか？')){
            /* キャンセルの時の処理 */
            return false;
        }else{
            var $form = $('<form/>', {'action': 'favorite.php', 'method': 'post'});
            $form.append($('<input/>', {'type': 'hidden', 'name': "delete", 'value': $(this).data("restaurant-id")}));
            $form.appendTo(document.body);
            $form.submit();
        }
    });
});