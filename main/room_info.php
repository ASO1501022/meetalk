<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
require_once '../php/SearchAPIManager.php';
require_once '../php/DBManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$searchAPIMng = new SearchAPIManager();
$dbm = new DBManager();
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
$restaurant = $searchAPIMng->searchRestaurantByRestaurantId($room->restaurantId);
$createUser = $userMng->getUserByUserId($room->userId);
$roomUserStatus = $roomMng->getUserStatusByRoomId($room->roomId,$_SESSION["user_id"]);

if(!empty($roomUserStatus->status)){
    if($roomUserStatus->status != 2 && $room->userId != $_SESSION["user_id"]){
        if(getMember($room->roomUserStatusList) >= $room->maxMember){
            $message = "制限人数に達しています";
        }else if($roomUserStatus->status != 0){
            switch($roomMng->checkDeadLine($room->deadLine)){
                case 2:
                    $message = "この部屋は既に閉め切られています";
                    break;
                case 3:
                    $message = "この部屋は解散しました";
                    break;
            }
        }else{
            $message = "キックされています";
        }
    }
}else{
    if($room->userId != $_SESSION["user_id"]){
        if(getMember($room->roomUserStatusList) >= $room->maxMember){
            $message = "制限人数に達しています";
        }else{
            switch($roomMng->checkDeadLine($room->deadLine)){
                case 1:
                case 2:
                    $message = "この部屋は既に閉め切られています";
                    break;
                case 3:
                    $message = "この部屋は解散しました";
                    break;
            }
        }
    }
}
if(empty($message)){
    if(!empty($_POST["break"])){
        if($_SESSION["user_id"] == $room->userId){
            $roomMng->dissolveRoom($_GET["room_id"]);
        }
    }else if(!empty($_POST["escape"])){
        if($_SESSION["user_id"] == $_POST["escape"] && $createUser->userId != $_POST["escape"]){
            $roomMng->escapeUserFromRoom($_POST["escape"],$_GET["room_id"]);
        }
    }else if(!empty($_POST["enter"])){
        if($_SESSION["user_id"] == $_POST["enter"] && $createUser->userId != $_POST["enter"]){
            if(!empty($roomUserStatus)){
                if($roomUserStatus->status == 3){
                    $dbm->updateRoomUserStatus($_SESSION["user_id"],$room->roomId,2);
                    $userMng->addHistoryByUserId($_SESSION["user_id"],$_GET["room_id"]);
                }
            }
            $roomMng->joinRoomByUserId($_POST["enter"],$_GET["room_id"]);
            $userMng->addHistoryByUserId($_SESSION["user_id"],$_GET["room_id"]);
        }
    }
}
$room = $roomMng->getRoomByRoomId($_GET["room_id"]);
$createUser = $userMng->getUserByUserId($room->userId);
$roomUserStatus = $roomMng->getUserStatusByRoomId($room->roomId,$_SESSION["user_id"]);
if(!empty($roomUserStatus->status)){
    if($room->userId != $_SESSION["user_id"]){
        if($roomUserStatus->status != 2 && $room->userId != $_SESSION["user_id"]){
            if(getMember($room->roomUserStatusList) >= $room->maxMember){
                $message = "制限人数に達しています";
            }else if($roomUserStatus->status != 0){
                switch($roomMng->checkDeadLine($room->deadLine)){
                    case 2:
                        $message = "この部屋は既に閉め切られています";
                        break;
                    case 3:
                        $message = "この部屋は解散しました";
                        break;
                }
            }else{
                $message = "キックされています";
            }
        }
    }
}else{
    if($room->userId != $_SESSION["user_id"]){
        if(getMember($room->roomUserStatusList) >= $room->maxMember){
            $message = "制限人数に達しています";
        }else{
            switch($roomMng->checkDeadLine($room->deadLine)){
                case 1:
                case 2:
                    $message = "この部屋は既に閉め切られています";
                    break;
                case 3:
                    $message = "この部屋は解散しました";
                    break;
            }
        }
    }
}
if(empty($roomUserStatus->status)){
    $roomUserStatus->status = -1;
}

