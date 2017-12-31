<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/RoomManager.php';
require_once '../../php/DBManager.php';
require_once '../../php/SearchRoomManager.php';
require_once '../../php/SearchAPIManager.php';
require_once '../../php/Room.php';

$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchRoomMng = new SearchRoomManager();
$searchAPIMng = new SearchAPIManager();
$room = new Room();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
if(!empty($_POST['save'])){
    $autoApply = (string)filter_input(INPUT_POST,"apply");
    if($autoApply == "on"){
        $autoApply = true;
    }else{
        $autoApply = false;
    }
    $deadLine = "";
    $deadLine .= (string)filter_input(INPUT_POST,"year")."-";
    $deadLine .= (string)filter_input(INPUT_POST,"month")."-";
    $deadLine .= (string)filter_input(INPUT_POST,"day")." ";
    $deadLine .= (string)filter_input(INPUT_POST,"hour").":";
    $deadLine .= (string)filter_input(INPUT_POST,"minutes").":00";
    $room = new Room; 
    $room->roomName = $_POST['room_name'];
    $room->userId = $_SESSION['user_id'];
    $room->explain = $_POST['explain'];
    $room->maxMember = $_POST['max_member'];
    $room->restaurantId = $_POST['restaurant_id'];
    $room->deadLine = $deadLine;
    $room->budget = $_POST['budget'];
    $room->autoApply = $autoApply;
    $room->address = $_POST['address'];
    $room->roomTagList = $_POST['tag'];
    $message = $roomMng->checkRoomValue($room);
    if(empty($message)){
        $roomId = $roomMng->registerRoom($room);
        $userMng->addHistoryByUserId($_SESSION['user_id'],$roomId);
        header('Location:room_info.php?room_id='.$roomId);
        exit;
    }else{
        $message = "<alert>".h($message)."</alert>";
        echo $message;
        exit;
    }
}

$user = $userMng->getUserByUserId($_SESSION['user_id']);

if(!empty($_GET['restaurant_id'])){
    $restaurantId = $_GET['restaurant_id'];
    $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($_GET["restaurant_id"]);
    if(empty($restaurant)){
        header('Location:login.php');
    }
} else {
    header('Location:login.php');
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
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
    <link rel="stylesheet" href="./css/room-register.css">
    <link rel="stylesheet" href="./css/flickity.css">
    <link rel="stylesheet" href="./css/ad.css">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <script src="./js/flickity.pkgd.min.js"></script>
    <script src="./js/date.js"></script>
    <script src="./js/tag.js"></script>
    <script src="./js/ad-footer.js"></script>
    <script>
        $(function(){
            $('#apply').change(function(){
                if ($(this).is(':checked')) {
                    $('.auto-apply').text('参加を確認する');
                } else {
                    $('.auto-apply').text('参加を自動承認する');
                }
            })
        });
    </script>
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
            <h2>部屋作成</h2>
        <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <form action="room_register.php" method="post">
                <div class="title-create-room">
                    <p>部屋名</p>
                    <input type="text" name="room_name">
                </div>
<!--タグの追加-->
                <div class="add-tag">
                    <input type="text" name="" placeholder="タグの追加" id="tag_input">
                    <button type="button" name="" onclick="addTag()">追加</button>
                </div>
                <div id="wrap-tag-edit">
<?php foreach($restaurant->categoryName as $tag): ?>
                    <!--タグリストの名前(name)をGETに指定してhrefに出力しましょう-->
                    <div class="tag_inner delete">
                        <div class="tag_name"><p id="user-tag"><?=h($tag)?></p>
                        <img src="./img/tag_delete.png" class="tag_delete"></div>
                        <input type="hidden" name="tag[]" value="<?=h($tag)?>">
                    </div>
<?php endforeach; ?>
                </div>
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
                <!--自動承認の有無ボタン-->
                
                <input type="checkbox" name="apply" id="apply" class="onoff">
                <label for="apply" class="auto-apply">参加を自動承認する</label> 
            
                <!--レストランの情報（文字）-->
                <div class="info-element">
                    <p>主催者</p>
                    <p><?=h($user->userName)?></p>
                </div>
                <div class="info-element">
                    <p>店舗名</p>
                    <p><?=h($restaurant->name)?></p>
                    <input type="hidden" name="address" value="<?=$restaurant->address?>">
                    <input type="hidden" name="restaurant_id" value="<?=$restaurant->restaurantId?>">
                </div>
                <div class="info-element">
                    <p>人数</p>
                    <input type="text" name="max_member" pattern="\d*" required>
                    <p class="unit">人</p>
                </div>
                <div class="info-element">
                    <p>開催日時</p>
                    <div id="wrap-dead-line">
                        <select name="year" id="year" class="dead_line">
                            <?php $year = (int)date('Y')?>
                            <?php //if(isset($_POST["year"])) $year = $_POST["year"];?>
                            <?php for($i = $year;$i <= $year + 50;$i++):?>
                                <?php if($i == $year):?>
                                    <option selected><?=$i?></option>
                                <?php else:?>
                                    <option><?=$i?></option>
                                <?php endif?>
                            <?php endfor?>
                        </select>
                        <p>年</p>
                        <select name="month" id="month" class="dead_line">
                            <?php $month = (int)date('n')?>
                            <?php if(isset($_POST["month"])) $month = $_POST["month"]; ?>
                            <?php for($i = 1;$i <= 12;$i++):?>
                                <?php if($i == $month):?>
                                    <option selected><?=$i?></option>
                                <?php else:?>
                                    <option><?=$i?></option>
                                <?php endif?>
                            <?php endfor?>
                        </select>
                        <p>月</p>
                        <select name="day" id="day" class="dead_line">
                            <?php $day = (int)date('t')?>
                            <?php if(isset($_POST["day"])) $day = $_POST["day"]; ?>
                            <?php for($i = 1;$i <= date('t');$i++):?>
                            <?php if($i == $day):?>
                                <option selected><?=$i?></option>
                            <?php else:?>
                                <option><?=$i?></option>
                            <?php endif?>
                            <?php endfor?>
                        </select>
                        <p>日</p>
                        <select name="hour" id="hour" class="dead_line">
                            <?php $hour = (int)date('G')?>
                            <?php if(isset($_POST["hour"])) $hour = $_POST["hour"]; ?>
                            <?php for($i = 0;$i <= 23;$i++):?>
                            <?php if($i == $hour):?>
                                <option selected><?=$i?></option>
                            <?php else:?>
                                <option><?=$i?></option>
                            <?php endif?>
                            <?php endfor?>
                        </select>
                        <p>時</p>
                        <select name="minutes" id="minutes" class="dead_line">
                            <?php $minutes = (int)date('i')?>
                            <?php if(isset($_POST["minutes"])) $minutes = $_POST["minutes"]; ?>
                            <?php for($i = 1;$i <= 59;$i++):?>
                                <?php if($i == $minutes):?>
                                    <option selected><?=$i?></option>
                                <?php else:?>
                                    <option><?=$i?></option>
                                <?php endif?>
                            <?php endfor?>
                        </select>
                        <p>分</p>
                    </div>

                </div>
                <div class="info-element">
                    <p>予算</p>
                    <input type="text" pattern="\d*" min="0" name="budget">
                    <p class="unit">円</p>
                </div>
                <div class="info-element">
                    <p>部屋紹介</p>
                    <textarea name="explain" placeholder="この部屋の紹介を書いてね(1000字以内)" wrap="hard" required></textarea>
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