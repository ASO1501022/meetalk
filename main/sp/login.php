<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/User.php';
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
                    $message = 'このIDは既に退会しています';
                    break;
                case -10:
                    $message = 'このアカウントは停止されています';
                    break;
                case 1:
                    $message = '会員登録が済んでいません。メールをご確認してください';
                    break;
                case 10:
                    $_SESSION["user_id"] = $_POST["user_id"];
                    session_regenerate_id();
                    header('Location:index.php');
                    exit;
            }
        }else{
            $message = 'ユーザID又はパスワードが間違っています';
        }
    }
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
<!doctype html>

<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>meetalk</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
        <link rel="stylesheet" href="./css/default.css">
        <link rel="stylesheet" href="./css/login.css">
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
    </head>


    <body>
        <div class="main">
            <p class="name">meetalk</p>
            <div class="login-form">
                <form action="login.php" method="post">
                    <p>ユーザID</p>
                    <input type="text" name="user_id" placeholder="your ID" required>
                    <p>パスワード</p>
                    <input type="password" name="password" placeholder="password" required> 
                    <button type="submit" name="login" value="login">ログイン</button>
                </form>
            </div>
            <p class="register"><a href="./user_register.php">新規アカウント登録</a></p>
        </div>
    </body>
</html>