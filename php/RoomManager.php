<?php
require_once "DBManager.php";
require_once "SearchAPIManager.php";
date_default_timezone_set('Asia/Tokyo');

class RoomManager{
    public function getRoomCountByRestaurantId($restaurantId){
        $dbm = new DBManager();
        return $dbm->getRoomCountByRestaurantId($restaurantId);
    }
    public function getUserStatusByRoomId($roomId,$userId){
        $dbm = new DBManager();
        return $dbm->getUserStatusByRoomId($roomId,$userId);
    }
    public function getJoinRoomByUserId($userId){
        $dbm = new DBManager();
        return $dbm->getJoinRoomByUserId($userId);
    }

    function checkRoomValue($room){
        //入力不備があった場合登録されない
        if(empty($room->userId)){
            return "ユーザーIDが未入力です。";
        }
        //部屋の名前
        if(empty($room->roomName)){
            return "部屋の名前が未入力です。";
        //部屋の名前の文字数をチェックする
        }else if(mb_strlen($room->roomName)<=1){
            return "部屋の名前は1文字以上です。";
        }else if(mb_strlen($room->roomName) >20){
            return "部屋の名前は20文字以内です。";
        }
        
        //部屋の説明文
        if(empty($room->explain)){
            return "部屋の説明が未入力です。";
        //部屋の説明文の文字数をチェックする
        }else if(mb_strlen($room->explain)<=9){
            return "部屋の情報は9文字以上です。";
        }else if(mb_strlen($room->explain) >1000){
            return "部屋の情報は1000文字以内です。";
        }
        
        //部屋の最大人数をチェックする&
        if(intval($room->maxMember) <= 1){
            return "部屋の最小人数は2人です。";
        }else if(intval($room->maxMember) > 100){
            return "部屋の最大人数は100人です。";
        }

        //レストランID
        if(is_null($room->restaurantId)){
            return "レストランIDが未入力です。";
        }

        //部屋の開催日
        $today = date("Y-m-d H:i");
        
        if(is_null($room->deadLine)){
            return "開催日が未入力です。";
        }else if($today >= $room->deadLine){
            return "開催日が過去になっています。";
        }

        //部屋の参加に申請が必要なのかどうか
        if(is_null($room->autoApply)){
            return "申請が必要かどうかが未入力です。";
        }

        

        //ご飯料金いくら必要か
        if(empty($room->budget)){
            return "予算が未入力です。";
        }else if(intval($room->budget) <= 0){
            return "予算は0円以上です";
        }else if(intval($room->budget) > 1000000){
            return "料金は100万円以内になります。";
        }
        //タグの数
        if(count($room->roomTagList) <= 0 || 10 < count($room->roomTagList)){
            return "部屋に登録できるタグの数は10個までです。";
        }


        return NULL;
    } 
    //メッセージ送信
    public function sendMessage($userId,$roomId,$message){

        $dbm = new DBManager;
        $dbm->insertMessage($userId,$roomId,$message);
    }
                
    //メッセージ取得
    public function getMessageListByRoomId($roomId){
        $dbm = new DBManager;

        $retList = array();
        $retList = $dbm->getMessageListByRoomId($roomId);
        
        return $retList;
    }
       
        
    //部屋情報を取得
    public function getRoomByRoomId($roomId){
        $dbm = new DBManager;
        
        $roomData = $dbm->getRoomByRoomId($roomId);
        $roomChat = $dbm->getMessageListByRoomId($roomId);
        $roomTag = $dbm->getTagListByRoomId($roomId);
        $roomUserStatus = $dbm->getUserStatusListByRoomId($roomId);
        
        if(!empty($roomData)){
            $room = new Room();

            $room->roomId = $roomData->roomId;
            $room->roomName = $roomData->roomName;
            $room->userId = $roomData->userId;
            $room->explain = $roomData->explain;
            $room->maxMember = $roomData->maxMember;
            $room->restaurantId = $roomData->restaurantId;
            $room->deadLine = $roomData->deadLine;
            $room->explain = $roomData->explain;
            $room->autoApply = $roomData->autoApply;
            $room->budget = $roomData->budget;
            $room->address = $roomData->address;
            $room->roomMessageList = $roomChat;
            $room->roomTagList = $roomTag;
            $room->roomUserStatusList = $roomUserStatus;

            return $room;
        }
    }

