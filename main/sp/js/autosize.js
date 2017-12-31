$("#autosize-text").height(30);//init
$("#autosize-text").css("lineHeight","20px");//init

$("#autosize-text").on("input",function(evt){
    if(evt.target.scrollHeight > evt.target.offsetHeight){   
        $(evt.target).height(evt.target.scrollHeight);
    }else{          
        var lineHeight = Number($(evt.target).css("lineHeight").split("px")[0]);
        while (true){
            $(evt.target).height($(evt.target).height() - lineHeight); 
            if(evt.target.scrollHeight > evt.target.offsetHeight){
                $(evt.target).height(evt.target.scrollHeight);
                break;
            }
        }
    }
});