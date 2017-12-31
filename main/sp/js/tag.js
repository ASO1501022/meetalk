$(function(){
    $(document).on('click', '.tag_delete', function() {
        $(this).parent().parent().remove();
    });
});
function addTag() {
    if($('#tag_input').val() != ""){
        $('#wrap-tag-edit').append('<div class="tag_inner delete"><div class="tag_name"><p>' + $('#tag_input').val() +'</p><input type="hidden" name="tag[]" value="' + $('#tag_input').val() + '"><img src="img/tag_delete.png" class="tag_delete"></div></div>');
        $('#tag_input').val("");
    }
}
