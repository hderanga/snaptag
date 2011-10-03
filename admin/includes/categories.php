<?
include(_LIBPATH."class.easyformproc.inc.php");

//Create Instance
	$mySqlObj = new dBAccess(_DBHOST, _DBUSERNAME, _DBPASSWORD);//MySQL Database Instance
	$mySqlObj -> selectDB(_DBNAME);	

$allFieldsList = array("*");
$res = $mySqlObj -> querySelect("categories",$allFieldsList);

//Create Instance
$formGen = new easyFormProc();

for($r=0;$r<count($res);$r++)
{
//$formGen -> setTextElement("order".$r,$res[$r]["ordering"],5, 5,"txtboxes"); 
$formGen -> setHiddenElement("id".$r,$res[$r]["catid"]); 

}

#Process Form
$disForm = $formGen -> processForm("Update","buttons", "submit", "index.php?action=categories");

#Get HTML of the elements
$disElementHTML = $formGen -> getDisElementHTML();

#Get Error Messages	
$errMsg = $formGen -> getErrorMsg("The following errors occured","style2");

#Get Posted Values
$postedVals = $formGen -> getPostedElementValues();
if(count($postedVals))
{
for($z=0;$z<count($postedVals)/2;$z++)
{
	$orderV = array("ordering" => $postedVals["order".$z]);
	$condition = "catid = ".$postedVals["id".$z];
	$resAddUpdate = $mySqlObj -> queryUpdate("categories",$orderV,true,$condition);

}
redirectPage("index.php?action=categories");
}


$cont[0][0] = array("value" => "<b>Category ID</b>", "width" => "100");
$cont[0][1] = array("value" => "<b>Category Name</b>", "width" => "200");
$cont[0][2] = array("value" => "<b>Order</b>");
$cont[0][3] = array("value" => "&nbsp;");

for($i=0; $i < count($res); $i++)
{
	
	$cont[$i+1][0] = array("value" => $res[$i]["catid"]);
	$cont[$i+1][1] = array("value" => $res[$i]["catname"]);
	$ord = "order".$i;
	
	//$orderVal[0][1] = array("value" => $disElementHTML[$ord]);
	
	//$cont[$i+1][2] = array("value" => $disElementHTML["order".$i].$disElementHTML["id".$i]);
	$cont[$i+1][3] = array("value" => genHTMLLink("index.php?action=addcat&catid=".$res[$i]["catid"],"Edit")." | ".genHTMLLink("index.php?action=categories&confirm=false&table=categories&field=catid&x=".$res[$i]["catid"],"Delete"));
}
$cont[$i+1][2] = array("value" => $disForm["submit"]);

$tbllist = genHTMLTable(count($res)+2,4, $cont, "100%", 3, 4,"",0);

$contVal[0][0] = array("value" => "<h3>Categories</h3>");
$contVal[1][0] = array("value" => $disForm["form"]["start"].$tbllist.$disForm["form"]["end"]);

$contTable = genHTMLTable(2, 1, $contVal, "100%", "", 4,"",0,"center","addtable");


$_disContentBody = $contTable;

if(isset($_GET["confirm"]))
{
	if($_GET["confirm"]=="false")
	{
		$_disContentBody="Are you sure you want to delete? <a href=\"index.php?action=".$_GET["action"]."&confirm=true&field=".$_GET["field"]."&table=".$_GET["table"]."&x=".$_GET["x"]."\">Yes</a> | <a href=\"index.php?action=".$_GET["action"]."\">No</a>";
	}
	else
	{
		$arrCon4 = $_GET["field"]."='".$_GET["x"]."'";
		$resAdd12 = $mySqlObj -> queryDelete($_GET["table"],$arrCon4);	
		//mysql_query("delete from ".." where ".);
			
		redirectPage("index.php?action=".$_GET["action"]);
	}

}

?>