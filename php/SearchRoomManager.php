<?php
require_once "DBManager.php";
require_once "SearchAPIManager.php";
require_once "RoomManager.php";
class SearchRoomManager{
    public function searchTrendRestaurant(){
        $dbm = new DBManager();
        $roomMng = new RoomManager();
        $searchAPIMng = new SearchAPIManager();
        $a = $dbm->searchTrendRestaurant();
        $list = null;
        foreach ($a as $_restaurant) {
            $restaurant = new Restaurant();
            $restaurant1 = $searchAPIMng->searchRestaurantByRestaurantId($_restaurant->restaurantId);
            if(empty($restaurant1)) continue;
            $restaurant->restaurantId = $_restaurant->restaurantId;
            $restaurant->name = $restaurant1->name;
            $restaurant->image = $restaurant1->image;
            $restaurant->roomNumber = $_restaurant->roomNumber;
            $list[] = $restaurant;
        }
        return $list;
    }
    public function searchFewTrendRestaurant(){
        $dbm = new DBManager();
        $roomMng = new RoomManager();
        $searchAPIMng = new SearchAPIManager();
        $a = $dbm->searchFewTrendRestaurant();
        $list = null;
        foreach ($a as $_restaurant) {
            $restaurant = new Restaurant();
            $restaurant1 = $searchAPIMng->searchRestaurantByRestaurantId($_restaurant->restaurantId);
            if(empty($restaurant1)) continue;
            $restaurant->restaurantId = $_restaurant->restaurantId;
            $restaurant->name = $restaurant1->name;
            $restaurant->image = $restaurant1->image;
            $restaurant->roomNumber = $_restaurant->roomNumber;
            $list[] = $restaurant;
        }
        return $list;
    }
    public function searchRoomListByRestaurantId($restaurantId,$order,$page){
        //部分一致でも可
        $dbm=new DBManager();
        $list=$dbm->getRoomListByRestaurantId($restaurantId,$order,$page);
        if(!empty($list)){
            return $list;
        }else{
            return NULL;
        }
    }
    public function searchRoomByTagName($tagName,$searchAddress,$order,$page){
        $dbm = new DBManager();
        $list = $dbm->getRoomByTagName($tagName,$searchAddress,$order,$page);
        if(!empty($list)){
            return $list;
        }else{
            return NULL;
        }

    }
    
    public function searchRoom($roomName,$address,$order,$page){
        //部分一致でも可
        $dbm=new DBManager();
        $list = null;
        if(empty($address)){
            $list=$dbm->getRoomByRoomName($roomName,$order,$page);
        }else{
            $list=$dbm->getRoomByRoomNameAndAddress($roomName,$address,$order,$page);
        }
        if(!empty($list)){
            return $list;
        }else{
            return NULL;
        }
    }
    public function searchRoomByAddress($address,$page){
        //部分一致でも可
        $dbm=new DBManager();
        $list=$dbm->getRoomByAddress($address,$page);
        if(!empty($list)){
            return $list;
        }else{
            return NULL;
        }
    }

    public function searchRoomByHoldDate($holdDate,$order,$page){
        //DBのdead_lineを開催日時として取得
        //部分一致でも可
        $dbm = new DBManager();
        $list = $dbm->getRoomByHoldDate($holdDate,$order,$page);
        if(!empty($list)){
            return $list;
        }else{
            return NULL;
        }
    }
    public function searchTrendTag(){
        //roomにつけられているタグの中から降順で出力
        $dbm = new DBManager();
        $list = $dbm->getTrendTagList();
        return $list;
    }
}
?>