<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Item
 *
 * @author Administrator
 */
class Item {
    static private $_tablename = 'item';
    static private $_primarykey = 'itemid';
    
    private $itemid = null;
    private $catid = null;
    private $userid = null;
    private $name = null;
    private $temp_img = null;
    private $desc = null;
    private $status = null;
    
    function __construct($itemid, $catid, $userid, $name, $temp_img, $desc, $status) {
        $this->itemid = $itemid;
        $this->catid = $catid;
        $this->userid = $userid;
        $this->name = $name;
        $this->temp_img = $temp_img;
        $this->desc = $desc;
        $this->status = $status;        
        
    }
    public function getItemid() {
        return $this->itemid;
    }

    public function setItemid($itemid) {
        $this->itemid = $itemid;
    }

    public function getCatid() {
        return $this->catid;
    }

    public function setCatid($catid) {
        $this->catid = $catid;
    }

    public function getUserid() {
        return $this->userid;
    }

    public function setUserid($userid) {
        $this->userid = $userid;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getTemp_img() {
        return $this->temp_img;
    }

    public function setTemp_img($temp_img) {
        $this->temp_img = $temp_img;
    }

    public function getDesc() {
        return $this->desc;
    }

    public function setDesc($desc) {
        $this->desc = $desc;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function Insert() {               
            $insArray["itemid"] =null;
            $insArray["catid"] = $this->catid;
            $insArray["userid"] = $this->userid;
            $insArray["name"] = $this->name;
            $insArray["temp_img"] = $this->temp_img;
            $insArray["desc"] = $this->desc;
            $insArray["status"] = $this->status;

            $user_id = DBAccess::queryInsert(self::$_tablename, $insArray);		
            return $user_id;            
	}
     static function Find($itemid){		
            $result = DBAccess::querySelect(self::$_tablename, self::$_primarykey. '=' .$userid);		
            $row = $result->fetch_object();		
            return new Item($row->itemid,$row->catid,$row->userid,$row->name,$row->temp_img,$row->desc,$row->status);

	}
    static function FindAll($wh = null, $arrFields = "*", $sort = array(), $sortBy = "A", $lStart = 0, $numRecs = 0, $status = array(_ACTIVE)){
            $result = DBAccess::querySelect(self::$_tablename, $wh, $arrFields, $sort, $sortBy, $lStart, $numRecs, $status);				
            $arr = array();	
            while($row = $result->fetch_object()){
                $arr[] =  new  Item($row->itemid,$row->catid,$row->userid,$row->name,$row->temp_img,$row->desc,$row->status);
            }	
            return $arr;		
	}    
    static function Update(){
            $insArray["itemid"] =null;
            $insArray["catid"] = $this->catid;
            $insArray["userid"] = $this->userid;
            $insArray["name"] = $this->name;
            $insArray["temp_img"] = $this->temp_img;
            $insArray["desc"] = $this->desc;
            $insArray["status"] = $this->status;
            $itemid = DBAccess::queryUpdate(self::$_tablename, $insArray, self::$_primarykey . '=' . $this->itemid);
            return $itemid;
		
	}    
}

?>
