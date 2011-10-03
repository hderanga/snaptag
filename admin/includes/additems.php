<?php
include_once(_LIBPATH."class.easyformproc.inc.php");

$taskVals = array("temp_id"=>"","price"=>"", "catid"=>"", "largeimg"=>"", "certpdf"=>"", "file_size"=>"","title" =>"","description" =>"","keyword" =>"");
$all = array("*");
$edit = false;
$chk = true;
if(isset($_GET["tempid"]))
{	
	$edit = true;
	$chk = false;
	$tempcon = "temp_id = '".$_GET["tempid"]."'";
	$editsql = $mySqlObj -> querySelect("template",$all,"","",$tempcon);
	for($y=0; $y < count($editsql); $y++)
	{
		$taskVals["temp_id"] = $editsql[$y]["temp_id"];
		$taskVals["largeimg"] = $editsql[$y]["temp_img"];
		$taskVals["catid"] = $editsql[$y]["catid"];
		$taskVals["title"] = $editsql[$y]["title"];
		$taskVals["description"] = $editsql[$y]["description"];
		$taskVals["keyword"] = $editsql[$y]["keyword"];
		$taskVals["price"] = $editsql[$y]["price"];
		
		$catLink = array("catlink");
		$catCon = "catid='".$taskVals["catid"]."'";
		$catNameQuery = $mySqlObj -> querySelect("categories",$catLink,"","",$catCon);
		$catNameDisp= $catNameQuery[0]['catlink'];
	}

}


$editsql = $mySqlObj -> querySelect("categories",$all);
for($x=0; $x < count($editsql); $x++)
{
	/*echo $catArr[$x] = $editsql[$x]["catname"];
	$taskVals = $editsql[$x]["catid"];*/
	$catArr[$editsql[$x]["catid"]] = $editsql[$x]["catname"];
}

//Create Instance
$formGen = new easyFormProc();	

$mimetypes = array("image/jpeg" => "JPG");  	
$mimetypes1 = array("application/pdf" => "PDF");
#Set Elements

$formGen -> setFileElement("largeimg", "txtboxes", $chk, "Upload large image..", 2048, $mimetypes);
$formGen -> setSelectElement("cat", $catArr, $taskVals["catid"], "txtboxes", 1, false, "valNonEmpty", "Select category...");
$formGen -> setTextElement("title", $taskVals["title"], "", 50, "txtboxes");
$formGen -> setTextElement("price", $taskVals["price"],255, 25,"txtboxes","valNonEmpty","Enter Price.");
$formGen -> setTextAreaElement("description", $taskVals["description"], 50, 8, "txtboxes");
$formGen -> setTextAreaElement("keyword", $taskVals["keyword"], 50, 8, "txtboxes");
 

$addFormPostURL = "";		
if($edit){
	$addFormPostURL = "&tempid=".$_GET["tempid"];
	$submitBtnLabel = "Update Item";
}
else{
	$submitBtnLabel = "Add Item";
}
		
#Process Form
$disForm = $formGen -> processForm($submitBtnLabel, "buttons", "AddTask", "index.php?action=additems".$addFormPostURL);
		
#Get HTML of the elements
$disElementHTML = $formGen -> getDisElementHTML();
		
#Get Error Messages	
$errMsg = $formGen -> getErrorMsg("Following errors occured:", "style2");
		
#Get Posted Values
$postedVals = $formGen -> getPostedElementValues();
		
