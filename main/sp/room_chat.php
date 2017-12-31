<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/RoomManager.php';
require_once '../../php/DBManager.php';
require_once '../../php/SearchRoomManager.php';
require_once '../../php/SearchAPIManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchRoomMng = new SearchRoomManager();
$searchAPIMng = new SearchAPIManager();

if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$user = $userMng->getUserByUserId($_SESSION['user_id']);
$roomId = filter_input(INPUT_GET, 'room_id');

if(empty($roomId)){
    header('Location:index.php');
    exit;
}
if(!empty($_POST['send_message']) && !empty($_POST['message'])){
    if($_POST['send_message'] == $_SESSION['user_id']){
        $roomMng->sendMessage($_SESSION["user_id"], $roomId, $_POST["message"]);
    }
}
$room = $roomMng->getRoomByRoomId($roomId);

//そのユーザーが部屋に属しているか
$userStatus = $roomMng->getUserStatusByRoomId($_GET["room_id"],$_SESSION["user_id"]);
if( $room->userId == $_SESSION["user_id"]){

} elseif(!empty($userStatus->status)){
    if($userStatus->status != 2){
        header('Location:room_info.php?room_id='.$_GET["room_id"]);
        exit;
    }
}else{
    header('Location:room_info.php?room_id='.$_GET["room_id"]);
    exit;
}

$roomChats = $room->roomMessageList;

// 出力の際に必ずこの関数を通して出力する
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>チャット</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/room-chat.css">
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <script type="text/javascript" src="./js/room-chat.js"></script>
    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->
</head>


<header>
    <?php require_once "./global.html" ?>
</header>
<div id="main">
    <body>
<!--GlobalMenuBar-->
        <div id="header">
            <h2>チャット</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <div class="wrap-chat">
<?php foreach($roomChats as $roomChat):?>
<?php     $user = $userMng->getUserByUserId($roomChat->userId) ?>
<?php     if($user->userId != $_SESSION['user_id']):?>
                <div class="another-message">
                    <div class="user-name">
                        <p><?=h($user->userName) ?></p>
                    </div>
                    <div class="image-message">
                        <div class="user-image">
                            <img src="../../img/user_img/<?=h($user->imageName)?>">
                        </div>
                        <div class="message">
                            <p><?=h($roomChat->message)?></p>
                        </div>
                    </div>
                </div>
<?php     else: ?>
                <div class="my-message">
                    <p><?=h($roomChat->message)?></p>
                </div>
<?php     endif ?>
<?php endforeach ?>
            </div>
        </div>
<!--送信ボックスと送信ボタン-->
        <form action="room_chat.php?room_id=<?=h($room->roomId)?>" method="post">
            <div id="send-message">
                <button type="button" onclick="location.href='./room_chat.php?room_id=<?=h($roomId)?>'">更新</button>
                <input type="text" name="message" placeholder="メッセージ" id="send_message">
                <button type="submit" name="send_message" value="<?=$_SESSION['user_id']?>">送信</button>
            </div>
        </form>
        
        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
    </body>
</div>