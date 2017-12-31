<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/User.php';
require_once '../php/RoomManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$searchAPIMng = new SearchAPIManager();
$message = null;
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$joinRoomList = $roomMng->getJoinRoomByUserId($_SESSION["user_id"]);
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
        <title>参加部屋 - meetalk</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/pc_default.css">
        <link rel="stylesheet" href="css/nav.css">
        <link rel="stylesheet" href="css/search.css">
        <link rel="stylesheet" href="css/tab.css">
        <link rel="stylesheet" href="css/main_default.css">
        <link rel="stylesheet" href="css/main_contents.css">
        <link rel="stylesheet" href="css/tag.css">
        <link rel="stylesheet" href="css/join_room.css">
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
                        <p id="main_center_title">参加中の部屋</p>
                        <div id="join_room_tab_wrap">
                            <ul>
                                <li id="create_room_tab" class="tab tab_on">作成者</li>
                                <li id="join_room_tab" class="tab">参加中</li>
                                <li id="request_room_tab" class="tab">申請中</li>
                            </ul>
                        </div>
                        <div class="index_contents">
<?php                   foreach((array)$joinRoomList as $room):?>
<?php                   $userStatus = $roomMng->getUserStatusByRoomId($room->roomId,$_SESSION["user_id"])?>
<?php                   if($room->userId == $_SESSION["user_id"]):?>
<?php                   $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($room->restaurantId); ?>
                            <!--部屋-->
                            <div class="list_wrap">
                                <div class="list_img_wrap">
                                    <div class="list_img_shadow"></div>
<?php                           if(is_string($restaurant->image->shop_image1)):?>
                                    <div class="list_img_inner"><img class="list_img" src="<?=h($restaurant->image->shop_image1)?>" alt=""></div>
<?php                           else:?>
                                    <div class="list_img_inner"><img class="list_img" src="img/no_image.png" alt=""></div>
<?php                           endif;?>
                                </div>
                                <div class="list_content">
                                    <p class="list_title"><a href="room_info.php?room_id=<?=h($room->roomId)?>"><?=h($room->roomName)?></a></p>
                                    <p class="list_member">人数:<?=h(getMember($room->roomUserStatusList))?>/<?=h($room->maxMember)?>人</p>
                                    <p class="list_dead_line">開催日時:<?=h(getDateToJpDate($room->deadLine))?></p>
                                    <p class="list_budget">予算:<?=h($room->budget)?>円</p>
                                </div>
                                <div class="list_tag_wrap">
<?php                       foreach((array)$room->roomTagList as $roomTag):?>
                                    <div class="tag_inner list_tag tag">
                                        <div class="tag_name"><p><a href="search.php?=search_tab=tag&search_text=<?=h($roomTag->tagName)?>"><?=h($roomTag->tagName)?></a></p></div>
                                    </div>
<?php                       endforeach?>
                                </div>
                            </div>
                            <!--部屋　ここまで-->
<?php                   endif?>
<?php                   endforeach?>
                        </div>
                        <div class="index_contents" style="display:none">
<?php                   foreach((array)$joinRoomList as $room):?>
<?php                   $userStatus = $roomMng->getUserStatusByRoomId($room->roomId,$_SESSION["user_id"])?>
<?php                   if(!empty($userStatus->status)):?>
<?php                   if($userStatus->status == 2):?>
<?php                   $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($room->restaurantId); ?>
                            <!--部屋-->
                            <div class="list_wrap">
                                <div class="list_img_wrap">
                                    <div class="list_img_shadow"></div>
<?php                           if(is_string($restaurant->image->shop_image1)):?>
                                    <div class="list_img_inner"><img class="list_img" src="<?=h($restaurant->image->shop_image1)?>" alt=""></div>
<?php                           else:?>
                                    <div class="list_img_inner"><img class="list_img" src="img/no_image.png" alt=""></div>
<?php                           endif;?>
                                </div>
                                <div class="list_content">
                                    <p class="list_title"><a href="room_info.php?room_id=<?=h($room->roomId)?>"><?=h($room->roomName)?></a></p>
                                    <p class="list_member">人数:<?=h(getMember($room->roomUserStatusList))?>/<?=h($room->maxMember)?>人</p>
                                    <p class="list_dead_line">開催日時:<?=h(getDateToJpDate($room->deadLine))?></p>
                                    <p class="list_budget">予算:<?=h($room->budget)?>円</p>
                                </div>
                                <div class="list_tag_wrap">
<?php                       foreach((array)$room->roomTagList as $roomTag):?>
                                    <div class="tag_inner list_tag tag">
                                        <div class="tag_name"><p><a href="search.php?=search_tab=tag&search_text=<?=h($roomTag->tagName)?>"><?=h($roomTag->tagName)?></a></p></div>
                                    </div>
<?php                       endforeach?>
                                </div>
                            </div>
                            <!--部屋　ここまで-->
<?php                   endif?>
<?php                   endif?>
<?php                   endforeach?>
                        </div>
                        <div class="index_contents" style="display:none">
<?php                   foreach((array)$joinRoomList as $room):?>
<?php                   $userStatus = $roomMng->getUserStatusByRoomId($room->roomId,$_SESSION["user_id"])?>
<?php                   if(!empty($userStatus->status)):?>
<?php                   if($userStatus->status == 1):?>
<?php                   $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($room->restaurantId); ?>
                            <!--部屋-->
                            <div class="list_wrap">
                                <div class="list_img_wrap">
                                    <div class="list_img_shadow"></div>
<?php                           if(is_string($restaurant->image->shop_image1)):?>
                                    <div class="list_img_inner"><img class="list_img" src="<?=h($restaurant->image->shop_image1)?>" alt=""></div>
<?php                           else:?>
                                    <div class="list_img_inner"><img class="list_img" src="img/no_image.png" alt=""></div>
<?php                           endif;?>
                                </div>
                                <div class="list_content">
                                    <p class="list_title"><a href="room_info.php?room_id=<?=h($room->roomId)?>"><?=h($room->roomName)?></a></p>
                                    <p class="list_member">人数:<?=h(getMember($room->roomUserStatusList))?>/<?=h($room->maxMember)?>人</p>
                                    <p class="list_dead_line">開催日時:<?=h(getDateToJpDate($room->deadLine))?></p>
                                    <p class="list_budget">予算:<?=h($room->budget)?>円</p>
                                </div>
                                <div class="list_tag_wrap">
<?php                       foreach((array)$room->roomTagList as $roomTag):?>
                                    <div class="tag_inner list_tag tag">
                                        <div class="tag_name"><p><a href="search.php?=search_tab=tag&amp;search_text=<?=h($roomTag->tagName)?>"><?=h($roomTag->tagName)?></a></p></div>
                                    </div>
<?php                       endforeach?>
                                </div>
                            </div>
                            <!--部屋　ここまで-->
<?php                   endif?>
<?php                   endif?>
<?php                   endforeach?>
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