if(count($postedVals))
{
/*print_ri($postedVals);
exit;
*/
	$condit = "temp_img = '".$_FILES['largeimg']['name']."' ";
	$chksql = $mySqlObj -> querySelect("template",$all,"","",$condit); 
	if(count($chksql) == 0 || $edit == true)/* */
	{
		
		$cana = array("catlink");
		$catnacon = "catid='".$postedVals['cat']."'";
		$catnames = $mySqlObj -> querySelect("categories",$cana,"","",$catnacon);
				
	
	#uploading image if there are any images uploaded 
		if(!empty($postedVals["largeimg"]["name"]))
			{
				//$uploaddir1 = '../templates/large/';
				//$uploadfile1 = _BASEDIR . basename($_FILES['largeimg']['name']);
				$uploadfile1 = _BASEDIR.basename($_FILES['largeimg']['name']);
				/*echo $uploadfile1;
				echo _BASEDIR;*/
				if (move_uploaded_file($_FILES['largeimg']['tmp_name'], $uploadfile1)) 
				{
					$errMsg = "File is valid, and was successfully uploaded.\n";
					chmod($uploadfile1, 0644);
				} else 
				{
					echo "";
				}
			}
		if(empty($postedVals["largeimg"]["name"]) && $edit == true)
		{
			$allVals = array("catid" => addslashes($postedVals["cat"]),"price"=>addslashes($postedVals["price"]), "title" => addslashes($postedVals["title"]), "description" => addslashes($postedVals["description"]),"keyword" => addslashes($postedVals["keyword"]));	
		}else
		{
		$allVals = array("temp_img" => addslashes($postedVals["largeimg"]["name"]),"price"=>addslashes($postedVals["price"]), "catid" => addslashes($postedVals["cat"]),"title" => addslashes($postedVals["title"]), "description" => addslashes($postedVals["description"]),"keyword" => addslashes($postedVals["keyword"]));
		}
		
		if($edit)
		{
			$condition = "temp_id = '".$_GET["tempid"]."'";
			$resAddUpdate = $mySqlObj -> queryUpdate("template",$allVals,true,$condition);
			if(isset($_SESSION["opt"]))
			{
				if(isset($_SESSION["count"]))
				{
					$count="&count=".$_SESSION["count"];
				}
					redirectPage("index.php?action=Search_poster&slValue=".$_SESSION["opt"].$count);
			}
			
			else
			{
			redirectPage("index.php?action=posters");
			}
		}
		else
		{
			$resAddUpdate = $mySqlObj -> queryUpdate("template",$allVals,"","");
			redirectPage("index.php?action=addposter");
		}
	
	}else
	{
		$errMsg = "File name already exsist please upload with different name.\n";
	}
		
	
}

$rowColor = getRowColor(0);
$rowColor2 = getRowColor(1);
$bodyVals[0][0] = array("value" => "<b>Item Name&nbsp; : <b>", "align" => "right", "style" => $rowColor, "valign" => "top");
$bodyVals[1][0] = array("value" => "<b>Category&nbsp; : <b>", "align" => "right", "style" => $rowColor2, "valign" => "top");
$bodyVals[2][0] = array("value" => "<b>Item Price&nbsp; : <b>", "align" => "right", "style" => $rowColor, "valign" => "top");	
$bodyVals[3][0] = array("value" => "<b>Image File&nbsp; : <b>", "align" => "right", "style" => $rowColor2, "valign" => "top", "width" => "20%");
$bodyVals[4][0] = array("value" => "<b>Meta Description&nbsp; : <b>", "align" => "right", "style" => $rowColor2, "valign" => "top");
$bodyVals[5][0] = array("value" => "<b>Meta Keywords&nbsp; : <b>", "align" => "right", "style" => $rowColor, "valign" => "top");
$bodyVals[6][0] = array("value" => "", "align" => "right", "style" => $rowColor2, "valign" => "top");
$bodyVals[0][1] = array("value" => $disElementHTML["title"], "align" => "left", "width" => "40%", "style" => $rowColor);
$bodyVals[1][1] = array("value" => $disElementHTML["cat"], "align" => "left", "width" => "40%", "style" => $rowColor2);
$bodyVals[2][1] = array("value" => $disElementHTML["price"], "align" => "left", "width" => "40%", "style" => $rowColor);
$bodyVals[3][1] = array("value" => $disElementHTML["largeimg"]."(Width = 150px, Height = 120px)", "align" => "left", "width" => "40%", "valign" => "top", "style" => $rowColor2);
	if($edit)
	{
	$bodyVals[0][3] = array("value" => genHTMLImage("../templates/thumb/".$taskVals["largeimg"]), "style" => $rowColor2);
	}
$bodyVals[4][1] = array("value" => $disElementHTML["description"], "align" => "left", "width" => "40%", "style" => $rowColor2);
$bodyVals[5][1] = array("value" => $disElementHTML["keyword"], "align" => "left", "width" => "40%", "style" => $rowColor);
$bodyVals[6][1] = array("value" => $disForm["submit"], "style" => $rowColor2);
$genBodyTableMain = genHTMLTable(count($bodyVals), 3, $bodyVals, "100%", "", 8, 0, 0);
$bodyVals1[0][0] = array("value" => "Add New Item" , "style" => "maintitle");
$bodyVals1[1][0] = array("value" => $errMsg, "style" => "tinyredtext");
$bodyVals1[2][0] = array("value" => $disForm["form"]["start"].$genBodyTableMain.$disForm["form"]["end"]);
$genBodyTable = genHTMLTable(count($bodyVals1), 1, $bodyVals1, "100%", "", 2, 0, 0);
$_disContentBody = $genBodyTable;
?>