$autoApplyStr = $room->autoApply == 0 ? '自動' : '手動';
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
        <title>部屋「<?=$room->roomName?>」 - meetalk</title>
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
        <link rel="stylesheet" href="css/room_info.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/restaurant_img_toggle.js"></script>
        <script src="js/explain_view.js"></script>
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
                        <p id="top_message"><?=h($message)?></p>
                        <div id="main_center_title_wrap">
                            <p id="main_center_title"><?=h($room->roomName)?></p>
<?php                       if($room->userId == $_SESSION["user_id"] && $roomMng->checkDeadLine($room->deadLine) == 1): //作成者かどうか?>
                            <a href="room_edit.php" id="item_info_btn" class="room_edit_btn">編集</a>
<?php                       endif?>
                        </div>
                        <div id="info_tag_wrap">
                            <div class="info_tag_inner">
<?php                       foreach($room->roomTagList as $roomTag):?>
                                <div class="tag_inner tag"><div class="tag_name"><p><a href="search.php?search_tab=tag&amp;search_text=<?=h($roomTag->tagName)?>"><?=h($roomTag->tagName)?></a></p></div></div>
<?php                       endforeach?>
                            </div>
                        </div>
                        <a id="homepage" href="<?=h($restaurant->pcUrl)?>">店舗のHP</a>
<?php                   if(is_string($restaurant->image->shop_image1)):?>
                            <div id="l_restaurant_img" style="background-image:url(<?=h($restaurant->image->shop_image1)?>)"></div>
<?php                   else:?>
                            <div id="l_restaurant_img" style="background-image:url(img/no_image.png)"></div>
<?php                   endif;?>
                        <div id="item_info_center">
                            <div id="s_restaurant_img_wrap">
<?php                   if(is_string($restaurant->image->shop_image1)):?>
                                <button type="button" id="s_restaurant_button_1" class="s_restaurant_img_button"><img id="s_restaurant_img_1" src="<?=h($restaurant->image->shop_image1)?>" alt=""></button>
<?php                   else:?>
                                <button type="button" id="s_restaurant_button_1" class="s_restaurant_img_button"><img id="s_restaurant_img_1" src="img/no_image.png" alt="" disabled></button>
<?php                   endif;?>
<?php                   if(is_string($restaurant->image->shop_image2)):?>
                                <button type="button" id="s_restaurant_button_2" class="s_restaurant_img_button"><img id="s_restaurant_img_2" src="<?=h($restaurant->image->shop_image2)?>" alt=""></button>
<?php                   else:?>
                                <button type="button" id="s_restaurant_button_2" class="s_restaurant_img_button"><img id="s_restaurant_img_2" src="img/no_image.png" alt="" disabled></button>
<?php                   endif;?>
                            </div>
                                
                            <div id="button_wrap">
                                <form action="room_info.php?room_id=<?=$_GET["room_id"]?>" method="POST">
<?php                           if(empty($message)): ?>
<?php                           switch($roomMng->checkDeadLine($room->deadLine)): ?>
<?php                               case 0: //開催日時に達していない(参加者募集中)?>
<?php                                   if($room->userId == $_SESSION["user_id"]): //作成者かどうか?>
                                            <a class="room_member"  href="room_member.php?room_id=<?=h($room->roomId)?>">メンバ一覧</a>
                                            <a class="room_chat"  href="room_chat.php?room_id=<?=h($room->roomId)?>">チャット</a>
                                            <a class="room_invite" href="room_invite.php?room_id=<?=h($room->roomId)?>">招待</a>
                                            <button name="break" type="submit" class="room_break" value="break">解散</button>
<?php                                   else:?>
<?php                                       switch($roomUserStatus->status):?>
<?php                                           case 0:?>
                                                    <button type="submit" class="room_no" disabled>参加</button>
<?php                                           break;?>
<?php                                           case 1:?>
                                                    <button type="submit" class="room_no" disabled>参加申請中</button>
