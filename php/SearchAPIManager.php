<?php
require_once 'SearchRestaurantManager.php';
require_once 'Restaurant.php';
set_error_handler(
    function ($errno, $errstr, $errfile, $errline) {
        throw new ErrorException(
            $errstr, 0, $errno, $errfile, $errline
        );
    }
);


class SearchAPIManager
{

    /**
     * アクセスキー
     * @var string
     */
    private $token = 'd9a7268cf3b08a4128a95055fabd3375';

    /**
     * 都道府県リストを取得
     * @return object
     */
    public function getPrefectureList()
    {
        $uri = "http://api.gnavi.co.jp/master/PrefSearchAPI/20150630/";
        $acckey = $this->token;
        $format = "json";
        $url = sprintf("%s?format=%s&keyid=%s", $uri, $format, $acckey);

        $ch = curl_init(); // 初期化
        curl_setopt( $ch, CURLOPT_URL, $url ); // URLの設定
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // 出力内容を受け取る設定
        $json = curl_exec( $ch ); // データの取得
        curl_close($ch); // cURLのクローズ

        $obj = json_decode($json);
        return $obj;
    }

    /**
     * レストラン検索
     * @return object
     */
    public function searchRestaurantByRestaurantName($page)
    {
        $uri = "http://api.gnavi.co.jp/RestSearchAPI/20150630/";
        $acckey = $this->token;
        $format = "json";

        $get = [
            'format' => $format
            , 'keyid' => $acckey
            , 'offset_page' => $page
        ];
        if (!is_null(filter_input_array(INPUT_GET))) {
            $get += filter_input_array(INPUT_GET);
        }
        $url = sprintf("%s?%s", $uri, http_build_query($get));

        $ch = curl_init(); // 初期化
        curl_setopt( $ch, CURLOPT_URL, $url ); // URLの設定
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // 出力内容を受け取る設定
        $json = curl_exec( $ch ); // データの取得
        curl_close($ch); // cURLのクローズ

        $obj = json_decode($json);
        try{
            //取得したデータを１件ずつループしながらクラスに入れていく
            $RestaurantData = array();
            foreach ($obj->rest as $row) {
                //データを取るクラスをnewする
                $rowData = new Restaurant();

                //APIから取れた情報を、クラスに入れていく
                $rowData->restaurantId     = $row->id;
                $rowData->name             = $row->name;
                $rowData->nameKana         = $row->name_kana;
                $rowData->tel             = $row->tel;
                $rowData->address          = $row->address;
                $rowData->image            = $row->image_url;
                $rowData->pcUrl            = $row->url;
                $rowData->mobileurl        = $row->url_mobile;
                $rowData->openTime         = $row->opentime;
                $rowData->holiday          = $row->holiday;
                $rowData->prShort          = $row->pr->pr_short;
                $rowData->prLong          = $row->pr->pr_long;
                $rowData->budget           = $row->budget;
                $rowData->categoryName = $this->getTagListByAPITagList($row->code->category_name_l,$row->code->category_name_s);
                $this->checkAPIData($rowData);
                //取得した一件を配列に追加する
                array_push($RestaurantData,$rowData);
            }

        return $RestaurantData;
        }catch(Exception $e){
            return NULL;
        }
    }

