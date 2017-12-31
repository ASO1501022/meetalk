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
if(!empty($roomId)){
    $room = $roomMng->getRoomByRoomId($roomId);
}
if(!empty($_POST['save'])){
    $room->roomName = $_POST['room_name'];
    $room->roomTagList = $_POST['tag'];
    $room->explain = $_POST['explain'];
    $error = $roomMng->checkRoomValue($room);
    if(empty($error)){
        $roomMng->modifyRoom($room);
    }
}



$room = null;
$restaurant = null;
$user = null;
if(!is_null($roomId)) {
    $room = $roomMng->getRoomByRoomId($roomId);
    $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($room->restaurantId);
    $user = $userMng->getUserByUserId($room->userId);
}
$curMember =  getMember($room->roomUserStatusList);
$deadLine =  modifyDateToJpDate($room->deadLine);

// 出力の際に必ずこの関数を通して出力する
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
function getMember($roomUserStatusList){
    $a = 1;
    foreach ($roomUserStatusList as $roomUserStatus) {
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

    <title>部屋作成</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/tag.css">
    <link rel="stylesheet" href="./css/room-edit.css">
    <link rel="stylesheet" href="./css/flickity.css">
    <link rel="stylesheet" href="./css/ad.css">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <script src="./js/flickity.pkgd.min.js"></script>
    <script src="./js/tag.js"></script>
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
<?php if(!empty($error)): ?>
<?php
echo <<<EOM
<script type="text/javascript">
    alert( <?php echo $error ?> )
</script>
EOM;
?>
<?php endif ?>
<!--GlobalMenuBar-->
        <div id="header">
            <h2>部屋編集</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <form action="room_edit.php" method="post">
                <div class="title-create-room">
                    <p>部屋名</p>
                    <input type="text" name="room_name" placeholder="<?=h($room->roomName)?>">
                </div>
<!--タグの追加-->
                <div class="add-tag">
                    <input type="text" name="" placeholder="タグの追加" id="tag_input">
                    <button type="button" name="" onclick="addTag()">追加</button>
                </div>
                <div id="wrap-tag-edit">
<?php foreach($room->roomTagList as $tag):?>
                    <div class="tag_inner tag">
                        <input name="tag[]" type="hidden" value="<?=h($tag->tagName)?>">
                        <div class="tag_name"><img class="tag_delete" src="img/tag_delete.png" alt=""><p><?=h($tag->tagName)?></p></div>
                    </div>
<?php endforeach?>
                </div>
<!--レストランの写真リスト-->
                <div id="flickity" class="js-flickity" data-flickity-options='{ "cellAlign": "left", "contain": false }'>
                    <div class="gallery-cell"><div class="slide-image"><img src="./img/example.jpg" alt=""></div></div>
                    <div class="gallery-cell"><div class="slide-image"><img src="./img/test.jpg" alt="" ></div></div>
                </div>
                
                <!--レストランの情報（文字）-->
                <div class="info-element">
                    <p>主催者</p>
                    <p><?=h($user->userName)?></p>
                </div>
                <div class="info-element">
                    <p>店舗名</p>
                    <p><?=h($restaurant->name)?></p>
                </div>
                <div class="info-element">
                    <p>人数</p>
                    <p><?=h($curMember) ?>人/<?=h($room->maxMember)?>人</p>
                </div>
                <div class="info-element">
                    <p>開催日時</p>
                    <p><?h($deadLine)?></p>
                </div>
                <div class="info-element">
                    <p>予算</p>
                    <p><?=h($room->budget)?>円</p>
                </div>
                <div class="info-element">
                    <p>部屋紹介</p>
                    <textarea name="explain" placeholder="部屋の説明"></textarea>
                </div>


                <input type="submit" name="save" value="保存" class="save-button">
            </form>
        </div>

        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
    </body>
</div>
