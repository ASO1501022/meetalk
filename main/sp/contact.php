<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/ContactManager.php';
require_once '../../php/Contact.php';
$userMng = new UserManager();
$contactMng = new ContactManager();
$message = null;
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$flg=0;
$user = $userMng->getUserByUserId($_SESSION['user_id']);
if(!empty($_POST["send"])){
    $message = getErrorMEssage();
    if(empty($message)){
        $contact = new Contact();
        $contact->contactName = (string)filter_input(INPUT_POST,"title");
        $contact->userId = $_SESSION["user_id"];
        $contact->content = (string)filter_input(INPUT_POST,"explain");
        $contactMng->insertContact($contact);
        $flg=1;
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

?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>ヘルプ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/contact.css">

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
            
            <h2>お問い合わせ</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <?php if($flg == 0):?>
            <p class="caution">お困りの内容をご記入の上、<br>送信ボタンを押してください。</p>
            <form action="contact.php" method="POST" enctype="multipart/form-data">
                <p>件名</p>
                <input id="contact_title" type="text" name="title" placeholder="不具合、ご意見の件名" required>
                <p>お問合わせの内容</p>
                <textarea name="explain" id="explain" wrap="hard" placeholder="(3000文字以内)" maxlength="3000" required><?php if(!empty($_POST["explain"])){ echo h($_POST["explain"]);}?></textarea>
                <button name="send" type="submit" id="send-button" value="send">送信</button>
            </form>
            <?php else:?>
            <p class="caution">貴重なご意見ありがとうございました。</p>
            <?php endif?>
        </div>
        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
    </body>
</div>