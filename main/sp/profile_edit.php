<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/RoomManager.php';
require_once '../../php/DBManager.php';
require_once '../../php/SearchRoomManager.php';
require_once '../../php/SearchAPIManager.php';
require_once '../../php/prefecture.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchRoomMng = new SearchRoomManager();
$searchAPIMng = new SearchAPIManager();

if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
if(empty($_GET['user_id'])){
    $userId = $_SESSION['user_id'];
} else {
    $userId = $_GET['user_id'];
}
if($userId != $_SESSION['user_id']){
    header('Location:profile.php?user_id=' . $_GET['user_id']);
    exit;
}
$user = $userMng->getUserByUserId($_SESSION['user_id']);
if(!empty($_POST['save'])){
    $user->userName = (string)filter_input(INPUT_POST,"user_name");
    $user->prefecture = (string)filter_input(INPUT_POST,"prefecture");
    $user->message = (string)filter_input(INPUT_POST,"message");
    $user->password = null;
    if(is_uploaded_file($_FILES['image']['tmp_name'])){
        $user->imageName = $_FILES['image']['type']."[kugiri]".$_FILES['image']['tmp_name'];
    }else{
        $user->imageName = null;
    }
    $message = $userMng->checkUserValue($user);
    if(empty($message)){
        $userMng->modifyUser($user);
        header('Location:profile.php');
        exit;
    }else{
        echo $message;
        exit;
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

    <title>プロフィール編集</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/profile-edit.css">
    <link rel="stylesheet" href="./css/tag.css">
    <link rel="stylesheet" href="./css/flickity.css">
    <link rel="stylesheet" href="./css/ad.css">

    <script src="./js/flickity.pkgd.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="./js/tag.js"></script>
    <script src="./js/autosize.js"></script>
    <script src="./js/change-image.js"></script>
    <script src="./js/ad-footer.js"></script>
</head>


<header>
    <?php require_once "./global.html"; ?>
</header>

    

<div id="main">
    <body>
<!--GlobalMenuBar-->
        <div id="header">
            <h2>プロフィール編集</h2>
            <span class="button menu-button-right"></span>
        </div>
        
        <div class="wrap-contents">
            <div class="title-profile">
                <p><?=h($user->userName)?>さんのプロフィール編集</p>
            </div>

            <form action="profile_edit.php" method="post" enctype="multipart/form-data">
                <div class="wrap-user-info">
                    <div class="user-image">
                        <label for="preview_img_input">
                            <img src="./img/camera.png">
                        </label>
                        <img id="preview_img" src="../../img/user_img/<?=h($user->imageName)?>">         
                        <input type="file" name="image" id="preview_img_input" accept="image/*" style="display:none;">
                    </div>
        
                    <div class="user-name-id">
                        <input type="text" name="user_name" placeholder="<?=h($user->userName)?>" required>
                        <p>ID:<?=h($user->userId)?></p>
                    </div>
                </div>
                <div class="wrap-info-element">
                    <div class="info-element">
                        <p>地域</p>
                        <select class="prefecture" name="prefecture">
                            <?php foreach((array)$prefectures as $prefecture):?>
                            <?php if($prefecture == $user->prefecture):?>
                                <option selected><?=h($prefecture)?></option>
                            <?php else:?>
                                <option><?=h($prefecture)?></option>
                            <?php endif?>
                            <?php endforeach?>
                        </select>
                    </div>
                    <div class="info-element">
                        <p>自己紹介</p>
                        <textarea id="autosize-text" name="message" required></textarea>
                    </div>
                    <button type="submit" class="save-button" name="save" value="save">保存</button>
                </div>
            </form>
        </div>

        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
    </body>
</div>