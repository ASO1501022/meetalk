<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/RoomManager.php';
require_once '../../php/DBManager.php';
require_once '../../php/SearchAPIManager.php';
require_once '../../php/SearchRoomManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchAPIMng = new SearchAPIManager();
$searchRoomMng = new SearchRoomManager();
$message = null;

if(!empty($_GET["Token"])){
    $message = $userMng->checkToken($_GET["Token"]);
    if(empty($message)){
        $userMng->changeTempToMainRegister($_GET["Token"]);
    }
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
<?php   if(empty($message)):?>
        <meta http-equiv="refresh" content="2; URL='./login.php'" />
<?php   endif?>
        <title>新規登録</title>
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/register_comp.css">
        
    </head>
    <body>
        <div id="contents">
            <div class="left_wrap">
                <div id="logo"></div>
                <div id="left_inner">
<?php           if(empty($message)):?>
                    <p>登録が完了しました。</p>
                    <p>自動的にログインページへ移動します。</p>
                    <div id="link_wrap"><p>移動しない場合は<a href="./login.php">こちら</a></p></div>
<?php           else:?>
                    <p><?=h($message)?></p>
<?php           endif?>
                </div>
            </div>
                <div class="img_wrap">

                </div>
        </div>
    </body>
</html>

