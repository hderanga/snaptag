<?php
include(_LIBPATH."class.easyformproc.inc.php");

$taskVals = array("meta_id"=>"","title"=>"", "description"=>"", "keyword"=>"", "page"=>"");
$pageArr = array("home"=>"Home","templates"=>"Templates", "tips"=>"Tips", "help"=>"Help", "aboutus"=>"About Us", "contact"=>"Contact Us");
if (isset($_REQUEST['metaid']) || isset($_REQUEST['type'])) 
{
	$optEdit = false;
	if (isset($_REQUEST['metaid'])) 
	{
	$optEdit = true;
	$taskList = mysql_fetch_array(mysql_query("select * from keywords where meta_id='".$_REQUEST['metaid']."'"));	
	
	$taskVals["title"] = $taskList["title"];
	$taskVals["description"] = $taskList["description"];
	$taskVals["keyword"] = $taskList["keyword"];
	$taskVals["page"] = $taskList["page"];
	}
//Create Instance
$formGen = new easyFormProc();	

#Set Elements
$formGen -> setSelectElement("page", $pageArr, $taskVals["page"], "txtboxes", 1, false, "valNonEmpty", "Select category...");
$formGen -> setTextElement("title", $taskVals["title"], "", 50, "txtboxes");
$formGen -> setTextAreaElement("description", $taskVals["description"], 50, 8, "txtboxes");
$formGen -> setTextAreaElement("keyword", $taskVals["keyword"], 50, 8, "txtboxes");
//$formGen -> setHiddenElement("data", $_REQUEST["metaid"], 50, 8, "txtboxes");

$addFormPostURL = "";		
if($optEdit){
	$addFormPostURL = "&metaid=".$_GET["metaid"]."&type=".$_REQUEST['type'];
	$submitBtnLabel = "Update";
}
else{
	$addFormPostURL = "&type=".$_REQUEST['type'];
	$submitBtnLabel = "Add Data";
}

		
#Process Form
$disForm = $formGen -> processForm($submitBtnLabel, "buttons", "subAddTask", "index.php?action=addmeta".$addFormPostURL);
		
#Get HTML of the elements
$disElementHTML = $formGen -> getDisElementHTML();
		
#Get Error Messages	
$errMsg = $formGen -> getErrorMsg($_appLang["errorMsgLabel"], "style2");
		
#Get Posted Values
$postedVals = $formGen -> getPostedElementValues();
		
if(count($postedVals))
{
	$allVals = array("title" => $postedVals["title"], "description" => $postedVals["description"], "keyword" => $postedVals["keyword"], "page" => $postedVals["page"]);
	if($optEdit)
		{
		$condition = "meta_id = '".$_GET["metaid"]."'";
		$resAddUpdate = $mySqlObj -> queryUpdate("keywords",$allVals,true,$condition);
		echo "Successfully added. Please wait...<META HTTP-EQUIV=\"refresh\" content=\"3;URL=index.php?action=addmeta\" />";
		die();
		}else
		{
		$pagetest = mysql_query("select * from keywords where page='".$postedVals["page"]."'");
		
		$rowss = mysql_num_rows($pagetest);
		if($rowss > 0)
		{
			$errMsg = "Meta data for the current page has been added already";
		}else
		{
			$resAddUpdate = $mySqlObj -> queryUpdate("keywords",$allVals,"","");
		echo "Successfully added. Please wait...<META HTTP-EQUIV=\"refresh\" content=\"3;URL=index.php?action=addmeta&type=add\" />";
		die();
		}
		}
		
}

$rowColor = getRowColor(0);
$rowColor2 = getRowColor(1);

$bodyVals[0][0] = array("value" => "<b>Title : <b>", "align" => "right", "width" => "30%", "style" => $rowColor2);
$bodyVals[0][1] = array("value" => $disElementHTML["page"], "align" => "left", "width" => "70%", "style" => $rowColor2);
$bodyVals[1][0] = array("value" => "<b>Title : <b>", "align" => "right", "width" => "30%", "style" => $rowColor2);
$bodyVals[1][1] = array("value" => $disElementHTML["title"], "align" => "left", "width" => "70%", "style" => $rowColor2);
$bodyVals[2][0] = array("value" => "<b>Description : <b>", "align" => "right", "width" => "30%", "style" => $rowColor);
$bodyVals[2][1] = array("value" => $disElementHTML["description"], "align" => "left", "width" => "70%", "style" => $rowColor);
$bodyVals[3][0] = array("value" => "<b>Keywords : <b>", "align" => "right", "width" => "30%", "style" => $rowColor2);
$bodyVals[3][1] = array("value" => $disElementHTML["keyword"], "align" => "left", "width" => "70%", "style" => $rowColor2);
//$bodyVals[4][0] = array("value" => $disElementHTML["data"]);
$bodyVals[4][1] = array("value" => $disForm["submit"]);
			
$genBodyTableMain = genHTMLTable(count($bodyVals), 2, $bodyVals, "100%", "", 8, 0, 0);
	
$bodyVals1[0][0] = array("value" => "Metadata" , "style" => "maintitle");
$bodyVals1[1][0] = array("value" => $errMsg, "style" => "tinyredtext", "align" => "center");
$bodyVals1[2][0] = array("value" => $disForm["form"]["start"].$genBodyTableMain.$disForm["form"]["end"]);

$genBodyTable = genHTMLTable(count($bodyVals1), 1, $bodyVals1, "100%", "", 3, 0, 0);
		
$_disContentBody = $genBodyTable;

}else
{
include("paginate.php");
$arr = paginate("select * from keywords",5);

$str = "<span class=maintitle>Metadata</span>
<table cellpadding=3 border=0 width=100%>
<tr>
	<td><b><a href=\"index.php?action=addmeta&type=add\">Add Data</a></b></td><td width=200>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
</tr>
<tr>
	<td width=200>&nbsp;</td><td colspan=4><b>".$arr["information"]."&nbsp; &nbsp;".$arr["navigation"]."</b></td>
</tr>
<tr>
	<td width=200>&nbsp;</td><td colspan=4><b>&nbsp; &nbsp;</b></td>
</tr>

<tr>
	<td  colspan=5><table cellpadding=3 cellspacing=1 border=0 width=100% bgcolor=#000000>
	<tr bgcolor=#cccccc>
	<td><b>Page Name</b></td><td><b>Page Title</b></td><td><b>Meta Description</b></td><td><b>Meta Keywords</b></td><td>&nbsp;</td>
</tr>";
while($row = mysql_fetch_array($arr["result"]))
{
	
	$str .= "<tr bgcolor=#ffffff><td>".ucwords($row["page"])."</td><td>".$row["title"]."</td><td>".$row["description"]."</td><td>".$row["keyword"]."</td><td><a href=\"index.php?action=addmeta&metaid=".$row["meta_id"]."\">Edit</a></td></tr>";
}
	
$str .= "</table></td>
</tr>
<tr>
	<td width=200>&nbsp;</td><td colspan=4><b>&nbsp; &nbsp;</b></td>
</tr>
<tr>
	<td width=200>&nbsp;</td><td colspan=4><b>".$arr["information"]."&nbsp; &nbsp;".$arr["navigation"]."</b></td>
</tr>
</table>";
$_disContentBody = $str;
//include("delete.php");
}
?>
