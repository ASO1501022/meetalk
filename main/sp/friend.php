<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/DBManager.php';
$userMng = new UserManager();
$dbMng = new DBManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$user = $userMng->getUserByUserId($_SESSION['user_id']);
if(!empty($_POST["delete"])){
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
$friendList = $userMng->getFriendListByUserId($_SESSION["user_id"]);
$inviteCnt = 0;
foreach((array)$friendList as $friend){
    if($_SESSION['user_id'] == $friend->friendUserId){
        $friendUserId = $friend->userId;
    }else{
        $friendUserId = $friend->friendUserId;
    }
    if($userMng->checkFriendStatus($_SESSION['user_id'], $friendUserId) == 2){
        $inviteCnt++;
    }
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>フレンド一覧</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/friend.css">
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
            <h2>フレンド</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <div class="friend-search-button">
                <a href="./friend_search.php"><p>ユーザーを検索</p></a>
            </div>
            <ul class="tab">
                <li class="select">フレンド</li>
                <li>申請中</li>
                <li>承認<?php if($inviteCnt != 0):?><img src="./img/notification.png" class="notification"><p class="notification-num"><?=$inviteCnt?></p><?php endif?></li>
            </ul>
            <ul class="content">
                <!--メンバー一覧-メンバー-->
                <li>
                    <?php foreach((array)$friendList as $friend):?>
                        <?php if($friend->status == 1):?>
                            <?php if($_SESSION['user_id'] == $friend->friendUserId):?>
                                <?php $friendUser = $userMng->getUserByUserId($friend->userId)?>
                            <?php else:?>
                                <?php $friendUser = $userMng->getUserByUserId($friend->friendUserId)?>
                            <?php endif?>
                            <a href="./profile.php?user_id=<?=h($friendUser->userId)?>">
                                <div class="member">
                                    <img src="../../img/user_img/<?=h($friendUser->imageName)?>">
                                    <div class="user-info">
                                        <p><?=h($friendUser->userName)?></p>
                                        <p>ID:<?=h($friendUser->userId)?></p>
                                    </div>
                                    <div class="control-button">
                                        <form action="./friend.php" method="post">
                                            <button type="submit" name="delete" value="<?=h($friend->friendUserId)?>" class="regist">解除</button>  
                                        </form>
                                    </div>    
                                </div>
                            </a>
                        <?php endif?>
                    <?php endforeach?>
                </li>

                <!--メンバー一覧-申請中-->
                <li class="hide">
                    <?php foreach((array)$friendList as $friend):?>
                        <?php if($_SESSION['user_id'] == $friend->friendUserId){
                                    $friendUserId = $friend->userId;
                        }else{
                                    $friendUserId = $friend->friendUserId;
                        } ?>
                        <?php if($userMng->checkFriendStatus($_SESSION['user_id'], $friendUserId) == 1):?>
                            <?php if($_SESSION['user_id'] != $friend->friendUserId):?>
                                <?php $friendUser = $userMng->getUserByUserId($friend->friendUserId)?>
                                <a href="./profile.php?user_id=<?=h($friendUser->userId)?>">
                                    <div class="member">
                                        <img src="../../img/user_img/<?=h($friendUser->imageName)?>">
                                        <div class="user-info">
                                            <p><?=h($friendUser->userName)?></p>
                                            <p>ID:<?=h($friendUser->userId)?></p>
                                        </div>
                                        <div class="control-button">
                                            <form action="./friend.php" method="post">
                                                <button type="submit" name="cancel" value="<?=h($friend->friendUserId)?>" class="regist">取消</button>
                                            </form>
                                        </div>                      
                                    </div>
                                </a>
                            <?php endif?>
                        <?php endif?>
                    <?php endforeach?>
                </li>

                <!--メンバー一覧-招待中-->
                <li class="hide">
                    <?php foreach((array)$friendList as $friend):?>
                        <?php if($_SESSION['user_id'] == $friend->friendUserId){
                                    $friendUserId = $friend->userId;
                        }else{
                                    $friendUserId = $friend->friendUserId;
                        } ?>
                        <?php if($userMng->checkFriendStatus($_SESSION['user_id'], $friendUserId) == 2):?>
                            <?php if($_SESSION['user_id'] != $friend->userId):?>
                                <?php $friendUser = $userMng->getUserByUserId($friend->userId)?>
                            
                                <a href="./profile.php?user_id=<?=h($friendUser->userId)?>">
                                    <div class="member">
                                        <img src="../../img/user_img/<?=h($friendUser->imageName)?>">
                                        <div class="user-info">
                                            <p><?=h($friendUser->userName)?></p>
                                            <p>ID:<?=h($friendUser->userId)?></p>
                                        </div>
                                        <div class="control-button">
                                            
                                            <form action="./friend.php" method="post">
                                                <button type="submit" name="apply" value="<?=h($friendUser->userId)?>" class="apply">承認</button>
                                                <button type="submit" name="regist" value="<?=h($friendUser->userId)?>" class="regist">拒否</button>
                                            </form>  
                                        </div>    
                                    </div>
                                </a>
                            <?php endif?>
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