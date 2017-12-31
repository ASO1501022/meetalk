<?php
if(!isset($_SESSION)){
    session_start();
}
set_error_handler(
    function ($errno, $errstr, $errfile, $errline) {
        throw new ErrorException(
            $errstr, 0, $errno, $errfile, $errline
        );
    }
);
try{
    require_once '../../php/RoomManager.php';
    $roomId = $_POST["room_id"];
    $roomMng = new RoomManager;
    $roomMng->rejectInvitationFromRoom($_SESSION["user_id"],$roomId);
    echo "success";
}catch(Exception $e){
    echo $_POST["room_id"];
}
?>