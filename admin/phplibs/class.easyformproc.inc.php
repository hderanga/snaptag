<?php

################################### Easy Form Process Class ################################
# easyFormProc v1.0.5
# Author : Nazly Ahmed
# Email : me@nazly.net
# Blog : http://www.nazly.net
# Start Date : 4th August 2005
# Last updated Date : 16th September 2005
# Manual : http://www.nazly.net/easyformproc
############################################################################################

include("functions.validation.inc.php");

class easyFormProc{
	var $elements;
	var $elTypes;
	var $hasFileUpload;
	var $hasRTE;
	var $submitBtnName;
	var $errMsg;
	
	function easyFormProc(){
		$this -> elements = array();
		$this -> elementDisHTML = array();
		$this -> elTypes = array("Text","TextArea","Password","Hidden","Select","Radio","Check","File","RTE");
		$this -> hasFileUpload = false;
		$this -> hasRTE = false;
		$this -> submitBtnName = "submit";
		$this -> errMsg = array();
	}
	
	function setElement($name,$data){
		if(!isset($this -> elements[$name])){
			$this -> elements[$name] = $data;
		}
		else{
			die("ERROR : Element <b>$name</b> already set");
		}
	}
	
	function setelm1Element($name,$value = "",$cols = 24,$numLines = 8,$style = "",$validate = false,$errMsg = "",$params = false){
		$this -> setElement($name,array("type" => 1,"validate" => $validate,"value" => $value,
										"cols" => $cols,"rows" => $numLines,"style" => $style,
										"error" => $errMsg,"params" => $params));							
	}
	
	function setElementValue($name,$value){	
		if(isset($this -> elements[$name])){
			$this -> elements[$name]["value"] = $value;
		}
		else{
			die("ERROR : Element <b>$name</b> doesn't exist");
		}
	}	
	
	function doValidation($validate,$value,$errMsg,$params){
		if($validate !== false){
			if($params !== false){
				if(!$validate($value,$params)){
					$this -> errMsg[] = $errMsg;
				}
			}
			else{
				if(!$validate($value)){
					$this -> errMsg[] = $errMsg;
				}
			}
		}
	}
	
	function setTextElement($name,$value = "",$maxLength = 255,$size = 24,$style = "",$validate = false,$errMsg = "",$params = false){		
		$this -> setElement($name,array("type" => 0,"validate" => $validate,"value" => $value,
										"style" => $style,"size" => $size,"maxlength" => $maxLength,
										"error" => $errMsg,"params" => $params));
	}
	
	function setTextAreaElement($name,$value = "",$cols = 24,$numLines = 8,$style = "",$validate = false,$errMsg = "",$params = false){
		$this -> setElement($name,array("type" => 1,"validate" => $validate,"value" => $value,
										"cols" => $cols,"rows" => $numLines,"style" => $style,
										"error" => $errMsg,"params" => $params));							
	}
	
	function setPasswordElement($name,$value = "",$maxLength = 255,$size = 24,$style = "",$showDefault=false,$validate = false,$errMsg = ""){
		$this -> setElement($name,array("type" => 2,"validate" => $validate,"value" => $value,
										"size" => $size,"style" => $style,"maxlength" => $maxLength,
										"default"=>$showDefault,"error" => $errMsg));	
	}
	
	function setHiddenElement($name,$value = ""){
		$this -> setElement($name,array("type" => 3,"value" => $value));	
	}
	
	function setSelectElement($name,$value = array(),$selected = "",$style = "",$height = 1,$mulSelect = false,$validate = false,$errMsg = "",$params = false, $onchange=""){		
		if(!is_array($value))die("ERROR : <b>$name</b> values should be an associative array");
		if($mulSelect && (!is_array($selected)))die("ERROR : <b>$name</b> selected values should be an array");
		$this -> setElement($name,array("type" => 4,"validate" => $validate,"value" => $selected,
										"keys" => $value,"height" => $height,"style" => $style,									
										"multiple" => $mulSelect,"error" => $errMsg,"params" => $params, "onchange"=>$onchange));
	}
	
