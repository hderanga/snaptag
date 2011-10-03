<?php
session_start();
include("settings.inc.php");
include("lang/en.inc.php");
include(_LIBPATH."functions.custom.inc.php");

if(isLoggedIn(_APPNAME)){
	redirectPage("index.php");		
}

include(_LIBPATH."class.easyformproc.inc.php");

//Create Instance
$formGen = new easyFormProc();

$formGen -> setTextElement("usName", "", 30, 20, "txtboxes", "valNonEmpty","Enter Username"); 
$formGen -> setPasswordElement("psWord", "", 20, 20, "txtboxes", false, true, "Enter Password"); 

#Process Form
$disForm = $formGen -> processForm("Login", "buttons");

#Get HTML of the elements
$disElementHTML = $formGen -> getDisElementHTML();

#Get Error Messages	
$errMsg = $formGen -> getErrorMsg("The following errors occured", "style2");

#Get Posted Values
$postedVals = $formGen -> getPostedElementValues();

if(count($postedVals)){
	include("dbaccess.class.php");
	//Create Instance
	$mySqlObj = new dBAccess(_DBHOST, _DBUSERNAME, _DBPASSWORD);//MySQL Database Instance
	$mySqlObj -> selectDB(_DBNAME);	
	
	$resLogin = $mySqlObj -> authenticateLogin($postedVals["usName"], $postedVals["psWord"], true);
	if(count($resLogin)){	
		loginUser(_APPNAME, $resLogin[0]["userId"], $resLogin[0]["lastLogin"]);
		redirectPage("index.php");		
	}
	else{
		$errMsg = $_appLang["errorLogin"];
	}
}

$contVal[0][0] = array("value" => "Username");
$contVal[0][1] = array("value" => $disElementHTML["usName"]);
$contVal[1][0] = array("value" => "Password");
$contVal[1][1] = array("value" => $disElementHTML["psWord"]);
$contVal[2][1] = array("value" => $disForm["submit"]);
$contTable = genHTMLTable(3, 2, $contVal, "100%", "", 4);

$contVal2[0][0] = array("value" => "Login", "style" => "titletext");
$contVal2[1][0] = array("value" => $errMsg, "style" => "tinyredtext");
$contVal2[2][0] = array("value" => $disForm["form"]["start"].$contTable.$disForm["form"]["end"]);
$contTable2 = genHTMLTable(3, 1, $contVal2, "220");

showHeaderHTML("Management Console", "appstyle.css", "", "");
echo useTemplate("mainlayout", "Management Console", "", "", $contTable2);
showFooterHTML();
?>