    //部屋に自動参加か確認する
    public function checkAutoApply($roomId){
        $dbm = new DBManager;

        $room = $this->getRoomByRoomId($roomId);
        return $room;
        
    }

    //ユーザが部屋に参加リクエストを送る
    public function joinRequestRoomByUserId($userId,$roomId){
        $dbm = new DBManager;
        
        $dbm->insertRoomUserStatus($roomId,$userId,1);
    }
           
    //部屋に入室
    public function joinRoomByUserId($userId,$roomId){
        $dbm = new DBManager;

        $checkAuto = $this->checkAutoApply($roomId);
        //配列を文字列へ変換
        $auto = $checkAuto->autoApply;
        $room = $this->getRoomByRoomId($roomId);
        $retList = null;
        $roomMeber = $this->_getMember($room->roomUserStatusList);
        if($roomMeber >= $room->maxMember){
            return ;
        }
        if($auto == 1){
            //参加するのに申請が必要
            $this->joinRequestRoomByUserId($userId,$roomId);
        }else{
            $userStatus = $this->getUserStatusByRoomId($roomId,$userId);
            if(!empty($userStatus->status)){
                if($userStatus->status == 3){
                    $dbm->updateRoomUserStatus($userId,$roomId,2);
                    //部屋の人数を更新
                    $retList = array();
                    $retList = $dbm->getUserStatusListByRoomId($roomId);
                }else if($userStatus->status != 0){
                    //自動で入室できる
                    $dbm->insertRoomUserStatus($roomId,$userId,2);
                    //部屋の人数を更新
                    $retList = array();
                    $retList = $dbm->getUserStatusListByRoomId($roomId);
                }
            }else{
                //自動で入室できる
                $dbm->insertRoomUserStatus($roomId,$userId,2);
                //部屋の人数を更新
                $retList = array();
                $retList = $dbm->getUserStatusListByRoomId($roomId);
            }
            return $retList;

        }
    }
    public function _getMember($_b){
        $_a = 1;
        foreach ($_b as $_c) {
            if($_c->status == 2) $_a++;
        }
        return $_a;
    }

    //部屋申請されたリクエストを許可する
    public function applyRequest($userId,$roomId){
        $dbm = new DBManager;

        $dbm->updateRoomUserStatus($userId,$roomId,2);
        //部屋の人数を更新
    }


    //部屋申請されたリクエストを拒否する
    public function rejectRequest($userId,$roomId){
        $dbm = new DBManager;

        $dbm->deleteUserByRoom($userId,$roomId);
    }

    //タグチェック
    private function checkTagNameToDB($tagName){
        $dbm = new DBManager;

        //タグの文字数確認
        if(mb_strlen($tagName)< 1){
            return;
        }else if(mb_strlen($tagName) > 15){
            return;
        }

        $retList = array();
        $retList = $dbm->getTagIdByName($tagName);
        if(!empty($retList)){
            return true;
        }else{
            return false;
        }
            
    }
    
    //部屋にタグを追加
    public function addTagForRoom($roomId,$tagId){
        $dbm = new DBManager;
        //$this->deleteAllTagByRoomId($roomId);
        
        $dbm->insertTagForRoom($roomId,$tagId);
    }

    //部屋に登録してあるタグを全て消去する
    private function deleteAllTagByRoomId($roomId){
        $dbm = new DBManager;

        $dbm->deleteAllTagByRoomId($roomId);
    }

    //タグ検索
    public function searchTagByName($tagName){
        $dbm = new DBManager;

        $retList = array();
        $retList = $dbm->getTagIdByName($tagName);
        
        return $retList;
        
    }

