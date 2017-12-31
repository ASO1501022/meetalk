<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/DBManager.php';
$userMng = new UserManager();
$dbMng = new DBManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$user = $userMng->getUserByUserId($_SESSION['user_id']);
if(!empty($_GET['search_id'])){
    $users = $dbMng->getUserByString($_GET['search_id'],$_GET["page"]);
}
if(!empty($_GET['page'])){
    if($_GET['page'] >= 0) {
        $prevPage = $_GET['page'] - 1;
        $curPage = $_GET['page'];
        $nextPage = $_GET['page'] + 1;
    } else {
        $prevPage = 0;
        $curPage = 0;
        $nextPage = 1;
    }
}else {
    $prevPage = 0;
    $curPage = 0;
    $nextPage = 1;
}
function getUnsetPageQueryString(){
    $a = "";
    if(!empty($_SERVER["QUERY_STRING"])){
        $querStrings = explode('&',$_SERVER["QUERY_STRING"]);
        foreach ($querStrings as $queryString) {
            $b = explode('=',$queryString);
            if($b[0] == "page") continue;
            $a .= $queryString."&";
        }
    }
    return $a;
}
function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
<!doctype html>

<html lang="ja">
<head>
    <meta charset="utf-8">

    <title>フレンド一覧</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/friend-search.css">
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
            <h2>ユーザー検索</h2>
            <span class="button menu-button-right"></span>
        </div>

        <div class="wrap-contents">
            <div class="wrap-search-form">
                <form action="friend_search.php" method="get">
                    <div class="inner-search-form">
                        <input type="search" name="search_id" placeholder="ユーザIDを検索">
                        <input type="hidden" name="page" value="0">
                        <button type="submit" name="search" id="button-image">
                            <img border="0" src="./img/search.png" width="25px" height="25px" alt="イラスト1">
                        </button>
                    </div>
                </form>
            </div>
            <?php if(!empty($_GET['search_id'])):?>
            <div class="title-search-result">
                <p><?=h($_GET['search_id'])?>の検索結果</p>
            </div>
            <?php else:?>
            <div class="title-search-result">
                <p>ユーザーIDを入力してください</p>
            </div>
            <?php endif?>
            <?php if(!empty($users)): ?>
            <?php foreach($users as $user):?>
            <a href="./profile.php?user_id=<?=h($user->userId)?>">
            <div class="member">
                <img src="../img/user_img/<?=h($user->imageName)?>">
                <div class="user-info">
                    <p><?=h($user->userName)?></p>
                    <p>ID:<?=h($user->userId)?></p>
                </div>
            </div>
            </a>
            <?php endforeach?>
            <?php endif?>

            <div class="wrap-page-number">
                <ul>
                    <li id='num'><a href="./friend_search.php?<?=h(getUnsetPageQueryString())?>page=<?=h($prevPage)?>">＜</a></li>
<?php
if($curPage <= 2){
    $leftPage = 0;
}else{
    $leftPage = $curPage -2;
}
?>
<?php for($i = $leftPage; $i < $leftPage + 5; $i++): ?>
<?php     if($i == $curPage):?>
                    <li id='num-selected'><a href="./friend_search.php?<?=h(getUnsetPageQueryString())?>page=<?=h($i)?>"><?=h($i+1)?></a></li>
<?php     else: ?>
                    <li id='num'><a href="./friend_search.php?<?=h(getUnsetPageQueryString())?>page=<?=h($i)?>"><?=h($i+1)?></a></li>
<?php     endif?>
<?php endfor ?>
                    <li id='num'><a href="./friend_search.php?<?=h(getUnsetPageQueryString())?>page=<?=h($nextPage)?>">＞</a></li>
                </ul>
            </div>
        </div>

        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
    </body>
</div>