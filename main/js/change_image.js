$(function(){
    $("#preview_img_input").change(function(){
        if ( !this.files.length ) {
            alaert("error")
        }            
        var file = $(this).prop('files')[0];
        var fr = new FileReader();
        fr.onload = function() {
            $("#preview_img").css("background-image","url(" + fr.result + ")");
        }
        fr.readAsDataURL(file);
    });
    $("#preview_img").click(function(){
        $("#preview_img_input").click();
    });
});
