<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
require_once '../php/SearchAPIManager.php';
require_once '../php/prefecture.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$searchAPIMng = new SearchAPIManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
if(empty($_GET["restaurant_id"])){
    header('Location:index.php');
    exit;
}
$restaurant = $searchAPIMng->searchRestaurantByRestaurantId($_GET["restaurant_id"]);
if(empty($restaurant)){
    header('Location:index.php');
    exit;
}
if(!empty($_POST["delete"])){
    $userMng->deleteFavoriteByRestaurantId($_SESSION["user_id"],$_GET["restaurant_id"]);
}else if(!empty($_POST["add"])){
    $userMng->addFavorite($_SESSION["user_id"],$_GET["restaurant_id"]);
}
adjustRestaurant($restaurant);
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
function adjustRestaurant($restaurant){
    if(is_string($restaurant->holiday)){
        $restaurant->holiday = str_ireplace('<br>','',$restaurant->holiday);
    }
    if(is_string($restaurant->openTime)){
        $restaurant->openTime = str_ireplace('<br>','',$restaurant->openTime);
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>店舗「<?=h($restaurant->name)?>」 - meetalk</title>
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
        <link rel="stylesheet" href="css/restaurant.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/restaurant_img_toggle.js"></script>
        <script src="js/explain_view.js"></script>
        <script src="js/invited.js"></script>
        <script src="js/favorite.js"></script>
    </head>
    <body>
        <?php include '../component/invited.php' ?>
        <div id="contents">
            <div id="main">
            <?php include '../component/header.php' ?>
                <div id="main_inner">
                    <div class="ad ad_left">
                        <?php include '../component/ad.php' ?>
                    </div>
                    <div class="main_center">
                        <div class="flexbox">
                            <p id="main_center_title"><?=h($restaurant->name)?></p>
<?php                       if($userMng->checkFavoriteRestaurant($_GET["restaurant_id"],$_SESSION["user_id"])):?>
                                <img data-restaurant-id="<?=h($restaurant->restaurantId)?>" class="favorited" src="img/favorited.png" alt="">
<?php                       else:?>
                                <img data-restaurant-id="<?=h($restaurant->restaurantId)?>" class="no_favorited" src="img/no_favorited.png" alt="">
<?php                       endif?>
                        </div>
                        <div id="info_tag_wrap">
                            <div class="info_tag_inner">
<?php                           foreach($restaurant->categoryName as $tag):?>
                                    <div class="tag_inner tag">
                                        <div class="tag_name"><p><a href="search.php?=search_tab=tag&amp;search_text=<?=h($tag)?>"><?=h($tag)?></a></p></div>
                                    </div>
<?php                           endforeach?>
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
                                <a class="room_register" href="room_register.php?restaurant_id=<?=h($restaurant->restaurantId)?>">部屋作成</a>
                                <a class="room_list"  href="search.php?restaurant_id=<?=h($restaurant->restaurantId)?>">部屋一覧</a>
                            </div>
                        </div>
                        <div id="item_info_content">
                            <p><span>住所：</span><?=h($restaurant->address)?></p>
                            <p><span>電話番号：</span><?=h($restaurant->tel)?></p>
                            <p><span>営業時間：</span><?=h($restaurant->openTime)?></p>
                            <p><span>休業日：</span><?=h($restaurant->holiday)?></p>
                            <p><span>平均予算：</span><?=h($restaurant->budget)?>円</p>
                            <p><span>店舗PR：</span></p>
                            <div id="item_info_content_explain">
                                <p><?=$restaurant->prLong?></p>
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