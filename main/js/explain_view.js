var flag = false;
$(function(){
    $('#item_info_content_view_btn_wrap').on('click',function(){
        explainViewtoggle();
    });
});

function explainViewtoggle(){
    if(!flag){
        $('#item_info_content_view_btn_wrap').css('background', 'none');
        $('#item_info_content_explain').css('overflow', 'auto');
        $('#item_info_content_explain').css('height', 'auto');
        $('#item_info_content_view_btn').css('background-image', 'url(../img/info_content_view_up_btn.png)');
    }else{
        $('#item_info_content_view_btn_wrap').css('background', 'linear-gradient(rgba(0,0,0,0),white)');
        $('#item_info_content_explain').css('overflow', 'hidden');
        $('#item_info_content_explain').css('height', '100px');
        $('#item_info_content_view_btn').css('background-image', 'url(../img/info_content_view_under_btn.png)');
    }
    flag = !flag;
}