	function setRadioElement($name,$value = array(),$checked = "",$style = "",$validate = false,$errMsg = "",$params = false){
		if(!is_array($value))die("ERROR : <b>$name</b> values should be an associative array");			
		$this -> setElement($name,array("type" => 5,"keys" => $value,"validate" => $validate,						
										"value" => $checked,"style" => $style,"error" => $errMsg,"params" => $params));	
	}
	
	function setCheckElement($name,$value = array(),$checked = array(),$style = "",$validate = false,$errMsg = "",$params = false){		
		if(!is_array($value))die("ERROR : <b>$name</b> values should be an associative array");
		if(!is_array($checked))die("ERROR : <b>$name</b> checked values should be an array");
		$this -> setElement($name,array("type" => 6,"validate" => $validate,"value" => $checked,
										"keys" => $value,"style" => $style,"error" => $errMsg,"params" => $params));					
	}
	
	function setFileElement($name,$style = "",$validate = false,$errMsg="",$maxSize = 100,$imgMimeTypes = array()){														
		$this -> setElement($name,array("type" => 7,"validate" => $validate,"value" => "",
										"style" => $style,"error" => $errMsg,"maxsize" => $maxSize,
										"mimetypes" => $imgMimeTypes));
		$this -> hasFileUpload = true;																
	}

	function setRTEElement($name,$value = "",$width = 24,$height = 8,$validate = false,$errMsg = "",$params = false){
		$this -> setElement($name,array("type" => 8,"validate" => $validate,"value" => $value,
										"width" => $width,"height" => $height,
										"error" => $errMsg,"params" => $params));
		$this -> hasRTE = true;							
	}
	
	function processText($name){
		if($this -> isPosted()){
			if(isset($_POST[$name]))$this -> setElementValue($name,$this -> stripSlashesInVals(trim($_POST[$name])));
			$this -> doValidation($this -> elements[$name]["validate"],$this -> elements[$name]["value"],$this -> elements[$name]["error"],$this -> elements[$name]["params"]);
		}				
		return "<input name=\"$name\" type=\"text\" class=\"".$this -> elements[$name]["style"]."\" value=\"".htmlentities($this -> elements[$name]["value"],ENT_QUOTES)."\" size=\"".$this -> elements[$name]["size"]."\" maxlength=\"".$this -> elements[$name]["maxlength"]."\">\n";	
	}
	
	function processTextArea($name){
		if($this -> isPosted()){
			if(isset($_POST[$name]))$this -> setElementValue($name,$this -> stripSlashesInVals(trim($_POST[$name])));
			$this -> doValidation($this -> elements[$name]["validate"],$this -> elements[$name]["value"],$this -> elements[$name]["error"],$this -> elements[$name]["params"]);
		}
		return "<textarea name=\"$name\" cols=\"".$this -> elements[$name]["cols"]."\" class=\"".$this -> elements[$name]["style"]."\" rows=\"".$this -> elements[$name]["rows"]."\">".htmlentities($this -> elements[$name]["value"],ENT_QUOTES)."</textarea>\n";
	}
	
	function processPassword($name){
		if($this -> isPosted()){
			if(isset($_POST[$name]))$this -> setElementValue($name,$this -> stripSlashesInVals(trim($_POST[$name])));
			if($this -> elements[$name]["validate"] === true){
				if(!valNonEmpty($this -> elements[$name]["value"])){
					$this -> errMsg[] = $this -> elements[$name]["error"];
				}
			}
			elseif($this -> elements[$name]["validate"] !== false){
				if(!$this -> valComparison($name,$this -> elements[$name]["validate"])){
					$this -> errMsg[] = $this -> elements[$name]["error"];
				}
			}
		}
		$str = "<input name=\"$name\" type=\"password\" class=\"".$this -> elements[$name]["style"]."\" size=\"".$this -> elements[$name]["size"]."\" maxlength=\"".$this -> elements[$name]["maxlength"]."\"";
		$str .= ($this -> elements[$name]["default"])?" value=\"".htmlentities($this -> elements[$name]["value"],ENT_QUOTES)."\">":" value=\"\">\n";
		return $str;
	}
	
