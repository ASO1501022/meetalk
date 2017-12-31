<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
require_once '../php/DBManager.php';
require_once '../php/SearchAPIManager.php';
require_once '../php/SearchRoomManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchAPIMng = new SearchAPIManager();
$searchRoomMng = new SearchRoomManager();
$message = null;
if($userMng->loggedinCheck()){
    header('Location:index.php');
    exit;
}
if(!empty($_GET["Token"])){
    $message = $userMng->checkToken($_GET["Token"]);
    if(empty($message)){
        $userMng->changeTempToMainRegister($_GET["Token"]);
    }
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
function getMember($_b){
    $_a = 1;
    foreach ($_b as $_c) {
        if($_c->status == 2) $_a++;
    }
    return $_a;
}
function getDateToJpDate($_a){
    return date('Y年n月j日G時i分', strtotime($_a));
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
<?php   if(empty($message)):?>
        <meta http-equiv="refresh" content="2; URL='login.php'" />
<?php   endif?>
        <title>登録完了 - meetalk</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/register_comp.css">
        <link rel="stylesheet" href="css/service_title.css">
    </head>
    <body>
        <div id="contents">
            <div class="left_wrap">
                <div id="logo"></div>
                <div id="left_inner">
<?php           if(empty($message)):?>
                    <p>登録が完了しました。</p>
                    <p>自動的にログインページへ移動します。</p>
                    <div id="link_wrap"><p>移動しない場合は<a href="login.php">こちら</a></p></div>
<?php           else:?>
                    <p><?=h($message)?></p>
<?php           endif?>
                </div>
            </div>
            <div class="img_wrap">
                <div class="service_title_img">
                </div>
                <div class="service_title_wrap">
                    <div class="service_title_inner">
                        <p class="service_title">meetalk</p>
                        <p class="service_catch_copy">広がる、見つかる、友達の輪</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

