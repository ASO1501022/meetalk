<?php
session_start();
require_once '../../php/UserManager.php';
require_once '../../php/RoomManager.php';
require_once '../../php/DBManager.php';
require_once '../../php/SearchRoomManager.php';
require_once '../../php/SearchAPIManager.php';
require_once '../../php/prefecture.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchRoomMng = new SearchRoomManager();
$searchAPIMng = new SearchAPIManager();
$sortArray = array("create_date DESC","budget ASC","budget DESC","dead_line ASC","dead_line DESC");
$prefecture = null;

if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$user = $userMng->getUserByUserId($_SESSION['user_id']);
//現在のページと前後のページ生成
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
//変数order生成
if(!empty($_GET['order'])){
    $order = $sortArray[$_GET['order']];
} else {
    $order = "create_date DESC";
}
if(empty($_GET['prefecture'])){
    $_GET['prefecture'] = $user->prefecture;
}
if(empty($_GET['page'])){
    $_GET['page'] = 0;
}
//ソート機能の変数生成
if(!empty($_GET['search_genre'])){
    if(empty($_GET['prefecture'])){
        $prefecture = $user->prefecture;
    }else{
        $prefecture = $_GET['prefecture'];
        if($prefecture == "全国"){
            $prefecture = null;
        }elseif(!empty($_GET['city'])){
            if($_GET['city'] != '全地域' && $_GET['city'] != 'all' ){
                $prefecture = $_GET['prefecture'] . $_GET['city'];
            } 
        }
    }
    switch ($_GET['search_genre']){
        case 'restaurant':
            if(!empty($_GET['latitude']) && !empty($_GET['longitude'])){
                $results = $searchAPIMng->searchRestaurantByCurrentPosition($_GET['latitude'], $_GET['longitude'], $_GET['page']+1);
            } else {
                $results = $searchAPIMng->searchRestaurantByRestaurantNameAndAddress($_GET['search_word'], $prefecture, $_GET['page']+1);
            }
            break;
        case 'tag':
            $results = $searchRoomMng->searchRoomByTagName($_GET['search_word'], $prefecture, $order, $_GET['page']);
            break;
        case 'room':
            if(!empty($_GET['restaurant_id'])){
                $results =  $searchRoomMng->searchRoomListByRestaurantId($_GET['restaurant_id'], $order, $_GET['page']);
            } else {
                $results = $searchRoomMng->searchRoom($_GET['search_word'], $prefecture, $order, $_GET['page']);
            }
            break;
        default:
            $results = null;
    }
} else {
    $results = null;
}
function getMember($_roomUserStatusList){
    $a = 1;
    foreach ($_roomUserStatusList as $roomUserStatus) {
        if($roomUserStatus->status == 2) $a++;
    }
    return $a;
}
//ページの文列字変換
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
function modifyDateToJpDate($_deadLine){
    return date('Y年n月j日G時i分', strtotime($_deadLine));
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

    <title>検索結果</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" href="./css/default.css">
    <link rel="stylesheet" href="./css/sp-default.css">
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/search.css">
    <link rel="stylesheet" href="./css/tag.css">
    <link rel="stylesheet" href="./css/ad.css">


    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    <script type="text/javascript" src="./js/area.js"></script>
    <script type="text/javascript" src="./js/order-change.js"></script>
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
            <h2>
            <?php if(!empty($_GET['search_word'])):?>
            <?php echo h($_GET['search_word'] . "の検索結果")?>
            <?php else:?>
            全ての部屋
            <?php endif?>
            </h2>
            <span class="button menu-button-right"></span>
        </div>
<!--メインコンテンツ-->
        <div class="wrap-contents">
<!--検索ボックス-->
            <?php if(empty($_GET['restaurant_id']) && empty($_GET['latitude'])):?>
            <div class="wrap-search-form">
                
                <form metod="get">
                    <div class="inner-search-form">
                        <select id="prefecture" name="prefecture">
                            <?php foreach((array)$prefectures as $prefecture):?>
                            <?php if(!empty($_GET["prefecture"] && $_GET["prefecture"] == $prefecture)):?>
                                <option selected><?=h($prefecture)?></option>
                            <?php else:?>
                                <option><?=h($prefecture)?></option>
                            <?php endif?>
                            <?php endforeach?>
                        </select>
                        <select id="city" name="city">
                            <option>全地域</option>
                        </select>
                        <input type="hidden" name="search_genre" value="<?=h($_GET['search_genre'])?>">
                        <input type="hidden" name="search_word" value="<?=h($_GET['search_word'])?>">
                        <input type="hidden" name="page" value="0">
                        <button type="submit" name="search" id="button-image"><img border="0" src="./img/search.png" width="25px" height="25px" alt="イラスト1"></button>
                    </div>
                </form>
            </div>
            <?php endif?>
            
<!--ここからは検索結果です。検索結果はroom,restaurant二つの情報を表示していきます-->
<!--それぞれで出力する内容は変わってくるので、判断する箇所が出てきます。-->
<!--selectの内容をroomとrestaurantで分けましょう。　検索結果は全てここで表示します。-->
            <div class="wrap-select-sort">
<?php if(!empty($_GET['search_genre'])):
    switch ($_GET['search_genre']): ?>
<?php case 'restaurant': ?>
                <form>
                    <div class="inner-select-sort">
                        <select name="order" id="order">
                            <!--店舗の検索結果の場合　ソート内容-->
                            <option value="">なし</option>
                        </select>
                    </div>
                </form>
<?php break; ?>
<?php case 'room': ?>
<?php case 'tag': ?>
                <form method="get">
                    <div class="inner-select-sort">         
                        <select name="order" id="order">
                            <!--部屋の検索結果の場合　ソート内容-->
                            <option value="0" <?php if($order == 0): ?>selected <?php endif?>>最近出来た部屋順</option>
                            <option value="1" <?php if($order == 1): ?>selected <?php endif?>>予算が安い順</option>
                            <option value="2" <?php if($order == 2): ?>selected <?php endif?>>予算が高い順</option>
                            <option value="3" <?php if($order == 3): ?>selected <?php endif?>>開催日時が近い順</option>
                            <option value="4" <?php if($order == 4): ?>selected <?php endif?>>開催日時が遅い順</option>
                        </select>
                    </div>
                    <input type="hidden" name="search_genre" value="<?=h($_GET['search_genre'])?>">
                    <?php if(!empty($_GET['search_word'])):?>
                    <input type="hidden" name="search_word" value="<?=h($_GET['search_word'])?>">
                    <?php elseif(!empty($_GET['restaurant_id'])):?>
                    <input type="hidden" name="search_word" value="<?=h($_GET['restaurant_id'])?>">
                    <?php endif?>
                    <input type="hidden" name="page" value="0">
                </form>
<?php endswitch ?>
                
<?php endif ?>
            </div>
<!--結果一覧　テストデータ部分にそのまま情報を出力してください-->
<!--結果ルーム-->
<?php if(!empty($_GET['search_genre'])):?>
    <?php if(empty($_GET['search_word']) && empty($_GET['restaurant_id']) && empty($_GET['latutude']) && empty($_GET['longitude'])):?>
        <div class="wrap-search-result">
            <div class="title-search-result">
                <p>該当項目なし</p>
            </div>
        </div>
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
    <?php exit?>
    <?php endif?>
    <?php switch ($_GET['search_genre']):
        //部屋をユーザが検索してきた
        case 'room': 
        case 'tag': ?>
            <div class="wrap-search-result">
                <div class="title-search-result">
                <?php if(!empty($_GET['search_word'])):?>
                    <p>"<?=h($_GET['search_word'])?>"の検索結果</p>
                <?php else:?>
                    <p>全件から検索した結果</p>
                <?php endif?>
                </div>
                
<!--foreachで検索結果のリストの中身を回して一件ずつ出力してください-->
<?php foreach((array)$results as $result):?>
<?php $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($result->restaurantId) ?>
                <div class="wrap-result-card">
                    <!--この店舗のIDをGETに指定してhrefに出力しましょう-->
                    <a href="./room_info.php?room_id=<?=h($result->roomId)?>">
                        <div class="title-result-card">
                            <!--部屋のタイトルです。-->
                            <p><?=h($result->roomName)?></p>
                        </div>
                        <div class="inner-result-card">
                            <!--レスポンスのアドレスをurl()の中に入れてあげてください-->
<?php if(is_string($restaurant->image->shop_image1)):?>
                            <div id="thumbnail" style="background-image:url(<?=h($restaurant->image->shop_image1) ?>)"></div>
<?php else: ?>
                            <div id="thumbnail" style="background-image:url(./img/no_image.png"></div>
<?php endif ?>
                            <div class="result-card-content">
                                <!--部屋の細かい情報です。それぞれ一行ずつ出力しましょう-->
                                <p>人数:<?=h(getMember($result->roomUserStatusList))?>人/<?=h($result->maxMember)?>人</p>
                                <p>開催日時:<?=h(modifyDateToJpDate($result->deadLine))?></p>
                                <p>平均予算:<?=h($result->budget)?>円</p>
                            </div>
                        </div>
<!--タグリストをforeachで回して全てのタグを出力しましょう-->
<?php foreach($result->roomTagList as $tag): ?>
                        <!--タグリストの名前(name)をGETに指定してhrefに出力しましょう-->
                        <div class="tag_inner general">
                            <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($tag->tagName)?>&prefecture=<?=h($user->prefecture)?>"><p id="popurarity-room-tag"><?=h($tag->tagName) ?></p></a></div>
                        </div>
<?php endforeach; ?>
                    </a>
                </div>
<?php endforeach; ?>
            </div>
<?php break; ?>

<!--結果レストラン-->
<?php case 'restaurant': ?>
            <div class="wrap-search-result">
                <div class="title-search-result">
                    <?php if(empty($_GET['latitude']) && empty($_GET['longitude'])):?>
                    <p>"<?=h($_GET['search_word'])?>"の検索結果</p>
                    <?php else:?>
                    <p>あなたの周辺のお店の検索結果</p>
                    <?php endif?>
                </div>
                
<!--foreachで検索結果のリストの中身を回して一件ずつ出力してください-->
<?php foreach((array)$results as $result): ?>
                <div class="wrap-result-card">
                    <!--この店舗のIDをGETに指定してhrefに出力しましょう-->
                    <a href="./restaurant_info.php?restaurant_id=<?=h($result->restaurantId)?>">
                        <div class="title-result-card">
                            <!--レストラン名です。-->
                            <p><?=h($result->name)?></p>
                        </div>
                        <div class="inner-result-card">
                            <!--レスポンスのアドレスをurl()の中に入れてあげてください-->
<?php if(is_string($result->image->shop_image1)):?>
<?php var_dump($result->image->shop_image1);?>
                            <div id="thumbnail" style="background-image:url(<?=h($result->image->shop_image1) ?>)"></div>
<?php else: ?>
                            <div id="thumbnail" style="background-image:url(./img/no_image.png"></div>
<?php endif ?>
                            <div class="result-card-content">
                                <!--部屋の細かい情報です。それぞれ一行ずつ出力しましょう-->
                                <p>電話番号:<?=h($result->tel)?></p>
                                <p>休業日:<?=$result->holiday?></p>
                                <p>平均予算:<?=h($result->budget)?>円</p>
                            </div>
                        </div>
<!--タグリストをforeachで回して全てのタグを出力しましょう-->
<?php foreach($result->categoryName as $tag): ?>
                        <!--タグリストの名前(name)をGETに指定してhrefに出力しましょう-->
                        <div class="tag_inner general">
                            <div class="tag_name"><a href="./search.php?search_genre=tag&search_word=<?=h($tag)?>&prefecture=<?=h($user->prefecture)?>"><p id="popurarity-room-tag"><?=h($tag)?></p></a></div>
                        </div>
<?php endforeach; ?>
                    </a>
                </div>
<?php endforeach; ?>
            </div>
<?php break; ?>

<?php default: ?>

<?php endswitch ?>
<?php endif ?>

            <div class="wrap-page-number">
                <ul>
                    <li id='num'><a href="./search.php?<?=h(getUnsetPageQueryString())?>page=<?=h($prevPage)?>">＜</a></li>
<?php
if($curPage == 0){
    $leftPage = 0;
}elseif($curPage == 1) {
    $leftPage = 0;
}elseif($curPage == 2){
    $leftPage = 0;
}else{
    $leftPage = $curPage -2;
}

?>
<?php for($i = $leftPage; $i < $leftPage + 5; $i++): ?>
<?php     if($i == $curPage):?>
                    <li id='num-selected'><a href="./search.php?<?=h(getUnsetPageQueryString())?>page=<?=h($i)?>"><?=h($i+1)?></a></li>
<?php     else: ?>
                    <li id='num'><a href="./search.php?<?=h(getUnsetPageQueryString())?>page=<?=h($i)?>"><?=h($i+1)?></a></li>
<?php     endif?>
<?php endfor ?>
                    <li id='num'><a href="./search.php?<?=h(getUnsetPageQueryString())?>page=<?=h($nextPage)?>">＞</a></li>
                </ul>
            </div>
        </div>
        
        <div id="ad-footer">
            <?php include '../../component/sp/ad.php'; ?>
        </div>
        <script type="text/javascript" src="./js/sp-slidemenu.js"></script>
        <script type="text/javascript" src="./js/slidemenu-right.js"></script>
        
    </body>
</div>