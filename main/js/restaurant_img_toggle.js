$(function(){
    $('#s_restaurant_button_1').on('click',function(){
        $('#l_restaurant_img').append('<div id="curtain">');
        $('#curtain').css({
            position: 'absolute',
            left: 0, top: 0,
            width: '100%', height: '100%',
            backgroundColor: '#fff',
            opacity: 0
        }).animate({opacity: 1},500,function(){
            $('#l_restaurant_img').css('background-image', 'url('+ $('#s_restaurant_img_1').attr('src') +')');
        });
        $('#curtain').animate({
            opacity: 0
        }, 500, function () {
            // アニメーション終了後に自身を消す
            $(this).remove();
        });
    });
    $('#s_restaurant_button_2').on('click',function(){
        $('#l_restaurant_img').append('<div id="curtain">');
        $('#curtain').css({
            position: 'absolute',
            left: 0, top: 0,
            width: '100%', height: '100%',
            backgroundColor: '#fff',
            opacity: 0
        }).animate({opacity: 1},500,function(){
            $('#l_restaurant_img').css('background-image', 'url('+ $('#s_restaurant_img_2').attr('src') +')');
        });
        $('#curtain').animate({
            opacity: 0
        }, 500, function () {
            // アニメーション終了後に自身を消す
            $(this).remove();
        });
    });
});
