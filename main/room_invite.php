<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
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
if($room->userId != $_SESSION["user_id"]){
    header('Location:room_info.php?room_id='.$_GET["room_id"]);
    exit;
}
if(!empty($_POST["invite"])){
    if(!empty($userMng->getUserByUserId($_POST["invite"]))){
        $roomMng->inviteFriendUserToRoom($_POST["invite"],$_GET["room_id"]);
    }
}

$friendUsers = $userMng->getFriendListByUserId($_SESSION["user_id"]);
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
        <title>招待「<?=$room->roomName?>」 - meetalk</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/pc_default.css">
        <link rel="stylesheet" href="css/nav.css">
        <link rel="stylesheet" href="css/search.css">
        <link rel="stylesheet" href="css/tab.css">
        <link rel="stylesheet" href="css/main_default.css">
        <link rel="stylesheet" href="css/main_contents.css">
        <link rel="stylesheet" href="css/item_info_default.css">
        <link rel="stylesheet" href="css/tag.css">
        <link rel="stylesheet" href="css/member_list.css">
        <link rel="stylesheet" href="css/friend.css">
        <link rel="stylesheet" href="css/invited.css">
        <link rel="stylesheet" href="css/room_invite.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/tab.js"></script>
        <script src="js/invited.js"></script>
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
                        <p id="main_center_title"><?=h($room->roomName)?> - 部屋招待</p>
                        <form action="room_invite.php?room_id=<?=h($_GET["room_id"])?>" method="POST">
                            <div class="index_contents">
<?php                           foreach((array)$friendUsers as $friendUser):?>
<?php                           if($friendUser->status == 1):?>
<?php                               $user = $userMng->getUserByUserId($friendUser->friendUserId);?>
<?php                               if($user->userId == $_SESSION["user_id"]):?>
<?php                               $user = $userMng->getUserByUserId($friendUser->userId);?>
<?php                               endif?>
<?php                              $userStatus = $roomMng->getUserStatusByRoomId($_GET["room_id"],$user->userId)?>
                                   <div class="member_wrap">
                                        <div class="member_img_wrap"><img src="img/user_img/<?=h($user->imageName)?>" alt=""></div>
                                        <div class="member_name_wrap"><p><a href="profile.php?user_id=<?=h($user->userId)?>"><?=h($user->userName)?></a></p><p>ID:<?=h($user->userId)?></p></div>
<?php                               if(!empty($userStatus->status)):?>
<?php                                   switch($userStatus->status):?>
<?php                                   case 0:?>
                                        <div class="member_btn_wrap"><button class="invite_btn no_btn" name="no" disabled>キックされています</button></div>
<?php                                       break;?>
<?php                                       case 1:?>
                                        <div class="member_btn_wrap"><button class="invite_btn no_btn" name="no" disabled>部屋に参加申請中です</button></div>
<?php                                       break;?>
<?php                                       case 2:?>
                                        <div class="member_btn_wrap"><button class="invite_btn no_btn" name="no" disabled>部屋に参加中です</button></div>
<?php                                       break;?>
<?php                                       case 3:?>
                                        <div class="member_btn_wrap"><button class="invite_btn no_btn" name="no" disabled>招待済</button></div>
<?php                                       break;?>
<?php                                   endswitch?>
<?php                               else:?>
                                        <div class="member_btn_wrap"><button type="submit" class="invite_btn" name="invite" value="<?=h($user->userId)?>">招待</button></div>
<?php                               endif;?>
                                </div>
<?php                           endif;?>
<?php                           endforeach?>
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
    </body>
</html>