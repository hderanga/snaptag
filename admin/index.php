<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", "on");

include("settings.inc.php");
include("lang/en.inc.php");
include("function.php");
include(_LIBPATH."functions.custom.inc.php");
include("dbaccess.class.php");
ini_set('memory_limit', "32M");  
logoutUser(_APPNAME);//Logout user if request to logout

if(!isLoggedIn(_APPNAME)){
	redirectPage("login.php");	
}

//Create DBAccess Instance
$mySqlObj = new dBAccess(_DBHOST, _DBUSERNAME, _DBPASSWORD);//MySQL Database Instance
$mySqlObj -> selectDB(_DBNAME);
//$mySqlObj -> enableDebugMode();


$pageJS = "";
/*initAJAX(_APPNAME, _USEAJAX);
if(getAJAXState(_APPNAME)){
	$pageJS = getAjaxJS();	
}*/

$incRPC = true;	
include("rpc.php");
$disPageCont = "<div id=\"content\">".$_disContentBody."</div>";

include("nav.php");


showHeaderHTML("Management Console", "appstyle.css", $pageJS);
echo useTemplate("mainlayout", "Management Console", "Welcome ".$mySqlObj -> getResourceName(getUserId(_APPNAME))."!", $_navLinkList, $disPageCont);
showFooterHTML();
?>