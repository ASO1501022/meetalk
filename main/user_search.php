<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
require_once '../php/DBManager.php';
require_once '../php/SearchAPIManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchAPIMng = new SearchAPIManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$searchText = (string)filter_input(INPUT_GET,"search_text");
$users = null;
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
if(!empty($searchText)){
    $users = $dbMng->getUserByString($searchText,$_GET["page"] - 1);
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
        <title>「<?=h($searchText)?>」のユーザ検索結果 - meetalk</title>
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
        <link rel="stylesheet" href="css/user_search.css">
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
                        <p id="main_center_title">ユーザ検索</p>
                        <form action="user_search.php" method="GET">
                            <div class="search_box_wrap"><input type="search" name="search_text" placeholder="ユーザIDを検索"><button type="submit" class="search_btn"><img src="img/search.png" alt=""></button></div>
                            <div class="index_contents">
<?php                           foreach((array)$users as $user):?>
                                <div class="member_wrap">
                                    <div class="member_img_wrap"><img src="img/user_img/<?=h($user->imageName)?>" alt=""></div>
                                    <div class="member_name_wrap"><p><a href="profile.php?user_id=<?=h($user->userId)?>"><?=h($user->userName)?></a></p><p>ID:<?=h($user->userId)?></p></div>
                                    <div class="member_btn_wrap"><a href="profile.php?user_id=<?=h($user->userId)?>" class="button detail">詳細</a></div>
                                </div>
<?php                           endforeach?>
                            </div>
                        </form>
<?php                   if(!empty($searchText)):?>
<?php                       if(empty($users)):?>
                            <p class="message" style="margin-top:10px">ヒットしませんでした</p>
<?php                       endif?>
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