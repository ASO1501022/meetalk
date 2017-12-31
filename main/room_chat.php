<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$message = null;
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
if(empty($_GET["room_id"])){
    header('Location:index.php');
    exit;
}

$room = $roomMng->getRoomByRoomId($_GET["room_id"]);
if(empty($room)){
    header('Location:index.php');
    exit;
}
$user = $userMng->getUserByUserId($_SESSION["user_id"]);
$userStatus = $roomMng->getUserStatusByRoomId($_GET["room_id"],$_SESSION["user_id"]);
if(!empty($userStatus)){
    if($userStatus->status != 2 && $room->userId != $_SESSION["user_id"]){
        header('Location:room_info.php?room_id='.$_GET["room_id"]);
        exit;
    }
}else{
    header('Location:room_info.php?room_id='.$_GET["room_id"]);
    exit;
}

if(!empty($_POST["send"])){
    if(empty($_POST["chat_message"])){
        $message = "メッセージが空です";
    }else if(3000 < strlen($_POST["chat_message"])){
        $message = "メッセージは3000文字以下にしてください";
    }
    if(empty($message)){
        $roomMng->sendMessage($_SESSION["user_id"],$_GET["room_id"],$_POST["chat_message"]);
    }else{
        $message = 'alert("'.$message.'")';
    }
}
$messageList = $roomMng->getMessageListByRoomId($_GET["room_id"]);

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
        <title>チャット「<?=$room->roomName?>」 - meetalk</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/pc_default.css">
        <link rel="stylesheet" href="css/nav.css">
        <link rel="stylesheet" href="css/search.css">
        <link rel="stylesheet" href="css/tab.css">
        <link rel="stylesheet" href="css/main_default.css">
        <link rel="stylesheet" href="css/main_contents.css">
        <link rel="stylesheet" href="css/main_contents.css">
        <link rel="stylesheet" href="css/tag.css">
        <link rel="stylesheet" href="css/room_chat.css">
        <link rel="stylesheet" href="css/invited.css">
        <style>
            .a_flexbox{
                display: -webkit-flex;
                display: flex;
                border-bottom: dashed 2px #8D5F31;
                align-items: center;
            }
            .a_flexbox button{
                margin-left:auto;
                padding: 0px 10px;
                color:white;
                background: #85E990;
                border-radius:10px;
                font-size:14px;
                height:30px;
            }
            .a_flexbox #main_center_title{
                border: none;
            }
        </style>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/onload.js"></script>
        <script src="js/restaurant_img_toggle.js"></script>
        <script src="js/explain_view.js"></script>
        <script src="js/invited.js"></script>
        <script>addOnload(function(){<?=$message?>})</script>
    </head>
    <body>
        <?php include '../component/invited.php' ?>
        <div id="contents">
            <?php include '../component/header.php' ?>
            <div id="main">
                <div id="main_inner">
                    <div class="ad ad_left">
                        <?php include '../component/ad.php' ?>
                    </div>
                    <div class="main_center">
                        <div class="a_flexbox"><p id="main_center_title"><?=h($room->roomName)?></p><button id="reload" onclick="location.href = location">更新</button></div>
<?php                   foreach($messageList as $roomChatMessage):?>
<?php                   $mUser = $userMng->getUserByUserId($roomChatMessage->userId);?>
<?php                   if($roomChatMessage->userId != $_SESSION["user_id"]):?>
                        <div class="chat_content_wrap member">
                            <div class="chat_content_inner">
                                <div class="content_left"><img src="img/user_img/<?=h($mUser->imageName)?>"></div>
                                <div class="content_right">
                                    <p class="member_name"><a href="profile.php?user_id=<?=h($mUser->userId)?>"><?=h($mUser->userName)?></a></p>
                                    <div class="chat_message_wrap">
                                        <p><?=h($roomChatMessage->message)?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
<?php                   else:?>
                        <div class="chat_content_wrap self">
                            <div class="chat_content_inner">
                                <div class="chat_message_wrap">
                                        <p><?=h($roomChatMessage->message)?></p>
                                </div>
                            </div>
                        </div>
<?php                   endif?>
<?php                   endforeach?>
                        <form action="room_chat.php?room_id=<?=$_GET["room_id"]?>" method="POST">
                            <div id="self_chat_message_input">
                                <textarea name="chat_message" wrap="hard" placeholder="(3000文字以下)" required></textarea>
                                <div id="chat_message_btn_wrap"><button name="send" type="submit" id="chat_message_btn" value="送信">送信</button></div>
                            </div>
                        </form>
                    </div>
                    <div class="ad ad_right">
                        <?php include '../component/ad.php' ?>
                    </div>
                </div>
            </div>
            <?php include '../component/footer.php' ?>
        </div>
        <script>
            $(function(){
                $(window).scrollTop($('#self_chat_message_input').offset().top);
            })
        </script>
    </body>
</html>