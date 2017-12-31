<?php
session_start();
require_once '../php/UserManager.php';
require_once '../php/User.php';
$userMng = new UserManager();
$message = null;
if($userMng->loggedinCheck()){
    header('Location:index.php');
    exit;
}
if(!empty($_POST["login"])){
    $message = getErrorMessage();
    if(empty($message)){
        if($userMng->loginCheck($_POST["user_id"],$_POST["password"])){
            $user = $userMng->getUserByUserId($_POST["user_id"]);
            switch ($user->userStatus) {
                case -1:
                    $message = 'このIDは既に退会しています。';
                    break;
                case -10:
                    $message = 'このアカウントは停止されています。';
                    break;
                case 1:
                    $message = '会員登録が完了していません。メールをご確認してください。';
                    break;
                case 10:
                    $_SESSION["user_id"] = $_POST["user_id"];
                    session_regenerate_id();
                    header('Location:index.php');
                    exit;
                    break;
            }
        }else{
            $message = 'ユーザID又はパスワードが間違っています。';
        }
    }

}
if(!empty($message)){
    $message = 'alert("'.$message.'")';
}

function getErrorMessage(){
    if(empty($_POST["user_id"])){
        return 'ユーザIDが空です';
    }
    if(empty($_POST["password"])){
        return 'パスワードが空です';
    }
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>meetalk</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/login.css">
        <link rel="stylesheet" href="css/service_title.css">
        <script src="js/onload.js"></script>
        <script>addOnload(function(){<?=$message?>})</script>
    </head>
    <body>
        <div id="contents">
            <div class="login_wrap">
                <div id="logo"></div>
                <div id="login_inner">
                    <form action="login.php" method="POST">
                        <p class="head">ユーザID</p>
                        <div class="input_box"><input type="text" name="user_id" placeholder="user1234" required></div>
                        <p class="head">パスワード</p>
                        <div class="input_box"><input type="password" name="password" placeholder="●●●●●●" required></div>
                        <button id="login" type="submit" name="login" value="login">ログイン</button>
                        <div id="link_wrap"><p>新規登録は<a href="register.php">こちら</a></p></div>
                    </form>
                </div>
            </div>
            <div class="img_wrap">
                <div class="service_title_img">
                </div>
                <div class="service_title_wrap">
                    <div class="service_title_inner">
                        <p class="service_title">meetalk</p>
                        <p class="service_catch_copy">広がる、見つかる、友達の輪</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

