<?php
require_once(_LIBPATH."class_db_v0_2.php");//MySQL DB Class
class dBAccess extends mySqlDB{
	function dBAccess($hostName = "localhost", $userName = "root", $passWord = "cenango"){
		$this->mySqlDB($hostName, $userName, $passWord);
	}
	
	function selectDB($dBase = "poster_street"){
		$this->dBSelect($dBase);
	}

	function authenticateLogin($usName, $psWord, $updateLog = false){
		$flds = array("userId" => "userid", "usName" => "usname");		
		if($updateLog){
			$flds["lastLogin"] = "lastlogin";
		}
		$rs = $this -> querySelect("usmgt_user", $flds, "", "A", "usname='".$this -> mySQLSafe($usName)."' and psword='".$this -> mySQLSafe(md5($psWord))."'");
		if(count($rs) && $updateLog){
			$this -> queryUpdate("usmgt_user", array("lastlogin" => gmdate("Y-m-d H:i:s")), true, "usname='".$this -> mySQLSafe($usName)."'");
		}
		return $this -> getResultSet($rs, $flds);
	}	
	
	function getActivityList($appId, $userId){
		$rs = $this -> queryManual("select distinct usmgt_activity.actid,usmgt_activity.actlink,usmgt_activity.actpageid,usmgt_activity.parentid,usmgt_activity.orderno from usmgt_usergroup,usmgt_groupactivity,usmgt_activity where usmgt_usergroup.userid='".$this -> mySQLSafe($userId)."' and usmgt_usergroup.appid='".$this -> mySQLSafe($appId)."' and usmgt_usergroup.grid=usmgt_groupactivity.grid and usmgt_groupactivity.actid=usmgt_activity.actid and usmgt_activity.appid='".$this -> mySQLSafe($appId)."'");		
		$retArr = array();
		$i = 0;
		while($row = mysql_fetch_assoc($rs)){
			$retArr[] =array("actId" => $row["actid"],
								"actLink" => $row["actlink"],
								"actPageId" => $row["actpageid"],
								"parentId" => $row["parentid"],
								"orderNo" => $row["orderno"]);
		}
		return $retArr;
	}
	
	function getCorpNavigation($appId, $pageId = false, $parentId = false, $thumbDis = false, $actId = false){
		$qStrAdd = "";
		$qStrFld = "";
		if($pageId !== false){
			$qStrAdd = " and actpageid='".$this -> mySQLSafe($pageId)."'";
			$qStrFld = ",tempid,incphp,incphpfile,intro";
		}
		if($parentId !== false){
			$qStrAdd = " and parentid='".$this -> mySQLSafe($parentId)."'";
			$qStrFld = ",intro,incphp";
		}
		if($actId !== false){
			$qStrAdd = " and actid='".$this -> mySQLSafe($actId)."'";
			$qStrFld = ",intro,incphp";
		}
		if($thumbDis){
			$qStrAdd .= " and thumb='1'";
		}
		$qStr = "select actid,actlink,actpageid,parentid,orderno,incpage".$qStrFld." from corpweb_nav where appid='".$this -> mySQLSafe($appId)."' and active='1'".$qStrAdd;		
		$rs = $this -> queryManual($qStr);
		$retArr = array();
		while($row = mysql_fetch_assoc($rs)){
			$retArr1 = array("actId" => $row["actid"],
								"actLink" => $row["actlink"],
								"actPageId" => $row["actpageid"],
								"parentId" => $row["parentid"],
								"orderNo" => $row["orderno"],
								"incPage" => $row["incpage"]);
			$retArr2 = array();
			if($pageId !== false){
				$retArr2 = array("tempId" => $row["tempid"], "incPhp" => $row["incphp"], "intro" => $row["intro"], "incPhpFile" => $row["incphpfile"]);
			}
			elseif($parentId !== false || $actId !== false){
				$retArr2 = array("intro" => $row["intro"], "incPhp" => $row["incphp"]);
			}			
			$retArr[] = array_merge($retArr1, $retArr2);
		}
		return $retArr;
	}

