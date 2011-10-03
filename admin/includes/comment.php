<?
include(_LIBPATH."class.easyformproc.inc.php");
include("paginate.php");
//Create Instance
	$mySqlObj = new dBAccess(_DBHOST, _DBUSERNAME, _DBPASSWORD);//MySQL Database Instance
	$mySqlObj -> selectDB(_DBNAME);	

//$res = $mySqlObj -> querySelect("template",$allFieldsList,$sortby,"D");
$cont[0][0] = array("value" => "<b>comment ID</b>", "width" => "100", "align" => "center");
$cont[0][1] = array("value" => "<b>user name</b>", "width" => "200", "align" => "center");
$cont[0][2] = array("value" => "<b>website</b>", "align" => "center");
$cont[0][3] = array("value" => "<b>status</b>", "align" => "center");
$cont[0][4] = array("value" => "<b>view</b>", "align" => "center");
$cont[0][5] = array("value" => "<b>delete</b>", "align" => "center");
$qryStr ="&action=".$_REQUEST['action'];


$arr = paginate("select * from comments order by com_id desc",10);
$i=0;
while($res = mysql_fetch_array($arr["result"]))
{ 
	//getting the category name
	$cana = array("catlink");
	$catnacon = "catid='".$res["catid"]."'";
	$catnames = $mySqlObj -> querySelect("categories",$cana,"","",$catnacon);
	$catname = $catnames[0]['catlink'];	
	
	//$numOfPpl = mysql_num_rows(mysql_query("select * from ratings where rating_id='".$res["temp_id"]."'"));
	//getting the category name
	$cont[$i+1][0] = array("value" => $res["com_id"],"valign" => "top", "align" => "center");
	$cont[$i+1][1] = array("value" => $res["name"],"valign" => "top", "align" => "center");
	$cont[$i+1][2] = array("value" => $res["website"] ,"valign" => "top", "align" => "center");
	$cont[$i+1][3] = array("value" => $res["status"] ,"valign" => "top", "align" => "center");
	$cont[$i+1][4] = array("value" => "<a href=\"index.php?action=viewcomment&id=".$res["com_id"]."\">view</a>" ,"valign" => "top", "align" => "center");
	$cont[$i+1][5] = array("value" => genHTMLLink("index.php?action=comment&confirm=false&table=comments&field=com_id&com_id=".$res["com_id"]."&name=".$res["name"]."&comment=".$res["comment"],"Delete"),"valign" => "top","align" => "center");
	
	
	$i=$i+1;// 
}
$tbllist = genHTMLTable($i+1,6, $cont, "100%", 3, 4,"",0);

$contVal[0][0] = array("value" => "<h3>View User comments details</h3>");
$contVal[1][0] = array("value" => $arr["information"]."&nbsp; &nbsp;".$arr["navigation"], "align" => "center");
$contVal[2][0] = array("value" => $tbllist);
$contVal[3][0] = array("value" => $arr["information"]."&nbsp; &nbsp;".$arr["navigation"], "align" => "center");

$contTable = genHTMLTable(4, 1, $contVal, "100%", "", 4,"",0,"center","addtable");


$_disContentBody = $contTable;

if(isset($_GET["confirm"]))
{
	if($_GET["confirm"]=="false")
	{
		$_disContentBody="Are you sure you want to delete? <a href=\"index.php?action=".$_GET["action"]."&confirm=true&field=".$_GET["field"]."&table=".$_GET["table"]."&com_id=".$_GET["com_id"]."&name=".$_REQUEST['name']."&comment=".$_REQUEST['comment']."\">Yes</a> | <a href=\"index.php?action=".$_GET["action"]."\">No</a>";
	}
	else
	{
		$arrCon4 = $_GET["field"]."='".$_GET["com_id"]."'";
		$resAdd12 = $mySqlObj -> queryDelete($_GET["table"],$arrCon4);
		redirectPage("index.php?action=".$_GET["action"]);
	}

}


?>