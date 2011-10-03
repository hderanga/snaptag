<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author Administrator
 */
class User {
    static private $_tablename = 'users';
    static private $_primarykey = 'userid';
    
    private $userid = null;
    private $firstname = null;
    private $lastname = null;
    private $company = null;
    private $email = null;
    private $password = null;
    private $regdate = null;
    private $lastlog = null;
    private $status =  null;
    private $act_code = null;
    
    function __construct($userid, $firstname, $lastname, $company, $email, $password, $regdate, $lastlog, $status, $act_code) {
        $this->userid = $userid;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->company = $company;
        $this->email = $email;
        $this->password = $password;
        $this->regdate = $regdate;
        $this->lastlog = $lastlog;
        $this->status = $status;
        $this->act_code = $act_code;
    }


    public function setUserId($userid){
        $this->userid=$userid;
    }
    public function getUserId(){
        return $this->userid;
    }
    public function setFirstname($firstname){
        $this->firstname=$firstname;
    }
    public function getFirstname(){
        return $this->firstname;
    }
    public function setLastname($lastname){
        $this->lastname = $lastname;
    }
    public function getLastname(){
        return $this->lastname;
    }
    public function setComapny($company){
        $this->company = $company;
    }
    public function getCompany(){
        return $this->company;
    }
    public function setEmail($email){
        $this->email = $email;
    }
    public function getEmail(){
        return $this->email;
    }
    public function setPassword($password){
        $char=strlen($password);
        if($char>=6){
            $this->password=md5($password);
        }
        else {
            return FALSE;
        }
        
    }
    public function getPassword(){
        return $this->password;
    }
    public function getRegdate() {
        return $this->regdate;
    }

    public function setRegdate($regdate) {
        $this->regdate = $regdate;
    }

    public function getLastlog() {
        return $this->lastlog;
    }

    public function setLastlog($lastlog) {
        $this->lastlog = $lastlog;
    }

        public function setStatus($status){
        $this->status=$status;
    }
    public function getStatus(){
        return $this->status;
    }
    public function setAct_code($act_code){
        $this->act_code = $act_code;
    }
    
    public function Insert() {
                
                $insArray["userid"] ="Null";
		$insArray["f_name"] = $this->firstname;
		$insArray["l_name"] = $this->lastname;
                $insArray["company"] = $this->company;
                $insArray["email"] = $this->email;
                $insArray["password"] = $this->password;
                $insArray["reg_date"] = $this->regdate;
                $insArray["last_log"] = $this->lastlog;
                $insArray["status"] = $this->status;
                $insArray["act_code"] = $this->act_code;
                
		$user_id = DBAccess::queryInsert(self::$_tablename, $insArray);
		
		return $user_id;            
	}
        
    static function Find($userid){
		
		$result = DBAccess::querySelect(self::$_tablename, self::$_primarykey. '=' .$userid);		
		$row = $result->fetch_object();		
		return new User($row->userid,$row->f_name,$row->l_name,$row->company,$row->email,$row->password,$row->reg_date,$row->lastlog,$row->status,$row->act_code);
                
	}
    static function FindAll($wh = null, $arrFields = "*", $sort = array(), $sortBy = "A", $lStart = 0, $numRecs = 0, $status = array(_ACTIVE)){
		$result = DBAccess::querySelect(self::$_tablename, $wh, $arrFields, $sort, $sortBy, $lStart, $numRecs, $status);				
                $arr = array();	
		while($row = $result->fetch_object()){
                    $arr[] =  new  User($row->userid,$row->f_name,$row->l_name,$row->company,$row->email,$row->password,$row->regdate,$row->lastlog,$row->status,$row->act_code);
		}	
		return $arr;		
	}    
  
    function Update(){
        $insArray["f_name"] = $this->firstname;
        $insArray["l_name"] = $this->lastname;
        $insArray["company"] = $this->company;
        $insArray["email"] = $this->email;
        $insArray["password"] = $this->password;
        $insArray["reg_date"] = $this->regdate;
        $insArray["last_log"] = $this->lastlog;
        $insArray["status"] = $this->status;
        $insArray["act_code"] = $this->act_code;
        $id = DBAccess::queryUpdate(self::$_tablename, $insArray, self::$_primarykey . '=' . $this->userid);
        return $id;
		
	}
    public function gen_actcode(){
        $activation = md5(uniqid(rand(), true));
        return $activation;
    }  
    public function sendEmail($email,$act_code){
        $message = "To Activate your account please click on this link : \n\n";
        $message .= _SITE_URL_.'index.php?page=singup&email='. urlencode($email)."&key=$act_code";
        //mail($email,'Registration Confirmation',$message,'From:eranga@cenango.lk');
        echo $message;
        $msg = "Email send succesfully";
        return $msg;
    }     
    public function checkEmail($email){
        $user_obj=$this->FindAll("email = '".$email."' and status='"._ACTIVE."'");       
        $check = count($user_obj);    
        if($check==0){          
            return $email;
        }
    }
    static function findF_name($userid){
        $user = self::FindAll("userid ='".$userid."'");
        foreach($user as $user_obj)
            $first_name=$user_obj->getFirstname ();
        return $first_name;
    }
    static function resetPasswd($email){
        $user_objs = self::FindAll("email ='".$email."'");
        
        $user_obj = $user_objs[0];
        $user_email=$user_obj ->getEmail();
        $user_id = $user_obj ->getUserId();
        $random_key=uniqid(rand());
        $sec_key = base64_encode($user_id+678);
        
        $key = $random_key;
        $message = "If you want to resset your password.Please follow the link : \n\n";
        $message .=  _SITE_URL_.'index.php?page=profile_edit&key='.$key.'&sec_key='.$sec_key;
        //mail($user_email, 'Reset password', $message, 'From:eranga@cenango.lk');
        echo $message;
        return $user_id;
        
    }
    static function dateTime(){
         $dateTime = date("F j, Y, g:i a");
         return $dateTime;
    }
}



?>
