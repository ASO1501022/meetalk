$(function(){
    $("#order").change(function() {
        var url = window.location.search;
        url = url + "&order=" + $("select[name='order']").val();
        location.href = url;
    });
});