	function processHidden($name){
		if($this -> isPosted()){
			if(isset($_POST[$name]))$this -> setElementValue($name,$this -> stripSlashesInVals(trim($_POST[$name])));
		}
		return "<input name=\"$name\" type=\"hidden\" value=\"".htmlentities($this -> elements[$name]["value"],ENT_QUOTES)."\">\n";
	}
	
	function processSelect($name){
		if($this -> isPosted()){
			if(isset($_POST[$name]))$this -> setElementValue($name,$_POST[$name]);
			$this -> doValidation($this -> elements[$name]["validate"],$this -> elements[$name]["value"],$this -> elements[$name]["error"],$this -> elements[$name]["params"]);
		}
		$str = "<select class=\"".$this -> elements[$name]["style"]."\"";
		$str .= ($this -> elements[$name]["height"]>1)?" size=\"".$this -> elements[$name]["height"]."\"":"";
		$str .= ($this -> elements[$name]["onchange"]!="")?" onchange=\"".$this -> elements[$name]["onchange"]."\"":"";
		$str .= ($this -> elements[$name]["multiple"])?" name=\"{$name}[]\" multiple":" name=\"$name\"";
		$str .= ">\n";
		foreach($this -> elements[$name]["keys"] as $key => $val){
			if($this -> elements[$name]["multiple"]){
				if(in_array($key,$this -> elements[$name]["value"]))$str .= "<option value=\"$key\" selected>".htmlentities($val,ENT_QUOTES)."</option>\n";
				else $str .= "<option value=\"$key\">".htmlentities($val,ENT_QUOTES)."</option>\n";
			}
			else{
				if($this -> elements[$name]["value"] == $key)$str .= "<option value=\"$key\" selected>".htmlentities($val,ENT_QUOTES)."</option>\n";
				else $str .= "<option value=\"$key\">".htmlentities($val,ENT_QUOTES)."</option>\n";
			}
		}
		$str .= "</select>";
		return $str;
	}
	
	function processRadio($name){
		if($this -> isPosted()){
			if(isset($_POST[$name]))$this -> setElementValue($name,$_POST[$name]);
			$this -> doValidation($this -> elements[$name]["validate"],$this -> elements[$name]["value"],$this -> elements[$name]["error"],$this -> elements[$name]["params"]);
		}
		foreach($this -> elements[$name]["keys"] as $key => $val){			
			if($this -> elements[$name]["value"] == $key)$str[$key] = "<table><tr align=\"left\" valign=\"middle\"><td><input type=\"radio\" value=\"$key\" name=\"$name\" checked></td><td><span class=\"".$this -> elements[$name]["style"]."\">".htmlentities($val,ENT_QUOTES)."</span></td></tr></table>\n";
			else $str[$key] = "<table><tr align=\"left\" valign=\"middle\"><td><input type=\"radio\" value=\"$key\" name=\"$name\"></td><td><span class=\"".$this -> elements[$name]["style"]."\">".htmlentities($val,ENT_QUOTES)."</span></td></tr></table>\n";			
		}
		return $str;
	}
	
	function processCheck($name){
		if($this -> isPosted()){
			$newArr=array();
			if(isset($_POST[$name])){				
				foreach($_POST[$name] as $key=>$val){
					$newArr[]=$key;
				}
			}
			$this -> setElementValue($name,$newArr);
			$this -> doValidation($this -> elements[$name]["validate"],$this -> elements[$name]["value"],$this -> elements[$name]["error"],$this -> elements[$name]["params"]);
		}
		foreach($this -> elements[$name]["keys"] as $key => $val){
			if(in_array($key,$this -> elements[$name]["value"]))$str[$key] = "<table><tr align=\"left\" valign=\"middle\"><td><input type=\"checkbox\" value=\"1\" name=\"{$name}[$key]\" checked></td><td><span class=\"".$this -> elements[$name]["style"]."\">".htmlentities($val,ENT_QUOTES)."</span></td></tr></table>\n";
			else $str[$key] = "<table><tr align=\"left\" valign=\"middle\"><td><input type=\"checkbox\" value=\"1\" name=\"{$name}[$key]\"></td><td><span class=\"".$this -> elements[$name]["style"]."\">".htmlentities($val,ENT_QUOTES)."</span></td></tr></table>\n";			
		}
		return $str;
	}
	