    private function getTagListByAPITagList($l,$s){
        $tags = array();
        foreach ($l as $tag) {
            if(is_string($tag)){
                $_tags = explode('・',$tag);
                $tags = array_merge($tags,$_tags);
            }
        }
        $tags = array_unique($tags);
        foreach ($s as $tag) {
            if(is_string($tag)){
                $_tags = explode('・',$tag);
                $tags = array_merge($tags,$_tags);
            }
        }
        // print_r($tag1);
        $tags = array_unique($tags);
        return $tags;
    }
    public function checkAPIData($restaurant){
        if(isset($restaurant->name)) {
            if (!is_string($restaurant->name) && !is_numeric($restaurant->name)){
                $restaurant->name = "なし";
            }
        }else{
            $restaurant->name = "なし";
        }
        if(isset($restaurant->nameKana)) {
            if (!is_string($restaurant->nameKana) && !is_numeric($restaurant->nameKana)){
                $restaurant->nameKana = "なし";
            }
        }else{
            $restaurant->nameKana = "なし";
        }
        if(isset($restaurant->tel)) {
            if (!is_string($restaurant->tel) && !is_numeric($restaurant->tel)){
                $restaurant->tel = "なし";
            }
        }else{
            $restaurant->tel = "なし";
        }
        if(isset($restaurant->pcUrl)) {
            if (!is_string($restaurant->pcUrl) && !is_numeric($restaurant->pcUrl)){
                $restaurant->pcUrl = "なし";
            }
        }else{
            $restaurant->pcUrl = "なし";
        }
        if(isset($restaurant->mobileurl)) {
            if (!is_string($restaurant->mobileurl) && !is_numeric($restaurant->mobileurl)){
                $restaurant->mobileurl = "なし";
            }
        }else{
            $restaurant->mobileurl = "なし";
        }
        if(isset($restaurant->openTime)) {
            if (!is_string($restaurant->openTime) && !is_numeric($restaurant->openTime)){
                $restaurant->openTime = false;
            }
        }else{
            $restaurant->openTime = "なし";
        }
        if(isset($restaurant->holiday)) {
            if (!is_string($restaurant->holiday) && !is_numeric($restaurant->holiday)){
                $restaurant->holiday = "なし";
            }
        }else{
            $restaurant->holiday = "なし";
        }
        if(!empty($restaurant->prShort)){
            if(is_string($restaurant->prShort)){
                $restaurant->prShort = str_ireplace('<br>','',$restaurant->prShort);
            }
        }
        if(isset($restaurant->prShort)) {
            if (!is_string($restaurant->prShort) && !is_numeric($restaurant->prShort)){
                $restaurant->prShort = "なし";
            }
        }else{
            $restaurant->prShort = "なし";
        }
        if(isset($restaurant->prLong)) {
            if (!is_string($restaurant->prLong) && !is_numeric($restaurant->prLong)){
                $restaurant->prLong = "なし";
            }
        }else{
            $restaurant->prLong = "なし";
        }
        if(isset($restaurant->budget)) {
            if (!is_string($restaurant->budget) && !is_numeric($restaurant->budget)){
                $restaurant->budget = "なし";
            }
        }else{
            $restaurant->budget = "なし";
        }
        
    }

//レストランID検索(一件のみ)
    public function searchRestaurantByRestaurantId($id){ 

        $uri    = "http://api.gnavi.co.jp/RestSearchAPI/20150630/";
        $acckey = $this->token;
        $format = "json";

        $url  = sprintf("%s%s%s%s%s%s%s",$uri,"?format=",$format,"&keyid=",$acckey,"&id=",$id);

        $ch = curl_init(); // 初期化
        curl_setopt( $ch, CURLOPT_URL, $url ); // URLの設定
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // 出力内容を受け取る設定
        $json = curl_exec( $ch ); // データの取得
        curl_close($ch); // cURLのクローズ

        $obj  = json_decode($json);
        try{
            //データを取るクラスをnewする
            $rowData = new Restaurant();
            //APIから取れた情報を、クラスに入れていく
            $rowData->restaurantId = $obj->rest->id;
            $rowData->name         = $obj->rest->name;
            $rowData->nameKana     = $obj->rest->name_kana;
            $rowData->tel          = $obj->rest->tel;
            $rowData->address      = $obj->rest->address;
            $rowData->image        = $obj->rest->image_url;
            $rowData->pcUrl        = $obj->rest->url;
            $rowData->mobileurl    = $obj->rest->url_mobile;
            $rowData->openTime     = $obj->rest->opentime;
            $rowData->holiday      = $obj->rest->holiday;
            $rowData->prShort      = $obj->rest->pr->pr_short;
            $rowData->prLong       = $obj->rest->pr->pr_long;
            $rowData->budget       = $obj->rest->budget;
            $rowData->categoryName = $this->getTagListByAPITagList($obj->rest->code->category_name_l,$obj->rest->code->category_name_s);
            $this->checkAPIData($rowData);
            return $rowData;
        }catch(Exception $e){
            return NULL;
        }
    }
    public function searchRestaurantByRestaurantNameAndAddress($name,$address,$page)
    {
        $uri = "http://api.gnavi.co.jp/RestSearchAPI/20150630/";
        $acckey = $this->token;
        $format = "json";
        $get = null;
        if(!empty($address)){
            $get = [
                'format' => $format
                , 'keyid' => $acckey
                , 'offset_page' => $page
                , 'name' => $name
                , 'address' => $address
            ];
        }else{
            $get = [
                'format' => $format
                , 'keyid' => $acckey
                , 'offset_page' => $page
                , 'name' => $name
            ];
        }
        $url = sprintf("%s?%s", $uri, http_build_query($get));

        $ch = curl_init(); // 初期化
        curl_setopt( $ch, CURLOPT_URL, $url ); // URLの設定
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // 出力内容を受け取る設定
        $json = curl_exec( $ch ); // データの取得
        curl_close($ch); // cURLのクローズ

        $obj = json_decode($json);
        $list = array();
            //取得したデータを１件ずつループしながらクラスに入れていく
        try{
            foreach ($obj->rest as $row) {
                //データを取るクラスをnewする
                $rowData = new Restaurant();
                //APIから取れた情報を、クラスに入れていく
                $rowData->restaurantId     = $row->id;
                $rowData->name             = $row->name;
                $rowData->nameKana         = $row->name_kana;
                $rowData->tel             = $row->tel;
                $rowData->address          = $row->address;
                $rowData->image            = $row->image_url;
                $rowData->pcUrl            = $row->url;
                $rowData->mobileurl        = $row->url_mobile;
                $rowData->openTime         = $row->opentime;
                $rowData->holiday          = $row->holiday;
                $rowData->prShort          = $row->pr->pr_short;
                $rowData->prLong          = $row->pr->pr_long;
                $rowData->budget           = $row->budget;
                $rowData->categoryName = $this->getTagListByAPITagList($row->code->category_name_l,$row->code->category_name_s);
                $this->checkAPIData($rowData);
                $list[] = $rowData;
                //取得した一件を配列に追加する
            }
            return $list;
        }catch(Exception $e){
            return null;
        }

    }
//レストランID検索(お気に入りリスト用)
    public function searchRestaurantByRestaurantIdforFavorite($id)
    { 

        $uri    = "https://api.gnavi.co.jp/RestSearchAPI/20150630/";
        $acckey = $this->token;
        $format = "json";


        $url  = sprintf("%s%s%s%s%s%s%s", $uri, "?format=", $format, "&keyid=", $acckey,"&id=",$id);

        $ch = curl_init(); // 初期化
        curl_setopt( $ch, CURLOPT_URL, $url ); // URLの設定
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // 出力内容を受け取る設定
        $json = curl_exec( $ch ); // データの取得
        curl_close($ch); // cURLのクローズ

        $obj  = json_decode($json);
        try{
            $RestaurantData = array();
                //データを取るクラスをnewする
                $rowData = new Restaurant();

                //APIから取れた情報を、クラスに入れていく
                $rowData->restaurantId = $obj->rest->id;
                $rowData->name         = $obj->rest->name;
                $rowData->nameKana     = $obj->rest->name_kana;
                $rowData->tel         = $obj->rest->tel;
                $rowData->address      = $obj->rest->address;
                $rowData->image        = $obj->rest->image_url;
                $rowData->pcUrl        = $obj->rest->url;
                $rowData->mobileurl    = $obj->rest->url_mobile;
                $rowData->openTime     = $obj->rest->opentime;
                $rowData->holiday      = $obj->rest->holiday;
                $rowData->prShort      = $obj->rest->pr->pr_short;
                $rowData->prLong      = $obj->rest->pr->pr_long;
                $rowData->budget       = $obj->rest->budget;
                $rowData->categoryName = $this->getTagListByAPITagList($row->rest->code->category_name_l,$row->rest->code->category_name_s);
                $this->checkAPIData($rowData);
                //取得した一件を配列に追加する
                array_push($RestaurantData,$rowData);

        return $RestaurantData;
        }catch(Exception $e){
            return NULL;
        }
    }
     
//現在地検索
    public function searchRestaurantByCurrentPosition($lat,$lon,$page)
    { 

        $uri    = "https://api.gnavi.co.jp/RestSearchAPI/20150630/";
        $acckey = $this->token;
        $format = "json";

        $range = 5;

        //配列使う場合
        //$lat = array['lat'];
        //$lon = array['lon'];
 
        $url  = sprintf("%s%s%s%s%s%s%s%s%s%s%s", $uri, "?format=", $format, "&keyid=", $acckey, "&latitude=",$lat,"&longitude=",$lon,"&range=",$range,"&offset_page=",$page);

        $ch = curl_init(); // 初期化
        curl_setopt( $ch, CURLOPT_URL, $url ); // URLの設定
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // 出力内容を受け取る設定
        $json = curl_exec( $ch ); // データの取得
        curl_close($ch); // cURLのクローズ

        $obj = json_decode($json);
        try{
            //取得したデータを１件ずつループしながらクラスに入れていく
            $RestaurantData = array();
            foreach ($obj->rest as $row) {
                //データを取るクラスをnewする
                $rowData = new Restaurant();

                //APIから取れた情報を、クラスに入れていく
                $rowData->restaurantId = $row->id;
                $rowData->name         = $row->name;
                $rowData->nameKana     = $row->name_kana;
                $rowData->tel         = $row->tel;
                $rowData->address      = $row->address;
                $rowData->image        = $row->image_url;
                $rowData->pcUrl        = $row->url;
                $rowData->mobileurl    = $row->url_mobile;
                $rowData->openTime     = $row->opentime;
                $rowData->holiday      = $row->holiday;
                $rowData->prShort      = $row->pr->pr_short;
                $rowData->prLong      = $row->pr->pr_long;
                $rowData->budget       = $row->budget;
                $rowData->categoryName = $this->getTagListByAPITagList($row->code->category_name_l,$row->code->category_name_s);
                $this->checkAPIData($rowData);
                //取得した一件を配列に追加する
                array_push($RestaurantData,$rowData);
            }

        return $RestaurantData;
        }catch(Exception $e){
            return NULL;
        }
    }
}
?>