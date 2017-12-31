<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/User.php';
require_once '../php/prefecture.php';
$userMng = new UserManager();
$message = null;
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$user = $userMng->getUserByUserId($_SESSION["user_id"]);
if(!empty($_POST["update"])){
    $_user = new User();
    $_user->userId = $_SESSION["user_id"];
    $_user->userName = (string)filter_input(INPUT_POST,"user_name");
    $_user->password = $user->password;
    $_user->mailAddress = $user->mailAddress;
    $_user->prefecture = (string)filter_input(INPUT_POST,"prefecture");
    $_user->birthday = $user->birthday;
    $_user->gender = $user->gender;
    $_user->password = null;
    $_user->registerDate = $user->registerDate;
    $_user->message = (string)filter_input(INPUT_POST,"explain");
    $_user->userStatus = $user->userStatus;
    if(is_uploaded_file($_FILES['image']['tmp_name'])){
        $_user->imageName = $_FILES['image']['type']."[kugiri]".$_FILES['image']['tmp_name'];
    }else{
        $_user->imageName = null;
    }
    $message = $userMng->checkUserValue($_user);
    if(empty($message)){
        $userMng->modifyUser($_user);
        header('Location:profile.php');
        exit;
    }else{
        $message = 'alert("'.$message.'")';
    }
}
$user = $userMng->getUserByUserId($_SESSION["user_id"]);

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
        <title>プロフィール編集- meetalk</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/pc_default.css">
        <link rel="stylesheet" href="css/nav.css">
        <link rel="stylesheet" href="css/search.css">
        <link rel="stylesheet" href="css/tab.css">
        <link rel="stylesheet" href="css/main_contents.css">
        <link rel="stylesheet" href="css/item_info_default.css">
        <link rel="stylesheet" href="css/room_register.css">
        <link rel="stylesheet" href="css/main_default.css">
        <link rel="stylesheet" href="css/tag.css">
        <link rel="stylesheet" href="css/profile_edit.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/onload.js"></script>
        <script src="js/restaurant_img_toggle.js"></script>
        <script src="js/explain_view.js"></script>
        <script src="js/invited.js"></script>
        <script src="js/tag.js"></script>
        <script src="js/change_image.js"></script>
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
                    <div class="main_center">
                        <p id="main_center_title">プロフィール編集</p>
                            <form action="profile_edit.php" method="POST" enctype="multipart/form-data">
                            <div id="main_center_inner">
                                <div id="preview_img" style="background-image:url(img/user_img/<?=h($user->imageName)?>)"></div>
                                <input type="file" id="preview_img_input" name="image" accept="image/*">
                                <label for="preview_img_input" id="l_preview_img_input">ファイルを選択</label>
                                <div id="user_profile_wrap">
                                    <p id="user_id"><span>ID：</span><?=h($user->userId)?></p>
                                    <div class="flex_box"><p id="user_name">名前：</p><input type="text" id="user_name_input" name="user_name" value="<?=h($user->userName)?>"></div>
                                    <p id="user_pref"><span>地域：</span></p>
                                        <select class="item_info_select" name="prefecture" id="prefecture">
<?php                                       foreach($prefectures as $prefecture):?>
<?php                                           if($user->prefecture == $prefecture):?>
                                                    <option selected><?=h($prefecture)?></option>
<?php                                           else:?>
                                                    <option><?=h($prefecture)?></option>
<?php                                           endif?>
<?php                                       endforeach?>
                                        </select>
                                    <p id="user_gender"><span>ユーザID：</span><?=h($user->userId)?></p>
                                    <p id="user_gender"><span>性別：</span><?=h($user->gender)?></p>
                                    <p id="user_mail_address"><span>メールアドレス</span>：<?=h($user->mailAddress)?></p>
                                    <p id="user_birthday"><span>生年月日：</span><?=h(date('Y年n月j日', strtotime($user->birthday)))?></p>
                                </div>
                            </div>
                            <p id="explain_title"><span>自己紹介：</span></p>
                            <div id="explian_wrap">
                                <textarea name="explain" id="explian" wrap="hard" placeholder="この部屋の紹介(1000文字以内)" maxlength="1000" required><?=h($user->message)?></textarea>
                            </div>
                            <div id="item_info_btn_wrap"><button name="update" type="submit" id="item_info_btn" value="update">更新</button></div>
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