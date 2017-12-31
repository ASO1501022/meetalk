<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>ヘルプ詳細</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/help-detail.css">
    <link rel="stylesheet" href="./css/tag.css">
    <link rel="stylesheet" href="./css/ad.css">

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
            <h2>ヘルプ詳細</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <?php 
            switch($_GET['index']) {
                case 1: ?>
                <p class="help-title">Q.部屋を作りたい</p>
                <div class="help-contents">
                    部屋は店舗を選択後、部屋作成より行えます。
                </div>
                <?php break; ?>

                <?php case 2: ?>
                <p class="help-title">Q.部屋に参加しようとしたら、申請中となった</p>
                <div class="help-contents">
                    部屋によっては部屋作成者の許可が必要な場合があります。部屋作成者が許可をするまで部屋に入ることは出来ません。
                </div>
                <?php break; ?>

                <?php case 3: ?>
                <p class="help-title">Q.部屋に参加できない</p>
                <div class="help-contents">
                    あなたの問題です。
                </div>
                <?php break; ?>

                <?php case 4: ?>
                <p class="help-title">Q.部屋にほかのユーザを招待したい</p>
                <div class="help-contents">
                    部屋にユーザを招待する場合は、部屋にある招待のボタンを押すことで招待するユーザを選べます。
                    <br><br>※招待できるユーザは部屋作成者のフレンドのみです。
                    <br>※部屋作成者以外はユーザを招待することはできません。
                </div>
                <?php break; ?>

                <?php case 11: ?>
                <p class="help-title">Q.それぞれ色が違うタグの違い</p>
                <div class="help-contents">
                    <div id="tag_1" class="help_content_wrap">
                        <p class="relation">タグ</p>                            
                        <p class="help_content_head">・それぞれ色が違うタグの違い</p>
                        <p>タグは下記の種類に分かれています。</p>
                        <div class="tag_inner hot">
                            <div class="tag_name"><p>タグ</p></div>
                            <div class="tag_num"><p class="a">20</p></div>
                        </div>
                        <span>：現在このタグが使われている部屋が20部屋以上ある </span><br>
                        <div class="tag_inner normal">
                            <div class="tag_name"><p>タグ</p></div>
                            <div class="tag_num"><p>10</p></div>
                        </div>
                        <span>：現在このタグが使われている部屋数が20部屋未満</span><br>
                        <div class="tag_inner general">
                            <div class="tag_name"><p>タグ</p></div>
                        </div>
                        <span>：ユーザのお気に入りや部屋についている標準的なタグ</span>
                    </div>
                </div>
                <?php break; ?>

                <?php case 12: ?>
                <p class="help-title">Q.タグの横についている数字</p>
                <div class="help-contents">
                    現在そのタグが使われている部屋の数を表しています。使われている部屋の数が20部屋以上になるとタグが赤くなります。
                </div>

                <?php case 21: ?>
                <p class="help-title">Q.自分の住んでいる地域から検索をしたい</p>
                <div class="help-contents">
                    検索後に左上に出てくる
                    <select class="item_info_select"><option selected="selected">東京都</option></select>
                    <select class="item_info_select"><option selected="selected">千代田区</option></select>
                    より検索出来ます。
                    また、スマートフォンの場合、現在位置からの検索も行えます。<br><br>
                    ※市町村を選択後、自動的に検索します。
                </div>
                <?php break; ?>

                <?php case 22: ?>
                <p class="help-title">Q.ユーザを検索したい</p>
                <div class="help-contents">
                    ユーザの検索は<a href="http://localhost/main/user_search.php">こちら</a>から行えます。
                </div>
                <?php break; ?>

                <?php case 31: ?>
                <p class="help-title">Q.退会したい</p>
                <div class="help-contents">
                    退会は<a href="http://localhost/main/deactive.php">こちら</a>から行えます
                </div>
                <?php break; ?>

                <?php default: ?>
                <p>そんなヘルプは存在しない</p>

            <?php } ?>

            <p class="return"><a href="./help.php">ヘルプに戻る</a></p>
        </div>


        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        </div>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
    </body>
</div>