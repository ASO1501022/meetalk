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
if(!empty($_GET['user_id'])){
    $user = $userMng->getUserByUserId($_GET['user_id']);
} else {
    $user = $userMng->getUserByUserId($_SESSION['user_id']);
}
if(!empty($_POST["request"])){
    if(!empty($userMng->getUserByUserId($_POST["request"]))){
        $userMng->friendRequest($_POST["request"]);
    }
} elseif(!empty($_POST["delete"])){
    if(!empty($userMng->getUserByUserId($_POST["delete"]))){
        $dbMng->deleteFriendByFriendUserId($_SESSION["user_id"],$_POST["delete"]);
    }
}else if(!empty($_POST["cancel"])){
    if(!empty($userMng->getUserByUserId($_POST["cancel"]))){
        $dbMng->deleteFriendByFriendUserId($_SESSION["user_id"],$_POST["cancel"]);
    }
}else if(!empty($_POST["apply"])){
    if(!empty($userMng->getUserByUserId($_POST["apply"]))){
        $userMng->friendAccept($_POST["apply"]);
    }
}else if(!empty($_POST["regist"])){
    if(!empty($userMng->getUserByUserId($_POST["regist"]))){
        $userMng->friendReject($_POST["regist"]);
    }
}
function modifyDateToJpDate($birthday){
    return date('Y年n月j日', strtotime($birthday));
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>プロフィール</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/profile.css">
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
            <h2>プロフィール</h2>
            <span class="button menu-button-right"></span>
        </div>

        <div class="wrap-contents">
            <div class="title-profile">
                <p><?=h($user->userName)?>さんのプロフィール</p>
            </div>
            
            <div class="wrap-user-info">
                <div class="user-image">
                    <?php if($user->imageName == "defalut_uesr_image.png") $user->imageName = "sp_default_user_image.png"?>
                    <img src="./../../img/user_img/<?=$user->imageName?>">
                </div>
                <div class="user-name-id">
                    <p><?=h($user->userName)?></p>
                    <p>ID:<?=h($user->userId)?></p>
                </div>
                <?php $friendStatus = $userMng->checkFriendStatus($_SESSION['user_id'], $user->userId) ?>
                <form action="profile.php?user_id=<?=h($user->userId)?>" method="post">
                <?php if($_SESSION['user_id'] == $user->userId):?>
                    <!--なにもしない(自分自身)-->
                <?php elseif($friendStatus == 0):?>
                    <button name="delete" value="<?=h($user->userId)?>" class="friend-button friend-red">-フレンド解除</button>    
                <?php elseif($friendStatus == 1):?>
                    <button name="regist" value="<?=h($user->userId)?>" class="friend-button friend-red">-フレンド申請解除</button>
                <?php elseif($friendStatus == 2):?>
                    <button name="apply" value="<?=h($user->userId)?>" class="friend-button friend-green">+申請を受ける</button>
                <?php elseif($friendStatus == -1):?>
                    <button name="request" value="<?=h($user->userId)?>" class="friend-button friend-green">+フレンド申請</button>
                <?php endif?>
                </form>
            </div>
            <?php if($user->userId == $_SESSION['user_id']):?>
            <div class="wrap-edit-button">
                <button type="button" onclick="location.href='./profile_edit.php?user_id=<?=h($_SESSION['user_id'])?>'">編集する</button>
            </div>
            <?php endif?>
            <div class="wrap-info-element">
                <div class="info-element">
                    <p>地域</p>
                    <p><?=h($user->prefecture)?></p>
                </div>
                <div class="info-element">
                    <p>性別</p>
                    <p><?=h($user->gender)?></p>
                </div>
                <div class="info-element">
                    <p>生年月日</p>
                    <p><?=h(modifyDateToJpDate($user->birthday))?></p>
                </div>
                <div class="info-element">
                    <p>メールアドレス</p>
                    <p><?=h($user->mailAddress)?></p>
                </div>
                <div class="info-element">
                    <p>自己紹介</p>
                    <p><?=h($user->message) ?></p>
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