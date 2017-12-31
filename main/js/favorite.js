$(function(){
    $(document).on('click', '.no_favorited', function() {
        if(!confirm('この店舗をお気に入りに登録しますか？')){
            /* キャンセルの時の処理 */
            return false;
        }else{
            var $form = $('<form/>', {'action': '', 'method': 'post'});
            $form.append($('<input/>', {'type': 'hidden', 'name': "restaurant_id", 'value': $(this).data("restaurant-id")}));
            $form.append($('<input/>', {'type': 'hidden', 'name': "add", 'value': "add"}));
            $form.appendTo(document.body);
            $form.submit();
        }
    });
    $(document).on('click', '.favorited', function() {
        if(!confirm('この店舗をお気に入りから削除しますか？')){
            /* キャンセルの時の処理 */
            return false;
        }else{
            var $form = $('<form/>', {'action': '', 'method': 'post'});
            $form.append($('<input/>', {'type': 'hidden', 'name': "restaurant_id", 'value': $(this).data("restaurant-id")}));
            $form.append($('<input/>', {'type': 'hidden', 'name': "delete", 'value': "delete"}));
            $form.appendTo(document.body);
            $form.submit();
        }
    });
});