<?php
function valNonEmpty($str){
	if(is_array($str)){
		if(count($str)>0){
			$retVal=false;
			foreach($str as $val){
				if(strlen($val)>0){
					$retVal=true;
					break;
				}
			}
			return $retVal;
		}
		else{
			return false;
		}
	}
	else{
		return (strlen($str)>0)?true:false;
	}
}

function valAlpha($str){
	if(valNonEmpty($str)){
		return ctype_alpha($str);
	}
	else{
		return false;
	}
}

function valNum($str){	
	if(valNonEmpty($str)){
		return ctype_digit($str);
	}
	else{
		return false;
	}
}

function valDecimal($str){	
	if(ereg("^[0-9]+(.[0-9]+)?$",$str)){
		return true;
	}
	else{
		return false;
	}
}

function valAlphaNum($str){	
	if(valNonEmpty($str)){
		return ctype_alnum($str);
	}
	else{
		return false;
	}
}

function valEmail($str){
	if(eregi ( 
    "^[a-z0-9\._-]+". 
    "@". 
    "([a-z0-9][a-z0-9-]*[a-z0-9]\.)+". 
    "([a-z]+\.)?". 
    "([a-z]+)$", 
    $str)) { 
        return TRUE; 
    } 
    return FALSE; 
}

function valDate($str){
	if(eregi("[0-9]{4}\-[0-9]{2}\-[0-9]{2}", $str)){
		list($year, $month, $day) = explode("-", $str);
		if(checkdate($month, $day, $year)){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

function valCheckEqual($str,$param){
	if($str == $param[0])return true;
	return false;
}

?>