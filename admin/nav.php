<?php
$_navLinkList = array();
ksort($disNavList);
reset($disNavList);
foreach($disNavList as $disNavigation){
	$valLinkId = $disNavigation["actPageId"];
	$valLinkName = $disNavigation["actLink"];	
	if(isset($_GET["action"]) && $_GET["action"] == $valLinkId && (!getAJAXState(_APPNAME))){	
		$_navLinkList[][0]["value"] = genHTMLLink(genAJAXLink(_APPNAME, $valLinkId, _ACTIONURL), " + ".$valLinkName, "", "menuLinkSel");
	}
	else{
		$_navLinkList[][0]["value"] = genHTMLLink(genAJAXLink(_APPNAME, $valLinkId, _ACTIONURL), " + ".$valLinkName, "", "menuLink");
	}
	if(isset($disSubNavList[$disNavigation["actId"]])){
		$tempSubNav = $disSubNavList[$disNavigation["actId"]];
		ksort($tempSubNav);
		reset($tempSubNav);
		foreach($tempSubNav as $disSubNavigation){
			$valSubLinkId = $disSubNavigation["actPageId"];
			$valSubLinkName = $disSubNavigation["actLink"];	
			if(isset($_GET["action"]) && $_GET["action"] == $valSubLinkId && (!getAJAXState(_APPNAME))){	
				$_navLinkList[][0]["value"] = genHTMLLink(genAJAXLink(_APPNAME, $valSubLinkId, _ACTIONURL), " --- ".$valSubLinkName, "", "menuLinkSmallSel");
			}
			else{
				$_navLinkList[][0]["value"] = genHTMLLink(genAJAXLink(_APPNAME, $valSubLinkId, _ACTIONURL), " --- ".$valSubLinkName, "", "menuLinkSmall");
			}
		}
	}
}

$_navLinkList[][0]["value"] = genHTMLLink(_ACTIONURL."logout", " + "."Logout", "", "menuLink");
$_navLinkList = genHTMLTable(count($_navLinkList), 1, $_navLinkList, "100%", "", 4);
?>