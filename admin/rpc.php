<?php
if(!(isset($incRPC) && $incRPC)){
	session_start();
	include("settings.inc.php");
	include("lang/"._LANGUAGE.".inc.php");
	include(_LIBPATH."functions.custom.inc.php");	
	include("dbaccess.class.php");
	
	if(!isLoggedIn(_APPNAME)){
		die();
	}						
	
	//Create DBAccess Instance
	$mySqlObj = new dBAccess(_DBHOST, _DBUSERNAME, _DBPASSWORD);//MySQL Database Instance
	$mySqlObj -> selectDB(_DBNAME);
	//$mySqlObj -> enableDebugMode();
}

$_incRsActivityList = $mySqlObj -> getActivityList(_APPID, getUserId(_APPNAME));
$allowCheckList = array();
$disNavList = array();
$disSubNavList = array();
foreach($_incRsActivityList as $rsActivity){
	$allowCheckList[] = $rsActivity["actPageId"];
	if($rsActivity["parentId"] == 0){
		if(!isset($disNavList[$rsActivity["orderNo"]])){
			$disNavList[$rsActivity["orderNo"]] = array("actPageId" => $rsActivity["actPageId"], "actLink" => $rsActivity["actLink"], "actId" => $rsActivity["actId"]);
		}
		else{
			die("Cannot Create Main Menu.. Order Mismatch");
		}
	}
	else{
		if(!isset($disSubNavList[$rsActivity["parentId"]][$rsActivity["orderNo"]])){
			$disSubNavList[$rsActivity["parentId"]][$rsActivity["orderNo"]] = array("actPageId" => $rsActivity["actPageId"], "actLink" => $rsActivity["actLink"], "actId" => $rsActivity["actId"]);
		}
		else{
			die("Cannot Create Sub Menu.. Order Mismatch");
		}
	}	
}
$allowAddCheckList = array("fmcgprodinfo", "hcproducts", "hcprojects", "hcmededu","viewcomment");
$allowCheckList = array_merge($allowCheckList, $allowAddCheckList);
if(isset($_GET["action"]) && in_array($_GET["action"], $allowCheckList)){
	$_rpcPageNameToInc = $_GET["action"].".php"; 
}
else{
	$_rpcPageNameToInc = "home.php"; 
}
include("includes/".$_rpcPageNameToInc);
if(!(isset($incRPC) && $incRPC)){
	echo "content|".$_disContentBody;
}
?>