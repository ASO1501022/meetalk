<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/ContactManager.php';
require_once '../php/Contact.php';
$userMng = new UserManager();
$contactMng = new ContactManager();
$message = null;
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
if(!empty($_POST["send"])){
    $message = getErrorMEssage();
    if(empty($message)){
        $contact = new Contact();
        $contact->contactName = (string)filter_input(INPUT_POST,"title");
        $contact->userId = $_SESSION["user_id"];
        $contact->content = (string)filter_input(INPUT_POST,"explain");
        if(is_uploaded_file($_FILES['image']['tmp_name'])){
            $contact->imageName = $_FILES['image']['type']."[kugiri]".$_FILES['image']['tmp_name'];
        }else{
            $contact->imageName = null;
        }
        $contactMng->insertContact($contact);
    }else{
        $message = "<alert>".$message."</alert>";
    }
}
function getErrorMEssage(){
    if(empty($_POST["title"])){
        return 'タイトルが空です';
    }
    if(empty($_POST["explain"])){
        return 'お問い合わせ内容が空です';
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
        <title>お問い合わせ - meetalk</title>
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
        <link rel="stylesheet" href="css/contact.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/onload.js"></script>
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
                        <p id="main_center_title">お問い合わせ</p>
                        <form action="contact.php" method="POST" enctype="multipart/form-data">
                            <div class="flex_box">
                                <p><span>タイトル：</span></p><input id="contact_title" type="text" name="title" placeholder="不具合、ご意見" required>
                            </div>
                            <div id="main_center_inner">
                                <p id="img_explain">※画像が必要な場合は添付してください</p>
                                <div id="preview_img" style="background-image:url()"></div>
                                <input type="file" id="preview_img_input" name="image" accept="image/*">
                                <label for="preview_img_input" id="l_preview_img_input">ファイルを選択</label>
                            </div>
                            <p id="explain_title"><span>お問い合わせ内容：</span></p>
                            <div id="explian_wrap">
                                <textarea name="explain" id="explian" wrap="hard" placeholder="(3000文字以内)" maxlength="3000" required><?php if(!empty($_POST["explain"])){ echo $_POST["explain"];}?></textarea>
                            </div>
                            <div id="item_info_btn_wrap"><button name="send" type="submit" id="item_info_btn" value="send">送信</button></div>
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