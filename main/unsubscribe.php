<?php
session_start();
require_once '../php/UserManager.php';
$userMng = new UserManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
if(!empty($_POST["unsubscribe"])){
    $userMng->unsubscribeUser($_SESSION["user_id"]);
    header('Location:unsubscribe_comp.php');
    exit;
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
        <title>退会 - meetalk</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/pc_default.css">
        <link rel="stylesheet" href="css/nav.css">
        <link rel="stylesheet" href="css/search.css">
        <link rel="stylesheet" href="css/tab.css">
        <link rel="stylesheet" href="css/main_contents.css">
        <link rel="stylesheet" href="css/item_info_default.css">
        <link rel="stylesheet" href="css/main_default.css">
        <link rel="stylesheet" href="css/tag.css">
        <link rel="stylesheet" href="css/deactive.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/onload.js"></script>
        <script src="js/explain_view.js"></script>
        <script src="js/invited.js"></script>
        <script src="js/tag.js"></script>
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
                        <p id="main_center_title">退会</p>
                        <p>退会すると今後サービスをご利用することが出来なくなります。<br>退会しますか？</p>
                        <form action="unsubscribe.php" method="POST">
                            <button type="submit" class="button deactive_btn" name="unsubscribe" value="unsubscribe">退会</button>
                            <a href="index.php" class="button top_btn">TOPへ戻る</a>
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