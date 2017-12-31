<?php
require_once 'User.php';
require_once 'DBManager.php';
require_once 'SearchAPIManager.php';
require_once ( 'PHPMailer-master/PHPMailerAutoload.php' );
class UserManager{
    public function checkFavoriteRestaurant($restaurantId,$userId){
      $dbm = new DBManager();
        return $dbm->checkFavoriteRestaurant($restaurantId,$userId);
    }

    public function logout(){
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        session_destroy();
    }

    public function logincheck($userid,$pass){
      $dbm = new DBManager();
      $result = $dbm->getUserInfoTblByUserID($userid);

      //$resultがNULLでないか確認
      if(!empty($result)){
         foreach($result as $user){
              $pscc = $this->passwordCheck($pass,$user->password);
         }
         //パスワードが一致
         if($pscc == true){

            //trueを返す
            return true;
         }else{
            //パスワード不一致
            return false;
         }
      }else{
            //id不一致
            return false;
      }
    }

    //パスワードをハッシュ化して抱き合わせる
    public function passwordCheck($inPass,$hashPass){
      $vry = password_verify($inPass,$hashPass);
      return $vry;
    }

    //ログインチェック
    public function loggedinCheck(){
        if (!empty($_SESSION['user_id'])){
            if(empty($this->getUserByUserId($_SESSION['user_id']))){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

    public function checkFriendStatus($userId, $friendUserId){
        //自分が別ユーザに対してどのような状態かを返すメソッド
        //0 フレンド状態
        //1 自分がフレンド申請している
        //2 フレンド申請されている
        //-1 何もしていない
        $dbm = new DBManager;
        $friendList = $dbm->getFriendListByUserId($userId);
        foreach($friendList as $friend){
            if(($friend->userId == $friendUserId || $friend->friendUserId == $friendUserId) && $friend->status == 1){
                return 0;
            } elseif($friend->friendUserId == $friendUserId && $friend->status == 0 ){
                return 1;
            } elseif($friend->userId == $friendUserId && $friend->status == 0 ){
                return 2;
            }
        }
        return -1;
    }
        //フレンドリストをIDから取得
    public function getFriendListByUserId($userid){
      $dbm = new DBManager();

            $list = $dbm->getFriendListByUserId($userid);
            if(!empty($list)){
                return $list;
            }else{
                return NULL;
            }
    }

        //フレンドリストを名前から取得
    public function getFriendListByUserName($userid,$username){
      $dbm = new DBManager();
            $list = $dbm->getFriendListByUserName($userid,$username);
            if(!empty($list)){
                return $list;
            }else{
                return NULL;
            }
    }



        //フレンド申請
    public function friendRequest($friendid){
        $dbm = new DBManager();

            $user_id  = $_SESSION["user_id"] ;
            $friend_user_id = $friendid;
            //申請側
            $dbm->insertFriendRequestByUserId($user_id,$friend_user_id);
        }

         //フレンド受理
    public function friendAccept($friendid){
      $dbm = new DBManager();

            //許可した後はstatusの値が1になる。
            $user_id  = $_SESSION["user_id"] ;
            $friend_user_id = $friendid;
            
            $dbm->updateFriendStatusByUserId($user_id,$friend_user_id);
         }
    public function deleteFriendByFriendUserId($friendUserId){
        $dbm = new DBManager();
        $userId  = $_SESSION["user_id"] ;
        $dbm->deleteFriendByFriendUserId($userId,$friendUserId);
    }
//フレンド削除
    public function friendReject($friendid){
      $dbm = new DBManager();

            $user_id  = $_SESSION["user_id"] ;
            $friend_user_id = $friendid;
            
            $dbm->deleteFriendByFriendUserId($user_id,$friend_user_id);
         }

         //お気に入り削除
     public function deleteFavoriteByRestaurantId($userid,$res_id){
      $dbm = new DBManager();

            $user_id = $userid;
            $restaurantid = $res_id;
            
            $dbm->deleteFavoriteByRestaurantId($user_id,$restaurantid);
    }

         //お気に入りリスト
     public function getFavoriteListByUserId($userid,$page){
      $dbm = new DBManager();
      $api = new SearchAPIManager();
            $user_id = $userid;
            $list = $dbm->getFavoriteListByUserId($user_id,$page);
            if(!empty($list)){
                return $list;
            }else{
                return NULL;
            }
    }


         //お気に入り追加
    public function addFavorite($userId,$restaurantId){
        $dbm = new DBManager();
        $dbm->insertFavoriteByRestaurantId($userId,$restaurantId);
    }

        //履歴取得
    public function getHistoryListByUserId($userId,$page = null){
        $dbm = new DBManager();
        return $dbm->getHistoryListByUserId($userId,$page);
    }
    public function addHistoryByUserId($userId,$roomId){
        $dbm = new DBManager();
        $historyList = $dbm->getHistoryByUserIdAndRoomId($userId,$roomId);
        foreach ((array)$historyList as $room){
            if($room->roomId == $roomId){
                return;
            }
        }
        $dbm->insertHistoryByUserId($userId,$roomId);
    }



//只隈

//ユーザーの情報確認
public function getUserByUserId($userId){
        $dbm = new DBManager();
        return $dbm->getUserByUserId($userId);
    }

//ユーザー登録
    public function registerUser($user){
        $dbm = new DBManager();
        //ユーザの値のチェック
        if(isset($user)){
            //パスワードを変数に格納
            $password = $user->password;
            //変数に格納したパスワードを元にハスワードハッシュへ渡す
            $user->password=$this->passwordHash($password);
            //DBManagerの中のinsertUserへユーザ型を引数にして渡す
            $dbm->insertUser($user);
            //変数の中にユーザ型に格納されているトークンを格納
            $token = $user->token;
        }
        //仮登録メールを送信
        $this->sendMail($user->mailAddress,$user->token);
    }
    public function passwordHash($password){
        //渡された引数を元にパスワードをハッシュする
        $password = password_hash($password,PASSWORD_DEFAULT);
        //パスワードをリターンする
        return $password;
    }
    public function getUserByString($string,$page){
        $dbm = new DBManager();
        return $dbm->getUserByString($string,$page);
    }
    public function checkUserValue($user){
        $dbm = new DBManager();
        //ユーザIDの確認
        if(!empty($user->userId)){
            if(strlen($user->userId)<5 || strlen($user->userId)>20){
                return 'ユーザIDは5文字以上20文字以下にしてください';
            }
            $userId = $user->userId;
            $list = $dbm->getUserByUserId($userId);
            if(!empty($list)){
                if(!$list->userStatus == 1 || !$list->userStatus == 10){
                    return 'そのユーザIDは使用されています。';
                }
            }
        }else{
            return 'ユーザIDを入力してください' ;
        }
        //ユーザの名前の確認
        if(isset($user->userName)){
            $userName=$user->userName;
            if(strlen($userName)<2 || strlen($userName)>20){
                return '名前は半角2文字以上20文字以下にしてください。';
            }
        }
        //パスワードの確認
        if(!empty($user->password)){
            if(strlen($user->password)<6 || strlen($user->password)>30){
                return 'パスワードは6文字以上30文字以下にしてください';
            }
        }
        //メッセージの確認
        if(!empty($user->message)){
            if(strlen($user->message)<10 || 1000 < strlen($user->message)){
                return 'メッセージは半角10文字以上1000文字以下にしてください。';
            }
        }
        return NULL;
    }
    public function createToken(){
        //ランダムなバイト数を作成
        $TOKEN = random_bytes(15);
        //変数の中にバイト数を渡して作成したトークンを格納
        $token = bin2hex($TOKEN);
        //トークンをリターン
        return $token;
    }
    public function sendMail($mailAddress,$token){
        //メールの言語を設定
        mb_language("Japanese");
        //UTF-8でエンコーディングする
        mb_internal_encoding("UTF-8");
        //各変数に情報を格納
        $title = "仮登録のお知らせ";
        $message = "30分以内に以下のURLにアクセスしてください。\n".'http://meetalk.php.xdomain.jp/main/register_comp.php'.'?Token='.$token;
        //メール送信
        $subject = $title;
        $body = $message;
        $to = $mailAddress;
        $fromname = "meetalk";
        $fromaddress = "haru.aso.project@gmail.com";
        $smtp_user = "haru.aso.project@gmail.com";
        $smtp_password = "haru1224";

        $mail = new PHPMailer();
        //$mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->CharSet = 'utf-8';
        $mail->SMTPSecure = 'tls';
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->IsHTML(false);
        $mail->Username = $smtp_user;
        $mail->Password = $smtp_password; 
        $mail->SetFrom($smtp_user);
        $mail->From     = $fromaddress;
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);
        $mail -> Send();
    }
    public function modifyuser($user){
        $dbm = new DBManager();
        $a = $this->getUserByUserId($user->userId);
        //ユーザ情報の確認
        if(isset($user)){
            if(!empty($user->imageName)){
                //変数の中にユーザ型の画像の名前を格納
                $imageName = $user->imageName;
                //ユーザ型のimageNameに作成したimageNameを格納
                $user->imageName = $this->uplodeImage($imageName);
            }else{
                $user->imageName = $a->imageName;
            }
            //DBManagerのupdateUserにユーザ型を渡す
            $dbm->updateUser($user);
        }
    }
    private function createImageName($list){
        //変数の中に今日の日付を格納
        $imageName = date("YmdHis");
        //変数の中にランダムで作成した数値を連結
        $imageName .= mt_rand();
        //arrayの中に画像の拡張子の情報を格納
        $mimearray = array(
            '.gif' => 'image/gif',
            '.jpg' => 'image/jpeg',
            '.png' => 'image/png',
            '.bmp' => 'image/bmp'
        );
        //imageNameの中にサーチして一致した拡張子を連結
        if($imageName.=array_search($list,$mimearray)){
        }
        //imageNameをリターン
        return $imageName;
    }
    public function uplodeImage($imageName){
        //変数の中に分割したimageNameを格納
        $list = explode("[kugiri]",$imageName);
        //変数の中にcreateImageNameで作成したものを格納
        $imageName = $this->createImageName($list);
        //作成した画像をフォルダに格納 
        move_uploaded_file($list[1],$_SERVER['DOCUMENT_ROOT'].'/main/img/user_img/'.$imageName);
        //imageNameをリターン
        return $imageName;
    }
    public function unsubscribeUser($userId){
        $dbm = new DBManager();
        if(!isset($_SESSION)){
            session_start();
        }
        if($_SESSION["user_id"] == $userId){
            $dbm->unsubscribeUser($userId);
        }
        $this->logout();
    }
    public function changeTempToMainRegister($token){
        $dbm = new DBManager();
        $dbm->changeTempToMainRegister($token);
    }
//トークンの確認
public function checkToken($userToken){
        $dbm = new DBManager();
        $user= $dbm->checkToken($userToken);
        if(!empty($user)){
            $today = date("Y-m-d H:i:s");
            if($user->userStatus != 10){
                if($today < $user->tokenDeadLine){
                    return NULL;
                }else{
                    return '有効期限が過ぎています';
                }
            }else{
                return '既に会員登録が済んでいます';
            }
        }else{
            return 'トークンが正しくありません';
        }
    }

}
?>
