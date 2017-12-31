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
$user = $userMng->getUserByUserId($_SESSION['user_id']);
$roomId = filter_input(INPUT_GET, 'room_id');
if(empty($roomId)){
    header('Location:index.php');
    exit;
}

$room = $roomMng->getRoomByRoomId($roomId);
//そのユーザーが部屋に属しているか
$userStatus = $roomMng->getUserStatusByRoomId($room->roomId,$_SESSION['user_id']);
if( $room->userId == $_SESSION["user_id"]){

} elseif(!empty($userStatus->status)){
    if($userStatus->status != 2){
        header('Location:room_info.php?room_id='.$_GET["room_id"]);
        exit;
    }
}else{
    header('Location:room_info.php?room_id='.$_GET["room_id"]);
    exit;
}

if(!empty($_POST['kick'])){
    if(!empty($userMng->getUserByUserId($_POST["kick"]))){
        $roomMng->kickUserFromRoom($_POST['kick'], $room->roomId);
    }
}elseif(!empty($_POST['apply'])) {
    if(!empty($userMng->getUserByUserId($_POST["apply"]))){
        $roomMng->applyRequest($_POST['apply'], $room->roomId);
    }
}elseif(!empty($_POST['reject'])) {
    if(!empty($userMng->getUserByUserId($_POST["reject"]))){
        $roomMng->rejectRequest($_POST['reject'], $room->roomId);
    }
}elseif(!empty($_POST['cancel'])) {
    if(!empty($userMng->getUserByUserId($_POST["cancel"]))){
        $roomMng->escapeUserFromRoom($_POST['cancel'], $room->roomId);
    }
}
$applyCnt = 0;
$inviteCnt = 0;
foreach($room->roomUserStatusList as $roomUserStatus){
    if($roomUserStatus->status == 1){
        $applyCnt++;
    }elseif($roomUserStatus->status == 3){
        $inviteCnt++;
    }
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

    <title>メンバー一覧</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/room-member.css">
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
            <h2>メンバー一覧</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <div class="title">
                <p><?=h($room->roomName)?></p>
            </div>

            <ul class="tab">
                <li class="select">メンバー</li>
                <li>申請中</li>
                <li>招待中<?php if($inviteCnt != 0): ?><img src="./img/notification.png" class="notification"><p class="notification-num"><?=$inviteCnt?></p><?php endif?></li>
            </ul>
            <ul class="content">
                <!--メンバー一覧-メンバー-->
                <li>
<?php foreach($room->roomUserStatusList as $roomUserStatus):
          if($roomUserStatus->status == 2): ?>
<?php         $user = $userMng->getUserByUserId($roomUserStatus->userId);?>
                    <a href="./profile.php?user_id=<?=h($user->userId)?>">
                    <div class="member">
                        <img src="../img/user_img/<?=h($user->imageName)?>">
                        <div class="user-info">
                            <p><?=h($user->userName)?></p>
                            <p>ID:<?=h($user->userId)?></p>
                        </div>
<?php         if($_SESSION['user_id'] == $room->userId): ?>
                        <form action="room_member.php?room_id=<?=h($_GET['room_id'])?>" method="post">
                            <div class="control-button">
                                <button type="submit" name="kick" value="<?=h($user->userId)?>" class="regist">キック</button>  
                            </div>
                        </form> 
<?php         endif ?>                       
                    </div>
                    </a>
<?php     endif ?>
<?php endforeach ?>
                </li>

                <!--メンバー一覧-申請中-->
                <li class="hide">
<?php foreach($room->roomUserStatusList as $roomUserStatus):
          if($roomUserStatus->status == 1): ?>
              $user = $userMng->getUserByUserId($roomUserStatus->userId);
                    <a href="./profile.php?user_id=<?=h($user->userId)?>">
                    <div class="member">
                        <img src="../img/user_img/<?=h($user->imageName)?>">
                        <div class="user-info">
                            <p><?=h($user->userName)?></p>
                            <p>ID:<?=h($user->userId)?></p>
                        </div>
<?php         if($_SESSION['user_id'] == $room->userId): ?>
                        <div class="control-button">
                            <button name="apply" value="<?=h($user->userId)?>" class="apply">承認</button>
                            <button name="regist" value="<?=h($user->userId)?>" class="regist">拒否</button>  
                        </div>                    
                    </div>
<?php         endif ?>
                    </div>
                    </a>
<?php     endif ?>
<?php endforeach ?>
                </li>

                <!--メンバー一覧-招待中-->
                <li class="hide">
<?php foreach($room->roomUserStatusList as $roomUserStatus):
          if($roomUserStatus->status == 3):
              $user = $userMng->getUserByUserId($roomUserStatus->userId); ?>
                    <a href="./profile.php?user_id=<?=h($user->userId)?>">
                    <div class="member">
                        <img src="../img/user_img/<?=h($user->imageName)?>">
                        <div class="user-info">
                            <p><?=h($user->userName)?></p>
                            <p>ID:<?=h($user->userId)?></p>
                        </div>
<?php         if($_SESSION['user_id'] == $room->userId): ?>
                        <div class="control-button">
                            <button name="cancel" value="<?=h($user->userId)?>" class="regist">取消</button>  
                        </div>
                    </div>
<?php         endif ?>
                    </div>
                    </a>
<?php     endif ?>
<?php endforeach ?>
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