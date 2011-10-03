<?php

class DBAccess{

	static private $_DBHOST = _DB_SERVER_;
	
	static private $_DBUSER = _DB_USER_;
	
	static private $_DBPASS	= _DB_PASSWD_;
	
	static private $_DBNAME = _DB_NAME_;
	
	static private $_DEBUGMODE = false;
	
	static function enableDebug(){
	    self::$_DEBUGMODE = true;
	}
	
	static function query($sql){		
		if(self::$_DEBUGMODE){
            die($sql);
        }
        else{
            $mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
            if($mysqli){
               $result = $mysqli->query($sql) or die($mysqli -> error);
                return $result;
            }
            else{
                die(die($mysqli -> error));
            }
        }
		
	}
	
	static function executeInstallQuery($sql){
        $mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
        if(mysqli_connect_errno()){
        	return array(
        			'errno' => mysqli_connect_errno(),
        	 		'error' => mysqli_connect_error(),
        			);
        }else{
        	// query caouldnt execute
        	if(!$mysqli->query($sql)){
        		return array(
        				'errno' => $mysqli->errno,
        	 			'error' => $mysqli->error,
        				'insert_id'=> 0,
        				);
        	}else{
        		return array(
						'errno' => true,
        				'error' => true,
        				'insert_id'=> $mysqli->insert_id,        				
        				);
        	}
        }
	}
	
	static function querySelect($tableName, $arrCon = false, $arrFields = "*", $sort = array(), $sortBy = "A", $lStart = 0, $numRecs = 0, $status = array(_ACTIVE, _INACTIVE)){
	    $fNames=is_array($arrFields) ? implode(",",$arrFields) : $arrFields;
	    if($arrCon!==false && $arrCon!=""){
            $query="select $fNames from $tableName where $arrCon and status IN (" . implode(",", $status) . ")";
        }
        else{
            $query="select $fNames from $tableName where status IN (" . implode(",", $status) . ")";
        }
        if($sort){
            $strSort=implode(",",$sort);
            $query.=" order by $strSort";
            if($sortBy=="D"){
                $query.=" desc";
            }
            elseif($sortBy=="A"){
                $query.=" asc";
            }
        }
        if($numRecs){
         $query.=" limit $lStart,$numRecs";
        }
        if(self::$_DEBUGMODE){
            die($query);
        }
        else{
            $mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
            //echo $query;
            if($mysqli){
            	$result = $mysqli->query($query) or die($mysqli -> error . $query);
                return $result;
            }
            else{
                die(die($mysqli -> error));
            }
        }
        //$query;
	}
	
	static function queryInsert($tblName, $insArr){
        $fNString=implode(",",array_keys($insArr));
        $insArr = array_map('addslashes', $insArr);

        $fValString="'".implode("','",$insArr)."'";
        $sqlString="insert into $tblName ($fNString) values ($fValString)";    
        if(self::$_DEBUGMODE){
            die($sqlString);
        }
        else{
            $mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
            if($mysqli){
            	$result =  $mysqli->query($sqlString) or die($sqlString.$mysqli -> error);
                return $mysqli->insert_id;
            }
            else{
                die(die($mysqli -> error));
            }
        }
    }
    
    static function queryUpdate($tblName,$insArr,$condStr=""){
        $fUpString=array();
        foreach($insArr as $key => $val){
            $fUpString[] = $key."='".$val."'";
        }
        $fUpString = implode(",", $fUpString);            
        if($condStr){
            $sqlString="update $tblName set $fUpString where $condStr";
            //echo $sqlString;
        }
        else{
            $sqlString="update $tblName set $fUpString";
        }        
        if(self::$_DEBUGMODE){
            die($sqlString);
        }
        else{
            $mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
            if($mysqli){ 
            	$result =  $mysqli->query($sqlString) or die($mysqli -> error);
                return $result;
            }
            else{
                die(die($mysqli -> error));
            }
        }
    }
    
    static function queryManual($sqlString){
        $mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
        if($mysqli){
        	$result =  $mysqli->query($sqlString) or die($mysqli -> error);
            return $result;
        }
        else{
            die(die($mysqli -> error));
        }
    }
    
    static function queryDelete($tblName,$condStr=""){
    	
    	$mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
    	$fDString	=	"delete from $tblName ";
    	if ($condStr != "") {
    		$fDString  .=	" where $condStr";
    	}
    	if(self::$_DEBUGMODE){
            die($fDString);
        }
        
        if($mysqli){
        	$result =  $mysqli->query($fDString) or die($mysqli -> error);
            return $result;
        }
        else{
            die(die($mysqli -> error));
        }
        
    }
    
	static public function tryToConnect($server, $user, $pwd, $db,$tables="")
	{
		
		if (!$link = @mysql_connect($server, $user, $pwd))
			return 1;
		if (!@mysql_select_db($db, $link))
			return 2;
		if(DBAccess::table_exists($tables,$db))
			return 3;
		return 0;
	} 
	 
	static public	function table_exists ($db_tables, $db) { 
	
		$tables = mysql_list_tables ($db); 
		while (list ($temp) = mysql_fetch_array ($tables)) {
			if (in_array($temp,$db_tables)) {
				return TRUE;
			}
		}
		return FALSE;
	}
  

	/**
	 * Sanitize data which will be injected into SQL query
	 *
	 * @param string $string SQL data which will be injected into SQL query
	 * @param boolean $htmlOK Does data contain HTML code ? (optional)
	 * @return string Sanitized data
	 */
	static public function pSQL($string, $htmlOK = false)
	{
	$con = mysql_connect(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS);

		if (_PS_MAGIC_QUOTES_GPC_)
			$string = stripslashes($string);
		if (!is_numeric($string))
		{
			$string = _PS_MYSQL_REAL_ESCAPE_STRING_ ? mysql_real_escape_string($string,$con) : addslashes($string);
			if (!$htmlOK){
				$string = strip_tags(self::nl2br2($string),_ALOW_TAG);
				}
		}
			
		return $string;
	}
	
	/**
	 * Convert \n to <br />
	 *
	 * @param string $string String to transform
	 * @return string New string
	 */
	function nl2br2($string)
	{
		return str_replace(array("\r\n", "\r", "\n"), '<br />', $string);
	}	
	
	static function begin(){
                                    $mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
		return $mysqli->query('BEGIN');
	}

	static function commit(){
                                    $mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
		return $mysqli->query('COMMIT');
	}
	
	static function rollback(){
                                    $mysqli = new mysqli(self::$_DBHOST, self::$_DBUSER, self::$_DBPASS, self::$_DBNAME);
		return $mysqli->query('ROLLBACK');
	}
	
}
?>
