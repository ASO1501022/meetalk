<!--編集ボタンが足りません！！！！！！！！！！！！！-->
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
$roomId = filter_input(INPUT_GET, 'room_id');
if(empty($roomId)){
    header('Location:index.php');
    exit;
}
if(!empty($_POST['enter'])){
    if($_POST['enter'] == $_SESSION['user_id']){
        $roomMng->joinRoomByUserId($_SESSION['user_id'], $roomId);
    }
}
if(!empty($_POST['escape'])){
    if($_POST['escape'] == $_SESSION['user_id']){
        $roomMng->escapeUserFromRoom($_SESSION['user_id'], $roomId);
    }
}
if(!empty($_POST['dissolve'])){
    if($_POST['dissolve'] == $_SESSION['user_id']){
        $roomMng->dissolveRoom($roomId);
    }
}
$userStatus = $roomMng->getUserStatusByRoomId($roomId, $_SESSION['user_id']);
$room = null;
$restaurant = null;
$user = null;
if(!is_null($roomId)) {
    $room = $roomMng->getRoomByRoomId($roomId);
    $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($room->restaurantId);
    $user = $userMng->getUserByUserId($room->userId);
}
$curMember = getMember($room->roomUserStatusList);
$deadLine =  modifyDateToJpDate($room->deadLine);

// 出力の際に必ずこの関数を通して出力する
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
function getMember($_roomUserStatusList){
    $a = 1;
    foreach ($_roomUserStatusList as $roomUserStatus) {
        if($roomUserStatus->status == 2) $a++;
    }
    return $a;
}
function modifyDateToJpDate($_deadLine){
    return date('Y年n月j日G時i分', strtotime($_deadLine));
}
?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>部屋情報</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/room-info.css">
    <link rel="stylesheet" href="./css/tag.css">
    <link rel="stylesheet" href="./css/flickity.css">
    <link rel="stylesheet" href="./css/ad.css">

    <script src="./js/flickity.pkgd.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="./js/ad-footer.js"></script>

</head>


<header>
    <?php require_once "./global.html"; ?>
</header>


<div id="main">
    <body>
<!--GlobalMenuBar-->
        <div id="header">
            <h2>部屋情報</h2>
            <!--もし参加していたら　していなかったら表示しない-->
<?php if($userStatus->status==2 && $roomMng->checkDeadLine($room->deadLine) == 0): ?> 
            <span class="button menu-button-left"><form action="room_info.php?room_id=<?=h($room->roomId)?>" method="post"><button  type="submit"name="escape" value="<?=h($_SESSION['user_id'])?>">退室</button></form></span>
<?php endif ?>
            <span class="button menu-button-right"></span>
        </div>
<!--この部屋に対するユーザーの状態 phpでifで分ける-->
        <!--入室拒否-->
<?php if($roomMng->checkDeadLine($room->deadLine) == 1): ?>
        <div class="user-status-reject">
            <p>締め切られています</p>
        </div>
<?php elseif($roomMng->checkDeadLine($room->deadLine) == 2): ?>
        <div class="user-status-reject">
            <p>終了した部屋です</p>
        </div>
<?php elseif($roomMng->checkDeadLine($room->deadLine) == 3): ?>
        <div class="user-status-reject">
            <p>解散した部屋です</p>
        </div>
<?php elseif(is_null($userStatus->status)):?>
<?php     if($curMember == $room->maxMember): ?>
        <div class="user-status-reject">
            <p>制限人数に達しています</p>
        </div>
<?php endif?>
<?php elseif($userStatus->status==0): ?>
        <div class="user-status-reject">
            <p>キックされています</p>
        </div>
<?php elseif($userStatus->status==1): ?>
        <!--入室待ち-->
        <div class="user-status-wait">
            <p>承認待ちの状態です</p>
        </div>
<?php elseif($userStatus->status==3): ?>
        <!--招待されている-->
        <div class="user-status-wait">
            <p>招待されています</p>
        </div>
<?php elseif($curMember == $room->maxMember): ?>
        <div class="user-status-reject">
            <p>制限人数に達しています</p>
        </div>
<?php endif?>
<!--メインコンテンツ-->
        <div class="title-room-info">
            <p><?=h($room->roomName)?></p>
        </div>
<!--部屋につけられているタグ-->
        <div class="wrap-contents">
            <div class="room-tag">
<?php foreach($room->roomTagList as $tag): ?>
                <div class="tag_inner general">
                    <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($tag->tagName)?>&prefecture=<?=h($user->prefecture)?>&page=0" style="text-decoration:none; color: white;"><p><?=h($tag->tagName)?></a></div>
                </div>
