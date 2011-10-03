<?php
require_once("class.easyformproc.inc.php");//FormGeneratorClass

class form2DB extends easyFormProc{
	var $pageTypeList;
	var $pageType;
	var $addQueryStr;
	var $tblParams;
	var $dbFieldMapVals;
	var $fieldMapVals;
	var $itemId;
	var $fieldList;
	var $tableName;
	var $primFld;
	var $debugMode;
	var $uploadPath;
	var $uploadFiles;
	var $queryInsertId;
	var $uploadWriteDB;
	var $upFilePrefix;
	
	function form2DB($hostName, $userName, $passWord, $dbName){
		$this -> pageTypeList = array("list" => "list", "show" => "show", "add" => "add", "edit" => "edit", "delete" => "delete", "delcon" => "delcon");
		$this -> pageType = (isset($_GET["classtask"]) && in_array($_GET["classtask"], $this -> pageTypeList))?$_GET["classtask"]:$this -> pageTypeList["list"];		
		$this -> addQueryStr = "";
		$this -> tblParams = array("tblWidth" => "", "tblHeight" => "", "tblCellPad" => 0, "tblCellSpace" => 0, "tblBorder" => 0, "tblAlign" => "", "tblStyle" => "");
		$this -> dbFieldMapVals = array("read" => array(), "write" => array());
		$this -> fieldMapVals = array("read" => array(), "write" => array());
		$this -> fieldList = array();
		$this -> primFld = "";
		$this -> debugMode = false;
		if($this -> pageType == $this -> pageTypeList["show"] || $this -> pageType == $this -> pageTypeList["edit"] || $this -> pageType == $this -> pageTypeList["delete"] || $this -> pageType == $this -> pageTypeList["delcon"]){
			if(isset($_GET["itemid"]) && trim($_GET["itemid"])!=""){
				$this -> itemId = $_GET["itemid"];
			}
			else{
				$this -> itemId = false;
				$this -> pageType = $this -> pageTypeList["list"];
			}
		}
		$this -> tableName = "";
		$this -> uploadPath = "";
		$this -> uploadFiles = array();
		$this -> queryInsertId = 0;
		$this -> uploadWriteDB = false;
		$this -> upFilePrefix = "";
		mysql_connect($hostName, $userName, $passWord) or die("Connect DB failed");//MySQL Database Instance
		mysql_select_db($dbName) or die("Select DB failed");
	}
	
	function setDebugMode(){
		$this -> debugMode = true;
	}
	
	function getList($sqlQuery){
		$retArr = array();
		$myResult = mysql_query($sqlQuery) or die("List Query failed - ".mysql_error());	
		$numFields = mysql_num_fields($myResult);
		$c = 0;
		$primKeyFld = "";
		while($rs = mysql_fetch_assoc($myResult)){
			for($i = 0; $i < $numFields; $i++){
				$fldName = mysql_field_name($myResult, $i);
				$primKeyFldList = mysql_field_flags($myResult, $i);
				if(in_array("primary_key", explode(" ", $primKeyFldList))){
					$retArr[$c][$fldName][0] = true;
					$retArr[$c][$fldName][1] = mysql_result($myResult, $c, $fldName);					
				}
				else{
					$retArr[$c][$fldName][0] = false;
					$retArr[$c][$fldName][1] = mysql_result($myResult, $c, $fldName);	
				}
			}
			$c++;
		}
		return $retArr;
	} 
	
	function getCount($sqlQuery){
		$myResult = mysql_query($sqlQuery) or die("Count Query failed - ".mysql_error());
		return mysql_result($myResult, 0, "numRecs");
	}
	
	function setTableParams($arrParams){
		foreach($this -> tblParams as $key => $val){
			if(isset($arrParams[$key])){
				$this -> tblParams[$key] = $arrParams[$key];
			}
		}
	}
	
	function setUploadPath($pathUpload){
		$this -> uploadPath = $pathUpload;
	}
	
	function setUploadFilePrefix($prefix){
		$this -> upFilePrefix = $prefix;
	}
	
	function writeUploads2DB(){
		$this -> uploadWriteDB = true;
	}
	
