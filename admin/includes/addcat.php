<?
include(_LIBPATH."class.easyformproc.inc.php");
$editcatname ="";

//Create Instance
	$mySqlObj = new dBAccess(_DBHOST, _DBUSERNAME, _DBPASSWORD);//MySQL Database Instance
	$mySqlObj -> selectDB(_DBNAME);	
$caid = "";
$edit = false;
if(isset($_REQUEST["catid"]))
{
	$edit = true;
	$caid = $_REQUEST["catid"];
	$all = array("catname");
	$con = "catid = '".$_REQUEST["catid"]."'";
	$editsql = $mySqlObj -> querySelect("categories",$all,"","",$con);
	$editcatname = $editsql[0]["catname"];
}
//Create Instance
$formGen = new easyFormProc();

#Set Elements
$formGen -> setTextElement("catname",$editcatname,255, 25,"txtboxes","valNonEmpty","Enter category name."); 

$addFormPostURL = "";		
if($edit){
	$addFormPostURL = "&catid=".$_GET["catid"];
	$submitBtnLabel = "Update Category";
}
else{
	$submitBtnLabel = "Add Category";
}
#Process Form
$disForm = $formGen -> processForm($submitBtnLabel,"buttons", "submit", "index.php?action=addcat".$addFormPostURL);

#Get HTML of the elements
$disElementHTML = $formGen -> getDisElementHTML();

#Get Error Messages	
$errMsg = $formGen -> getErrorMsg("The following errors occured","style2");

#Get Posted Values
$postedVals = $formGen -> getPostedElementValues();

#If Form is posted
if(count($postedVals))
{	
	$catLink = str_replace(' ','_',$postedVals["catname"]);
	$allVals = array("catname" => $postedVals["catname"], "catlink" => strtolower($catLink), "status" => 1);
	if($edit)
	{
	$condition = "catid = '".$_GET["catid"]."'";
	$resAddUpdate = $mySqlObj -> queryUpdate("categories",$allVals,true,$condition);
	}else
	{
	$resAddUpdate = $mySqlObj -> queryUpdate("categories",$allVals,"","");
	}
	redirectPage("index.php?action=addcat");
}

$contVal[0][0] = array("value" => "<b>Category Name</b>", "width" => "150");
$contVal[0][1] = array("value" => $disElementHTML["catname"]);
$contVal[1][1] = array("value" => $disForm["submit"]);
$contTable = genHTMLTable(2, 2, $contVal, "80%", "", 4,"",0,"center","addtable");

$contVal2[0][0] = array("value" => "Add Category", "style" => "titletext");
$contVal2[2][0] = array("value" => $errMsg, "style" => "tinyredtext");
$contVal2[3][0] = array("value" => $disForm["form"]["start"].$contTable.$disForm["form"]["end"]);

$contTable2 = genHTMLTable(4, 1, $contVal2, "100%");

$_disContentBody = $contTable2;
?>