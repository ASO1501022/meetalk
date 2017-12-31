<?php
require_once "DBManager.php";
require_once "UserManager.php";
class ContactManager{
    public function insertContact($contact){
        $dbm = new DBManager();
        $umg = new UserManager();
        //変数の中にcontactの中のimageNameを格納
        if(!empty($user->imageName)){
            $imageName = $contact->imageName;
            //contactの中のimageNameにUserManagerで作成した画像の名前を格納
          //  $contact->imageName=$umg->uplodeImage($imageName);
        }
        //DBのinsertContactへcontact型を送りDBに格納
        $dbm->insertContact($contact);
    }
}

 ?>