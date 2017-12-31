<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/DBManager.php';
$userMng = new UserManager();
$dbMng = new DBManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
// $display = array('','style="display:none"','style="display:none"');
if(!empty($_POST["delete"])){
    if(!empty($userMng->getUserByUserId($_POST["delete"]))){
        $dbMng->deleteFriendByFriendUserId($_SESSION["user_id"],$_POST["delete"]);
    }
}else if(!empty($_POST["cancel"])){
    if(!empty($userMng->getUserByUserId($_POST["cancel"]))){
        $dbMng->deleteFriendByFriendUserId($_SESSION["user_id"],$_POST["cancel"]);
        $display[1] = '';
        $display[0] = 'style="display:none"';
    }
}else if(!empty($_POST["apply"])){
    if(!empty($userMng->getUserByUserId($_POST["apply"]))){
        $userMng->friendAccept($_POST["apply"]);
    }
}else if(!empty($_POST["reject"])){
    if(!empty($userMng->getUserByUserId($_POST["reject"]))){
        $userMng->friendReject($_POST["reject"]);
        $display[2] = '';
        $display[0] = 'style="display:none"';
    }
}

$friendList = $userMng->getFriendListByUserId($_SESSION["user_id"]);
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
        <title>フレンドリスト - meetalk</title>
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
        <link rel="stylesheet" href="css/member_list.css">
        <link rel="stylesheet" href="css/friend.css">
        <link rel="stylesheet" href="css/invited.css">
        <link rel="stylesheet" href="css/user_search.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/tab.js"></script>
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
                        <p id="main_center_title">フレンドリスト</p>
                            <p id="user_search_wrap">ユーザ検索は<a href="user_search.php">こちら</a></p>
                            <div id="member_tab_wrap">
                                <ul>
                                    <li id="member_tab" class="tab tab_on">フレンド</li>
                                    <li id="invite_member_tab" class="tab">申請中</li>
                                    <li id="join_request_member_tab" class="tab">承認待ち</li>
                                </ul>
                            </div>
                        <form action="friend.php" method="POST">
                            <div class="index_contents">
<?php                       foreach((array)$friendList as $frienduser):?>
<?php                           if($frienduser->status == 1):?>
<?php                           $user = $userMng->getUserByUserId($frienduser->friendUserId);?>
<?php                           if($user->userId == $_SESSION["user_id"]):?>
<?php                           $user = $userMng->getUserByUserId($frienduser->userId);?>
<?php                           endif?>
                                <div class="member_wrap">
                                    <div class="member_img_wrap"><img src="img/user_img/<?=h($user->imageName)?>" alt=""></div>
                                    <div class="member_name_wrap"><p><a href="profile.php?user_id=<?=h($user->userId)?>"><?=h($user->userName)?></a></p><p>ID:<?=h($user->userId)?></p></div>
                                    <div class="member_btn_wrap"><a href="profile.php?user_id=<?=h($user->userId)?>" class="button detail">詳細</a></div>
                                </div>
<?php                           endif?>
<?php                       endforeach?>
                            </div>
                            <div class="index_contents" style="display:none">
<?php                       foreach((array)$friendList as $frienduser):?>
<?php                           if($frienduser->status == 0 && $frienduser->userId == $_SESSION["user_id"]):?>
<?php                           $user = $userMng->getUserByUserId($frienduser->friendUserId);?>
                                <div class="member_wrap">
                                    <div class="member_img_wrap"><img src="img/user_img/<?=h($user->imageName)?>" alt=""></div>
                                    <div class="member_name_wrap"><p><a href="profile.php?user_id=<?=h($user->userId)?>"><?=h($user->userName)?></a></p><p>ID:<?=h($user->userId)?></p></div>
                                    <div class="member_btn_wrap"><button type="submit" class="kick" name="cancel" value="<?=h($frienduser->friendUserId)?>">取消</button></div>
                                </div>
<?php                           endif?>
<?php                       endforeach?>
                            </div>
                            <div class="index_contents" style="display:none">
<?php                       foreach((array)$friendList as $frienduser):?>
<?php                           if($frienduser->status == 0 && $frienduser->friendUserId == $_SESSION["user_id"]):?>
<?php                           $user = $userMng->getUserByUserId($frienduser->userId);?>
                                <div class="member_wrap">
                                    <div class="member_img_wrap"><img src="img/user_img/<?=h($user->imageName)?>" alt=""></div>
                                    <div class="member_name_wrap"><p><a href="profile.php?user_id=<?=h($user->userId)?>"><?=h($user->userName)?></a></p><p>ID:<?=h($user->userId)?></p></div>
                                    <div class="member_btn_wrap"><button type="submit" class="apply" name="apply" value="<?=h($frienduser->userId)?>">承認</button><button type="submit" class="kick" name="reject" value="<?=h($frienduser->userId)?>">拒否</button></div>
                                </div>
<?php                           endif?>
<?php                       endforeach?>
                            </div>
                        </form>
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