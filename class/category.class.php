<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of category
 *
 * @author Administrator
 */
class category {
    static private $_tablename = 'categories';
    static private $_primarykey = 'catid';
    
    private $catid = null;
    private $catname = null;
    private $catlink = null;
    private $catstatus = null;
    
    function __construct($catid, $catname, $catlink, $catstatus) {
        $this->catid = $catid;
        $this->catname = $catname;
        $this->catlink = $catlink;
        $this->catstatus = $catstatus;
    }
    public function getCatid() {
        return $this->catid;
    }

    public function setCatid($catid) {
        $this->catid = $catid;
    }

    public function getCatname() {
        return $this->catname;
    }

    public function setCatname($catname) {
        $this->catname = $catname;
    }

    public function getCatlink() {
        return $this->catlink;
    }

    public function setCatlink($catlink) {
        $this->catlink = $catlink;
    }

    public function getCatstatus() {
        return $this->catstatus;
    }

    public function setCatstatus($catstatus) {
        $this->catstatus = $catstatus;
    }

    
    public function Insert() {               
                $insArray["catid"] ="Null";
		$insArray["catname"] = $this->catname;
		$insArray["catlink"] = $this->catname;
                $insArray["status"] = $this->catstatus;                
		$itemid = DBAccess::queryInsert(self::$_tablename, $insArray);		
		return $itemid;            
	}
    static function Find($catid){		
		$result = DBAccess::querySelect(self::$_tablename, self::$_primarykey. '=' .$userid);		
		$row = $result->fetch_object();		
		return new Category($row->catid,$row->catname,$row->catlink,$row->status);
                
	}
    static function FindAll($wh = null, $arrFields = "*", $sort = array(), $sortBy = "A", $lStart = 0, $numRecs = 0, $status = array(_ACTIVE)){
		$result = DBAccess::querySelect(self::$_tablename, $wh, $arrFields, $sort, $sortBy, $lStart, $numRecs, $status);				
                $arr = array();	
		while($row = $result->fetch_object()){
                    $arr[] =  new  Category($row->catid,$row->catname,$row->catlink,$row->status);
		}	
		return $arr;		
	}    
}


?>
