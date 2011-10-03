<?php
function paginate($query,$recsPerPage,$next=true,$prev=true,$search="")
{
	$nav = ""; $info = "";
	$result = mysql_query($query) or die("Failed to execute query : ".mysql_error());
	$total = mysql_num_rows($result);
	$numPages = ceil($total/$recsPerPage)+1;
	if(!isset($_GET["count"])) $_GET["count"]=1;
	for($i=1;$i<$numPages;$i++)
	{
		if($search!="")
		{
			if($i<>$_GET["count"])
			{ 
				if($search=="a"||$search=="b"||$search=="c")
				{
				$nav .= " <a href=\"index.php?action=".$_GET["action"]."&slValue=".$search."&count=$i\">";
				}
				else
				{
				$nav .= " <a href=\"index.php?action=".$_GET["action"]."&keyword=".$search."&count=$i\">";
				}
			}
		}
		
		else
		{
			if($i<>$_GET["count"]) $nav .= " <a href=\"index.php?action=".$_GET["action"]."&count=$i\">";
		}
		if($i==$_GET["count"]) $nav .= "  ";
		$nav .= $i;
		if($i==$_GET["count"]) $nav .= "  ";
		
		if($i<>$_GET["count"]) $nav .= "</a> "; 
		if($i<>$numPages-1) $nav .= " | ";
	}
	if($_GET["count"]==($numPages-1))$a=$recsPerPage-($total%$recsPerPage);
	else $a=0;
	
	$info = "Showing ".((($_GET["count"]-1)*$recsPerPage)+1)."-".((($_GET["count"]-1)*$recsPerPage)+$recsPerPage-$a).
			", Page ".$_GET["count"]." of ".($numPages-1).".";
			
	$result = mysql_query($query." LIMIT ".(($_GET["count"]-1)*$recsPerPage).",".$recsPerPage) or die("Failed to execute query : ".mysql_error());
	return array("navigation"=>$nav,"information"=>$info,"result"=>$result);
}
?>