<?php endforeach ?>
            </div>

<!--部屋の情報一覧-->
            <div class="contents-room-info">
<!--部屋の写真リスト-->
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

<!--ボタン関係　phpでどれを出すか制御-->
<?php if($roomMng->checkDeadLine($room->deadLine) == 0 || $roomMng->checkDeadLine($room->deadLine) == 1):?>
<?php if($curMember >= $room->maxMember):?>
                <div class="room-button">
                    <button type="submit" name="" class="gray-button" disabled>入室</button>
                    <button type="submit" name="member_list" class="orange-button" onclick="location.href='./room_member.php?room_id=<?=h($room->roomId)?>'">メンバ一覧</button>
                </div>
<?php elseif($userStatus->status == null && $room->userId == $_SESSION['user_id']):?>
                <!--入室済み&&管理者-->
                <form action="./room_info.php?room_id=<?=h($room->roomId)?>" method="post">
                    <div class="room-button">
                        <button type="button" name="room_chat" class="blue-button" onclick="location.href='./room_chat.php?room_id=<?=h($room->roomId)?>'">チャット</button>
                        <button type="button" name="member_list" class="orange-button" onclick="location.href='./room_member.php?room_id=<?=h($room->roomId)?>'">メンバ一覧</button>
                    </div>
                    <div class="room-button">
                        <button type="button" name="invite_friend" class="green-button" onclick="location.href='./invite_friend.php?room_id=<?=h($room->roomId)?>'">フレンドを招待</button>
                        <button type="submit" name="dissolve" value="<?=h($_SESSION['user_id'])?>" class="red-button" >部屋の解散</button>
                    </div>
                </form>
<?php elseif($userStatus->status == null || $userStatus->status == 3): ?>
                <!--未入室-->
                <form action="./room_info.php?room_id=<?=h($roomId)?>" method="post">
                    <div class="room-button">
                        <button type="submit" name="enter" value="<?=h($_SESSION['user_id'])?>" class="green-button">入室</button>
                        <button type="button" name="member_list" class="gray-button" onclick="location.href='./room_member.php?room_id=<?=h($room->roomId)?>'" disabled>メンバ一覧</button>
                    </div>
                </form>
<?php elseif($userStatus->status == 2): ?>
                <!--入室済み-->
                <form action="./room_info.php?room_id=<?=h($room->roomId)?>" method="post">
                    <div class="room-button">
                        <button type="button" name="room_chat" class="blue-button" onclick="location.href='./room_chat.php?room_id=<?=h($room->roomId)?>'">チャット</button>
                        <button type="button" name="member_list" class="orange-button" onclick="location.href='./room_member.php?room_id=<?=h($room->roomId)?>'">メンバ一覧</button>
                    </div>
                </form>
<?php elseif($userStatus->status == 0): ?>
                <!--入室拒否-->
                <form action="./room_info.php" method="post">
                    <div class="room-button">
                        <button type="submit" name="" class="gray-button" disabled>入室</button>
                        <button type="submit" name="" class="gray-button" disabled>メンバ一覧</button>
                    </div>
                </form>
<?php elseif($userStatus->status == 1): ?>
                <!--申請中-->
                <div class="room-button">
                    <button type="submit" name="" class="gray-button" disabled>入室</button>
                    <button type="submit" name="member_list" class="orange-button" onclick="location.href='./room_member.php?room_id=<?=h($room->roomId)?>'">メンバ一覧</button>
                </div>
<?php endif ?>
<?php endif?>
<?php
if($room->autoApply == 0){
    $autoApply = "する";
}else {
    $autoApply = "しない";
}
?>
<!--部屋の情報（文字）-->
                <div class="info-element">
                    <p>主催者</p>
                    <p><?=h($user->userName)?></p>
                </div>
                <div class="info-element">
                    <p>店舗名</p>
                    <p><?=h($restaurant->name)?></p>
                </div>
                <div class="info-element">
                    <p>自動承認</p>
                    <p><?=$autoApply?></p>
                </div>
                <div class="info-element">
                    <p>人数</p>
                    <p><?=h($curMember) ?>人/<?=h($room->maxMember)?>人</p>
                </div>
                <div class="info-element">
                    <p>開催日時</p>
                    <p><?=h($deadLine)?></p>
                </div>
                <div class="info-element">
                    <p>予算</p>
                    <p><?=h($room->budget)?>円</p>
                </div>
                <div class="info-element">
                    <p>部屋紹介</p>
                    <p><?=h($room->explain)?></p>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        <div id="ad-footer">
    <?php include '../../component/sp/ad.php'; ?>
</div>
    </body>
</div>