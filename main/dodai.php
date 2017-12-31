<?php
require_once '../php/UserManager.php';
require_once '../php/RoomManager.php';
require_once '../php/DBManager.php';
require_once '../php/SearchAPIManager.php';
$userMng = new UserManager();
$roomMng = new RoomManager();
$dbMng = new DBManager();
$searchAPIMng = new SearchAPIManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}


function h($a){
    return htmlspecialchars($a,ENT_QUOTES);
}
?>
