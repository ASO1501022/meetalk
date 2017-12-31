<?php
session_start();
require_once '../php/UserManager.php';
$userMng = new UserManager();
if(!$userMng->loggedinCheck()){
    header('Location:login.php');
    exit;
}
$userMng->logout();
header('Location:login.php');
exit;
?>