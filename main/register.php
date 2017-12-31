<?php
session_start();
date_default_timezone_set('Asia/Tokyo');
require_once '../php/UserManager.php';
require_once '../php/User.php';
require_once '../php/DBManager.php';
$userMng = new UserManager();
$dbMng = new DBManager();
$message = null;
if($userMng->loggedinCheck()){
    header('Location:index.php');
    exit;
}

if(!empty($_POST["send_mail"])){
    $message = getErrorMessage();
    $birthday = "";
    if(!empty($message)){
        $user = new User();
        $birthday .= (string)filter_input(INPUT_POST,"year")."-";
        $birthday .= (string)filter_input(INPUT_POST,"month")."-";
        $birthday .= (string)filter_input(INPUT_POST,"day");

        $user->userId = (string)filter_input(INPUT_POST,"user_id");
        $user->userName = (string)filter_input(INPUT_POST,"user_name");
        $user->password = (string)filter_input(INPUT_POST,"password");
        $user->mailAddress = (string)filter_input(INPUT_POST,"mail_address");
        $user->gender = (string)filter_input(INPUT_POST,"gender");
        $user->prefecture = (string)filter_input(INPUT_POST,"prefecture");
        $user->message = 'よろしくお願いします';
        $user->birthday = $birthday;
        $user->token = $userMng->createToken();
        $user->tokenDeadLine = date('Y-m-d H-i-s' , strtotime('+30 minutes'));
        $message = $userMng->checkUserValue($user);
        if(empty($message)){
            $userMng->registerUser($user);
            header('Location:send_mail.html');
            exit;
        }else{
            $message = 'alert("'.$message.'")';
        }
    }else{
        $message = 'alert("'.$message.'")';
    }
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
function getErrorMessage(){
    if(empty($_POST["pass_conf"])){
        return 'パスワードが空です。';
    }
    if($_POST["pass_conf"] != $_POST["password"]){
        return 'パスワードが一致していません。';
    }
    if(empty($_POST["year"]) || empty($_POST["month"]) || empty($_POST["day"])){
        return '開催日時が空です。';
    }
    if(!checkdate($_POST["year"], $_POST["month"], $_POST["day"])){
        return '存在しない日付です。';
    }

    return NULL;
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>新規登録</title>
        <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/register.css">
        <link rel="stylesheet" href="css/service_title.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <script src="js/onload.js"></script>
        <script src="js/date.js"></script>
        <script>addOnload(function(){<?=$message?>})</script>
    </head>
    <body>
        <div id="contents">
            <div class="left_wrap">
                <div id="logo"></div>
                <div id="left_inner">
                    <form action="register.php" method="POST">
                        <p class="head">ユーザID</p>
                        <div class="input_box"><input type="text" name="user_id" placeholder="user1234" required <?php if(!empty($_POST["user_id"])){ echo 'value="'.$_POST["user_id"].'"'; } ?>></div>
                        <p class="head">名前</p>
                        <div class="input_box"><input type="text" name="user_name" placeholder="てすと" required <?php if(!empty($_POST["user_name"])){ echo 'value="'.$_POST["user_name"].'"'; } ?>></div>
                        <p class="head">パスワード</p>
                        <div class="input_box"><input type="password" name="password" placeholder="password" required></div>
                        <p class="head">確認パスワード</p>
                        <div class="input_box"><input type="password" name="pass_conf" placeholder="もう一度入力" required></div>
                        <p class="head">メールアドレス</p>
                        <div class="input_box"><input type="email" name="mail_address" placeholder="example@a.com" required <?php if(!empty($_POST["mail_address"])){ echo 'value="'.$_POST["mail_address"].'"'; } ?>></div>
                        <div id="radio_box"><p class="left">性別:</p><input id="man" type="radio" name="gender" value="男" required><label for="man">男</label><input id="woman" type="radio" name="gender" value="女"><label for="woman">女</label></div>
                        <div id="prefecture_wrap"><p class="left">都道府県:</p>
                            <select name="prefecture" id="prefecture">
                                <option>北海道</option>
                                <option>青森県</option>
                                <option>岩手県</option>
                                <option>宮城県</option>
                                <option>秋田県</option>
                                <option>山形県</option>
                                <option>福島県</option>
                                <option>茨城県</option>
                                <option>栃木県</option>
                                <option>群馬県</option>
                                <option>埼玉県</option>
                                <option>千葉県</option>
                                <option selected>東京都</option>
                                <option>神奈川県</option>
                                <option>新潟県</option>
                                <option>富山県</option>
                                <option>石川県</option>
                                <option>福井県</option>
                                <option>山梨県</option>
                                <option>長野県</option>
                                <option>岐阜県</option>
                                <option>静岡県</option>
                                <option>愛知県</option>
                                <option>三重県</option>
                                <option>滋賀県</option>
                                <option>京都府</option>
                                <option>大阪府</option>
                                <option>兵庫県</option>
                                <option>奈良県</option>
                                <option>和歌山県</option>
                                <option>鳥取県</option>
                                <option>島根県</option>
                                <option>岡山県</option>
                                <option>広島県</option>
                                <option>山口県</option>
                                <option>徳島県</option>
                                <option>香川県</option>
                                <option>愛媛県</option>
                                <option>高知県</option>
                                <option>福岡県</option>
                                <option>佐賀県</option>
                                <option>長崎県</option>
                                <option>熊本県</option>
                                <option>大分県</option>
                                <option>宮崎県</option>
                                <option>鹿児島県</option>
                                <option>沖縄県</option>
                            </select>
                        </div>
                        <div id="birthday_wrap"><p class="left">生年月日:</p>
                            <select name="year" id="year">
<?php                       for($i = 1900;$i<=(int)date('Y') - 18;$i++):?>
                                <option><?=$i?></option>
<?php                       endfor?>
                            </select>
                            <select name="month" id="month">
<?php                       for($i = 1;$i<=12;$i++):?>
                                <option><?=$i?></option>
<?php                       endfor?>
                            </select>
                            <select name="day" id="day">
<?php                       for($i = 1;$i<=(int)date('t');$i++):?>
                                <option><?=$i?></option>
<?php                       endfor?>
                            </select>
                        </div>
                        <button id="send_mail" type="submit" name="send_mail" value="send_mail">メール送信</button>
                        <div id="link_wrap"><p>ログインは<a href="login.php">こちら</a></p></div>
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

