<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
require_once '../php/SearchAPIManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$searchAPIMng = new SearchAPIManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
if(empty($_GET["room_id"])){
    header('Location:index.php');
    exit;
}
$room = $roomMng->getRoomByRoomId($_GET["room_id"]);
if(empty($room)){
    header('Location:index.php');
    exit;
}
$restaurant = $searchAPIMng->searchRestaurantByRestaurantId($room->restaurantId);
$createUser = $userMng->getUserByUserId($room->userId);

if($room->userId != $_SESSION["user_id"]){
    header('Location:room_info.php?room_id='.$_GET["room_id"]);
    exit;
}
if(!empty($_POST["update"])){
    $_room = new Room();
    $_room->roomName = (string)filter_input(INPUT_POST,"title");
    $_room->userId = $_SESSION["user_id"];
    $_room->explain = (string)filter_input(INPUT_POST,"explain");
    $_room->maxMember = $room->maxMember;
    $_room->restaurantId = (string)filter_input(INPUT_GET,"restaurant_id");
    $_room->deadLine = $room->deadLine;
    $_room->autoApply = $room->$autoApply;
    $_room->budget = $room->budget;
    $_room->address = $room->address;
    $_room->roomTagList = filter_input(INPUT_POST,"tag");
    $message = $roomMng->checkRoomValue($_room);
    if(empty($message)){
        $roomMng->modifyRoom($_room);
        header('Location:room_info.php');
        exit;
    }else{
        $message = "<alert>".h($message)."</alert>";
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
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>部屋編集「<?=$room->roomName?>」 - meetalk</title>
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
        <link rel="stylesheet" href="css/room_register.css">
        <link rel="stylesheet" href="css/room_edit.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/explain_view.js"></script>
        <script src="js/onload.js"></script>
        <script src="js/date.js"></script>
        <script src="js/tag.js"></script>
        <script src="js/invited.js"></script>
        <script>addOnload(function(){<?=$message?>})</script>
    </head>
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
                    <form action="room_edit.php?room_id=<?=$_GET["room_id"]?>" method="POST">
                        <div class="main_center">
                        <p id="main_center_title">部屋編集</p>
                            <div id="room_title_input">
                                <p id="room_title">部屋名：</p>
                                <input type="text" name="title" placeholder="部屋名" value="<?=$room->roomName?>" required>
                            </div>
                            <div id="info_tag_wrap">
                                <div id="info_tag_inner">
<?php                           foreach($room->roomTagList as $roomTag):?>
                                    <div class="tag_inner tag">
                                        <input name="tag[]" type="hidden" value="<?=h($roomTag->tagName)?>">
                                        <div class="tag_name"><img class="tag_delete" src="img/tag_delete.png" alt=""><p><?=h($roomTag->tagName)?></p></div>
                                    </div>
<?php                           endforeach?>
                                </div>
                                <div id="add_tag_wrap"><input onkeypress="pushKey(event.keyCode)" id="tag_input" type="text" name="tag"><button type="button" onclick="addTag()" id="add_tag_btn">タグを追加</button></div>
                            </div>
 <?php                                  /*ここPHP*/ ?>
                            <div id="l_restaurant_img" style="background-image:url(<?=h($restaurant->image[0])?>)"></div>
                            <div id="item_info_content">
                                <p><span>店舗名：</span><?=h($restaurant->name)?></p>
                                <p><span>主催者：</span><?=h($createUser->userName)?></p>
                                <div id="dead_line_wrap">
                                    <p><span>開催日時：</span><?=h(getDateToJpDate($room->deadLine))?></p>
                                </div>
                                <div id="join"><p><span>参加可能人数：</span></p><?=h($room->maxMember)?>人</p></div>
                                <div id="budget"><p><span>予算：</span></p><?=$room->budget?>円</p></div>
                                <p><span>部屋の説明：</span></p>
                                <div id="item_info_content_explain">
                                    <textarea maxlength="3000" name="explian" id="explian" wrap="hard" placeholder="この部屋の紹介を書いてね(1000文字以内)"><?=h($room->explain)?></textarea>
                                    <!--<button id="item_info_content_view_btn_wrap"><div id="item_info_content_view_btn"></div></button>-->
                                </div>
                                <div id="item_info_btn_wrap"><button name="update" type="button" id="item_info_btn" onclick="submit()" value="保存">保存</button></div>
                            </div>
                        </div>
                    </form>
                    <div class="ad ad_right">
                        <?php include '../component/ad.php' ?>
                    </div>
                </div>
            </div>
            <?php include '../component/footer.php' ?>
        </div>
    </body>
</html>