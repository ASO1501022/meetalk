<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/SearchAPIManager.php';
require_once '../php/SearchRoomManager.php';
require_once '../php/prefecture.php';
$userMng = new UserManager();
$searchAPIMng = new SearchAPIManager();
$searchRoomMng = new SearchRoomManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
//-----ページング-----
$pageMin = 1;
$pageMax = 13;
$beforePage = null;
$afterPage = 2;
if(!empty($_GET["page"])){
    if(13 < $_GET["page"]){
        $pageMin = $_GET["page"] - 12;
        $pageMax = $_GET["page"];
    }
    if(1 < $_GET["page"]){
        $beforePage = $_GET["page"] - 1;
        $afterPage = $_GET["page"] + 1;
    }
}else{
    $_GET["page"] = 1;
}
//-----ページング ここまで-----
$user = $userMng->getUserByUserId($_SESSION["user_id"]);
$trendRestaurantList = $searchRoomMng->searchTrendRestaurant();
$roomList = $searchRoomMng->searchRoomByAddress($user->prefecture,$_GET["page"] - 1);
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
function getUnsetPageQueryString(){
    $a = "?";
    if(!empty($_SERVER["QUERY_STRING"])){
        $querStrings = explode('&',$_SERVER["QUERY_STRING"]);
        foreach ($querStrings as $queryString) {
            $b = explode('=',$queryString);
            if($b[0] == "page") continue;
            $a .= $queryString."&";
        }
    }
    return $a;
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>TOP - meetalk</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/pc_default.css">
        <link rel="stylesheet" href="css/nav.css">
        <link rel="stylesheet" href="css/search.css">
        <link rel="stylesheet" href="css/tab.css">
        <link rel="stylesheet" href="css/main_default.css">
        <link rel="stylesheet" href="css/main_contents.css">
        <link rel="stylesheet" href="css/tag.css">
        <link rel="stylesheet" href="css/top.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/onload.js"></script>
        <script src="js/invited.js"></script>
        <script src="js/infiniteslide.js"></script>
        <script src="js/jquery.pause.min.js"></script>
        <script src="js/slide_contents.js"></script>
    </head>
    <body>
        <?php include '../component/invited.php' ?>
        <div id="contents">
            <?php include '../component/header.php' ?>
            <div id="main">
                <!--流行の店舗-->
                <div id="hot_restaurant_wrap">
                    <ul>
<?php               foreach((array)$trendRestaurantList as $restaurant):?>
                        <li class="hot_restaurant">
                            <div class="restaurant_title"><p><a href="restaurant.php?restaurant_id=<?=h($restaurant->restaurantId)?>"><?=h($restaurant->name)?></a></p></div>
<?php                   if(is_string($restaurant->image->shop_image1)):?>
                            <div class="restaurant_img"><img src="<?=h($restaurant->image->shop_image1)?>" alt=""></div>
<?php                   else:?>
                            <div class="restaurant_img"><img src="img/no_image.png" alt=""></div>
<?php                   endif;?>
                            <div class="under"><p class="room_num">部屋数:<?=h($restaurant->roomNumber)?></p><a href="search.php?restaurant_id=<?=h($restaurant->restaurantId)?>" class="restaurant_btn">部屋一覧</a></div>
                        </li>
<?php               endforeach?>
                    </ul>
                </div>
                <!--流行の店舗　ここまで-->
                <div id="main_inner">
                    <div class="ad ad_left">
                        <?php include '../component/ad.php' ?>
                    </div>
                    <div class="main_center">
                        <p id="main_center_title"><?=h($user->prefecture)?></p>
                        <!--部屋-->
<?php                foreach((array)$roomList as $room):?>
<?php                   $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($room->restaurantId); ?>
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
                                <p class="list_title"><a href="room_info.php?room_id=<?=$room->roomId?>"><?=h($room->roomName)?></a></p>
                                <p class="list_member">人数:<?=h(getMember($room->roomUserStatusList))?>/<?=h($room->maxMember)?>人</p>
                                <p class="list_dead_line">開催日時:<?=h(getDateToJpDate($room->deadLine))?></p>
                                <p class="list_budget">予算:<?=h($room->budget)?>円</p>
                            </div>
                            <div class="list_tag_wrap">
<?php                       foreach((array)$room->roomTagList as $roomTag):?>
                                <div class="tag_inner list_tag tag">
                                    <div class="tag_name"><p><a href="search.php?search_tab=tag&amp;search_text=<?=h($roomTag->tagName)?>"><?=h($roomTag->tagName)?></a></p></div>
                                </div>
<?php                       endforeach?>
                            </div>
                        </div>
<?php                   endforeach?>
                        <!--部屋　ここまで-->
<?php                   if(empty($roomList)):?>
                            <p class="message" style="margin-top:10px">部屋はありません</p>
<?php                   endif?>
                        <div id="page_num_wrap">
                            <p class="page_num before"><a href="<?=h(getUnsetPageQueryString())?>page=<?=$beforePage?>">&lt;</a></p>
<?php                       for($i = $pageMin;$i <= $pageMax;$i++):?>
<?php                           if($i == $_GET["page"]):?>
                                <p class="page_num page_selected"><a href="<?=h(getUnsetPageQueryString())?>page=<?=$i?>"><?=$i?></a></p>
<?php                           else:?>
                                <p class="page_num"><a href="<?=h(getUnsetPageQueryString())?>page=<?=$i?>"><?=$i?></a></p>
<?php                           endif;?>
<?php                       endfor?>
                            <p class="page_num after"><a href="<?=h(getUnsetPageQueryString())?>page=<?=$afterPage?>">></a></p>
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