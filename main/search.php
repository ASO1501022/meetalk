<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
require_once '../php/SearchAPIManager.php';
require_once '../php/SearchRoomManager.php';
require_once '../php/prefecture.php';
$userMng = new UserManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
    }
$searchRoomSortArray = array("最近出来た部屋順","予算が安い順","予算が高い順","開催日時が近い順","開催日時が遅い順");
$roomMng = new RoomManager();
$searchAPIMng = new SearchAPIManager();
$searchRoomMng = new SearchRoomManager();
$results = null;
$getPrefecture = null;
$getCity = null;
$searchAddress = null;
$sortIndex = 1;
$restaurant = null;
$sortArray = array("create_date DESC","budget ASC","budget DESC","dead_line ASC","dead_line DESC");
$page = 1;
$user = $userMng->getUserByUserId($_SESSION["user_id"]);
$searchTab = null;
if(!empty($_GET["page"]) && is_numeric($_GET["page"])){
    $page = $_GET["page"];
}
if(!empty($_GET["prefecture"])){
    $searchAddress = $_GET["prefecture"];
}else{
    $searchAddress = $user->prefecture;
    $_GET["prefecture"] = $user->prefecture;
}
if(!empty($_GET["city"])){
    if($_GET["city"] != '全地域'){
        $searchAddress .= $_GET["city"];
    }
}
//-----ページング-----
$pageMin = 1;
$pageMax = 13;
$beforePage = null;
$afterPage = 2;
if(!empty($_GET["page"])){
    if($pageMax < $_GET["page"]){
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
if(!empty($_GET["sort"])){
    $sortIndex = $_GET["sort"];
}
$searchText = (string)filter_input(INPUT_GET,"search_text");
$searchRestaurantId = (string)filter_input(INPUT_GET,"restaurant_id");
if(!empty($_GET["sort"]) && !empty($_GET["search_tab"])){
    $order = $sortArray[$_GET["sort"] - 1];
}else{
    $order = "create_date DESC";
}
if(!empty($searchText) && !empty($_GET["search_tab"])){
    $_address = $searchAddress;
    if(!empty($_GET["prefecture"])){
        if($_GET["prefecture"] == '全国'){
            $_address = NULL;
        }
    }
    switch ($_GET["search_tab"]) {
        case 'room':
            $results = $searchRoomMng->searchRoom($searchText,$_address,$order,$page - 1);
            $searchTab = '部屋名';
            break;
        case 'tag':
            $results = $searchRoomMng->searchRoomByTagName($searchText,$_address,$order,$page - 1);
            $searchTab = 'タグ名';
            break;
        
        case 'restaurant':
            $results = $searchAPIMng->searchRestaurantByRestaurantNameAndAddress($searchText,$_address,$_GET["page"]);
            $searchTab = '店舗名';
            break;
    }
}else if(!empty($searchRestaurantId)){
    $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($searchRestaurantId);
    try{
        $searchText = $restaurant->name;
        $results = $searchRoomMng->searchRoomListByRestaurantId($searchRestaurantId,$order,$_GET["page"] - 1);
        $_GET["search_tab"] = 'room';
    }catch(Exception $e){
        header('Location:index.php');
        exit;
    }
}
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
        <title>「<?=h($searchText)?>」の検索結果 - meetalk</title>
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
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/invited.js"></script>
        <script src="js/onload.js"></script>
        <script src="js/area.js"></script>
        <script src="js/sort.js"></script>
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
<?php                   if(!empty($restaurant)):?>
                        <p id="main_center_title"><?=h($searchText)?>の部屋一覧</p>
<?php                   else:?>
                        <p id="main_center_title"><?=h($searchAddress)?>での<?=h($searchText)?>の検索結果 - <?=h($searchTab)?></p>
<?php                   endif?>
                        <div id="sort_wrap">
<?php                   if(empty($restaurant)):?>
                            <select name="prefecture" id="prefecture" class="item_info_select">
                                <?php foreach((array)$prefectures as $prefecture):?>
                                <?php if(!empty($_GET["prefecture"] && $_GET["prefecture"] == $prefecture)):?>
                                    <option selected><?=h($prefecture)?></option>
                                <?php else:?>
                                    <option><?=h($prefecture)?></option>
                                <?php endif?>
                                <?php endforeach?>
                            </select>
                            <select name="city" id="city" class="item_info_select">
                                <option value="全地域" selected>全地域で検索</option>
                            </select>
                            <select name="sort" id="sort" class="item_info_select">
<?php                           if(!empty($_GET['search_tab'])):?>
<?php                               switch($_GET['search_tab']):?>
<?php                                   case "room":?>
<?php                                   case "tag":?>
<?php                                       $i = 1;?>
<?php                                       foreach($searchRoomSortArray as $a):?>
<?php                                           if($i == $sortIndex):?>
                                                    <option value="<?=$i?>" selected><?=h($a)?></option>
<?php                                           else:?>
                                                    <option value="<?=$i?>"><?=h($a)?></option>
<?php                                           endif?>
<?php                                       $i++; ?>
<?php                                       endforeach; ?>
<?php                                   break; ?>
<?php                                   case "restaurant": ?>
                                            <option>なし</option>
<?php                                   break; ?>
<?php                               endswitch ?>
<?php                           endif?>
                            </select>
<?php                   endif?>
                        </div>
                        <!--部屋-->
<?php                   foreach((array)$results as $result):?>
<?php                       if(!empty($_GET['search_tab'])):?>
<?php                           switch($_GET['search_tab']):?>
<?php                              case "room":?>
<?php                              case "tag":?>
<?php                                   $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($result->restaurantId); ?>
<?php                                   if(empty($restaurant)) continue; ?>
                                        <div class="list_wrap">
                                            <div class="list_img_wrap">
                                                <div class="list_img_shadow"></div>
<?php                                           if(is_string($restaurant->image->shop_image1)):?>
                                                    <div class="list_img_inner"><img class="list_img" src="<?=h($restaurant->image->shop_image1)?>" alt=""></div>
<?php                                           else:?>
                                                    <div class="list_img_inner"><img class="list_img" src="img/no_image.png" alt=""></div>
<?php                                           endif;?>
                                            </div>
                                            <div class="list_content">
                                                <p class="list_title"><a href="room_info.php?room_id=<?=h($result->roomId)?>"><?=h($result->roomName)?></a></p>
                                                <p class="list_member">人数：<?=h(getMember($result->roomUserStatusList))?>/<?=h($result->maxMember)?>人</p>
                                                <p class="list_dead_line">開催日時:<?=h(getDateToJpDate($result->deadLine))?></p>
                                                <p class="list_budget">予算:<?=h($result->budget)?> <?php if($result->budget != 'なし'){ echo "円";}?></p>
                                            </div>
                                            <div class="list_tag_wrap">
<?php                               foreach((array)$result->roomTagList as $roomTag):?>
                                        <div class="tag_inner list_tag tag">
                                            <div class="tag_name"><p><a href="search.php?search_tab=tag&amp;search_text=<?=h($roomTag->tagName)?>"><?=h($roomTag->tagName)?></a></p></div>
                                        </div>
<?php                               endforeach?>
                                    </div>
                                </div>
<?php                           break; ?>
<?php                           case "restaurant": ?>
<?php                               if(empty($result)) continue; ?>
                                <div class="list_wrap">
                                            <div class="list_img_wrap">
                                                <div class="list_img_shadow"></div>
<?php                                           if(is_string($result->image->shop_image1)):?>
                                                    <div class="list_img_inner"><img class="list_img" src="<?=h($result->image->shop_image1)?>" alt=""></div>
<?php                                           else:?>
                                                    <div class="list_img_inner"><img class="list_img" src="img/no_image.png" alt=""></div>
<?php                                           endif;?>
                                            </div>
                                    <div class="list_content">
                                        <p class="list_title"><a href="restaurant.php?restaurant_id=<?=h($result->restaurantId)?>"><?=h($result->name)?></a></p>
                                        <p class="list_member">部屋数：<?=$roomMng->getRoomCountByRestaurantId($result->restaurantId)?></p>
                                            <p class="list_budget">平均予算:<?=h($result->budget)?> <?php if($result->budget != 'なし'){ echo "円";}?></p>
                                        <p class="list_restaurant_explian">店舗PR：<?=$result->prShort?></p>
                                    </div>
                                    <div class="list_tag_wrap">
<?php                               foreach($result->categoryName as $tag):?>
                                        <div class="tag_inner list_tag tag">
                                            <div class="tag_name"><p><a href="search.php?search_tab=tag&amp;search_text=<?=h($tag)?>"><?=h($tag)?></a></p></div>
                                        </div>
<?php                               endforeach?>
                                    </div>
                                </div>
<?php                           break; ?>
<?php                           endswitch ?>
<?php                       endif?>
<?php                   endforeach?>
                        <!--部屋　ここまで-->
<?php                   if(empty($results)):?>
                            <p class="message" style="margin-top:10px">ヒットしませんでした</p>
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