<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/RoomManager.php';
require_once '../../php/DBManager.php';
require_once '../../php/SearchRoomManager.php';
require_once '../../php/SearchAPIManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchRoomMng = new SearchRoomManager();
$searchAPIMng = new SearchAPIManager();

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
        if($_POST['gender'] == 'man'){
            $user->gender = '男';
        } else {
            $user->gender = '女';
        }
        $user->prefecture = (string)filter_input(INPUT_POST,"prefecture");
        $user->birthday = $birthday;
        $user->message = 'よろしくお願いします';
        $user->token = $userMng->createToken();
        $user->tokenDeadLine = date('Y-m-d H-i-s' , strtotime('+30 minute'));
        $message = $userMng->checkUserValue($user);
        if(is_null($message)){
            $userMng->registerUser($user);
            header('Location:./send_mail.html');
            exit;
        }else{
            var_dump($message);
            exit;
        }
    }else{
        $message = $message;
    }
}
function getErrorMessage(){
    if(empty($_POST["retype_password"])){
        return 'パスワードが空です';
    }
    if($_POST["retype_password"] != $_POST["password"]){
        return 'パスワードが一致していません';
    }
    if(empty($_POST["year"]) || empty($_POST["month"]) || empty($_POST["day"])){
        return '開催日時が空です';
    }
    if(!checkdate($_POST["year"], $_POST["month"], $_POST["day"])){
        return '存在しない日付です';
    }

    return NULL;
}

// 出力の際に必ずこの関数を通して出力する
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>登録</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/.sp-default.css">
    <link rel="stylesheet" href="./css/user-register.css">
    <link href="https://fonts.googleapis.com/css?family=Capriola" rel="stylesheet">
</head>


<body>
    <div class="main">
        <p class="name">meetalk</p>
        <div class="register-form">
            <form action="user_register.php" method="post">
                <p>ユーザID</p>
                <input type="text" name="user_id" placeholder="your ID" required>
                <p>ニックネーム</p>
                <input type="text" name="user_name" placeholder="your name" required>
                <p>パスワード</p>
                <input type="password" name="password" placeholder="password" required>
                <p>パスワードの確認</p>
                <input type="password" name="retype_password" placeholder="retype password" required>
                <p>メールアドレス</p>
                <input type="email" name="mail_address" placeholder="example@matching.com" required>
                <p>性別</p>
                <div class="gender-select">
                    <input type="radio" name="gender" value="man" id="man" checked><label for="man" class="switch-man">男</label>
                    <input type="radio" name="gender" value="woman" id="woman"><label for="woman" class="switch-woman">女</label>
                </div>
                <p id="prefecture-sentence">都道府県</p>
                <select class="prefecture" name="prefecture">
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
                <p id="prefecture-sentence">誕生日</p>
                <div class="birthday">
                    <select name="year" id="year">
<?php                       for($i = 1900;$i<=(int)date('Y');$i++):?>
<?php                           if($i != (int)date('Y')):?>
                        <option><?=$i?></option>
<?php                           else:?>
                        <option selected><?=$i?></option>
<?php                           endif?>
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
                <button type="submit" name="send_mail" value="send">メール送信</button>
            </form>
        </div>


        <p class="login"><a href="./login.php">登録済みの方はこちら</a></p>
    </div>
</body>
</html>