	function getCorpNavVals($actId){
		$qStr = "select corpweb_nav_values.navvalid,
				corpweb_nav_values.valuestr,
				corpweb_nav_values.actid,
				corpweb_tempvars.varname,
				corpweb_var_types.defstring,
				corpweb_var_types.label 
				from corpweb_nav_values,corpweb_tempvars,corpweb_var_types 
				where corpweb_nav_values.actid='".$this -> mySQLSafe($actId)."' and 
				corpweb_tempvars.varid=corpweb_nav_values.varid and 
				corpweb_tempvars.vartypeid=corpweb_var_types.vartypeid";
		$retArr = array();
		$rs = $this -> queryManual($qStr);
		while($row = mysql_fetch_assoc($rs)){
			$retArr[] = array("navValId" => $row["navvalid"],
						"actId" => $row["actid"],
						"varName" => $row["varname"],
						"defString" => $row["defstring"],
						"label" => $row["label"],
						"valueStr" => $row["valuestr"]);
		}
		return $retArr;
	}
	
	function getMainNavigation($mainNavId = false, $mainKey = false){
		$whereCl = "";
		if($mainNavId !== false){
			$whereCl = "mainnavid='".$this -> mySQLSafe($mainNavId)."'";			
		}
		elseif($mainKey !== false){
			$whereCl = "navkey='".$this -> mySQLSafe($mainKey)."'";
		}
		$flds = array("mainnavid", "navlabel", "navkey", "navi");
		$rs = $this -> querySelect("mainnav", $flds, "", "A", $whereCl);		
		return $rs;
	}
	
	function getSubMainNavigation($mainNavId = false, $subMainNavId = false, $subMainKey = false){
		$flds = array("subnavid", "subnavlabel", "subnavkey", "incphp");
		$whereCl = "";
		if($mainNavId !== false){
			$whereCl .= "mainnavid = '".$this -> mySQLSafe($mainNavId)."'";			
		}
		if($subMainNavId !== false){ 
			$whereCl .= ($whereCl)?" and ":"";
			$whereCl .= "subnavid='".$this -> mySQLSafe($subMainNavId)."'";
			$flds[] = "strcontent";
		}
		elseif($subMainKey !== false){
			$whereCl .= ($whereCl)?" and ":"";
			$whereCl .= "subnavkey='".$this -> mySQLSafe($subMainKey)."'";
			$flds[] = "strcontent";
		}
		
		$rs = $this -> querySelect("mainsubnav", $flds, "", "A", $whereCl);		
		return $rs;
	}
	
	
	
	function getAllMimeTypes($attTypeId = ""){
		$flds = array("attTypeId" => "atttypeid", "attMimeType" => "attmimetype", "attTypeName" => "atttypename");
		$whCl = ($attTypeId)?"atttypeid='".$this -> mySQLSafe($attTypeId)."'":"";
		$rs = $this -> querySelect("taskman_allowattfiles", $flds, array("atttypeid"), "A", $whCl);
		return $this -> getResultSet($rs, $flds);
	}
	
	function getUserGroups($appId, $userId){
		$flds = array("grid");
		$rs = $this -> querySelect("usmgt_usergroup", $flds, "", "A", "appid='".$this -> mySQLSafe($appId)."' and userid='".$this -> mySQLSafe($userId)."'");
		$retArr = array();
		foreach($rs as $row){
			$retArr[] = $row["grid"];
		}
		return $retArr;
	}
	
	function getGroupList($grId = ""){
		$flds = array("groupId" => "grid", "groupName" => "groupname");
		$whCl = ($grId)?"grid='".$this -> mySQLSafe($grId)."'":"";
		$rs = $this -> querySelect("usmgt_group", $flds, array("grid"), "A", $whCl);
		return $this -> getResultSet($rs, $flds);
	}	
	
	function getResourceName($userId){	
		$flds = array("disname");
		$whCl = ($userId)?"userid='".$this -> mySQLSafe($userId)."'":"";
		$rs = $this -> querySelect("usmgt_user", $flds, array("userid"), "A", $whCl);
		return $rs[0]["disname"];
	}
		
	function getResourceList($appId, $resId = ""){
		$whCl = ($resId)?" and usmgt_user.userid='".$this -> mySQLSafe($resId)."'":"";
		$rs = $this -> queryManual("select usmgt_user.userid,usmgt_user.disname from usmgt_user,usmgt_usergroup where usmgt_user.userid=usmgt_usergroup.userid and usmgt_usergroup.appid='{$appId}'{$whCl} order by usmgt_user.userid");
		$retArr = array();
		while($row = mysql_fetch_assoc($rs)){
			$retArr[] = array("userId" => $row["userid"],
								"disName" => $row["disname"]);																	
		}
		return $retArr;
	}
	
	
}
?>
