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
if( $room->userId != $_SESSION["user_id"]){
    header('Location:room_info.php?room_id='.$_GET["room_id"]);
    exit;
}
if(!empty($_POST["invite"])){
    if(!empty($userMng->getUserByUserId($_POST["invite"]))){
        $roomMng->inviteFriendUserToRoom($_POST["invite"],$_GET["room_id"]);
    }
}
$friendUserList = $userMng->getFriendListByUserId($_SESSION["user_id"]);


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
    <link rel="stylesheet" href="./css/invite-friend.css">
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
                <li class="select">招待可フレンド</li>
                <li>その他のフレンド</li>
            </ul>
            <ul class="content">
                <!--メンバー一覧-メンバー-->
                <li>
<?php foreach($friendUserList as $friendUser):
          if($friendUser->status == 1): ?>
<?php         $user = $userMng->getUserByUserId($friendUser->userId);?>
                <?php if($friendUser->status == 1):?>
                <?php $user = $userMng->getUserByUserId($friendUser->friendUserId)?>
                    <?php if($user->userId == $_SESSION["user_id"]):?>
                    <?php $user = $userMng->getUserByUserId($friendUser->userId);?>
                    <?php endif?>
                    <?php $userStatus = $roomMng->getUserStatusByRoomId($_GET["room_id"],$user->userId) ?>
                    <?php if(empty($userStatus->status)):?>
                    <a href="./profile.php?user_id=<?=h($user->userId)?>">
                    <div class="member">
                        <img src="../img/user_img/<?=h($user->imageName)?>">
                        <div class="user-info">
                            <p><?=h($user->userName)?></p>
                            <p>ID:<?=h($user->userId)?></p>
                        </div>
<?php         if($_SESSION['user_id'] == $room->userId): ?>
                        <form action="invite_friend.php?room_id=<?=h($_GET['room_id'])?>" method="post">
                            <div class="control-button">
                                <button name="invite" value="<?=h($user->userId)?>" class="invite">招待</button>  
                            </div>
                        </form> 
<?php         endif ?>                       
                    </div>
                    </a>
                    <?php endif?>
<?php endif?>
<?php     endif ?>
<?php endforeach ?>
                </li>

                <!--メンバー一覧-申請中-->
                <li class="hide">
<?php foreach($friendUserList as $friendUser):
          if($friendUser->status == 1): ?>
<?php         $user = $userMng->getUserByUserId($friendUser->userId);?>
                <?php if($friendUser->status == 1):?>
                <?php $user = $userMng->getUserByUserId($friendUser->friendUserId)?>
                    <?php if($user->userId == $_SESSION["user_id"]):?>
                    <?php $user = $userMng->getUserByUserId($friendUser->userId);?>
                    <?php endif?>
                    <?php $userStatus = $roomMng->getUserStatusByRoomId($_GET["room_id"],$user->userId) ?>
                    <?php if(!empty($userStatus->status)):?>
                    
                    <a href="./profile.php?user_id=<?=h($user->userId)?>">
                    <div class="member">
                        <img src="../img/user_img/<?=h($user->imageName)?>">
                        <div class="user-info">
                            <p><?=h($user->userName)?></p>
                            <p>ID:<?=h($user->userId)?></p>
                        </div>
<?php         if($_SESSION['user_id'] == $room->userId): ?>
                        <div class="control-button">
                            <?php switch($userStatus->status):
                                  case 0:?>
                                <button name="none" value="<?=h($user->userId)?>" class="none" disabled>キック済</button>
                            <?php break?>
                            <?php case 1:?>
                                <button name="none" value="<?=h($user->userId)?>" class="none" disabled>申請中</button>
                            <?php break?>
                            <?php case 2:?>
                                <button name="none" value="<?=h($user->userId)?>" class="none" disabled>参加済</button>
                            <?php break?>
                            <?php case 3:?>
                                <button name="none" value="<?=h($user->userId)?>" class="none" disabled>招待済</button>
                            <?php break?>
                            <?php endswitch?>
                        </div>                    
                    </div>
<?php         endif ?>
                    </div>
                    </a>
                    <?php endif?>
<?php endif?>
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