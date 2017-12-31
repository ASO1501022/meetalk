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
$page = 1;

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
if(!empty($_POST["delete"])){
    if(!empty($searchAPIMng->searchRestaurantByRestaurantId($_POST["delete"]))){
        $userMng->deleteFavoriteByRestaurantId($_SESSION["user_id"],$_POST["delete"]);
    }
}
$favoriteList = $userMng->getFavoriteListByUserId($_SESSION["user_id"],$_GET["page"] - 1);
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
        <title>お気に入り店舗 - meetalk</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/pc_default.css">
        <link rel="stylesheet" href="css/nav.css">
        <link rel="stylesheet" href="css/search.css">
        <link rel="stylesheet" href="css/tab.css">
        <link rel="stylesheet" href="css/main_default.css">
        <link rel="stylesheet" href="css/main_contents.css">
        <link rel="stylesheet" href="css/item_info_default.css">
        <link rel="stylesheet" href="css/favorite.css">
        <link rel="stylesheet" href="css/tag.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/invited.js"></script>
        <script src="js/delete_fav.js"></script>
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
                        <p id="main_center_title">お気に入り店舗</p>
<?php                   foreach((array)$favoriteList as $favorite):?>
<?php                   $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($favorite->restaurantId) ?>
                        <!--部屋-->
                        <div class="list_wrap">
                            <img data-restaurant-id="<?=h($restaurant->restaurantId)?>" class="fav_delete" src="img/tag_delete.png" alt="">
                            <div class="list_img_wrap">
                                <div class="list_img_shadow"></div>
<?php                       if(is_string($restaurant->image->shop_image1)):?>
                                <div class="list_img_inner"><img class="list_img" src="<?=h($restaurant->image->shop_image1)?>" alt=""></div>
<?php                       else:?>
                                <div class="list_img_inner"><img class="list_img" src="img/no_image.png" alt=""></div>
<?php                       endif?>
                            </div>
                            <div class="list_content">
                                <p class="list_title"><a href="restaurant.php?restaurant_id=<?=h($restaurant->restaurantId)?>"><?=h($restaurant->name)?></a></p>
                                <p class="list_member">部屋数：<?=h($roomMng->getRoomCountByRestaurantId($restaurant->restaurantId))?></p>
                                <p class="list_dead_line">平均予算:<?=h($restaurant->budget)?>円</p>
                                <p class="list_restaurant_explian">店舗PR：<?=h($restaurant->prShort)?></p>
                            </div>
                            <div class="list_tag_wrap">
<?php                       foreach((array)$restaurant->categoryName as $tag):?>
                                    <div class="tag_inner list_tag tag">
                                        <div class="tag_name"><p><a href="search.php?search_tab=tag&search_text=<?=h($tag)?>"><?=h($tag)?></a></p></div>
                                    </div>
<?php                       endforeach?>
                            </div>
                        </div>
                        <!--部屋　ここまで-->
<?php                   endforeach?>
<?php                   if(empty($favoriteList)):?>
                            <p class="message" style="margin-top:10px">お気に入り店舗はありません</p>
<?php                   endif?>
                        <div id="page_num_wrap">
                            <p class="page_num before"><a href="?page=<?=$beforePage?>">&lt;</a></p>
<?php                       for($i = $pageMin;$i <= $pageMax;$i++):?>
<?php                           if($i == $_GET["page"]):?>
                                <p class="page_num page_selected"><a href="<?=h(getUnsetPageQueryString())?>page=<?=$i?>"><?=$i?></a></p>
<?php                           else:?>
                                <p class="page_num"><a href="<?=h(getUnsetPageQueryString())?>page=<?=$i?>"><?=$i?></a></p>
<?php                           endif;?>
<?php                       endfor?>
                            <p class="page_num after"><a href="?page=<?=$afterPage?>">></a></p>
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