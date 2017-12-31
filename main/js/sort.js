$(function(){
    $("#sort").change(function() {
        postSortToSearch();
    });
});
function postSortToSearch(){
    var a = getUrlVars();
    var queryString = "";
    if(location.search !== ''){
        var cnt = 0;
        for(key in a){
            if(!isNaN(key) || key === "sort") continue;
            if(cnt === 0){
                queryString += '?';
            }else{
                queryString += "&";
            };
            queryString += key + "=" + a[key];
            cnt++;
        }
        if(cnt !== 0){
            queryString += "&";
        }else{
            queryString += '?';
        }
    }else{
        queryString += '?';
    }
    queryString += "sort=" + $("#sort").val();
    location.href = queryString;
}

function getUrlVars()
{
    var vars = [], max = 0, hash = "", array = "";
    var url = window.location.search;


    hash  = url.slice(1).split('&');    
    max = hash.length;
    for (var i = 0; i < max; i++) {
        array = hash[i].split('=');    //keyと値に分割。
        vars.push(array[0]);    //末尾にクエリ文字列のkeyを挿入。
        vars[array[0]] = array[1];    //先ほど確保したkeyに、値を代入。
    }

    return vars;
}
