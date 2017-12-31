$(function(){
    $(document).on('click', '.tag_delete', function() {
        $(this).parent().parent().fadeOut("fast",function(){
            $(this).remove();
        });
    });
});
function pushKey(code) {
	if(13 === code){
		addTag();
	}
}
function addTag() {
    if($('#tag_input').val() != ""){
        $('#info_tag_inner').append('<div class="tag_inner tag"><div class="tag_name"><input type="hidden" name="tag[]" value="' + $('#tag_input').val() + '"><img class="tag_delete" src="img/tag_delete.png" alt="X"><p>' + $('#tag_input').val() +'</p></div></div>');
        $('#tag_input').val("");
    }else{
        alert("文字を入力してください");
    }
}
