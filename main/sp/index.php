<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/RoomManager.php';
require_once '../../php/DBManager.php';
require_once '../../php/SearchRoomManager.php';
require_once '../../php/SearchAPIManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchRoomMng = new SearchRoomManager();
$searchAPIMng = new SearchAPIManager();
$trendTags = $searchRoomMng->searchTrendTag();

if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$user = $userMng->getUserByUserId($_SESSION['user_id']);
$trendRestaurantList = $searchRoomMng->searchFewTrendRestaurant();
$trendRestaurant = $trendRestaurantList[rand(0,2)];
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>TOPページ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/top.css">
    <link rel="stylesheet" href="./css/tag.css">
    <link rel="stylesheet" href="./css/ad.css">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <script src="./js/location.js"></script>
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
        <?php include './adblock.html'?>
<!--GlobalMenuBar-->
        <div id="header">
            <h2>TOP</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
<!--検索ボックス-->
        <div class="wrap-search-form">
            <form action="search.php?" method="get">
                <div class="inner-search-form">
                    <div class="select-form">
                        <select name="search_genre"><option value="restaurant">店舗</option><option value="room">部屋</option><option value="tag">タグ</option></select>
                    </div>
                    <input type="search" name="search_word">
                    <input type="hidden" name="page" value="0">
                    <input type="hidden" name="prefecture" value="<?=h($user->prefecture)?>">
                    <button type="submit" name="search" id="button-image" value="search"><img border="0" src="./img/search.png" width="25px" height="25px" alt="イラスト1"></button>
                </div>
            </form>
        </div>
<!--流行のタグ-->
            <div id="tag_wrap">
<?php foreach($trendTags as $trendTag): 
    if($trendTag->tagNumber >= 20 ): ?>
                <div class="tag_inner hot">
                    <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($trendTag->tagName)?>&prefecture=<?=h($user->prefecture)?>&page=0"><p><?=h($trendTag->tagName)?></p></a></div>
                    <div class="tag_num"><p><?=h($trendTag->tagNumber)?></p></div>
                </div>
<?php else: ?>
                <div class="tag_inner normal">
                    <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($trendTag->tagName)?>&prefecture=<?=h($user->prefecture)?>&page=0"><p><?=h($trendTag->tagName)?></p></a></div>
                    <div class="tag_num"><p><?=h($trendTag->tagNumber)?></p></div>
                </div>
<?php endif ?>
<?php endforeach ?>
            </div>

<!--タイルメニュー-->
            <div class="top-menu">
                <ul>
                    <div class="big-menu">
                        <li id="big-menu-popularity" class="bg-image" style="background-image: url(<?=$trendRestaurant->image->shop_image1?>);">
                            <a href="./restaurant_info.php?restaurant_id=<?=h($trendRestaurant->restaurantId)?>">
                            <p id="popurality-title">人気のレストラン<br><?=$trendRestaurant->name?></p>
                            <div class="tag_inner general">
                                <div class="tag_name">
                                    <p id="popurarity-room-tag">部屋数：<?=$trendRestaurant->roomNumber?></p>
                                </div>
                            </div>
                           
                        </li>
                        <li id="big-menu-content">
                            
                            <div id="room-img"></div>
                            <a href="./user_room.php" id="room-link"></a>
                            <p>あなたの部屋</p>
                            
                        </li>
                    </div>
                    <div class="small-menu1">
                        <li id="small-menu1-content" class="bkg-brown get-location">
                            <div id="current-position-img"></div>
                            <p>現在地から</p>
                        </li>
                        <li id="small-menu1-content" class="bkg-yellow">
                            <a href="./search.php?search_genre=room&prefecture=<?=$user->prefecture?>&search_word=" class="link"></a>
                            <div id="map-img"></div>
                            <p>地域から</p>
                        </li>
                        <li id="small-menu1-content" class="bkg-brown">
                            <a href="./profile.php" class="link"></a>
                            <div id="mypage-img"></div>
                            <p>マイページ</p>
                        </li>
                    </div>
                    <div class="small-menu2">
                        <li id="small-menu2-content" class="bkg-yellow">
                            <a href="friend.php" class="link"></a>
                            <div id="friend-img"></div>
                            <p>フレンド</p>
                        </li>
                        <li id="small-menu2-content" class="bkg-brown">
                            <a href="favorite.php" class="link"></a>
                            <div id="favorite-img"></div>
                            <p>お気に入り</p>
                        </li>
                        <li id="small-menu2-content" class="bkg-yellow">
                            <a href="history.php" class="link"></a>
                            <div id="history-img"></div>
                            <p>履歴</p>
                        </li>
                    </div>
                </ul>


            </div>



        </div>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
    </body>
</div>

</html>