	function processFile($name){
		/*if($this -> isPosted()){			
			$this -> setElementValue($name,$_FILES[$name]);
			if($this -> elements[$name]["validate"]){
				$retArr = $this -> uploadFile($this -> elements[$name]["value"],$this -> elements[$name]["maxsize"],$this -> elements[$name]["mimetypes"]);
				if($retArr["errMsg"]){
					$this -> errMsg[] = $this -> elements[$name]["error"]." : ".$retArr["errMsg"];
				}
			}						
		}
		$str = "<input name=\"$name\" type=\"file\" class=\"".$this -> elements[$name]["style"]."\">\n";
		return $str;*/
		if($this -> isPosted()){			
			$this -> setElementValue($name,$_FILES[$name]);
			$retArr = $this -> uploadFile($this -> elements[$name]["value"],$this -> elements[$name]["maxsize"],$this -> elements[$name]["mimetypes"],$this -> elements[$name]["validate"]);
			if($retArr["errMsg"]){
				$this -> errMsg[] = $this -> elements[$name]["error"]." : ".$retArr["errMsg"];
			}
		}
		$str = "<input name=\"$name\" type=\"file\" class=\"".$this -> elements[$name]["style"]."\">\n";
		return $str;
	}
	
	function processRTE($name){
		if($this -> isPosted()){			
			if(isset($_POST[$name]))$this -> setElementValue($name,$this -> rteSafe($this -> stripSlashesInVals(trim($_POST[$name]))));
			$this -> doValidation($this -> elements[$name]["validate"],$this -> elements[$name]["value"],$this -> elements[$name]["error"],$this -> elements[$name]["params"]);
		}
		$strCode = "<script language=\"JavaScript\" type=\"text/javascript\">
				<!--
				writeRichText('{$name}', '".$this -> rteSafe($this -> elements[$name]["value"])."', {$this -> elements[$name]["width"]}, {$this -> elements[$name]["height"]}, true, false);
				//-->
				</script>";
		return $strCode;				
	}
	
	function genSubmit($value = "",$style = ""){				
		$str = "<input type=\"submit\" name=\"".$this -> submitBtnName."\" value=\"$value\" class=\"$style\">";												
		return $str;										
	}	
	
