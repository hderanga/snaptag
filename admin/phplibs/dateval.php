<?php
function valDate($str){
	if(eregi("[0-9]{4}\-[0-9]{2}\-[0-9]{2}", $str)){
		list($year, $month, $day) = explode("-", $str);
		if(checkdate ($month, $day, $year)){
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
?>