    //タグ作成
    public function addNewTag($tagName){
        $dbm = new DBManager;
        
        $lastTagId = $dbm->insertNewTag($tagName);
        return $lastTagId;
    }
    //部屋編集
    public function modifyRoom($room){
        $dbm = new DBManager;
        //$this->checkRoomValue($room);
        $this->deleteAllTagByRoomId($room->roomId);
        foreach($room->roomTagList as $key => $value){
            $tagName = $value;
                
            //同じタグがあるかどうかを確認する
            if($this->checkTagNameToDB($tagName)){
                    
                //存在したらタグを部屋に追加していく
                $tagIdarray = $this->searchTagByName($tagName);
                //配列のタグIDだけ取り出す
                $tg =(array_column($tagIdarray,'tagId'));
                //配列を文字列へ変換
                $tagId = implode($tg);
                // $tagId = serialize($tagIdarray);
                $this->addTagForRoom($room->roomId,$tagId);
            }else{
                //存在しなかったら新しくタグを作る
                $lastTagId = $this->addNewTag($tagName);
                $this->addTagForRoom($room->roomId,$lastTagId);
            }
                    
        }
        
        $dbm->updateRoom($room);


    }


    //部屋作成
    public function registerRoom($room){
        $dbm = new DBManager;
        $searchAPIMng = new SearchAPIManager;
        //入力されたものをチェックする
        //$this->checkRoomValue($room);
        //全て入力されると登録
        $restaurant = $searchAPIMng->searchRestaurantByRestaurantId($room->restaurantId);
        $room->address = $restaurant->address;
        $roomId = $dbm->insertRoom($room);
        foreach($room->roomTagList as $key => $value){
            $tagName = $value;
                
            //同じタグがあるかどうかを確認する
                if($this->checkTagNameToDB($tagName)){
                    
                //存在したらタグを部屋に追加していく
                $tagIdarray = $this->searchTagByName($tagName);
                //配列のタグIDだけ取り出す
                $tg =(array_column($tagIdarray,'tagId'));
                //配列を文字列へ変換
                $tagId = implode($tg);
                // $tagId = serialize($tagIdarray);
                $this->addTagForRoom($roomId,$tagId);
                    
            }else{
                //存在しなかったら新しくタグを作る
                $lastTagId = $this->addNewTag($tagName);
                $this->addTagForRoom($roomId,$lastTagId);

            }
        }
        return $roomId;
    }

    //部屋退出
    public function escapeUserFromRoom($userId,$roomId){
        $dbm = new DBManager;

        $dbm->deleteUserByRoom($userId,$roomId);

    }

    //部屋フレンド招待
    public function inviteFriendUserToRoom($friendUserId,$roomId){
        $dbm = new DBManager;

        $userId = $friendUserId;
        
        $dbm->insertRoomUserStatus($roomId,$userId,3);
        
    }

    //招待された部屋を許可する
    public function applyInvitationFromRoom($userId,$roomId){
        $dbm = new DBManager;

        $dbm->updateRoomUserStatus($userId,$roomId,2);
    }

    //招待された部屋を拒否する
    public function rejectInvitationFromRoom($userId,$roomId){
        $dbm = new DBManager;

        $dbm->deleteUserByRoom($userId,$roomId);
    }

    //部屋に入っているユーザをキックする
    public function kickUserFromRoom($userId,$roomId){
        $dbm = new DBManager;

        $dbm->updateRoomUserStatus($userId,$roomId,0);
    }

    //部屋が生きているか死んでいるか
    public function checkDeadLine($roomDeadLine){
        $dbm = new DBManager;
        
        $today = date('Y-m-d H:i:s');
        if($roomDeadLine > $today){
            return 0;
        }else if($today < date($roomDeadLine,strtotime('+24 hour'))){
            return 1;
        }else if(date($roomDeadLine ,strtotime('+24 hour')) < $today && $roomDeadLine != "1000-01-01 00:00:00" ){
            return 2;
        }else if($roomDeadLine == "1000-01-01 00:00:00"){
            return 3;
        }


    }
    //部屋を解散させるdeadlineを1000年1月1日 00:00:00にする
    public function dissolveRoom($roomId){
        $dbm = new DBManager;

        $dbm->updateDeadLineByRoomId($roomId);
    }

        
}

?>