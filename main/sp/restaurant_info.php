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


if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
if(empty($_GET["restaurant_id"])){
    header('Location:index.php');
    exit;
}
$user = $userMng->getUserByUserId($_SESSION['user_id']);
$restaurant = $searchAPIMng->searchRestaurantByRestaurantId($_GET["restaurant_id"]);
if(empty($restaurant)){
    header('Location:index.php');
    exit;
}
//お気に入りの操作
if(!empty($_POST["favorite_delete"])){
    $userMng->deleteFavoriteByRestaurantId($_SESSION["user_id"],$_GET["restaurant_id"]);
}else if(!empty($_POST["favorite_add"])){
    $userMng->addFavorite($_SESSION["user_id"],$_GET["restaurant_id"]);
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}

?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>店舗情報</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/restaurantInfo.css">
    <link rel="stylesheet" href="./css/tag.css">
    <link rel="stylesheet" href="./css/flickity.css">
    <link rel="stylesheet" href="./css/ad.css">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="./js/flickity.pkgd.min.js"></script>
    <script src="./js/ad-footer.js"></script>
    
</head>


<header>
    <?php require_once "./global.html"; ?>
</header>

    

<div id="main">
    <body>
<!--GlobalMenuBar-->
        <div id="header">
            <h2>店舗情報</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="title-restaurant-info">
            <p><?=$restaurant->name?></p>
        </div>
<!--レストランにつけられているタグ-->
        <div class="wrap-contents">
<?php foreach($restaurant->categoryName as $tag): ?>
            <!--タグリストの名前(name)をGETに指定してhrefに出力しましょう-->
            <div class="tag_inner general">
                <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($tag)?>&prefecture=<?=h($user->prefecture)?>&page=0"><p><?=h($tag)?></p></a></div>
            </div>
<?php endforeach; ?>
            </div>

<!--レストランの情報一覧-->
            <div class="contents-restaurant-info">
                
<!--レストランの写真リスト-->
                <div id="flickity" class="js-flickity" data-flickity-options='{ "cellAlign": "left", "contain": false }'>
<?php if(is_string($restaurant->image->shop_image1)):?>
                    <div class="gallery-cell"><div class="slide-image"><img src="<?=h($restaurant->image->shop_image1)?>" alt=""></div></div>
<?php     if(is_string($restaurant->image->shop_image2)):?>
                    <div class="gallery-cell"><div class="slide-image"><img src="<?=h($restaurant->image->shop_image2)?>" alt="" ></div></div>
<?php     endif ?>
<?php else: ?>
                    <div class="gallery-cell"><div class="slide-image"><img src="./img/no_image.png" alt="" ></div></div>
<?php endif ?>
                </div>
                <div class="favorite-button">
                    <form action="./restaurant_info.php?restaurant_id=<?=h($restaurant->restaurantId)?>" method="post">
                    <?php if($userMng->checkFavoriteRestaurant($_GET["restaurant_id"],$_SESSION["user_id"])):?>
                        <button type="submit" name="favorite_delete" value="<?=$_SESSION['user_id']?>" class="favorite-delete">お気に入りを解除する</button>
                    <?php else:?>
                        <button type="submit" name="favorite_add" value="<?=$_SESSION['user_id']?>" class="favorite-register">お気に入り登録する</button>
                    <?php endif?>
                    </form>
                </div>
<!--この店で部屋作る？ボタン-->
                <div class="room-button">
                    <button type="button" class="green-button" onclick="location.href='./room_register.php?restaurant_id=<?=$restaurant->restaurantId?>'">部屋作成</button>
                    <button type="button" name="" class="orange-button" onclick="location.href='./search.php?search_genre=room&restaurant_id=<?=$restaurant->restaurantId?>'">部屋一覧</button>
                </div>
<!--レストランの情報（文字）-->
                <div class="info-element">
                    <p>住所</p>
                    <p><?=$restaurant->address?></p>
                </div>
                <div class="info-element">
                    <p>電話番号</p>
                    <p><?=$restaurant->tel?></p>
                </div>
                <div class="info-element">
                    <p>営業時間</p>
                    <p><?=$restaurant->openTime?></p>
                </div>
                <div class="info-element">
                    <p>休業日</p>
                    <p><?=$restaurant->holiday?></p>
                </div>
                <div class="info-element">
                    <p>平均予算</p>
                    <p><?=$restaurant->budget?></p>
                </div>
                <div class="info-element">
                    <p>店舗PR</p>
                    <p><?=$restaurant->prLong;?></p>
                </div>
                <p id="official-page"><a href="<?=$restaurant->pcUrl?>">店舗のHPへ</a></p>
            </div>
        </div>

        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
    </body>
</div>