	function disList($sqlQuery, $recsPerPage = 0, $addQStr = "", $headerSet = array(), $cellSet = array(), $addOpt = true, $delOpt = true){
		$this -> addQueryStr = $addQStr;
		if($this -> pageType == $this -> pageTypeList["list"]){			
			$cellVals = array();
			$totRecCountSQL = $this -> modifySQL2Count($sqlQuery);
			$totRecs = $this -> getCount($totRecCountSQL);
			$paginateArr = $this -> pageRecords($totRecs, (isset($_GET["pageNum"]))?$_GET["pageNum"]:1, $recsPerPage, $addQStr, "listLinks");
			if($recsPerPage){
				$sqlQuery .= " limit ".$paginateArr["stRec"].",".$paginateArr["noRec"];
			}
			$dataVals = $this -> getList($sqlQuery);
			$i = 1;
			$j = 0;
			foreach($dataVals as $dataVal){
				$j = 0;
				foreach($dataVal as $key => $val){
					if($val[0])$primaryFld = $j;
					$val = $val[1];
					if(!($i-1)){					
						$cellVals[0][$j] = $headerSet;					
						$cellVals[0][$j]["value"] = (isset($headerSet[$key]["value"]))?$headerSet[$key]["value"]:$key;	
					}				
					if(isset($cellSet[$key])){
						$cellVals[$i][$j] = $cellSet[$key];
						if(isset($cellSet[$key]["style"])){
							$cellVals[$i][$j]["style"] = ($i%2)?($cellSet[$key]["style"]."2"):$cellSet[$key]["style"];
						}
						elseif(isset($cellSet["style"])){
							$cellVals[$i][$j]["style"] = ($i%2)?$cellSet["style"]."2":$cellSet["style"];
						}
					}
					else{
						$cellVals[$i][$j] = $cellSet;
						if(isset($cellSet["style"])){
							$cellVals[$i][$j]["style"] = ($i%2)?$cellSet["style"]."2":$cellSet["style"];
						}
					}	
					if(isset($cellSet[$key][$val])){
						$cellVals[$i][$j]["value"] = $cellSet[$key][$val];
					}
					else{
						$cellVals[$i][$j]["value"] = $val;
					}
					$j++;
				}
				if(isset($headerSet["OPTEDIT"])){
					$cellVals[$i][$j] = $headerSet["OPTEDIT"];
					if(isset($cellVals[$i][$j]["style"])){
						$cellVals[$i][$j]["style"] = ($i%2)?$headerSet["OPTEDIT"]["style"]."2":$headerSet["OPTEDIT"]["style"];
					}
				}
				$cellVals[$i][$j]["value"] = "&nbsp;".$this -> genHTMLLink($_SERVER["PHP_SELF"]."?classtask=".$this -> pageTypeList["edit"]."&itemid=".$cellVals[$i][$primaryFld]["value"].$addQStr, (isset($cellVals[$i][$j]["value"]))?$cellVals[$i][$j]["value"]:"Edit", "", "listLinks");
				$j++;
				if($delOpt){					
					if(isset($headerSet["OPTDELETE"])){
						$cellVals[$i][$j] = $headerSet["OPTDELETE"];
						if(isset($cellVals[$i][$j]["style"])){
							$cellVals[$i][$j]["style"] = ($i%2)?$headerSet["OPTDELETE"]["style"]."2":$headerSet["OPTDELETE"]["style"];
						}
					}
					$cellVals[$i][$j]["value"] = "&nbsp;".$this -> genHTMLLink($_SERVER["PHP_SELF"]."?classtask=".$this -> pageTypeList["delcon"]."&itemid=".$cellVals[$i][$primaryFld]["value"].$addQStr, (isset($cellVals[$i][$j]["value"]))?$cellVals[$i][$j]["value"]:"Delete", "", "listLinks");
					$j++;
				}				
				$i++;
			}
			$subTable = $this -> genHTMLTable($i, $j, $cellVals, $this -> tblParams["tblWidth"], $this -> tblParams["tblHeight"], $this -> tblParams["tblCellPad"], $this -> tblParams["tblCellSpace"], $this -> tblParams["tblBorder"], $this -> tblParams["tblAlign"], $this -> tblParams["tblStyle"]);
			$mainCellVals[0][0] = array("align" => "right", "value" => "Showing Records ".$paginateArr["recString"], "style" => "title");
			$mainCellVals[1][0] = array("value" => $subTable);
			$mainCellVals[2][0] = array("value" => (($paginateArr["prevLink"])?$this -> genHTMLLink($paginateArr["prevLink"], "Previous", "", "listLinks"):"")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$paginateArr["pageNumStr"]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".(($paginateArr["nextLink"])?$this -> genHTMLLink($paginateArr["nextLink"], "Next", "", "listLinks"):""), "style" => "title", "align" => "center");
			if($addOpt)$mainCellVals[3][0] = array("value" => $this -> genHTMLLink($_SERVER["PHP_SELF"]."?classtask=".$this -> pageTypeList["add"].$addQStr, "Add", "", "listLinks"), "align" => "right");
			return $this -> genHTMLTable(count($mainCellVals), 1, $mainCellVals, $this -> tblParams["tblWidth"], $this -> tblParams["tblHeight"], $this -> tblParams["tblCellPad"], $this -> tblParams["tblCellSpace"], $this -> tblParams["tblBorder"], $this -> tblParams["tblAlign"], $this -> tblParams["tblStyle"]);
		}
		return false;
	}
	
	function setFormFields($formFlds, $formSubType, $tableStyle = array()){
		if($this -> pageType == $this -> pageTypeList["add"] || $this -> pageType == $this -> pageTypeList["edit"]){
			$this -> easyFormProc();
			$fldDefValMap = array("Text" => 1, "TextArea" => 1, "Password" => 1, "Hidden" => 1, "Select" => 2, "Radio" => 2, "Check" => 2, "RTE" => 1, "File" => 1);			
			$fldTypeArr = array();			
			foreach($formFlds as $key => $val){
				$fldTypeArr[$key] = $val[0];
				$fldType = $val[0];				
				$fldLabelStr[$key] = $val[1];				
				array_shift($val);
				array_shift($val);
				array_unshift($val, $key);
				if($fldType=="File"){
					$this -> uploadFiles[] = $key;
				}
				if($this -> pageType == $this -> pageTypeList["edit"]){	
					$ctrlTextValDis = $this -> dbFieldMapVals["read"][$key];					
					if($fldType=="File"){						
						$val[$fldDefValMap[$fldType]] = "";
					}
					else{						
						$val[$fldDefValMap[$fldType]] = $ctrlTextValDis;
					}
				}
				call_user_func_array(array(&$this, "set".$fldType."Element"), $val);
			}			
			$itemIdQStr = "";
			if($this -> pageType == $this -> pageTypeList["edit"]){
				$itemIdQStr = "&itemid=".$this -> itemId;
			}
			$disForm = $this -> processForm($formSubType["submit"]["label"], $formSubType["submit"]["style"], $formSubType["submit"]["name"], $_SERVER["PHP_SELF"]."?classtask=".$this -> pageType.$this -> addQueryStr.$itemIdQStr);
			$disElementHTML = $this -> getDisElementHTML();
			$errMsg = $this -> getErrorMsg($formSubType["error"]["message"], $formSubType["error"]["style"]);
			$postedVals = $this -> getPostedElementValues();//			
			if(count($postedVals)){				
				$writeFldsVals = array();
				$moveUploadedFiles = array();
				foreach($postedVals as $key => $val){
					if(in_array($key, $this -> uploadFiles)){
						if($this -> uploadWriteDB){
							if($this -> fieldMapVals["write"][$key]["function"]!==false){
								$fNameValArr = array($val["name"]);
								$this -> dbFieldMapVals["write"][$key] = call_user_func_array($this -> fieldMapVals["write"][$key]["function"], array_merge($fNameValArr, $this -> fieldMapVals["write"][$key]["params"]));
							}
							else{
								$this -> dbFieldMapVals["write"][$key] = $val["name"];								
							}
						}
						$moveUploadedFiles[$key] = array($val["tmp_name"], $val["name"]);
					}
					else{
						if($this -> fieldMapVals["write"][$key]["function"]!==false){
							$fNameValArr = array($val["fileName"]);
							$this -> dbFieldMapVals["write"][$key] = call_user_func_array($this -> fieldMapVals["write"][$key]["function"], array_merge($fNameValArr, $this -> fieldMapVals["write"][$key]["params"]));
						}
						else{
							$this -> dbFieldMapVals["write"][$key] = $val;
						}
					}
				}				
				$qBuildFldNameArr = array();
				$qBuildValArr = array();				
				foreach($this -> dbFieldMapVals["write"] as $key => $val){
					$qBuildFldNameArr[] = $this -> fieldList[$key];
					$writeVal = $this -> dbFieldMapVals["write"][$key];//					
					$qBuildValArr[] = mysql_escape_string($writeVal);
				}								
				if($this -> pageType == $this -> pageTypeList["add"]){
					$qBuildInsertStr = "insert into `".$this -> tableName."`(`".implode("`,`", $qBuildFldNameArr)."`) values ('".implode("','", $qBuildValArr)."')";
				}
				elseif($this -> pageType == $this -> pageTypeList["edit"]){
					$qBuildFldEditArr = array();
					foreach($qBuildFldNameArr as $key => $val){
						$qBuildFldEditArr[] = "`".$val."`='".$qBuildValArr[$key]."'";
					}
					$qBuildInsertStr = "update `".$this -> tableName."` set ".implode(",", $qBuildFldEditArr)." where `".$this -> primFld."`='".$this -> itemId."'";
				}				
				
				if($this -> debugMode){					
					echo $qBuildInsertStr."<br><br>";
					foreach($moveUploadedFiles as $key => $val){
						if(isset($this -> upFilePrefix[$key])){
							$getFileEx = explode(".", $val[1]);
							$getFileEx = $getFileEx[count($getFileEx)-1];
							echo $val[0]." => ".$this -> uploadPath[$key].$this -> upFilePrefix[$key]."_".$this -> queryInsertId.".".$getFileEx."<br>";
						}
						else{
							echo $val[0]." => ".$this -> uploadPath[$key].$this -> queryInsertId."_".$val[1]."<br>";
						}
					}
					die();
				}
				else{
					mysql_query($qBuildInsertStr);
					if($this -> pageType == $this -> pageTypeList["edit"]){
						$this -> queryInsertId = $this -> itemId;
					}
					else{
						$resultLastInId = mysql_query("select LAST_INSERT_ID() as lastid");
						$this -> queryInsertId = mysql_result($resultLastInId, 0, "lastid");
					}
					foreach($moveUploadedFiles as $key => $val){
						if(isset($this -> upFilePrefix[$key])){
							$getFileEx = explode(".", $val[1]);
							$getFileEx = $getFileEx[count($getFileEx)-1];
							move_uploaded_file($val[0], $this -> uploadPath[$key].$this -> upFilePrefix[$key]."_".$this -> queryInsertId.".".$getFileEx);
						}
						else{
							move_uploaded_file($val[0], $this -> uploadPath[$key].$this -> queryInsertId."_".$val[1]);
						}
					}
				}
				$mainCellVals[0][0]["value"] = "Records Successfully Updated<META HTTP-EQUIV=\"refresh\" content=\"5;URL=".$_SERVER["PHP_SELF"]."?classtask=".$this -> pageTypeList["list"].$this -> addQueryStr."\">";
				return $this -> genHTMLTable(1, 1, $mainCellVals);
			}
			else{
				$mainCellVals = array();									
				$i = 0;
				foreach($disElementHTML as $key => $val){
					$mainCellVals[$i][0] = array("value" => $fldLabelStr[$key]);
					$mainCellVals[$i][1] = array("value" => $val);
					$i++;
				}
				$mainCellVals[$i][1]["value"] = $disForm["submit"];
				return $errMsg.$disForm["form"]["start"].$this -> genHTMLTable(count($mainCellVals), 2, $mainCellVals, $this -> tblParams["tblWidth"], $this -> tblParams["tblHeight"], $this -> tblParams["tblCellPad"], $this -> tblParams["tblCellSpace"], $this -> tblParams["tblBorder"], $this -> tblParams["tblAlign"], $this -> tblParams["tblStyle"]).$disForm["form"]["end"];			
			}
		}	
		elseif($this -> pageType == $this -> pageTypeList["delete"] || $this -> pageType == $this -> pageTypeList["delcon"]){
			if($this -> pageType == $this -> pageTypeList["delcon"]){				
				$mainCellVals[0][0]["value"] = "Delete Record? ".$this -> genHTMLLink($_SERVER["PHP_SELF"]."?classtask=".$this -> pageTypeList["delete"].$this -> addQueryStr."&itemid=".$this -> itemId, "Yes")."&nbsp;&nbsp;&nbsp;".$this -> genHTMLLink($_SERVER["PHP_SELF"]."?classtask=".$this -> pageTypeList["list"].$this -> addQueryStr, "No");
				return $this -> genHTMLTable(1, 1, $mainCellVals);
			}
			elseif($this -> pageType == $this -> pageTypeList["delete"]){										
				$qBuildInsertStr = "delete from `".$this -> tableName."` where `".$this -> primFld."`='".$this -> itemId."'";
				mysql_query($qBuildInsertStr);
				$mainCellVals[0][0]["value"] = "Records Successfully Updated<META HTTP-EQUIV=\"refresh\" content=\"5;URL=".$_SERVER["PHP_SELF"]."?classtask=".$this -> pageTypeList["list"].$this -> addQueryStr."\">";
				return $this -> genHTMLTable(1, 1, $mainCellVals);
			}
		}
	}
	
	function setDBFieldMap($tableName, $primFld, $fldMapArr){
		$this -> primFld = $primFld;
		$this -> tableName = $tableName;
		foreach($fldMapArr as $fldMapKey => $fldMapVal){			
			$fldNames = array_keys($fldMapVal);						
			$this -> fieldList[$fldMapKey] = $fldNames[0];
			if(isset($fldMapVal[$fldNames[0]][0])){					
				$this -> fieldMapVals["read"][$fldMapKey]["function"] = (isset($fldMapVal[$fldNames[0]][0][0]))?$fldMapVal[$fldNames[0]][0][0]:false;
				$this -> fieldMapVals["read"][$fldMapKey]["params"] = (isset($fldMapVal[$fldNames[0]][0][1]))?$fldMapVal[$fldNames[0]][0][1]:array();
			}
			else{
				$this -> fieldMapVals["read"][$fldMapKey]["function"] = false;
				$this -> fieldMapVals["read"][$fldMapKey]["params"] = array();
			}
			if(isset($fldMapVal[$fldNames[0]][1])){					
				$this -> fieldMapVals["write"][$fldMapKey]["function"] = (isset($fldMapVal[$fldNames[0]][1][0]))?$fldMapVal[$fldNames[0]][1][0]:false;
				$this -> fieldMapVals["write"][$fldMapKey]["params"] = (isset($fldMapVal[$fldNames[0]][1][1]))?$fldMapVal[$fldNames[0]][1][1]:array();
			}
			else{
				$this -> fieldMapVals["write"][$fldMapKey]["function"] = false;
				$this -> fieldMapVals["write"][$fldMapKey]["params"] = array();
			}
		}
		if(($this -> pageType == $this -> pageTypeList["show"] || $this -> pageType == $this -> pageTypeList["edit"]) && $this -> itemId !==false){
			$queryStr = "select `".implode("`,`", $this -> fieldList)."` from `{$tableName}` where `".$this -> primFld."`='".mysql_escape_string($this -> itemId)."'";
			$resultSet = mysql_query($queryStr) or die("DB map Query failed - ".mysql_error());
			$resArr = array();		
			while($row = mysql_fetch_assoc($resultSet)){	
				foreach($this -> fieldList as $fNameKey => $fNameVal){					
					if($this -> fieldMapVals["read"][$fNameKey]["function"]!==false){
						$fNameValArr = array($row[$fNameVal]);
						$this -> dbFieldMapVals["read"][$fNameKey] = call_user_func_array($this -> fieldMapVals["read"][$fNameKey]["function"], array_merge($fNameValArr, $this -> fieldMapVals["read"][$fNameKey]["params"]));
					}
					else{
						$this -> dbFieldMapVals["read"][$fNameKey] = $row[$fNameVal];
					}
				}
			}						
		}		
	}
	
	function genHTMLTable($noRows, $noColumns, $cellValues, $width = "", $height = "", $cellPad = 0, $cellSpace = 0, $border = 0, $align = "", $style = ""){		
		$str = "<table";
		if($width)$str .= " width=\"$width\"";
		if($height)$str .= " height=\"$height\"";
		$str .= " cellpadding=\"$cellPad\"";
		$str .= " cellspacing=\"$cellSpace\"";
		$str .= " border=\"$border\"";
		if($align)$str .= " align=\"$align\"";
		if($style)$str .= " class=\"$style\"";
		$str .= ">\n";
			
		for($i = 0;$i < $noRows;$i++){
			$str .= "<tr>\n";
			for($j = 0;$j < $noColumns;$j++){
				$str .= "<td";
				if(isset($cellValues[$i][$j]["width"]))$str .= " width=\"".$cellValues[$i][$j]["width"]."\"";
				if(isset($cellValues[$i][$j]["height"]))$str .= " height=\"".$cellValues[$i][$j]["height"]."\"";
				if(isset($cellValues[$i][$j]["style"]))$str .= " class=\"".$cellValues[$i][$j]["style"]."\"";
				if(isset($cellValues[$i][$j]["align"]))$str .= " align=\"".$cellValues[$i][$j]["align"]."\"";
				if(isset($cellValues[$i][$j]["valign"]))$str .= " valign=\"".$cellValues[$i][$j]["valign"]."\"";
				$str .= ">";
				$str .= (isset($cellValues[$i][$j]["value"]))?$cellValues[$i][$j]["value"]:"&nbsp;";
				$str .= "</td>\n";
			}
			$str .= "</tr>\n";
		}
			
		$str .= "</table>\n";		
		return $str;		
	}
	
	function genHTMLLink($link, $label ="", $target = "", $style = ""){
		$str = "<a href=\"{$link}\"";
		if($style)$str .= " class=\"$style\"";
		$str .= ($target)?" target=\"{$target}\">":">";
		$str .= ($label)?$label:$link;
		$str .= "</a>";
		return $str;
	}
	
	function modifySQL2Count($str){
		return "select count(*) as numRecs ".substr($str, strpos($str, "from"));	
	}
	
	function pageRecords($totRecs,$curPage,$rPerPage,$qryStr="",$hrefstyle=""){        
        $balRecs=$totRecs%$rPerPage;
        $totalPages=($balRecs)?intval($totRecs/$rPerPage)+1:intval($totRecs/$rPerPage);                
        if($curPage<1 || $curPage>$totalPages){
                $retArr["stRec"]=($curPage-1)*$rPerPage;
                $retArr["noRec"]=1;
                $retArr["recString"]="0 Records";
        }
        elseif($curPage==$totalPages){
                if($balRecs){
                        $retArr["stRec"]=($curPage-1)*$rPerPage;
                        $retArr["noRec"]=$balRecs;
                        $retArr["recString"]=($balRecs==1)?($retArr["stRec"]+1)." of ".$totRecs:($retArr["stRec"]+1)."-".($retArr["stRec"]+$balRecs)." of ".$totRecs;        
                }
                else{
                        $retArr["stRec"]=($curPage-1)*$rPerPage;
                        $retArr["noRec"]=$rPerPage;
                        $retArr["recString"]=($retArr["stRec"]+1)."-".($retArr["stRec"]+$rPerPage)." of ".$totRecs;
                }                
        }
        else{
                $retArr["stRec"]=($curPage-1)*$rPerPage;
                $retArr["noRec"]=$rPerPage;
                $retArr["recString"]=($retArr["stRec"]+1)."-".($retArr["stRec"]+$rPerPage)." of ".$totRecs;                
        }                        
        $retArr["prevLink"]=($curPage>1)?$_SERVER["PHP_SELF"]."?pageNum=".($curPage-1).$qryStr:"";
        $retArr["nextLink"]=($curPage<$totalPages)?$_SERVER["PHP_SELF"]."?pageNum=".($curPage+1).$qryStr:"";
        $lPageNum=array();
		$disFirstRec="";
        if($curPage>2){
                if($curPage>=$totalPages-1 && $totalPages>=5)$pNumStart=$totalPages-4;
                else $pNumStart=$curPage-2;        
                if($curPage>3)$disFirstRec="<a href=\"".$_SERVER["PHP_SELF"]."?pageNum=1".$qryStr."\" class=\"$hrefstyle\">&laquo;</a>&nbsp;&nbsp;&nbsp;";                                        
        }
        else{
                $pNumStart=1;
        }
		$disLastRec="";    
        if($curPage<$totalPages-1){
                if($curPage<=2 && $totalPages>=5)$pNumEnd=5;
                else $pNumEnd=$curPage+2;        
                if($curPage<$totalPages-2)$disLastRec="&nbsp;&nbsp;&nbsp;<a href=\"".$_SERVER["PHP_SELF"]."?pageNum=$totalPages".$qryStr."\" class=\"$hrefstyle\">&raquo;</a>";        
        }
        else{
                $pNumEnd=$totalPages;                                                    
        }                
        for($i=$pNumStart;$i<=$pNumEnd;$i++){
                $lPageNum[]=($curPage!=$i)?"<a href=\"".$_SERVER["PHP_SELF"]."?pageNum=$i".$qryStr."\" class=\"$hrefstyle\">$i</a>":"<b>$i</b>";
        }        
        $retArr["pageNumStr"]=$disFirstRec.implode("&nbsp;&nbsp;&nbsp;",$lPageNum).$disLastRec;
        return $retArr;
	}
}
?>