<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Fbuser
 *
 * @author Administrator
 */
class Fbuser {
    static private $_tablename = 'fb_users';
    static private $_primarykey = 'fb_userid';
    
    private $fb_userid = null;
    private $fb_username = null;
    private $fb_firstname = null;
    private $fb_lastname = null;
    private $status = null;
    
    function __construct($fb_userid, $fb_username, $fb_firstname, $fb_lastname, $status) {
        $this->fb_userid = $fb_userid;
        $this->fb_username = $fb_username;
        $this->fb_firstname = $fb_firstname;
        $this->fb_lastname = $fb_lastname;
        $this->status = $status;
    }
    public function getFb_userid() {
        return $this->fb_userid;
    }

    public function setFb_userid($fb_userid) {          
        $this->fb_userid = $fb_userid;           
    }

    public function setFb_username($fb_username) {
        $this->fb_username = $fb_username;
    }
    
    public function getFb_username() {
        return $this->fb_username;
    }    

    public function getFb_firstname() {
        return $this->fb_firstname;
    }

    public function setFb_firstname($fb_firstname) {
        $this->fb_firstname = $fb_firstname;
    }

    public function getFb_lastname() {
        return $this->fb_lastname;
    }

    public function setFb_lastname($fb_lastname) {
        $this->fb_lastname = $fb_lastname;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function Insert(){
         $insArray["fb_userid"] = $this->fb_userid;
         $insArray["fb_username"] = $this->fb_username;
         $insArray["fb_firstname"] = $this->fb_firstname;
         $insArray["fb_lastname"] = $this->fb_lastname;
         $insArray["status"] = $this->status;               
         $fb_userid = DBAccess::queryInsert(self::$_tablename, $insArray);		
         return $fb_userid;
    }
    static function Find($userid){
        $result = DBAccess::querySelect(self::$_tablename, self::$_primarykey. '=' .$userid);		
        $row = $result->fetch_object();		
        return new Fbuser($row->fb_userid,$row->fb_username,$row->fb_firstname,$row->fb_lastname,$row->status);
    }
    static function FindAll($wh = null, $arrFields = "*", $sort = array(), $sortBy = "A", $lStart = 0, $numRecs = 0, $status = array(_ACTIVE)){
        $result = DBAccess::querySelect(self::$_tablename, $wh, $arrFields, $sort, $sortBy, $lStart, $numRecs, $status);				
                $arr = array();	
		while($row = $result->fetch_object()){
                    $arr[] =  new  Fbuser($row->fb_userid,$row->fb_username,$row->fb_firstname,$row->fb_lastname,$row->status);
		}	
		return $arr;	
    }
    function Update(){
         $insArray["fb_userid"] = $this->fb_userid;
         $insArray["fb_username"] = $this->fb_username;
         $insArray["fb_firstname"] = $this->fb_firstname;
         $insArray["fb_lastname"] = $this->fb_lastname;
         $insArray["status"] = $this->status;
        $id = DBAccess::queryUpdate(self::$_tablename, $insArray, self::$_primarykey . '=' . $this->fb_userid);
        return $id;
	
    }
    
    static function valUser($userid){        
        $user_obj = self::FindAll("fb_userid ='".$userid."'and status='1'");       
        if(count($user_obj)==0){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    
    static function FindFb_name($fb_userid){
        $fbuser_objs = self::FindAll("fb_userid ='".$fb_userid."'");
        //print_r($fbuser_objs);
        foreach ($fbuser_objs as $fbuser_obj) {
            $fbusername=$fbuser_obj->getFb_username();
        }
        return $fbusername;
        
    }
}

?>
