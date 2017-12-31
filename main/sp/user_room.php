<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/User.php';
require_once '../../php/RoomManager.php';
require_once '../../php/SearchAPIManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$searchAPIMng = new SearchAPIManager();
$message = null;
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$joinRoomList = $roomMng->getJoinRoomByUserId($_SESSION["user_id"]);
$user = $user = $userMng->getUserByUserId($_SESSION["user_id"]);
function getMember($_roomUserStatusList){
    $a = 1;
    foreach ($_roomUserStatusList as $roomUserStatus) {
        if($roomUserStatus->status == 2) $a++;
    }
    return $a;
}
function modifyDateToJpDate($_deadLine){
    return date('Y年n月j日', strtotime($_deadLine));
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>部屋一覧</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/user-room.css">
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
            <h2><?=h($user->userName)?>さんの部屋</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <div class="title">
                <p><?=h($user->userName)?>さんの部屋</p>
            </div>

            <ul class="tab">
                <li class="select">参加中</li>
                <li>申請中</li>
                <li>招待<!--<img src="./img/notification.png" class="notification"><p class="notification-num">1</p>--></li>
            </ul>
            <ul class="content">
                <!--部屋一覧-参加中-->
                <li>
                    <?php foreach((array)$joinRoomList as $joinRoom):?>
                    <?php if($roomMng->checkDeadLine($joinRoom->deadLine) == 0 || $roomMng->checkDeadLine($joinRoom->deadLine) == 1):?>
                    <?php $userStatus = $roomMng->getUserStatusByRoomId($joinRoom->roomId, $_SESSION['user_id']) ?>
                    <?php if($userStatus->status == 2 || $joinRoom->userId == $_SESSION['user_id']):?>
                    <?php $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($joinRoom->restaurantId) ?>
                    
                    <div class="wrap-result-card">
                        <a href="./room_info.php?room_id=<?=h($joinRoom->roomId)?>">                        
                        <div class="title-result-card">
                            <p><?=h($joinRoom->roomName)?></p>
                        </div>
                        <div class="inner-result-card">
                            <?php if(is_string($restaurant->image->shop_image1)):?>
                                <div id="thumbnail" style="background-image:url(<?=h($restaurant->image->shop_image1) ?>)"></div>   
                            <?php else: ?>
                                <div id="thumbnail" style="background-image:url(./img/no_image.png"></div>
                            <?php endif ?>
                            <div class="result-card-content">
                                <p>人数:<?=h(getMember($joinRoom->roomUserStatusList))?>人/<?=h($joinRoom->maxMember)?>人</p>
                                <p>開催日:<?=h(modifyDateToJpDate($joinRoom->deadLine))?></p>
                                <p>平均予算:<?=h($joinRoom->budget)?>円</p>
                            </div>
                        </div>
                        <?php foreach($joinRoom->roomTagList as $tag): ?>
                        <!--タグリストの名前(name)をGETに指定してhrefに出力しましょう-->
                        <div class="tag_inner general">
                            <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($tag->tagName)?>&prefecture=<?=h($user->prefecture)?>&page=0"><p id="popurarity-room-tag"><?=h($tag->tagName) ?></p></a></div>
                        </div>
                       
                        <?php endforeach; ?>
                        </a>
                    </div>
                    <?php endif?>
                    <?php endif?>
                    <?php endforeach?>
                </li>

                <!--部屋一覧-申請中-->
                <li class="hide">
                    <?php foreach((array)$joinRoomList as $joinRoom):?>
                    <?php $userStatus = $roomMng->getUserStatusByRoomId($joinRoom->roomId, $_SESSION['user_id']) ?>
                    <?php if($userStatus->status == 1):?>
                    <?php $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($joinRoom->restaurantId) ?>
                    
                    <div class="wrap-result-card">
                        <a href="./room_info.php?room_id=<?=h($joinRoom->roomId)?>">                        
                        <div class="title-result-card">
                            <p><?=h($joinRoom->roomName)?></p>
                        </div>
                        <div class="inner-result-card">
                            <?php if(is_string($restaurant->image->shop_image1)):?>
                                <div id="thumbnail" style="background-image:url(<?=h($restaurant->image->shop_image1) ?>)"></div>   
                            <?php else: ?>
                                <div id="thumbnail" style="background-image:url(./img/no_image.png"></div>
                            <?php endif ?>
                            <div class="result-card-content">
                                <p>人数:<?=h(getMember($joinRoom->roomUserStatusList))?>人/<?=h($joinRoom->maxMember)?>人</p>
                                <p>開催日:<?=h(modifyDateToJpDate($joinRoom->deadLine))?></p>
                                <p>平均予算:<?=h($joinRoom->budget)?>円</p>
                            </div>
                        </div>
                        <?php foreach($joinRoom->roomTagList as $tag): ?>
                        <!--タグリストの名前(name)をGETに指定してhrefに出力しましょう-->
                        <div class="tag_inner general">
                            <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($tag->tagName)?>&prefecture=<?=h($user->prefecture)?>"><p id="popurarity-room-tag"><?=h($tag->tagName) ?></p></a></div>
                        </div>
                       
                        <?php endforeach; ?>
                        </a>
                    </div>
                    <?php endif?>
                    <?php endforeach?>
                </li>

                <!--部屋一覧-被招待-->
                <li class="hide">
                    <?php foreach((array)$joinRoomList as $joinRoom):?>
                    <?php $userStatus = $roomMng->getUserStatusByRoomId($joinRoom->roomId, $_SESSION['user_id']) ?>
                    <?php if($userStatus->status == 3):?>
                    <?php $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($joinRoom->restaurantId) ?>
                    
                    <div class="wrap-result-card">
                        <a href="./room_info.php?room_id=<?=h($joinRoom->roomId)?>">
                        <div class="title-result-card">
                            <p><?=h($joinRoom->roomName)?></p>
                        </div>
                        <div class="inner-result-card">
                            <?php if(is_string($restaurant->image->shop_image1)):?>
                                <div id="thumbnail" style="background-image:url(<?=h($restaurant->image->shop_image1) ?>)"></div>   
                            <?php else: ?>
                                <div id="thumbnail" style="background-image:url(./img/no_image.png"></div>
                            <?php endif ?>
                            <div class="result-card-content">
                                <p>人数:<?=h(getMember($joinRoom->roomUserStatusList))?>人/<?=h($joinRoom->maxMember)?>人</p>
                                <p>開催日:<?=h(modifyDateToJpDate($joinRoom->deadLine))?></p>
                                <p>平均予算:<?=h($joinRoom->budget)?>円</p>
                            </div>
                        </div>
                        <?php foreach($joinRoom->roomTagList as $tag): ?>
                        <!--タグリストの名前(name)をGETに指定してhrefに出力しましょう-->
                        <div class="tag_inner general">
                            <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($tag->tagName)?>&prefecture=<?=h($user->prefecture)?>"><p id="popurarity-room-tag"><?=h($tag->tagName) ?></p></a></div>
                        </div>
                       
                        <?php endforeach; ?>
                        </a>
                    </div>
                    <?php endif?>
                    <?php endforeach?>
                </li>
            </ul>
        </div>
        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
    </body>
</div>