<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/User.php';
$userMng = new UserManager();
$message = null;
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$user = null;
if(!empty($_GET["user_id"])){
    $user = $userMng->getUserByUserId($_GET["user_id"]);
}else{
    $user = $userMng->getUserByUserId($_SESSION["user_id"]);
}
if(empty($user)){
    header('Location:profile.php');
    exit;
}
if(!empty($_POST["request"])){
    if(!empty($userMng->getUserByUserId($_POST["request"]))){
        $userMng->friendRequest($_POST["request"]);
    }
}else if(!empty($_POST["delete"])){
    if(!empty($userMng->getUserByUserId($_POST["delete"]))){
        $userMng->deleteFriendByFriendUserId($_POST["delete"]);
    }
}else if(!empty($_POST["accept"])){
    if(!empty($userMng->getUserByUserId($_POST["accept"]))){
        $userMng->friendAccept($_POST["accept"]);
    }
}else if(!empty($_POST["reject"])){
    if(!empty($userMng->getUserByUserId($_POST["reject"]))){
        $userMng->friendReject($_POST["reject"]);
    }
}
if(!empty($_GET["user_id"])){
    $user = $userMng->getUserByUserId($_GET["user_id"]);
}else{
    $user = $userMng->getUserByUserId($_SESSION["user_id"]);
}
$now = date("Ymd"); 
$birthday = date("Ymd",strtotime($user->birthday)); 
$age = floor(($now-$birthday)/10000);
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
        <title>「<?=h($user->userName)?>」さんのプロフィール - meetalk</title>
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
        <link rel="stylesheet" href="css/profile.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/restaurant_img_toggle.js"></script>
        <script src="js/explain_view.js"></script>
        <script src="js/invited.js"></script>
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
                    <div class="main_center">
                        <form action="profile.php?user_id=<?=h($user->userId)?>" method="POST">
                            <div id="main_center_title">
                                <p >プロフィール</p>
<?php                           if($user->userId == $_SESSION["user_id"]):?>
                                    <a href="profile_edit.php" id="item_info_btn">編集</a>
<?php                           else:?>
<?php                               switch($userMng->checkFriendStatus($_SESSION["user_id"], $user->userId)):?>
<?php                                   case -1:?>
                                        <button name="request" value="<?=h($user->userId)?>" class="btn friend_no">フレンド申請</button>
<?php                                   break;?>
<?php                                   case 0:?>
                                        <button name="delete" value="<?=h($user->userId)?>" class="btn already_friend">フレンド解除</button>
<?php                                   break;?>
<?php                                   case 1:?>
                                        <button class="btn already_request" disabled>フレンド申請済みです</button>
<?php                                   break;?>
<?php                                   case 2:?>
                                        <button name="accept" value="<?=h($user->userId)?>" class="btn request_accept">フレンド承認</button>
                                        <button name="reject" value="<?=h($user->userId)?>" class="btn request_reject">フレンド拒否</button>
<?php                                   break;?>
<?php                               endswitch;?>
<?php                           endif;?>
                            </div>
                        </form>
                        <div id="main_center_inner">
                            <div id="user_img" style="background-image:url(img/user_img/<?=h($user->imageName)?>)"></div>
                            <div id="user_profile_wrap">
                                <p id="user_name"><?=h($user->userName)?></p>
                                <p id="user_pref"><span>ユーザID</span>：<?=h($user->userId)?></p>
                                <p id="user_pref"><span>地域</span>：<?=h($user->prefecture)?></p>
                                <p id="user_gender"><span>性別</span>：<?=h($user->gender)?></p>
                                <p id="user_mail_address"><span>メールアドレス</span>：<?=h($user->mailAddress)?></p>
                                <p id="user_birthday"><span>生年月日</span>：<?=h(date('Y年n月j日',strtotime($user->birthday)))?></p>
                                <p id="user_age"><span>年齢</span>：<?=h($age)?>歳</p>
                            </div>
                        </div>
                        <p id="explain_title"><span>自己紹介：</span></p>
                        <div id="explian_wrap">
                            <p id="explian"><?=h($user->message)?></p>
                        </div>
                    </div>
                    <div class="ad ad_right">
                        <?php include '../component/ad.php' ?>
                    </div>
                </div>
            </div>
            <?php include '../component/footer.php' ?>
        </div>
    </body>
</html>