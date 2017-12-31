<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
require_once '../php/Room.php';
require_once '../php/SearchAPIManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$searchAPIMng = new SearchAPIManager();
$message = null;
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
$user = $userMng->getUserByUserId($_SESSION["user_id"]);

if(!empty($_POST["create"])){
    $deadLine = "";
    $autoApply = (string)filter_input(INPUT_POST,"auto");
    if(empty($autoApply)){
        $autoApply = 1;
    }else{
        $autoApply = 0;
    }
    $deadLine .= (string)filter_input(INPUT_POST,"year")."-";
    $deadLine .= (string)filter_input(INPUT_POST,"month")."-";
    $deadLine .= (string)filter_input(INPUT_POST,"day")." ";
    $deadLine .= (string)filter_input(INPUT_POST,"hour").":";
    $deadLine .= (string)filter_input(INPUT_POST,"minutes").":00";
    $_room = new Room();
    $_room->roomName = (string)filter_input(INPUT_POST,"title");
    $_room->userId = $_SESSION["user_id"];
    $_room->explain = (string)filter_input(INPUT_POST,"explain");
    $_room->maxMember = (string)filter_input(INPUT_POST,"max_member");
    $_room->restaurantId = $_GET["restaurant_id"];
    $_room->deadLine = $deadLine;
    $_room->autoApply = $autoApply;
    $_room->budget = (string)filter_input(INPUT_POST,"budget");
    $_room->address = (string)filter_input(INPUT_POST,"address");
    $_room->roomTagList = !empty($_POST["tag"]) ? $_POST["tag"] : null;
    $message = $roomMng->checkRoomValue($_room);
    if(empty($message)){
        $roomId = $roomMng->registerRoom($_room);
        header('Location:room_info.php?room_id='.$roomId);
        exit;
    }else{
        $restaurant->categoryName = !empty($_POST["tag"]) ? $_POST["tag"] : null;
        $message = 'alert("'.h($message).'")';
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
        <title>部屋作成「<?=h($restaurant->name)?>」 - meetalk</title>
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
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/explain_view.js"></script>
        <script src="js/onload.js"></script>
        <script src="js/date.js"></script>
        <script src="js/tag.js"></script>
        <script src="js/invited.js"></script>
        <script>addOnload(function(){<?=$message?>})</script>
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
                    <form action="room_register.php?restaurant_id=<?=$_GET["restaurant_id"]?>" method="POST">
                        <div class="main_center">
                        <p id="main_center_title">部屋作成</p>
                            <div id="room_title_input">
                                <p id="room_title">部屋名：</p>
                                <input type="text" name="title" placeholder="部屋名" required <?php if(isset($_POST["title"])) echo 'value="'.$_POST["title"].'"'?>>
                            </div>
                            <div id="info_tag_wrap">
                                <div id="info_tag_inner">
<?php                           foreach((array)$restaurant->categoryName as $tagName):?>
                                    <div class="tag_inner tag">
                                        <input type="hidden" name="tag[]" value="<?=h($tagName)?>">
                                        <div class="tag_name"><img class="tag_delete" src="img/tag_delete.png" alt=""><p><?=h($tagName)?></p></div>
                                    </div>
<?php                           endforeach?>
                                </div>
                                <div id="add_tag_wrap"><input onkeypress="pushKey(event.keyCode)" id="tag_input" type="text"><button type="button" onclick="addTag()" id="add_tag_btn">タグを追加</button></div>
                            </div>
 <?php                                  /*ここPHP*/ ?>
<?php                   if(is_string($restaurant->image->shop_image1)):?>
                            <div id="l_restaurant_img" style="background-image:url(<?=h($restaurant->image->shop_image1)?>)"></div>
<?php                   else:?>
                            <div id="l_restaurant_img" style="background-image:url(img/no_image.png)"></div>
<?php                   endif;?>
                            <div id="item_info_content">
                                <p><span>店舗名：</span><?=$restaurant->name?></p>
                                <p><span>主催者：</span><?=$user->userName?></p>
                                <div id="dead_line_wrap">
                                    <p><span>開催日時：</span></p>
                                    <select name="year" id="year">
                                    <?php $year = (int)date('Y')?>
                                    <?php if(isset($_POST["year"])){ $year = $_POST["year"];}?>
                                    <?php for($i = $year;$i <= $year + 50;$i++):?>
                                        <?php if($i == $year):?>
                                            <option selected><?=$i?></option>
                                        <?php else:?>
                                            <option><?=$i?></option>
                                        <?php endif?>
                                    <?php endfor?>
                                    </select>
                                    <p><span>年</span></p>
                                    <select name="month" id="month">
                                    <?php $month = (int)date('n')?>
                                    <?php if(isset($_POST["month"])){ $month = $_POST["month"]; }?>
                                    <?php for($i = 1;$i <= 12;$i++):?>
                                        <?php if($i == $month):?>
                                            <option selected><?=$i?></option>
                                        <?php else:?>
                                            <option><?=$i?></option>
                                        <?php endif?>
                                    <?php endfor?>
                                    </select>
                                    <p><span>月</span></p>
                                    <select name="day" id="day">
                                    <?php $day = (int)date('t')?>
                                    <?php if(isset($_POST["day"])){ $day = $_POST["day"]; }?>
                                    <?php for($i = 1;$i <= date('t');$i++):?>
                                    <?php if($i == $day):?>
                                        <option selected><?=$i?></option>
                                    <?php else:?>
                                        <option><?=$i?></option>
                                    <?php endif?>
                                    <?php endfor?>
                                    </select>
                                    <p><span>日</span></p>
                                    <select name="hour" id="hour">
                                    <?php $hour = (int)date('G')?>
                                    <?php if(isset($_POST["hour"])){ $hour = $_POST["hour"]; }?>
                                    <?php for($i = 0;$i <= 23;$i++):?>
                                    <?php if($i == $hour):?>
                                        <option selected><?=$i?></option>
                                    <?php else:?>
                                        <option><?=$i?></option>
                                    <?php endif?>
                                    <?php endfor?>
                                    </select>
                                    <p><span>時</span></p>
                                    <select name="minutes" id="minutes">
                                    <?php $minutes = (int)date('i')?>
                                    <?php if(isset($_POST["minutes"])){ $minutes = $_POST["minutes"]; }?>
                                    <?php for($i = 0;$i <= 59;$i++):?>
                                        <?php if($i == $minutes):?>
                                            <option selected><?=$i?></option>
                                        <?php else:?>
                                            <option><?=$i?></option>
                                        <?php endif?>
                                    <?php endfor?>
                                    </select>
                                    <p><span>分</span></p>
                                </div>
                                <div id="join"><p><span>参加可能人数：</span></p><input min="0" type="number" name="max_member" required <?php if(isset($_POST["max_member"])) echo 'value="'.$_POST["max_member"].'"'?>><p id="join_unit">人</p></div>
                                <div id="budget"><p><span>予算：</span></p><input min="0" type="number" name="budget" required <?php if(isset($_POST["budget"])) echo 'value="'.$_POST["budget"].'"'?>><p id="budget_unit">円</p></div>
                                <div id="auto"><p><span>自動承認：</span></p><input type="checkbox" name="auto" value="auto"></div>
                                <p><span>部屋の説明：</span></p>
                                <div id="item_info_content_explain">
                                    <textarea name="explain" id="explian" wrap="hard" placeholder="この部屋の紹介(1000文字以内)" required><?php if(isset($_POST["explain"])){ echo $_POST["explain"];}?></textarea>
                                    <!--<button id="item_info_content_view_btn_wrap"><div id="item_info_content_view_btn"></div></button>-->
                                </div>
                                <div id="item_info_btn_wrap"><button type="button" onclick="submit()" id="item_info_btn">作成</button></div>
                            </div>
                        </div>
                        <input type="hidden" name="create" value="create">
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