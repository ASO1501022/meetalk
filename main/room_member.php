<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
require_once '../php/RoomUserStatus.php';
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
$room =  $roomMng->getRoomByRoomId($_GET["room_id"]);
if(empty($room)){
    header('Location:index.php');
    exit;
}
$roomUserStatus = new RoomUserStatus();
$roomUserStatus->roomId = $_GET["room_id"];
$roomUserStatus->userId = $_SESSION["user_id"];
$userStatus = $roomMng->getUserStatusByRoomId($_GET["room_id"],$_SESSION["user_id"]);
if(!empty($room->userId != $_SESSION["user_id"])){
    if(!empty($userStatus->status)){
        if($userStatus->status != 2){
            header('Location:room_info.php?room_id='.$_GET["room_id"]);
            exit;
        }
    }else{
        header('Location:room_info.php?room_id='.$_GET["room_id"]);
        exit;
    }
}
if(!empty($_POST["kick"])){
    if(!empty($userMng->getUserByUserId($_POST["kick"]))){
        $roomMng->kickUserFromRoom($_POST["kick"], $room->roomId);
    }
}else if(!empty($_POST["apply"])){
    if(!empty($userMng->getUserByUserId($_POST["apply"]))){
        $roomMng->applyRequest($_POST["apply"], $room->roomId);
    }
}else if(!empty($_POST["reject"])){
    if(!empty($userMng->getUserByUserId($_POST["reject"]))){
        $roomMng->rejectRequest($_POST["reject"], $room->roomId);
    }
}else if(!empty($_POST["cancel"])){
    if(!empty($userMng->getUserByUserId($_POST["cancel"]))){
        $roomMng->escapeUserFromRoom($_POST["cancel"], $room->roomId);
    }
}
$room =  $roomMng->getRoomByRoomId($_GET["room_id"]);
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
        <title>ルームメンバ「<?=$room->roomName?>」 - meetalk</title>
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
        <link rel="stylesheet" href="css/invited.css">
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
                        <form action="room_member.php?room_id=<?=$_GET["room_id"]?>" method="POST">
                        <p id="main_center_title"><?=h($room->roomName)?></p>
                        <div id="member_tab_wrap">
                            <ul>
                                <li id="member_tab" class="tab tab_on">メンバ</li>
                                <li id="join_request_member_tab" class="tab">申請中</li>
                                <li id="invite_member_tab" class="tab">招待中</li>
                            </ul>
                        </div>
                        <div class="index_contents">
<?php                       foreach((array)$room->roomUserStatusList as $userStatus):?>
<?php                           if($userStatus->status == 2):?>
<?php                               $roomUser = $userMng->getUserByUserId($userStatus->userId)?>
                                    <div class="member_wrap">
                                        <div class="member_img_wrap"><img src="img/user_img/<?=$roomUser->imageName?>"></div>
                                        <div class="member_name_wrap"><p><a href="profile.php?user_id=<?=h($roomUser->userId)?>"><?=h($roomUser->userName)?></a></p><p>ID:<?=h($roomUser->userId)?></p></div>
<?php                               if($_SESSION["user_id"] == $room->userId):?>
                                        <div class="member_btn_wrap"><button type="submit" class="kick" name="kick" value="<?=$roomUser->userId?>">キック</button></div>
<?php                               endif?>
                                    </div>
<?php                           endif?>
<?php                       endforeach?>
                        </div>
                        <div class="index_contents" style="display:none">
<?php                       foreach((array)$room->roomUserStatusList as $userStatus):?>
<?php                           if($userStatus->status == 1):?>
<?php                               $roomUser = $userMng->getUserByUserId($userStatus->userId)?>
                                    <div class="member_wrap">
                                        <div class="member_img_wrap"><img src="img/user_img/<?=$roomUser->imageName?>" alt=""></div>
                                        <div class="member_name_wrap"><p><a href="profile.php?user_id=<?=h($roomUser->userId)?>"><?=h($roomUser->userName)?></a></p><p>ID:<?=h($roomUser->userId)?></p></div>
<?php                               if($_SESSION["user_id"] == $room->userId):?>
                                        <div class="member_btn_wrap"><button type="submit" class="apply" name="apply" value="<?=$roomUser->userId?>">承認</button><button type="submit" class="kick" name="reject" value="<?=$roomUser->userId?>">拒否</button></div>
<?php                               endif?>
                                    </div>
<?php                           endif?>
<?php                       endforeach?>
                        </div>
                        <div class="index_contents" style="display:none">
<?php                       foreach((array)$room->roomUserStatusList as $userStatus):?>
<?php                           if($userStatus->status == 3):?>
<?php                               $roomUser = $userMng->getUserByUserId($userStatus->userId)?>
                                    <div class="member_wrap">
                                        <div class="member_img_wrap"><img src="img/user_img/<?=$roomUser->imageName?>" alt=""></div>
                                        <div class="member_name_wrap"><p><a href="profile.php?user_id=<?=h($roomUser->userId)?>"><?=h($roomUser->userName)?></a></p><p>ID:<?=h($roomUser->userId)?></p></div>
<?php                               if($_SESSION["user_id"] == $room->userId):?>
                                        <div class="member_btn_wrap"><button type="submit" class="kick" name="cancel" value="<?=$roomUser->userId?>">取消</button></div>
<?php                               endif?>
                                    </div>
<?php                           endif?>
<?php                       endforeach?>
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