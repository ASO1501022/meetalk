<?php
 //テーブル用のクラスを読み込む
 date_default_timezone_set('Asia/Tokyo');

 require_once 'Friend.php';
 require_once 'History.php';
 require_once 'Restaurant.php';
 require_once "RoomMessage.php";
 require_once "Room.php";
 require_once "RoomTag.php";
 require_once "RoomUserStatus.php";
 require_once "User.php";
 require_once "Favorite.php";
 class DBManager{
    // private $userId = "root";
    // private $dbHost = "localhost";
    // private $password = "";
    // private $dbName = "project";

    // private $userId = "project";
    // private $dbHost = "localhost";
    // private $password = "haru1224";
    // private $dbName = "project";

    private $userId = "meetalk_db";
    private $dbHost = "mysql1.php.xdomain.ne.jp";
    private $password = "haru1224";
    private $dbName = "meetalk_db";


    private $pdo;
    public function unsubscribeUser($userId){
        try{
            $this->dbConnect();
            $stmt = $this->pdo->prepare('
                UPDATE user SET user_status = -1 WHERE user_id = :user_id
            ');
            $currentDate = date('Y-m-d');
            $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
            $stmt->execute();
            $this->dbDisconnect();
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }
    }
    public function getJoinRoomByUserId($userId){
        try{
            $this->dbConnect();
            $stmt = $this->pdo->prepare('
                SELECT DISTINCT room.room_id,room_name,room.user_id,room_explain,max_member,restaurant_id,dead_line,budget,address,auto_apply
                FROM room,room_user_status
                WHERE room.dead_line + interval 24 hour > :current_date AND
                (room.user_id = :create_user_id OR (room_user_status.user_id = :user_id AND (room_user_status.status = 1 OR room_user_status.status = 2 OR room_user_status.status = 3))
                )
            ');
            $currentDate = date('Y-m-d');
            $stmt->bindValue(':current_date',$currentDate,PDO::PARAM_STR);
            $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
            $stmt->bindValue(':create_user_id',$userId,PDO::PARAM_STR);
            $stmt->execute();
            $retList = null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room();
                $room->roomId = $row["room_id"];
                $room->roomName = $row["room_name"];
                $room->userId = $row["user_id"];
                $room->explain = $row["room_explain"];
                $room->maxMember = $row["max_member"];
                $room->restaurantId = $row["restaurant_id"];
                $room->deadLine = $row["dead_line"];
                $room->budget = $row["budget"];
                $room->address = $row["address"];
                $room->autoApply = $row["auto_apply"];
                $room->roomTagList = $this->getTagListByRoomId($room->roomId);
                $room->roomChatMessageList = $this->getMessageListByRoomId($room->roomId);
                $room->roomUserStatusList = $this->getUserStatusListByRoomId($room->roomId);
                $retList[] = $room;
            }
            $this->dbDisconnect();
            return $retList;
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }
    }
    public function deleteToken($userId){
        try{
            $this->dbConnect();
            $stmt = $this->pdo->prepare('
                UPDATE user SET token = NULL WHERE user_id = :user_id
            ');
            $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
            $stmt->execute();
            $this->dbDisconnect();
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }
    }
    // public function getMeInviteFriendListByUserId($userId){
    //     try{
    //         $this->dbConnect();
    //         $stmt = $this->pdo->prepare('
    //              SELECT *
    //              FROM user a
    //              WHERE a.user_id IN (SELECT b.user_id FROM friend b
    //                 WHERE friend_user_id = :user_id
    //                 AND status = 0
    //                 )
    //         ');
    //         $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
    //         $stmt->execute();
    //         $retList = null;
    //         while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    //             $user = new User();
    //             $user->userId = $row["user_id"];
    //             $user->userName = $row["user_name"];
    //             $user->password = $row["password"];
    //             $user->mailAddress = $row["mail_address"];
    //             $user->prefecture = $row["prefecture"];
    //             $user->birthday = $row["birthday"];
    //             $user->gender = $row["gender"];
    //             $user->registerDate = $row["register_date"];
    //             $user->message = $row["message"];
    //             $user->userStatus = $row["user_status"];
    //             $user->imageName = $row["image_name"];
    //             $retList[] = $user;
    //         }
    //         $this->dbDisconnect();
    //         return $retList;
    //     }catch (PDOException $e){
    //         print('挿入に失敗'.$e->getMessage());
    //     }
    // }
    public function searchTrendRestaurant(){
        try{
            $this->dbConnect();
            $today = date("Y-m-d H:i:s");
            $stmt = $this->pdo->prepare('
                 SELECT restaurant_id,COUNT(*) room_number
                 FROM room
                 WHERE :today < dead_line
                 GROUP BY restaurant_id
                 ORDER BY room_number DESC
                 LIMIT 20
            ');
            $stmt->bindValue(':today',$today,PDO::PARAM_STR);
            $stmt->execute();
            $retList = null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $restaurant = new Restaurant();
                $restaurant->restaurantId = $row["restaurant_id"];
                $restaurant->roomNumber = $row["room_number"];
                $retList[] = $restaurant;
            }
            $this->dbDisconnect();
            return $retList;
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }
    }
    public function searchFewTrendRestaurant(){
        try{
            $this->dbConnect();
            $today = date("Y-m-d H:i:s");
            $stmt = $this->pdo->prepare('
                 SELECT restaurant_id,COUNT(*) room_number
                 FROM room
                 WHERE :today < dead_line
                 GROUP BY restaurant_id
                 ORDER BY room_number DESC
                 LIMIT 3
            ');
            $stmt->bindValue(':today',$today,PDO::PARAM_STR);
            $stmt->execute();
            $retList = null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $restaurant = new Restaurant();
                $restaurant->restaurantId = $row["restaurant_id"];
                $restaurant->roomNumber = $row["room_number"];
                $retList[] = $restaurant;
            }
            $this->dbDisconnect();
            return $retList;
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }
    }
    public function getRoomByAddress($address,$page){
        try{
            $this->dbConnect();
            $today = date("Y-m-d H:i:s");
            $address = '%'.$address.'%';
            $page = $page*10;
            $stmt=$this->pdo->prepare('
                SELECT *
                FROM room
                WHERE :today < dead_line
                AND address LIKE :address
                LIMIT :page,10
            ');
            $stmt->bindValue(':today',$today,PDO::PARAM_STR);
            $stmt->bindParam(':address',$address,PDO::PARAM_STR);
            $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            $stmt->execute();
            $retList = null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room();
                $room->roomId = $row["room_id"];
                $room->roomName = $row["room_name"];
                $room->userId = $row["user_id"];
                $room->explain = $row["room_explain"];
                $room->maxMember = $row["max_member"];
                $room->restaurantId = $row["restaurant_id"];
                $room->deadLine = $row["dead_line"];
                $room->budget = $row["budget"];
                $room->address = $row["address"];
                $room->autoApply = $row["auto_apply"];
                $room->roomTagList = $this->getTagListByRoomId($room->roomId);
                $room->roomChatMessageList = $this->getMessageListByRoomId($room->roomId);
                $room->roomUserStatusList = $this->getUserStatusListByRoomId($room->roomId);
                $retList[] = $room;
            }
            $this->dbDisconnect();
            return $retList;
        }catch(PDOException $e) {
            print('検索失敗。'.$e->getMessage());
            throw $e;
        }
    }
//松永
    //接続メソッド
    private function dbConnect(){
        try{
            $this->pdo = new PDO('mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName . ';charset=utf8', $this->userId,$this->password,
            array(PDO::ATTR_EMULATE_PREPARES => false));
        }catch(PDOException $e){
            print('データベース接続失敗。'.$e->getMessage());
            throw $e;
        }
    }
    //切断メソッド
    private function dbDisConnect(){
        unset($pdo);  
    }
   public function getRoomCountByRestaurantId($restaurantId){
        try{
            $this->dbConnect();
            $today = date("Y-m-d H:i:s");
            $stmt = $this->pdo->prepare('
            SELECT COUNT(*) cnt FROM room WHERE restaurant_id = :restaurant_id AND :today < dead_line
            ');
            $stmt->bindParam(':today',$today,PDO::PARAM_STR);
            $stmt->bindValue(':restaurant_id',$restaurantId,PDO::PARAM_STR);
            $stmt->execute();
            
            $retList = array();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!empty($row)){
                return $row["cnt"];
            }
            $this->dbDisconnect();

            return $retList;
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }
    }

    public function checkFavoriteRestaurant($restaurantId,$userId){
        try{
            $this->dbConnect();
            $stmt = $this->pdo->prepare('
                 SELECT *
                 FROM favorite
                 WHERE restaurant_id = :restaurant_id
                 AND user_id = :user_id
            ');
            
            $stmt->bindValue(':restaurant_id',$restaurantId,PDO::PARAM_STR);
            $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->dbDisconnect();
            if(!empty($row)){
                return true;
            }
            return false;
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }
    }

    public function getRoomByRoomNameAndAddress($roomName,$address,$order,$page){
        try{
            $this->dbConnect();
            $roomName = '%'.$roomName.'%';
            $address = '%'.$address.'%';
            $page = $page*10;
            $today = date("Y-m-d H:i:s");
            $stmt=$this->pdo->prepare('
                SELECT *
                FROM room
                WHERE room_name LIKE :room_name
                AND :today < dead_line
                AND address LIKE :address
                ORDER BY '.$order.'
                LIMIT :page,10
            ');
            $stmt->bindParam(':room_name',$roomName,PDO::PARAM_STR);
            $stmt->bindParam(':today',$today,PDO::PARAM_STR);
            $stmt->bindParam(':address',$address,PDO::PARAM_STR);
            $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            $stmt->execute();
            $retList = null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room();
                $room->roomId = $row["room_id"];
                $room->roomName = $row["room_name"];
                $room->userId = $row["user_id"];
                $room->explain = $row["room_explain"];
                $room->maxMember = $row["max_member"];
                $room->restaurantId = $row["restaurant_id"];
                $room->deadLine = $row["dead_line"];
                $room->budget = $row["budget"];
                $room->address = $row["address"];
                $room->autoApply = $row["auto_apply"];
                $room->roomTagList = $this->getTagListByRoomId($room->roomId);
                $room->roomChatMessageList = $this->getMessageListByRoomId($room->roomId);
                $room->roomUserStatusList = $this->getUserStatusListByRoomId($room->roomId);
                $retList[] = $room;
            }
            $this->dbDisconnect();
            return $retList;
        }catch(PDOException $e) {
            print('検索失敗。'.$e->getMessage());
            throw $e;
        }
    }

    public function getRoomListByRestaurantId($restaurantId,$order,$page){
        try{
            $this->dbConnect();
            $page = $page*10;
            $stmt=$this->pdo->prepare('
                SELECT *
                FROM room
                WHERE restaurant_id = :restaurant_id
                ORDER BY '.$order.'
                LIMIT :page,10
            ');
            $stmt->bindParam(':restaurant_id',$restaurantId,PDO::PARAM_STR);
            $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            $stmt->execute();
            $retList = null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room();
                $room->roomId = $row["room_id"];
                $room->roomName = $row["room_name"];
                $room->userId = $row["user_id"];
                $room->explain = $row["room_explain"];
                $room->maxMember = $row["max_member"];
                $room->restaurantId = $row["restaurant_id"];
                $room->deadLine = $row["dead_line"];
                $room->budget = $row["budget"];
                $room->address = $row["address"];
                $room->autoApply = $row["auto_apply"];
                $room->roomTagList = $this->getTagListByRoomId($room->roomId);
                $room->roomChatMessageList = $this->getMessageListByRoomId($room->roomId);
                $room->roomUserStatusList = $this->getUserStatusListByRoomId($room->roomId);
                $retList[] = $room;
            }
            $this->dbDisconnect();
            return $retList;
        }catch(PDOException $e) {
            print('検索失敗。'.$e->getMessage());
            throw $e;
        }
    }

    //検索メソッド
    public function getUserInfoTblByUserID($userid){
        try{

            //DBに接続
            $this->dbConnect();
    
            //SQLを生成
            $stmt = $this->pdo->prepare('SELECT * FROM user WHERE user_id = :keyid');
            $stmt->bindValue(':keyid',$userid,PDO::PARAM_STR);
            //SQLを実行
            $stmt->execute();
    
            //取得結果をListに格納
            $list = array();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                //データを入れるクラスをnew
                $rowData = new User();
    
                //DBから取れた情報をカラム毎に、クラスに入れていく
                $rowData->userId   = $row["user_id"];
                $rowData->password = $row["password"];
                $rowData->userName = $row["user_name"];
    
                //取得した一件を配列に追加する
                array_push($list,$rowData);

            }
            //DB切断
            $this->dbDisconnect();
    
            //結果が格納された配列を返す
            return $list;

        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
            throw $e;
        }
    }

    //フレンド検索(ID)
 public function getFriendListByUserId($userid){
        try{

            //DBに接続
            $this->dbConnect();
    
            //SQLを生成
            $stmt = $this->pdo->prepare('
                SELECT *
                FROM friend
                WHERE user_id = :user_id
                   OR friend_user_id = :friend_user_id
            ');
            $stmt->bindValue(':user_id',$userid,PDO::PARAM_STR);
            $stmt->bindValue(':friend_user_id',$userid,PDO::PARAM_STR);

            //SQLを実行
            $stmt->execute();
    
            //取得結果をListに格納
            $list = array();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                //データを入れるクラスをnew
                $rowData = new Friend();
                //DBから取れた情報をカラム毎に、クラスに入れていく
                $rowData->userId       = $row["user_id"];
                $rowData->friendUserId = $row["friend_user_id"];
                $rowData->status       = $row["status"];
    
                //取得した一件を配列に追加する
                array_push($list,$rowData);

            }
            //DB切断
            $this->dbDisconnect();
    
            //結果が格納された配列を返す
            return $list;

        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
            throw $e;
        }
    }

    //フレンド検索(名前)
    // public function getFriendListByUserName($userid,$username){
    //     try{

    //         //DBに接続
    //         $this->dbConnect();
    
    //         //SQLを生成
    //         $stmt = $this->pdo->prepare('SELECT * FROM user WHERE user_name LIKE (:keyname)');

    //         $friendname = $_POST['friname'];
	// 		$like_friendname = "%".$friendname."%";

    //         $stmt->bindValue(':keyname',$like_friendname,PDO::PARAM_STR);

    //         //SQLを実行
    //         $stmt->execute();
    
    //         //取得結果をListに格納
    //         $list = array();
    //         while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
    //             //データを入れるクラスをnew
    //             $rowData = array();;
    
    //             //DBから取れた情報をカラム毎に、クラスに入れていく
    //             $rowData['frienduserId'] = $row["user_id"];
    //             $rowData['friendname']   = $row["user_name"];
    //         if(($key = array_search($userid, $rowData)) !== false) {
    //             unset($rowData[$key]);
    //         }
    //         if(($key = array_search($username, $rowData)) !== false) {
    //             unset($rowData[$key]);
    //         }
    //         if(!empty($rowData)){

    //             array_push($list,$rowData);
    //         }
    //     }

    //         //DB切断
    //         $this->dbDisconnect();
    
    //         //結果が格納された配列を返す
    //         return $list;

    //     }catch(PDOException $e){
    //         print('検索に失敗。'.$e->getMessage());
    //         throw $e;
    //     }
    // }



    //フレンド申請
    public function insertFriendRequestByUserId($user_id,$friend_user_id){
        try{
            //DB接続
            $this->dbConnect();
            
            //SQL生成
            $stmt = $this->pdo->prepare("INSERT INTO friend (user_id,friend_user_id,status) VALUES (:userid,:frienduserid,0)");
            $stmt->bindValue(':userid',$user_id,PDO::PARAM_STR);
            $stmt->bindValue(':frienduserid',$friend_user_id,PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();

            //DB切断
            $this->dbDisConnect();

        }catch(PDOException $e){
            print('書き込み失敗。'.$e->getMessage());
            throw $e;
        }    
    }

   //申請リスト検索
    public function getinsertFriendListByUserId($userid){
        try{

            //DBに接続
            $this->dbConnect();
    
            //SQLを生成
            $stmt = $this->pdo->prepare('SELECT friend.user_id,friend.friend_user_id,friend.status,user.user_name FROM friend JOIN user ON friend.friend_user_id = user.user_id WHERE friend.user_id = :userid AND friend.status = 2');
            $stmt->bindValue(':userid',$userid,PDO::PARAM_STR);

            //SQLを実行
            $stmt->execute();
    
            //取得結果をListに格納
            $list = array();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                //データを入れるクラスをnew
                $rowData = new Friend();
    
                //DBから取れた情報をカラム毎に、クラスに入れていく
                $rowData->userId       = $row["user_id"];
                $rowData->frienduserId = $row["friend_user_id"];
                $rowData->friendname   = $row["user_name"];
                $rowData->status       = $row["status"];

                //取得した一件を配列に追加する
                array_push($list,$rowData);
            }
            //DB切断
            $this->dbDisconnect();
    
            //結果が格納された配列を返す
            return $list;

        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
            throw $e;
        }
    }

   //申請依頼リスト検索
    public function getupdateFriendListByUserId($userid){
        try{

            //DBに接続
            $this->dbConnect();
    
            //SQLを生成
            $stmt = $this->pdo->prepare('SELECT friend.user_id,friend.friend_user_id,friend.status,user.user_name FROM friend JOIN user ON friend.friend_user_id = user.user_id WHERE friend.user_id = :userid AND friend.status = 3');
            $stmt->bindValue(':userid',$userid,PDO::PARAM_STR);

            //SQLを実行
            $stmt->execute();
    
            //取得結果をListに格納
            $list = array();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                //データを入れるクラスをnew
                $rowData = new Friend();
    
                //DBから取れた情報をカラム毎に、クラスに入れていく
                $rowData->userId       = $row["user_id"];
                $rowData->frienduserId = $row["friend_user_id"];
                $rowData->friendname   = $row["user_name"];
                $rowData->status       = $row["status"];
    
                //取得した一件を配列に追加する
                array_push($list,$rowData);
            }
            //DB切断
            $this->dbDisconnect();
    
            //結果が格納された配列を返す
            return $list;

        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
            throw $e;
        }
    }


    //フレンド受理
    public function updateFriendStatusByUserId($user_id,$friend_user_id){
        try{
            //DB接続
            $this->dbConnect();

            //SQL生成
            $stmt = $this->pdo->prepare('UPDATE friend SET status = 1 WHERE user_id = :userid AND friend_user_id = :frienduserid');
            $stmt->bindValue(':userid',$friend_user_id,PDO::PARAM_STR);
            $stmt->bindValue(':frienduserid',$user_id,PDO::PARAM_STR);
            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisConnect();

        }catch(PDOException $e){
            print('書き込み失敗。'.$e->getMessage());
            throw $e;
        }    
    }

    //フレンド削除
    public function deleteFriendByFriendUserId($userid,$frienduserid){
        try{
            //DB接続
            $this->dbConnect();

            //SQL生成
            $stmt = $this->pdo->prepare("DELETE FROM friend WHERE user_id = :user_id AND friend_user_id = :frienduserid");
            $stmt->bindValue(':user_id',$userid,PDO::PARAM_STR);
            $stmt->bindValue(':frienduserid',$frienduserid,PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();
            $stmt = $this->pdo->prepare("DELETE FROM friend WHERE user_id = :user_id AND friend_user_id = :frienduserid");
            $stmt->bindValue(':user_id',$frienduserid,PDO::PARAM_STR);
            $stmt->bindValue(':frienduserid',$userid,PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisConnect();

        }catch(PDOException $e){
            print('書き込み失敗。'.$e->getMessage());
            throw $e;
        }    
    }

    public function checkFriendByUserId($userId,$frienduserId){
        try{
          //DB接続
            $this->dbConnect();

            //SQL生成
            $stmt = $this->pdo->prepare("INSERT INTO favorite (restaurant_id,user_id)  VALUES (:res_id,:userid)");
            $stmt->bindValue(':res_id',$restaurantid,PDO::PARAM_STR);
            $stmt->bindValue(':userid',$user_id,PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();

            //DB切断
            $this->dbDisConnect();

        }catch(PDOException $e){
            print('書き込み失敗。'.$e->getMessage());
            throw $e;
        }    
    }

    //お気に入り登録
    public function insertFavoriteByRestaurantId($user_id,$restaurantid){
        try{
          //DB接続
            $this->dbConnect();

            //SQL生成
            $stmt = $this->pdo->prepare("INSERT INTO favorite (restaurant_id,user_id)  VALUES (:res_id,:userid)");
            $stmt->bindValue(':res_id',$restaurantid,PDO::PARAM_STR);
            $stmt->bindValue(':userid',$user_id,PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();

            //DB切断
            $this->dbDisConnect();

        }catch(PDOException $e){
            print('書き込み失敗。'.$e->getMessage());
            throw $e;
        }    
    }

   //お気に入りリスト取得
    public function getFavoriteListByUserId($userid,$page){
        try{

            //DBに接続
            $this->dbConnect();
            $page = $page*10;
            //SQLを生成
            $stmt = $this->pdo->prepare('SELECT * FROM favorite WHERE user_id = :keyid LIMIT :page,10');
            $stmt->bindValue(':keyid',$userid,PDO::PARAM_STR);
            $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            //SQLを実行

            $stmt->execute();
    
            //取得結果をListに格納
            $list = array();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                $favorite = new Favorite();
                //DBから取れた情報をカラム毎に、クラスに入れていく
                $favorite->userId  = $row["user_id"];
                $favorite->restaurantId  = $row["restaurant_id"];
                $list[] = $favorite;
            }
            //DB切断
            $this->dbDisconnect();
    
            //結果が格納された配列を返す
            return $list;

        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
            throw $e;
        }
    }

    //お気に入り削除
    public function deleteFavoriteByRestaurantId($userid,$restaurantid){
        try{
            //DB接続
            $this->dbConnect();

            //SQL生成
            $stmt = $this->pdo->prepare("DELETE FROM favorite WHERE user_id = :user_id AND restaurant_id = :restaurantid");
            $stmt->bindValue(':user_id',$userid,PDO::PARAM_STR);
            $stmt->bindValue(':restaurantid',$restaurantid,PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();

            //DB切断
            $this->dbDisConnect();

        }catch(PDOException $e){
            print('書き込み失敗。'.$e->getMessage());
            throw $e;
        }    
    }

    //履歴登録
    public function insertHistoryByUserId($user_id,$room_id){
        try{
          //DB接続
            $this->dbConnect();

            //SQL生成
            $stmt = $this->pdo->prepare("INSERT INTO history (room_id,user_id)  VALUES (:roomid,:userid)");
            $stmt->bindValue(':roomid',$room_id,PDO::PARAM_STR);
            $stmt->bindValue(':userid',$user_id,PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();

            //DB切断
            $this->dbDisConnect();

        }catch(PDOException $e){
            print('書き込み失敗。'.$e->getMessage());
            throw $e;
        }    
    }

   //履歴roomid取得
    public function getHistoryByUserId($userid){
        try{

            //DBに接続
            $this->dbConnect();
    
            //SQLを生成
            $stmt = $this->pdo->prepare('SELECT * FROM history WHERE user_id = :keyid');
            $stmt->bindValue(':keyid',$userid,PDO::PARAM_STR);
            //SQLを実行
            $stmt->execute();
    
            //取得結果をListに格納
            $list = array();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                $rowData = new History();

                //DBから取れた情報をカラム毎に、クラスに入れていく
                $rowData->userid  = $row["user_id"];
                $rowData->roomid  = $row["room_id"];
                $rowData->joinDate  = $row["join_date"];

                array_push($list,$rowData);
            }
            //DB切断
            $this->dbDisconnect();
    
            //結果が格納された配列を返す
            return $list;

        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
            throw $e;
        }
    }

   //履歴リスト取得
    public function getHistoryListByUserId($userId,$page){
        try{

            //DBに接続
            $this->dbConnect();
            $page = $page*10;
            //SQLを生成
            $stmt = $this->pdo->prepare('
                SELECT *
                FROM history
                WHERE history.user_id = :userid
                LIMIT :page,10
            ');
            $stmt->bindValue(':userid',$userId,PDO::PARAM_STR);
            $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            //SQLを実行
            $stmt->execute();
    
            //取得結果をListに格納
            $list = array();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                $history = new History();
                //DBから取れた情報をカラム毎に、クラスに入れていく
                $history->userId  = $row["user_id"];
                $history->roomId  = $row["room_id"];
                $history->joinDate  = $row["join_date"];
                $list[] = $history;
            }
            //DB切断
            $this->dbDisconnect();

            //結果が格納された配列を返す
            return $list;

        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
            throw $e;
        }
    }
    public function getHistoryByUserIdAndRoomId($userId,$roomId){
        try{

            //DBに接続
            $this->dbConnect();
            //SQLを生成
            $stmt = $this->pdo->prepare('
                SELECT *
                FROM history,room
                WHERE history.user_id = :userid
                AND history.room_id = :room_id
            ');
            $stmt->bindValue(':userid',$userId,PDO::PARAM_STR);
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            //SQLを実行
            $stmt->execute();
    
            //取得結果をListに格納
            $list = array();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
                $history = new History();
                //DBから取れた情報をカラム毎に、クラスに入れていく
                $history->userId  = $row["user_id"];
                $history->roomId  = $row["room_id"];
                $history->joinDate  = $row["join_date"];
                $list[] = $history;
            }
            //DB切断
            $this->dbDisconnect();

            //結果が格納された配列を返す
            return $list;

        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
            throw $e;
        }
    }





//長澤

//部屋情報取得
    public function getRoomByRoomId($roomId){
        try{
            //DB接続
            $this->dbConnect();
            //SQL生成
            $stmt = $this->pdo->prepare('SELECT * FROM  room WHERE room_id = :room_id');
            
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            //SQL実行
            $stmt->execute();
            
            //結果を格納するLISTを作成
            $retList = array();
            //結果をrowに格納する
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $rowData = null;
            //店IDが存在しているか
            if(!empty($row)){
                //存在していたら
                //roomをrowDataの中に継承する
                $rowData = new Room;
            
                //rowDataから取得したデータをカラム毎にクラスに挿入する
                $rowData->roomId = $row["room_id"];
                $rowData->userId = $row["user_id"];
                $rowData->roomName = $row["room_name"];
                $rowData->explain = $row["room_explain"];
                $rowData->maxMember = $row["max_member"];
                $rowData->explain = $row["room_explain"];
                $rowData->restaurantId = $row["restaurant_id"];
                $rowData->deadLine = $row["dead_line"];
                $rowData->autoApply  = $row["auto_apply"];
                $rowData->budget = $row["budget"];
                $rowData->address = $row["address"];
                
                
            }
            //DB切断
            $this->dbDisconnect();

            //存在していたら挿入したデータを返す
            //存在していなかったらnullを返す

            return $rowData;
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }
    }

    //部屋のIDからタグを取得
    public function getTagListByRoomId($roomId){
        try{
            //DB接続
            $this->dbConnect();
            //SQL生成
            $stmt = $this->pdo->prepare('
            SELECT room_tag.room_id,room_tag.tag_id,tag.tag_name
            FROM room_tag,tag
            WHERE room_tag.tag_id = tag.tag_id AND room_id = :room_id');
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();

            $retList = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $rowData = new RoomTag();

                $rowData->roomId = $row["room_id"];
                $rowData->tagId = $row["tag_id"];
                $rowData->tagName = $row["tag_name"];

                array_push($retList,$rowData);
            }
            $this->dbDisconnect();
            return $retList;
        }catch(PDOException $e){
            print ('データベースから取得できません'.$e->getMessage());
        }
    }
    //部屋のユーザのstatusを取得
    public function getUserStatusListByRoomId($roomId){
        try{
            //DB接続
            $this->dbConnect();
            //SQL生成
            $stmt = $this->pdo->prepare("SELECT * FROM room_user_status WHERE room_id = :room_id");
            $stmt->bindValue('room_id',$roomId,PDO::PARAM_STR);
            //SQL実行
            $stmt->execute();

            $retList = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $rowData = new RoomUserStatus();

                $rowData->roomId = $row["room_id"];
                $rowData->userId = $row["user_id"];
                $rowData->status = $row["status"];

                array_push($retList,$rowData);
            }
            $this->dbDisconnect();
            return $retList;
        }catch(PDOException $e){
            print('データベースから取得できません'.$e->getMessage());
        }
    }

    //チャット送信
    public function insertMessage($userId,$roomId,$message){
        try{
            //DB接続
            $this->dbConnect();
            
            //SQL生成
            $stmt = $this->pdo->prepare("INSERT INTO room_chat_message (user_id,room_id,message) VALUES (:user_id,:room_id,:message)");
            
            $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            $stmt->bindValue(':message',$message,PDO::PARAM_STR);
            
            //SQL実行
            $stmt->execute();
            //DB接続
            $this->dbDisconnect();
        }catch(PDOException $e){
            print('挿入に失敗'.$e->getMessage());
            throw $e;
        }
    }
    //チャット履歴取得
    public function getMessageListByRoomId($roomId){
        try{
            //DB接続
            $this->dbConnect();

            //SQL生成
            $stmt = $this->pdo->prepare('SELECT * FROM room_chat_message WHERE room_id = :room_id');
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);

            //SQLを実行
            $stmt->execute();
            //結果を格納するLISTを作成
            $retList = array();
            //結果をLISTに格納する
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //データ格納のクラスをnew
                $rowData = new RoomMessage;

                //DBから取得したデータをカラム毎にクラスに挿入する
                
                $rowData->messageId = $row["message_id"];
                $rowData->message = $row["message"];
                $rowData->messageDate = $row["message_date"];
                $rowData->userId = $row["user_id"];
                $rowData->roomId = $row["room_id"];

                //取得した一件を配列に追加
                array_push($retList,$rowData);
            }
            //DB切断
            $this->dbDisconnect();
            //配列$retListを返す
            return $retList;
        }catch (PDOException $e){
            print('取得に失敗'.$e->getMessage());
        }
    }

    //部屋入室とフレンド部屋招待
    public function insertRoomUserStatus($roomId,$userId,$status){
        try{
            //DB接続
            $this->dbConnect();

            //SQL生成
            $stmt = $this->pdo->prepare("INSERT INTO room_user_status(room_id,user_id,status) VALUES (:room_id,:user_id,:status)");
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
            $stmt->bindValue(':status',$status,PDO::PARAM_STR);
            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisconnect();
            
        }catch (PDOException $e){
            print('データベースに書き込めません'.$e->getMessage());
            throw $e;
        }

    }

    //タグネームからタグIDを取得
    public function getTagIdByName($tagName){
        try{
            //DB接続
            $this->dbConnect();
            //SQL生成
            $stmt = $this->pdo->prepare("SELECT * FROM tag WHERE tag_name = :tag_name");
            $stmt->bindValue(':tag_name',$tagName,PDO::PARAM_STR);

                //SQL実行
                $stmt->execute();
                $retList = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $rowData = new RoomTag();
                    
                    $rowData->tagId = $row["tag_id"];
                    $rowData->tagName = $row["tag_name"];

                    array_push($retList,$rowData);
                }
                //DB切断
                $this->dbDisconnect();
                //配列$retListを返す
                return $retList;
                
                
            }catch (PDOException $e){
                print('取得に失敗'.$e->getMessage());
            }

        }

        //タグネーム追加
        public function insertNewTag($tagName){
            try{
                //DB接続
                $this->dbConnect();
                //SQL生成
                $stmt = $this->pdo->prepare("INSERT INTO tag(tag_name) VALUES (:tag_name)");
                //トランザクション開始
                $this->pdo->beginTransaction();
                try{
                    $stmt->bindValue(':tag_name',$tagName,PDO::PARAM_STR);
                    //SQL実行
                    $stmt->execute();

                    //INSERTされたデータのIDを取得
                    $lastTagId = $this->pdo->lastInsertId('tag_id');

                    //トランザクション完了
                    $this->pdo->commit();

                    //DB切断
                    $this->dbDisconnect();

                    return $lastTagId;

                }catch(Exception $e){
                    //トランザクション取り消し
                    $this->pdo->roolBack();
                    throw $e;
                }
                    
            }catch (PDOException $e){
                print('新しくタグを作れません'.$e->getMessage());
            throw $e;
            }
        }

    //部屋作成機能
    public function insertRoom($room){
        try{
            //DB接続
            $this->dbConnect();
            
            //SQL生成
            $stmt = $this->pdo->prepare("INSERT INTO room(user_id,room_name,room_explain,max_member,restaurant_id,dead_line,auto_apply,budget,address)
                                              VALUES (:user_id,:room_name,:room_explain,:max_member,:restaurant_id,:dead_line,:auto_apply,:budget,:address)");
            //トランザクション開始
            $this->pdo->beginTransaction();
            try{
                $stmt->bindValue(':user_id',$room->userId,PDO::PARAM_STR);
                $stmt->bindValue(':room_name',$room->roomName,PDO::PARAM_STR);
                $stmt->bindValue(':room_explain',$room->explain,PDO::PARAM_STR);
                $stmt->bindValue(':max_member',$room->maxMember,PDO::PARAM_STR);
                $stmt->bindValue(':restaurant_id',$room->restaurantId,PDO::PARAM_STR);
                $stmt->bindValue(':dead_line',$room->deadLine,PDO::PARAM_STR);
                $stmt->bindValue(':auto_apply',$room->autoApply,PDO::PARAM_STR);
                $stmt->bindValue(':budget',$room->budget,PDO::PARAM_STR);
                $stmt->bindValue(':address',$room->address,PDO::PARAM_STR);
                //SQL実行
                $stmt->execute();
                
                //INSERTされたデータのIDを取得
                $roomId = $this->pdo->lastInsertId('room_id');

                //トランザクション完了
                $this->pdo->commit();
                
                //DB切断
                $this->dbDisconnect();
                return $roomId;
            
               
                }catch(Exception $e){
                    //トランザクション取り消し
                    $this->pdo->roolBack();
                    throw $e;
                }

           

            }catch (PDOException $e){
                print('新しく部屋を作れません'.$e->getMessage());
                throw $e;
            }
        }

        //部屋編集
        public function updateRoom($room){
            
            try{
                //DB接続
                $this->dbConnect();
                //SQL生成
                $stmt = $this->pdo->prepare("UPDATE room SET room_name = :room_name,room_explain = :room_explain,max_member = :max_member,restaurant_id = :restaurant_id,dead_line = :dead_line,auto_apply = :auto_apply,budget = :budget WHERE room_id = :room_id AND user_id = :user_id");
                
                $stmt->bindValue(':room_id',$room->roomId,PDO::PARAM_STR);
                $stmt->bindValue(':user_id',$room->userId,PDO::PARAM_STR);
                $stmt->bindValue(':room_name',$room->roomName,PDO::PARAM_STR);
                $stmt->bindValue(':room_explain',$room->explain,PDO::PARAM_STR);
                $stmt->bindValue(':max_member',$room->maxMember,PDO::PARAM_STR);
                $stmt->bindValue(':restaurant_id',$room->restaurantId,PDO::PARAM_STR);
                $stmt->bindValue(':dead_line',$room->deadLine,PDO::PARAM_STR);
                $stmt->bindValue(':auto_apply',$room->autoApply,PDO::PARAM_STR);
                $stmt->bindValue(':budget',$room->budget,PDO::PARAM_STR);
                //SQL実行
                $stmt->execute();
                //DB切断
                $this->dbDisconnect();
            }catch (PDOException $e){
                print('接続出来ません'.$e->getMessage());
                throw $e;
            }
        }

    //部屋にタグをつける
    public function insertTagForRoom($roomId,$tagId){
        try{
            //DB接続
            $this->dbConnect();
            //SQL生成
            $stmt = $this->pdo->prepare("INSERT INTO room_tag(room_id,tag_id) VALUES (:room_id,:tag_id)");
            
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            $stmt->bindValue(':tag_id',$tagId,PDO::PARAM_STR);
            
           
            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisconnect();
        }catch (PDOException $e){
            print('接続できません'.$e->getMessage());
            throw $e;
        }
    }

    //部屋にある全てのタグを消去する
    public function deleteAllTagByRoomId($roomId){
        try{
            //DB接続
            $this->dbConnect();
            //SQL生成
            $stmt = $this->pdo->prepare("DELETE FROM room_tag WHERE room_id = :room_id");
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisconnect();
        }catch(PDOException $e){
            print('消せません'.$e->getMessage());
            throw $e;
        }
    }



    //部屋退出と部屋拒否
    public function deleteUserByRoom($userId,$roomId){
        try{
            //DB接続
            $this->dbConnect();
            //SQL生成
            $stmt = $this->pdo->prepare("DELETE FROM room_user_status WHERE user_id = :user_id AND room_id = :room_id");
            $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisconnect();
        }catch (PDOException $e){
            print('退出できません'.$e->getMessage());
            throw $e;
        }
    }

    
    //部屋招待に招待され参加した人、部屋からキックされた人等のstatusを変えるとこ
    public function updateRoomUserStatus($userId,$roomId,$roomUserStatus){
        try{
            //DB接続
            $this->dbConnect();
            //SQL生成
            $stmt = $this->pdo->prepare("UPDATE room_user_status SET status = :status WHERE user_id = :user_id AND room_id = :room_id");
            $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            $stmt->bindValue(':status',$roomUserStatus,PDO::PARAM_STR);
            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisconnect();
        }catch (PDOException $e){
            print('データベース変更できません'.$e->getMessage());
            throw $e;
        }
    }
    
    //deadlineを1000年1月1日に書き換える
    public function updateDeadLineByRoomId($roomId){
        try{
            //DB接続
            $this->dbConnect();
            //SQL生成
            $stmt = $this->pdo->prepare("UPDATE room SET dead_line = :dead_line WHERE room_id = :room_id ");

            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            $stmt->bindValue(':dead_line',"1000-01-01 00:00:00",PDO::PARAM_STR);
            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisconnect();
        }catch (PDOException $e){
            print('データベースに書き込めません。'.$e->getMessage());
            throw $e;
        }
    }

    public function changeTempToMainRegister($token){
        try{
            $this->dbConnect();
            $stmt=$this->pdo->prepare("
                UPDATE user SET user_status = 10 WHERE token = :token
            ");
            $stmt->bindParam(':token',$token,PDO::PARAM_STR);
            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisconnect();
        }catch(PDOException $e){
            print('データベース接続失敗。'.$e->getMessage());
            throw $e;
        }
    }


//只隈

//ユーザー登録
    public function insertUser($user){
        try{
            $this->dbConnect();
            $stmt=$this->pdo->prepare("INSERT INTO user 
            (user_id,user_name,password,mail_address,prefecture,gender,birthday,message,image_name,token,token_dead_line)
            VALUES(:user_id,:user_name,:password,:mail_address,:prefecture,:gender,:birthday,:message,'defalut_uesr_image.png',:token,:token_dead_line)
            ");
            $stmt->bindParam(':user_id',$user->userId, PDO::PARAM_STR);
            $stmt->bindParam(':user_name',$user->userName, PDO::PARAM_STR);
            $stmt->bindParam(':password',$user->password, PDO::PARAM_STR);
            $stmt->bindParam(':mail_address',$user->mailAddress, PDO::PARAM_STR);
            $stmt->bindParam(':prefecture',$user->prefecture,PDO::PARAM_STR);
            $stmt->bindParam(':gender',$user->gender,PDO::PARAM_STR);
            $stmt->bindParam(':birthday',$user->birthday,PDO::PARAM_STR);
            $stmt->bindParam(':message',$user->message,PDO::PARAM_STR);
            $stmt->bindParam(':token',$user->token,PDO::PARAM_STR);
            $stmt->bindParam(':token_dead_line',$user->tokenDeadLine,PDO::PARAM_STR);
            //SQL実行
            $stmt->execute();
            //DB切断
            $this->dbDisconnect();
        }catch(PDOException $e){
            print('データベース接続失敗。'.$e->getMessage());
            throw $e;
        }
    }
//ユーザー情報更新
    public function updateUser($user){
        try{
            $this->dbConnect();
            $stmt=$this->pdo->prepare("
            UPDATE user SET 
            user_name=:user_name,prefecture=:prefecture,message=:message,image_name=:image_name
            WHERE user_id = :user_id");
            $stmt->bindParam(':user_name',$user->userName,PDO::PARAM_STR);
            $stmt->bindParam(':prefecture',$user->prefecture,PDO::PARAM_STR);
            $stmt->bindParam(':message',$user->message,PDO::PARAM_STR);
            $stmt->bindParam(':image_name',$user->imageName,PDO::PARAM_STR);
            $stmt->bindParam(':user_id',$user->userId,PDO::PARAM_STR);
            $stmt->execute();
            $this->dbDisconnect();
        }catch(PDOException $e){
            print('更新失敗。'.$e->getMessage());
        }
    }

//ユーザー情報の取得
    public function getUserByUserId($userId){
        try{
            $this->dbConnect();
            $stmt = $this->pdo->prepare("SELECT * FROM user WHERE user_id = :keyid");
            $stmt->bindValue(':keyid',$userId,PDO::PARAM_STR);
            $stmt->execute();
            $rowData = null;
            while($row = $stmt ->fetch(PDO::FETCH_ASSOC)){
                //データを入れるクラスをnew
                $rowData = new User();
                $rowData->userId = $row["user_id"];
                $rowData->password = $row["password"];
                $rowData->userName = $row["user_name"];
                $rowData->mailAddress = $row["mail_address"];
                $rowData->prefecture = $row["prefecture"];
                $rowData->birthday = $row["birthday"];
                $rowData->userStatus = $row["user_status"];
                $rowData->gender = $row["gender"];
                $rowData->message = $row["message"];
                $rowData->imageName = $row["image_name"];
            }
            $this->dbDisconnect();
            //結果が格納された配列を返す
            return $rowData;
        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
        }
    }
    public function getUserByString($string,$page){
        try{
            $this->dbConnect();
            $string = '%'.$string.'%';
            $page = $page*10;
            $stmt = $this->pdo->prepare("SELECT * FROM user WHERE user_id LIKE :string LIMIT :page,10");
            $stmt->bindValue(':string',$string,PDO::PARAM_STR);
            $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            $stmt->execute();
            $retList = array();
            while($row = $stmt ->fetch(PDO::FETCH_ASSOC)){
                //データを入れるクラスをnew
                $rowData = new User();
                $rowData->userId = $row["user_id"];
                $rowData->password = $row["password"];
                $rowData->userName = $row["user_name"];
                $rowData->mailAddress = $row["mail_address"];
                $rowData->prefecture = $row["prefecture"];
                $rowData->birthday = $row["birthday"];
                $rowData->userStatus = $row["user_status"];
                $rowData->gender = $row["gender"];
                $rowData->message = $row["message"];
                $rowData->imageName = $row["image_name"];
                $retList[] = $rowData;
            }
            $this->dbDisconnect();
            //結果が格納された配列を返す
            return $retList;
        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
        }
    }

//タグの名前で部屋検索
    public function getRoomByTagName($tagName,$searchAddress,$order,$page){
        try{
            $this->dbConnect();
            $page = $page*10;
            $today = date("Y-m-d H:i:s");
            if(!empty($searchAddress)){
            $searchAddress = '%'.$searchAddress.'%';
            $stmt = null;
                $stmt=$this->pdo->prepare('
                    SELECT * 
                    FROM room,tag,room_tag
                    WHERE
                    :today < dead_line AND
                    room.room_id = room_tag.room_id AND room_tag.tag_id = tag.tag_id AND
                    room.address LIKE :address AND tag.tag_name = :tag_name ORDER BY '.$order.' LIMIT :page,10
                ');
                $stmt->bindParam(':tag_name',$tagName,PDO::PARAM_STR);
                $stmt->bindParam(':address',$searchAddress,PDO::PARAM_STR);
                $stmt->bindParam(':today',$today,PDO::PARAM_STR);
                $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            }else{
                $stmt=$this->pdo->prepare('
                    SELECT * 
                    FROM room,tag,room_tag
                    WHERE
                    :today < dead_line AND
                    room.room_id = room_tag.room_id AND
                    room_tag.tag_id = tag.tag_id AND
                    tag.tag_name = :tag_name ORDER BY '.$order.' LIMIT :page,10
                ');
                $stmt->bindParam(':tag_name',$tagName,PDO::PARAM_STR);
                $stmt->bindParam(':today',$today,PDO::PARAM_STR);
                $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            }
            $stmt->execute();
            $retList = null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room();
                $room->roomId = $row["room_id"];
                $room->roomName = $row["room_name"];
                $room->userId = $row["user_id"];
                $room->explain = $row["room_explain"];
                $room->maxMember = $row["max_member"];
                $room->restaurantId = $row["restaurant_id"];
                $room->deadLine = $row["dead_line"];
                $room->budget = $row["budget"];
                $room->address = $row["address"];
                $room->autoApply = $row["auto_apply"];
                $room->roomTagList = $this->getTagListByRoomId($room->roomId);
                $room->roomChatMessageList = $this->getMessageListByRoomId($room->roomId);
                $room->roomUserStatusList = $this->getUserStatusListByRoomId($room->roomId);
                $retList[] = $room;
            }
            $this->dbDisconnect();
            return $retList;
        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
        }
    }

//トレンドタグ取得
    public function getTrendTagList(){
        try{
            $today = date("Y-m-d H:i:s");
            $this->dbConnect();
            $stmt=$this->pdo->prepare('
            SELECT * FROM tag NATURAL JOIN (
                SELECT tag_id ,COUNT(*) cnt FROM room,room_tag WHERE :today < dead_line AND room.room_id = room_tag.room_id GROUP BY tag_id ORDER BY cnt DESC
            ) a
            ORDER BY cnt DESC
            LIMIT 20
            ');
            $stmt->bindParam(':today',$today,PDO::PARAM_STR);
            $stmt->execute();
            $retList=null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                $roomTag = new RoomTag();
                $roomTag->tagId = $row['tag_id'];
                $roomTag->tagName =$row['tag_name'];
                $roomTag->tagNumber =$row['cnt'];
                $retList[] = $roomTag;
            }
            $this->dbDisconnect();
            return $retList;
        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
        }
    }

//日時検索
    public function getRoomByHoldDate($holdDate,$order,$page){
        try{
            $this->dbConnect();
            $page = $page*10;
            $stmt= $this->pdo->prepare('SELECT * FROM room WHERE dead_line= :dead_line  ORDER BY '.$order.' LIMIT :page,10');
            $stmt->bindValue(':dead_line',$holdDate,PDO::PARAM_STR);
            $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            $stmt->execute();
            $retList = null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room();
                $room->roomId = $row["room_id"];
                $room->roomName = $row["room_name"];
                $room->userId = $row["user_id"];
                $room->explain = $row["room_explain"];
                $room->maxMember = $row["max_member"];
                $room->restaurantId = $row["restaurant_id"];
                $room->deadLine = $row["dead_line"];
                $room->budget = $row["budget"];
                $room->address = $row["address"];
                $room->autoApply = $row["auto_apply"];
                $room->roomTagList = $this->getTagListByRoomId($room->roomId);
                $room->roomChatMessageList = $this->getMessageListByRoomId($room->roomId);
                $room->roomUserStatusList = $this->getUserStatusListByRoomId($room->roomId);
                $retList[] = $room;
            }
            $this->dbDisconnect();
            return $retList;
        }catch(PDOException $e){
            print('検索に失敗。'.$e->getMessage());
        }
    }

//部屋名検索
    public function getRoomByRoomName($roomName,$order,$page){
        try{
            $this->dbConnect();
            $roomName = '%'.$roomName.'%';
            $page = $page*10;
            $today = date("Y-m-d H:i:s");
            $stmt=$this->pdo->prepare('
                SELECT *
                FROM room
                WHERE room_name LIKE :room_name
                AND :today < dead_line
                ORDER BY '.$order.' LIMIT :page,10
            ');
            $stmt->bindParam(':today',$today,PDO::PARAM_STR);
            $stmt->bindParam(':room_name',$roomName,PDO::PARAM_STR);
            $stmt->bindValue(':page',$page,PDO::PARAM_STR);
            $stmt->execute();
            $retList = null;
            while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                $room = new Room();
                $room->roomId = $row["room_id"];
                $room->roomName = $row["room_name"];
                $room->userId = $row["user_id"];
                $room->explain = $row["room_explain"];
                $room->maxMember = $row["max_member"];
                $room->restaurantId = $row["restaurant_id"];
                $room->deadLine = $row["dead_line"];
                $room->budget = $row["budget"];
                $room->address = $row["address"];
                $room->autoApply = $row["auto_apply"];
                $room->roomTagList = $this->getTagListByRoomId($room->roomId);
                $room->roomChatMessageList = $this->getMessageListByRoomId($room->roomId);
                $room->roomUserStatusList = $this->getUserStatusListByRoomId($room->roomId);
                $retList[] = $room;
            }
            $this->dbDisconnect();
            return $retList;
        }catch(PDOException $e) {
            print('検索失敗。'.$e->getMessage());
            throw $e;
        }
    }

//トークン確認
    public function checkToken($userToken){
        try{
            $this->dbConnect();
            $stmt = $this->pdo->prepare('SELECT * FROM user WHERE token=:token');
            $stmt->bindParam(':token',$userToken,PDO::PARAM_STR);
            $stmt->execute();
            $a = $row = $stmt ->fetch(PDO::FETCH_ASSOC);
            $rowData = null;
            if(!empty($a)){
                $rowData = new User();
                $rowData->userId = $row["user_id"];
                $rowData->userName = $row["user_name"];
                $rowData->password = $row["password"];
                $rowData->mailAddress = $row["mail_address"];
                $rowData->prefecture = $row["prefecture"];
                $rowData->gender = $row["gender"];
                $rowData->message = $row["message"];
                $rowData->userStatus = $row["user_status"];
                $rowData->token = $row["token"];
                $rowData->tokenDeadLine = $row["token_dead_line"];
            }
            $this->dbDisconnect();
            return $rowData;
        }catch(PDOException $e){
            print('取得失敗' .$e->getMessage());
            throw $e;
        }

    }
//お問い合わせ登録
    public function insertContact($contact){
        try{
            $this->dbConnect();
            $stmt=$this->pdo->prepare("INSERT INTO contact(contact_name,user_id,image_name,content) VALUES(:contact_name,:user_id,:image_name,:content)");
            $stmt->bindParam(':contact_name',$contact->contactName,PDO::PARAM_STR);
            $stmt->bindParam(':user_id',$contact->userId,PDO::PARAM_STR);
            $stmt->bindParam('image_name',$contact->imageName,PDO::PARAM_STR);
            $stmt->bindParam('content',$contact->content,PDO::PARAM_STR);
            //実行
            $stmt->execute();
            //切断
            $this->dbDisconnect();
        }catch(PDOException $e){
            print('格納失敗' .$e->getMessage());
            throw $e;
        }
    }



   function getRoomCountByRoomId($roomId){
        try{
            $this->dbConnect();
            $stmt = $this->pdo->prepare('SELECT COUNT(*) cnt FROM room WHERE room_id = :room_id');
            
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            $stmt->execute();
            
            $retList = array();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!empty($row)){
                $rowData = new Room;
                $rowData->roomNumber = $row["cnt"];
                return $rowData;
            }
            $this->dbDisconnect();

            return $retList;
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }
    
}


    function getUserStatusByRoomId($roomId,$userId){
        try{
            $this->dbConnect();
            $stmt = $this->pdo->prepare('
                 SELECT *
                 FROM room_user_status
                 WHERE room_id = :room_id
                 AND user_id = :user_id
            ');
            
            $stmt->bindValue(':room_id',$roomId,PDO::PARAM_STR);
            $stmt->bindValue(':user_id',$userId,PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $userStatus = new RoomUserStatus();
            $userStatus->roomId = $row['room_id'];
            $userStatus->userId = $row['user_id'];
            $userStatus->status = $row['status'];
            $this->dbDisconnect();
            return $userStatus;
        }catch (PDOException $e){
            print('挿入に失敗'.$e->getMessage());
        }    }


 }
?>