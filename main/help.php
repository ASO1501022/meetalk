<?php
session_start();
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
        <title>ヘルプ - meetalk</title>
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
        <link rel="stylesheet" href="css/help.css">
        <link rel="stylesheet" href="css/invited.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
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
                        <p id="main_center_title">ヘルプ</p>
                        <p class="help_head">部屋</p>
                        <ul>
                            <li><a href="#room_1">部屋を作りたい</a></li>
                            <li><a href="#room_2">部屋に参加しようとしたら、申請中となった</a></li>
                            <li><a href="#room_2">部屋に参加できない</a></li>
                            <li><a href="#room_3">部屋にほかのユーザを招待したい</a></li>
                        </ul>
                        <p class="help_head">タグ</p>
                        <ul>
                            <li><a href="#tag_1">それぞれ色が違うタグの違い</a></li>
                            <li><a href="#tag_2">タグの横についている数字</a></li>
                        </ul>
                        <p class="help_head">検索</p>
                        <ul>
                            <li><a href="#search_1">自分の住んでいる地域から検索をしたい</a></li>
                            <li><a href="#search_2">ユーザを検索したい</a></li>
                        </ul>
                        <p class="help_head">ユーザ</p>
                        <ul>
                            <li><a href="#search_1">退会したい</a></li>
                        </ul>
                        <div id="room_1" class="help_content_wrap">
                            <p class="relation">部屋</p>
                            <p class="help_content_head">・部屋を作りたい</p>
                            <p>部屋は店舗を選択後、<span class="button create">部屋作成</span>より行えます。</p>
                        </div>
                        <div id="room_2" class="help_content_wrap">
                            <p class="relation">部屋</p>                            
                            <p class="help_content_head">・部屋に参加しようとしたら申請中となった</p>
                            <p>部屋によっては部屋作成者の許可が必要な場合があります。部屋作成者が許可をするまで部屋に入ることは出来ません。</p>
                        </div>
                        <div id="room_3" class="help_content_wrap">
                            <p class="relation">部屋</p>                            
                            <p class="help_content_head">・部屋にほかのユーザを招待したい</p>
                            <p>部屋にユーザを招待する場合は、部屋にある<span class="button invite">招待</span>のボタンを押すことで招待するユーザを選べます。
                            <br><br>※招待できるユーザは部屋作成者のフレンドのみです。
                            <br>※部屋作成者以外はユーザを招待することはできません。
                            </p>
                        </div>
                        <div id="tag_1" class="help_content_wrap">
                            <p class="relation">タグ</p>                            
                            <p class="help_content_head">・それぞれ色が違うタグの違い</p>
                            <p>タグは下記の種類に分かれています。</p>
                            <div class="tag_inner hot">
                                <div class="tag_name"><p>タグ</p></div>
                                <div class="tag_num"><p class="a">20</p></div>
                            </div>
                            <span>：現在このタグが使われている部屋が20部屋以上ある(人気のタグ)</span><br>
                            <div class="tag_inner normal">
                                <div class="tag_name"><p>タグ</p></div>
                                <div class="tag_num"><p>10</p></div>
                            </div>
                            <span>：現在このタグが使われている部屋数が20部屋未満</span><br>
                            <div class="tag_inner tag">
                                <div class="tag_name"><p>タグ</p></div>
                            </div>
                            <span>：店舗や部屋についているタグ</span>
                        </div>
                        <div id="tag_2" class="help_content_wrap">
                            <p class="relation">タグ</p>                            
                            <p class="help_content_head">・タグの横についている数字</p>
                            <p>現在そのタグが使われている部屋の数を表しています。使われている部屋の数が20部屋以上になるとタグが赤くなります</p>
                        </div>
                        <div id="search_1" class="help_content_wrap">
                            <p class="relation">検索</p>                            
                            <p class="help_content_head">・自分の住んでいる地域から検索をしたい</p>
                            <p>検索後に左上に出てくる
                                <select class="item_info_select"><option>東京都</option></select>
                                <select class="item_info_select"><option>千代田区</option></select>
                                より検索出来ます。
                                また、スマートフォンの場合、現在位置からの検索も行えます。<br><br>
                                ※市町村を選択後、自動的に検索します。
                            </p>
                        </div>
                        <div id="search_2" class="help_content_wrap">
                            <p class="relation">検索</p>                            
                            <p class="help_content_head">・ユーザを検索したい</p>
                            <p>ユーザの検索は<a href="user_search.php">こちら</a>から行えます。</p>
                        </div>
                        <div id="user_1" class="help_content_wrap">
                            <p class="relation">ユーザ</p>                            
                            <p class="help_content_head">・退会したい</p>
                            <p>退会は<a href="unsubscribe.php">こちら</a>から行えます。</p>
                        </div>
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