	function genForm($overWriteDefAction = ""){
		$str["start"] = "";
		if($this -> hasRTE){
			$str["start"].="<script language=\"JavaScript\" type=\"text/javascript\" src=\"html2xhtml.js\"></script>
						<script language=\"JavaScript\" type=\"text/javascript\" src=\"richtext.js\"></script>
						<script language=\"JavaScript\" type=\"text/javascript\">
						<!--
						function submitForm(){
							updateRTEs();	
							return true;
						}
						initRTE(\"images/\", \"\", \"\", true);
						//-->
						</script>
						<noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>";
		}
		$str["start"] .= ($overWriteDefAction)?"<form action=\"".$overWriteDefAction."\" method=\"post\"":"<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\"";
		if($this -> hasFileUpload)$str["start"] .= " enctype=\"multipart/form-data\"";
		if($this -> hasRTE)$str["start"] .= " onsubmit=\"return submitForm();\"";
		$str["start"] .= ">\n\n";	
		$str["end"] = "</form>\n";			
		return $str;								
	}
	
	function isPosted(){		
		if(isset($_POST[$this -> submitBtnName])){
			return true;
		}
		return false;
	}
	
	function processForm($subValue,$style = "",$subName = "submit", $overWriteDefAction = ""){
		$this -> submitBtnName = $subName;		
		$str["form"] = $this -> genForm($overWriteDefAction);			
		$str["submit"] = $this -> genSubmit($subValue,$style);				
		foreach($this -> elements as $elName => $elVals){
			$callFun = "process".$this -> elTypes[$elVals["type"]];			
			$this -> elementDisHTML[$elName] = $this -> $callFun($elName);
		}		
		return $str;
	}
	
	function getDisElementHTML(){
		$newArr = array();
		foreach($this -> elementDisHTML as $key => $val){			
			$newArr[$key] = $this -> elementDisHTML[$key];			
		}
		return $newArr;
	}
	
	function getPostedElementValues(){
		$newArr = array();
		if($this -> isPosted() && (!count($this -> errMsg))){
			foreach($this -> elementDisHTML as $key => $val){			
				$newArr[$key] = $this -> elements[$key]["value"];			
			}
		}
		return $newArr;
	}
	
	function getBrowserSafePostedElementValues(){
		$newArr = array();
		if($this -> isPosted() && (!count($this -> errMsg))){
			foreach($this -> elementDisHTML as $key => $val){			
				if(is_array($this -> elements[$key]["value"])){
					$newArr[$key] = $this -> elements[$key]["value"];			
				}
				else{
					$newArr[$key] = htmlentities($this -> elements[$key]["value"],ENT_QUOTES);			
				}				
			}
		}
		return $newArr;
	}	
	
	function getErrorMsg($errStr = "",$style = ""){
		$str = "";
		$strArr = array();
		foreach($this -> errMsg as $key => $val){
			$strArr[] = "<li>$val</li>";
		}
		if(count($strArr)){
			if($errStr)$str .= "<span class=\"$style\">$errStr\n";	
			$str .= "<ul>";
			$str .= implode("\n",$strArr);
			$str .= "</ul></span>";
		}
		return $str;
	}
	
	function valComparison($elementName,$comElementName){
		if($this -> elements[$elementName]["value"] === $this -> elements[$comElementName]["value"])return true;
		return false;
	}
	
	function uploadFile($fileFld, $maxSize, $imgMimeTypes, $nullCheck){
		$errFun = "";        
		$fileName = $fileFld["name"];
		$fileSize = ceil($fileFld["size"]/1024);
		$fileTmpName = $fileFld["tmp_name"];
		$fileError = $fileFld["error"];
		$fileType = $fileFld["type"];				
		if($fileError != 0 && $fileError != 4){
			$errFun = "Unknown";				
		}
		elseif($fileError != 4){
			if($fileSize < 1 || $fileSize > $maxSize){
				$errFun = "Max File Size allowed is ".$maxSize."K";
			}
			else{								
				if(!in_array($fileType, array_keys($imgMimeTypes))){				
					$errFun = "Only ".implode(", ", $imgMimeTypes)." files are allowed.";			
				}
			}            
		}
		elseif($nullCheck && $fileError == 4){
			$errFun = "Select file..";
		}				
		return array("errMsg" => $errFun, "fileName" => $fileName, "fileTmpName" => $fileTmpName, "fileContType" => $fileType);
	}
	
	function stripSlashesInVals($str){
		if(get_magic_quotes_gpc()){
			return stripslashes($str);
		}
		return $str;
	}
	
	function rteSafe($strText) {
		//returns safe code for preloading in the RTE
		$tmpString = $strText;
		
		//convert all types of single quotes
		$tmpString = str_replace(chr(145), chr(39), $tmpString);
		$tmpString = str_replace(chr(146), chr(39), $tmpString);
		$tmpString = str_replace("'", "&#39;", $tmpString);
		
		//convert all types of double quotes
		$tmpString = str_replace(chr(147), chr(34), $tmpString);
		$tmpString = str_replace(chr(148), chr(34), $tmpString);
	//	$tmpString = str_replace("\"", "\"", $tmpString);
		
		//replace carriage returns & line feeds
		$tmpString = str_replace(chr(10), " ", $tmpString);
		$tmpString = str_replace(chr(13), " ", $tmpString);
		
		return $tmpString;
	}
}
?>