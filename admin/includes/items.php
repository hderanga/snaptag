<?
include(_LIBPATH."class.easyformproc.inc.php");
include("paginate.php");
//Create Instance
	$mySqlObj = new dBAccess(_DBHOST, _DBUSERNAME, _DBPASSWORD);//MySQL Database Instance
	$mySqlObj -> selectDB(_DBNAME);	

$allFieldsList = array("*");
$sortby = array("catid","temp_id");
$res = $mySqlObj -> querySelect("template",$allFieldsList,$sortby,"D");
$cont[0][0] = array("value" => "<b>Item ID</b>", "width" => "100");
$cont[0][1] = array("value" => "<b>Item Image</b>", "width" => "200");
$cont[0][2] = array("value" => "<b>Item Name </b>");
$cont[0][3] = array("value" => "<b>Item Category</b>");
$cont[0][4] = array("value" => "&nbsp;");
$qryStr ="&action=".$_REQUEST['action'];


$arr = paginate("select * from template order by temp_id desc",10);
$i=0;
while($res = mysql_fetch_array($arr["result"]))
{ 
	//getting the category name
	$cana = array("catlink");
	$catnacon = "catid='".$res["catid"]."'";
	$catnames = $mySqlObj -> querySelect("categories",$cana,"","",$catnacon);
	$catname = $catnames[0]['catlink'];	
	//getting the category name
	$cont[$i+1][0] = array("value" => $res["temp_id"],"valign" => "top");
	$cont[$i+1][1] = array("value" => '<img src="../item_images/'.$res["temp_img"].'" width="150px" height="120px" />');
	$cont[$i+1][2] =  array("value" => $res["title"],"valign" => "top");
	$cont[$i+1][3] = array("value" => ucwords($catname),"valign" => "top");
	$cont[$i+1][4] = array("value" => genHTMLLink("index.php?action=additems&tempid=".$res["temp_id"],"Edit")." | ".genHTMLLink("index.php?action=items&confirm=false&table=template&field=temp_id&x=".$res["temp_id"]."&img=".$res["temp_img"]."&catname=".$catname,"Delete"),"valign" => "top");
	
	$i=$i+1;// 
}
$tbllist = genHTMLTable($i+1,5, $cont, "100%", 3, 4,"",0);

$contVal[0][0] = array("value" => "<h3>Categories</h3>");
$contVal[1][0] = array("value" => $arr["information"]."&nbsp; &nbsp;".$arr["navigation"], "align" => "center");
$contVal[2][0] = array("value" => $tbllist);
$contVal[3][0] = array("value" => $arr["information"]."&nbsp; &nbsp;".$arr["navigation"], "align" => "center");

$contTable = genHTMLTable(4, 1, $contVal, "100%", "", 4,"",0,"center","addtable");


$_disContentBody = $contTable;

if(isset($_GET["confirm"]))
{
	if($_GET["confirm"]=="false")
	{
		$_disContentBody="Are you sure you want to delete? <a href=\"index.php?action=".$_GET["action"]."&confirm=true&field=".$_GET["field"]."&table=".$_GET["table"]."&x=".$_GET["x"]."&img=".$_REQUEST['img']."&pdf=".$_REQUEST['pdf']."&catname=".$_REQUEST['catname']."\">Yes</a> | <a href=\"index.php?action=".$_GET["action"]."\">No</a>";
	}
	else
	{
		$arrCon4 = $_GET["field"]."='".$_GET["x"]."'";
		$resAdd12 = $mySqlObj -> queryDelete($_GET["table"],$arrCon4);
		
			
		redirectPage("index.php?action=".$_GET["action"]);
	}

}

?>