<?php                                           break;?>
<?php                                           case 2:?>
                                                    <a class="room_member"  href="room_member.php?room_id=<?=h($room->roomId)?>">メンバ一覧</a>
                                                    <a class="room_chat"  href="room_chat.php?room_id=<?=h($room->roomId)?>">チャット</a>
                                                    <button name="escape" type="submit" class="room_break" value="<?=h($_SESSION["user_id"])?>">退室</button>
<?php                                           break;?>
<?php                                           case 3:?>
<?php                                           case -1:?>
                                                    <button type="submit" class="room_invite" name="enter" value="<?=h($_SESSION["user_id"])?>">参加</button>
<?php                                           break;?>
<?php                                       endswitch?>
<?php                                   endif?>
<?php                               break;?>
<?php                               case 1: ?>
<?php                                   if($room->userId == $_SESSION["user_id"]): //作成者かどうか?>
                                            <a class="room_member"  href="room_member.php?room_id=<?=h($room->roomId)?>">メンバ一覧</a>
                                            <a class="room_chat"  href="room_chat.php?room_id=<?=h($room->roomId)?>">チャット</a>
<?php                                   else:?>
<?php                                       switch($roomUserStatus->status):?>
<?php                                           case 2:?>
                                                    <a class="room_member"  href="room_member.php?room_id=<?=h($room->roomId)?>">メンバ一覧</a>
                                                    <a class="room_chat"  href="room_chat.php?room_id=<?=h($room->roomId)?>">チャット</a>
<?php                                           break;?>
<?php                                       endswitch?>
<?php                                   endif?>
<?php                               break;?>
<?php                               case 2: //開催日時に達していない(参加者募集中)?>
<?php                                   if($room->userId == $_SESSION["user_id"]): //作成者かどうか?>
                                            <a class="room_member"  href="room_member.php?room_id=<?=h($room->roomId)?>">メンバ一覧</a>
                                            <a class="room_chat"  href="room_chat.php?room_id=<?=h($room->roomId)?>">チャット</a>
<?php                                   else:?>
<?php                                       switch($roomUserStatus->status):?>
<?php                                           case 2:?>
                                                    <a class="room_member"  href="room_member.php?room_id=<?=h($room->roomId)?>">メンバ一覧</a>
                                                    <a class="room_chat"  href="room_chat.php?room_id=<?=h($room->roomId)?>">チャット</a>
<?php                                           break;?>
<?php                                           case 3:?>
<?php                                           case -1:?>
                                                    <button type="submit" class="room_invite" name="enter" value="<?=h($_SESSION["user_id"])?>">参加</button>
<?php                                           break;?>
<?php                                       endswitch?>
<?php                                   endif?>
<?php                               break;?>
<?php                           endswitch;?>
<?php                           else:?>
                                                <button type="submit" class="room_no" disabled>参加</button>
<?php                           endif;?>
                                </form>
                            </div>
                        </div>
                        <div id="item_info_content">
                            <p><span>店舗名：</span><a href="restaurant.php?restaurant_id=<?=h($restaurant->restaurantId)?>"><?=h($restaurant->name)?></a></p>
                            <p><span>住所：</span><?=h($room->address)?></p>
                            <p><span>主催者：</span><a href="profile.php?user_id=<?=h($room->userId)?>"><?=h($createUser->userName)?></a></p>
                            <p><span>承認：</span><?=h($autoApplyStr)?></p>
                            <div id="dead_line_wrap">
                                <p><span>開催日時：</span><?=h(getDateToJpDate($room->deadLine))?></p>
                            </div>
                            <div id="join"><p><span>参加人数：</span><?=h(getMember($room->roomUserStatusList))?>/<?=h($room->maxMember)?>人</p></div>
                            <div id="budget"><p><span>予算：</span><?=h($room->budget)?>円</p></div>
                            <p><span>部屋の説明：</span></p>
                            <div id="item_info_content_explain">
                                <div id="explian">
                                <?=h($room->explain)?>
                                </div>
                                <!--<button id="item_info_content_view_btn_wrap"><div id="item_info_content_view_btn"></div></button>-->
                            </div>
                        </div>
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