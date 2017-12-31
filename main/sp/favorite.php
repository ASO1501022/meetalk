<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/RoomManager.php';
require_once '../../php/DBManager.php';
require_once '../../php/SearchAPIManager.php';
require_once '../../php/SearchRoomManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchAPIMng = new SearchAPIManager();
$searchRoomMng = new SearchRoomManager();

if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
if(!empty($_GET['page'])){
    if($_GET['page'] >= 0) {
        $prevPage = $_GET['page'] - 1;
        $curPage = $_GET['page'];
        $nextPage = $_GET['page'] + 1;
    } else {
        $prevPage = 0;
        $curPage = 0;
        $nextPage = 1;
    }
}else {
    $prevPage = 0;
    $curPage = 0;
    $nextPage = 1;
}
$user = $userMng->getUserByUserId($_SESSION['user_id']);
$results = $userMng->getFavoriteListByUserId($_SESSION["user_id"],$curPage);
function getMember($_roomUserStatusList){
    $a = 1;
    foreach ($_roomUserStatusList as $roomUserStatus) {
        if($roomUserStatus->status == 2) $a++;
    }
    return $a;
}
//ページの文列字変換
function getUnsetPageQueryString(){
    $a = "";
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
function modifyDateToJpDate($_deadLine){
    return date('Y年n月j日G時i分', strtotime($_deadLine));
}

// 出力の際に必ずこの関数を通して出力する
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>おきに入り</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/favorite.css">
    <link rel="stylesheet" href="./css/tag.css">
    <link rel="stylesheet" href="./css/ad.css">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <script type="text/javascript" src="./js/tab.js"></script>
    <script src="./js/ad-footer.js"></script>
    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->
</head>


<header>
    <?php require_once "./global.html"; ?>
</header>

<div id="main">
    <body>
<!--GlobalMenuBar-->
        <div id="header">
            <h2>お気に入り</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <div class="title-search-result">
                <p>お気に入りの店舗</p>
            </div>
            
        <?php foreach((array)$results as $result): ?>
        <?php $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($result->restaurantId)?>
                <div class="wrap-result-card">
                    <!--この店舗のIDをGETに指定してhrefに出力しましょう-->
                    <a href="./restaurant_info.php?restaurant_id=<?=h($result->restaurantId)?>">
                        <div class="title-result-card">
                            <!--レストラン名です。-->
                            <p><?=h($restaurant->name)?></p>
                        </div>
                        <div class="inner-result-card">
                            <!--レスポンスのアドレスをurl()の中に入れてあげてください-->
<?php if(is_string($restaurant->image->shop_image1)):?>
                            <div id="thumbnail" style="background-image:url(<?=h($restaurant->image->shop_image1) ?>)"></div>
<?php else: ?>
                            <div id="thumbnail" style="background-image:url(./img/no_image.png"></div>
<?php endif ?>
                            <div class="result-card-content">
                                <!--部屋の細かい情報です。それぞれ一行ずつ出力しましょう-->
                                <p>電話番号:<?=h($restaurant->tel)?></p>
                                <p>休業日:<?=$restaurant->holiday?></p>
                                <p>平均予算:<?=h($restaurant->budget)?>円</p>
                            </div>
                        </div>
<!--タグリストをforeachで回して全てのタグを出力しましょう-->
<?php foreach($restaurant->categoryName as $tag): ?>
                        <!--タグリストの名前(name)をGETに指定してhrefに出力しましょう-->
             
                        <div class="tag_inner general">
                            <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($tag)?>&prefecture=<?=h($user->prefecture)?>"><p id="popurarity-room-tag"><?=h($tag)?></p></a></div>
                        </div>
<?php endforeach; ?>
                    </a>
                </div>
<?php endforeach; ?>

            <div class="wrap-page-number">
                <ul>
                    <li id='num'><a href="./favorite.php?page=<?=h($prevPage)?>">＜</a></li>
<?php
if($curPage == 0){
    $leftPage = 0;
}elseif($curPage == 1) {
    $leftPage = 0;
}elseif($curPage == 2){
    $leftPage = 0;
}else{
    $leftPage = $curPage -2;
}

?>
<?php for($i = $leftPage; $i < $leftPage + 5; $i++): ?>
<?php     if($i == $curPage):?>
                    <li id='num-selected'><a href="./favorite.php?page=<?=h($i)?>"><?=h($i+1)?></a></li>
<?php     else: ?>
                    <li id='num'><a href="./favorite.php?page=<?=h($i)?>"><?=h($i+1)?></a></li>
<?php     endif?>
<?php endfor ?>
                    <li id='num'><a href="./favorite.php?page=<?=h($nextPage)?>">＞</a></li>
                </ul>
            </div>
            
            <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
            <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        </div>
    
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
    </body>
</div>