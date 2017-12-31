$(function(){
    $(document).on('click', '.invited_reject_btn', function() {
        var roomId = $(this).data("room-id");
        var _this = $(this);
        $.ajax({
            data: {
                room_id: roomId,
            },
            dataType: "text",
            error: function(e, f, d) {
                alert("通信に失敗しました")
            },
            success: function(b) {
                if (b === "success") {
                    $("body").css("overflow-x","hidden");
                    _this.parent().parent().animate({'right':'-500'},500,function(){
                        $(this).hide("fast",function(){
                            $("body").css("overflow-x","auto");
                        });
                    });
                } else{
                    alert(b)
                }
            },
            type: "POST",
            url: "ajax/invite.php"
        });
        return false
    });
});
