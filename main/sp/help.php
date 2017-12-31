<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>ヘルプ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/help.css">
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
            <h2>ヘルプ</h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
            <div class="wrap-help-room">
                <p class="title-help">部屋</p>
                <div class="contents-help">
                    <p class="line-help"><a href="./help_detail.php?index=1">部屋を作りたい</a></p>
                    <p class="line-help"><a href="./help_detail.php?index=2">部屋に参加しようとしたら、申請中になった</a></p>
                    <p class="line-help"><a href="./help_detail.php?index=3">部屋に参加できない</a></p>
                    <p class="line-help"><a href="./help_detail.php?index=4">部屋に他のユーザを招待したい</a></p>
                </div>
            </div>

            <div class="wrap-help-tag">
                <p class="title-help">タグ</p>
                <div class="contents-help">
                    <p class="line-help"><a href="./help_detail.php?index=11">それぞれ色が違うタグの違い</a></p>
                    <p class="line-help"><a href="./help_detail.php?index=12">タグの横についている数字</a></p>
                </div>
            </div>

            <div class="wrap-help-search">
                <p class="title-help">検索</p>
                <div class="contents-help">
                    <p class="line-help"><a href="./help_detail.php?index=21">自分の住んでいる地域から検索をしたい</a></p>
                    <p class="line-help"><a href="./help_detail.php?index=22">ユーザを検索したい</a></p>
                </div>
            </div>

            <div class="wrap-help-user">
                <p class="title-help">ユーザ</p>
                <div class="contents-help">
                    <p class="line-help"><a href="./help_detail.php?index=31">退会したい</a></p>
                </div>
            </div>


        </div>
        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
    </body>
</div>