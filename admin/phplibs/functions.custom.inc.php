<?php
function isLoggedIn($appName){
	if(isset($_SESSION[$appName]["AccLogin"]) && $_SESSION[$appName]["AccLogin"]){
		return true;
	}
	else return false;
}

function loginUser($appName, $userId, $lastLogin){
	$_SESSION[$appName]["AccLogin"] = true;
	$_SESSION[$appName]["userId"] = $userId;
	$_SESSION[$appName]["lastLogin"] = $lastLogin;
}

function logoutUser($appName){
	if(isset($_GET["action"]) && $_GET["action"] == "logout")$_SESSION[$appName] = array();
}

function redirectPage($page){
	header("Location: {$page}");
	die();
}

function redirectConfirmPage($message, $page, $interval, $style = false){
	$output = $message."<META HTTP-EQUIV=\"refresh\" content=\"$interval;URL={$page}\" />";
	return ($style)?genSpanStyle($output, $style):$output;
}

function getUserId($appName){
	return $_SESSION[$appName]["userId"];
}

function getLastLogin($appName){
	return $_SESSION[$appName]["lastLogin"];
}

function genIdCode($taskId, $numLen, $padChr, $prefix = ""){
	$tempTaskId = $prefix.str_pad($taskId, $numLen, "0", STR_PAD_LEFT);
	return $tempTaskId;
}

function getRowColor($rowNum){
	return ($rowNum%2)?"rowBg1":"rowBg2";
}

function convHTMLEntities($str){
	return htmlentities($str, ENT_QUOTES);
}

function decodeHTMLEntities($str){
	return html_entity_decode($str, ENT_QUOTES);
}

function initAJAX($appName, $ajaxVal){
	if(!isset($_SESSION[$appName]["useAJAX"])){
		$_SESSION[$appName]["useAJAX"] = $ajaxVal;		
	}
	else{		
		if(isset($_GET["dismode"]) && $_GET["dismode"] == "html")$_SESSION[$appName]["useAJAX"] = false;
		elseif(isset($_GET["dismode"]) && $_GET["dismode"] == "ajax" && $ajaxVal)$_SESSION[$appName]["useAJAX"] = true;
	}
}

function getAJAXState($appName){	
	//return $_SESSION[$appName]["useAJAX"];
}

function genAJAXLink($appName, $val, $url){
	if(getAJAXState($appName)){		
		return "javascript:sndReq('".htmlentities($val, ENT_QUOTES)."')";
	}
	else{
		return $url.htmlentities($val, ENT_QUOTES);
	}
}

function convN2BR($str){
	return str_replace("\n", "<br />", $str);
}

function convBR2N($str){
	return str_replace("<br />", "\n", $str);
}

function disFormattedText($str, $linkStyle = ""){
	return ubb2html($str, $linkStyle);
}

function ubb2html($string,$linkStyle,$url=""){    
	$string = preg_replace("/\[b\](.*?)\[\/b\]/si", "<b>\\1</b>", $string);
	$string = preg_replace("/\[i\](.*?)\[\/i\]/si", "<i>\\1</i>", $string);
	$string = preg_replace("/\[u\](.*?)\[\/u\]/si", "<u>\\1</u>", $string);
	$string = preg_replace("/\[p\](.*?)\[\/p\]/si", "<p>\\1</p>", $string);
	$string = preg_replace("/\[code\](.*?)\[\/code\]/si", "<blockquote>Code<hr noshade size=1 align=left width=100%><pre>\\1</pre><br><br><hr noshade size=1 align=left width=100%></blockquote>", $string);
	$string = preg_replace_callback("/\[php\](.*?)\[\/php\]/si","phpHighlight", $string);
	$string = preg_replace("/\[quote\](.*?)\[\/quote\]/si", "<blockquote>Quote<hr noshade size=1 align=left width=100%>\\1<br><br><hr noshade size=1 align=left width=100%></blockquote>", $string);
	$string = preg_replace("/(^|\s)(http:\/\/\S+)/si", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $string);
	$string = preg_replace("/(^|\s)(www\.\S+)/si", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $string);
	$string = preg_replace("/\[url\](http|https|ftp)(:\/\/\S+?)\[\/url\]/si", "<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>", $string);
	$string = preg_replace("/\[url\](\S+?)\[\/url\]/si","<a href=\"http://\\1\" target=\"_blank\">\\1</a>", $string);
	$string = preg_replace("/\[url=(http|https|ftp)(:\/\/\S+?)\](.*?)\[\/url\]/si", "<a href=\"\\1\\2\" target=\"_blank\">\\3</a>", $string);
	$string = preg_replace("/\[url=(\S+?)\](\S+?)\[\/url\]/si", "<a href=\"http://\\1\" target=\"_blank\">\\2</a>", $string);
	$string = preg_replace("/\[email\](\S+?@\S+?\\.\S+?)\[\/email\]/si", "<a href=\"mailto:\\1\">\\1</a>", $string);
	$string = preg_replace("/\[img=(\S+?)\](\S+?)\[\/img\]/si", "<img src=\"\\2\" alt=\"\\1\" />", $string);
	$string = preg_replace("/\[img\](\S+?)\[\/img\]/si", "<img src=\"\\1\" alt=\"\\1\" />", $string);
	$string = repSmilies($string,$url);
	return $string;
}

function phpHighlight($source){                 
	foreach($source as $sourceStr){
		$sourceStr = html_entity_decode(ereg_replace("<br>","",$sourceStr),ENT_QUOTES);
		$sourceStr = "<?php".$sourceStr."?>";                
		$sourceStr = highlight_string($sourceStr,true);        
		$sourceStr = linkToManual($sourceStr);        
		$sourceStr="<blockquote>php<hr noshade size=1 align=left width=100%><pre>$sourceStr</pre><hr noshade size=1 align=left width=100%></blockquote>";
	}
	return $sourceStr;                
}

function repSmilies($string,$url=""){
	$arrSmiley=array("\:\)"=>"icon_smile.gif",
                        "\:\("=>"icon_sad.gif",
                        "\:D"=>"icon_biggrin.gif",
                        "\:lol\:"=>"icon_lol.gif",
                        "8\)"=>"icon_cool.gif",
                        "\:eek\:"=>"icon_eek.gif",
                        "\:x"=>"icon_mad.gif",
                        "\:wink\:"=>"icon_wink.gif",
                        "\:oops\:"=>"icon_redface.gif",
                        "\:\?"=>"icon_confused.gif",
                        "\:roll\:"=>"icon_rolleyes.gif",
                        "\:evil\:"=>"icon_evil.gif");
	foreach($arrSmiley as $key=>$val){
		$string=eregi_replace($key,"<img src=\"".$url."smiles/".$val."\">",$string);
	}
	return $string;
}

function linkToManual($str){
        $keyword = ini_get("highlight.keyword");
        $manual = 'http://www.php.net/manual-lookup.php?lang=en&pattern=';
        $result_code = preg_replace('{([\w_]+)(\s*</font>)(\s*<font\s+color="'
                        . $keyword . '">\s*\()}m', '<a title="View manual page for \\1" href="'
                        . $manual . '\\1" target="_blank"><tt>\\1</tt></a>\\2\\3', $str);                
        return $result_code;
}

function print_ri(){	
	$numArgs=func_num_args();
	if($numArgs<1){
		echo "print_ri() requires at least one argument as an array!!";
	}
	else{	
		$argList=func_get_args();
		for($i=0;$i<$numArgs;$i++){
			echo "<pre>";
			print_r($argList[$i]);
			echo "</pre>";
		}
	}
}

function getDateTimeVals($myDate,$adHour=0,$adMinute=0,$adSecond=0,$adMonth=0,$adDay=0,$adYear=0){
	$splData=explode(" ",$myDate);
	$dateArr=explode("-",$splData[0]);
	$timeArr=explode(":",$splData[1]);
	return mktime($timeArr[0]+$adHour,$timeArr[1]+$adMinute,$timeArr[2]+$adSecond,$dateArr[1]+$adMonth,$dateArr[2]+$adDay,$dateArr[0]+$adYear);
}

function disProcessSelect($name, $arrVal, $defVal, $style){	
	$str = "<select name=\"{$name}\" class=\"{$style}\">\n";
	foreach($arrVal as $key => $val){
		if($defVal == $key)$str .= "<option value=\"$key\" selected>".htmlentities($val,ENT_QUOTES)."</option>\n";
		else $str .= "<option value=\"$key\">".htmlentities($val,ENT_QUOTES)."</option>\n";
	}
	$str .= "</select>";
	return $str;
}

function pageRecords($totRecs,$curPage,$rPerPage,$qryStr="",$hrefstyle=""){        
        $balRecs=$totRecs%$rPerPage;
        $totalPages=($balRecs)?intval($totRecs/$rPerPage)+1:intval($totRecs/$rPerPage);                
        if($curPage<1 || $curPage>$totalPages){
                $retArr["stRec"]=($curPage-1)*$rPerPage;
                $retArr["noRec"]=1;
                $retArr["recString"]="0 Records";
        }
        elseif($curPage==$totalPages){
                if($balRecs){
                        $retArr["stRec"]=($curPage-1)*$rPerPage;
                        $retArr["noRec"]=$balRecs;
                        $retArr["recString"]=($balRecs==1)?($retArr["stRec"]+1)." of ".$totRecs:($retArr["stRec"]+1)."-".($retArr["stRec"]+$balRecs)." of ".$totRecs;        
                }
                else{
                        $retArr["stRec"]=($curPage-1)*$rPerPage;
                        $retArr["noRec"]=$rPerPage;
                        $retArr["recString"]=($retArr["stRec"]+1)."-".($retArr["stRec"]+$rPerPage)." of ".$totRecs;
                }                
        }
        else{
                $retArr["stRec"]=($curPage-1)*$rPerPage;
                $retArr["noRec"]=$rPerPage;
                $retArr["recString"]=($retArr["stRec"]+1)."-".($retArr["stRec"]+$rPerPage)." of ".$totRecs;                
        }                        
        $retArr["prevLink"]=($curPage>1)?$_SERVER["PHP_SELF"]."?pageNum=".($curPage-1).$qryStr:"";
        $retArr["nextLink"]=($curPage<$totalPages)?$_SERVER["PHP_SELF"]."?pageNum=".($curPage+1).$qryStr:"";
        $lPageNum=array();
		$disFirstRec="";
        if($curPage>2){
                if($curPage>=$totalPages-1 && $totalPages>=5)$pNumStart=$totalPages-4;
                else $pNumStart=$curPage-2;        
                if($curPage>3)$disFirstRec="<a href=\"".$_SERVER["PHP_SELF"]."?pageNum=1".$qryStr."\" class=\"$hrefstyle\">&laquo;</a>&nbsp;&nbsp;&nbsp;";                                        
        }
        else{
                $pNumStart=1;
        }
		$disLastRec="";    
        if($curPage<$totalPages-1){
                if($curPage<=2 && $totalPages>=5)$pNumEnd=5;
                else $pNumEnd=$curPage+2;        
                if($curPage<$totalPages-2)$disLastRec="&nbsp;&nbsp;&nbsp;<a href=\"".$_SERVER["PHP_SELF"]."?pageNum=$totalPages".$qryStr."\" class=\"$hrefstyle\">&raquo;</a>";        
        }
        else{
                $pNumEnd=$totalPages;                                                    
        }                
        for($i=$pNumStart;$i<=$pNumEnd;$i++){
                $lPageNum[]=($curPage!=$i)?"<a href=\"".$_SERVER["PHP_SELF"]."?pageNum=$i".$qryStr."\" class=\"$hrefstyle\">$i</a>":"<b>$i</b>";
        }        
        $retArr["pageNumStr"]=$disFirstRec.implode("&nbsp;&nbsp;&nbsp;",$lPageNum).$disLastRec;
        return $retArr;
}


function genSpanStyle($str, $style){
	return "<span class=\"$style\">$str</span>";
}

function useTemplate(){
	$numargs = func_num_args();
	if($numargs > 0){
		$arg_list = func_get_args();
		$tempStr = file_get_contents("templates/".$arg_list[0].".htm");
		for($i = 1;$i < count($arg_list);$i++){
			$tempStr = str_replace("[__{$i}__]", $arg_list[$i], $tempStr);
		}
		return $tempStr;			
	}
	else{
		die("Template: File not defined.");
	}
}

function useSpTemplate($tempFile, $valueArr, $disMode = true, $cache = true, $tempDir = "templates", $cacheDir = "cache"){
	$tempFilePath = $tempDir."/".$tempFile;			
	if(file_exists($tempFilePath)){
		$fModTime = filemtime($tempFilePath);		
		if($cache){
			$cacheFilePath = $cacheDir."/".md5($fModTime.$tempFile).".php";
			if((!file_exists($cacheFilePath))){			
				if(is_writable($cacheDir)){
					$tempStr = file_get_contents($tempFilePath);
					foreach($valueArr as $key => $val){
						$tempStr = str_replace("[__{$key}__]", "<?php echo \${$key};?>", $tempStr);
					}
					$handle = fopen($cacheFilePath, 'wb');
					fwrite($handle, $tempStr);
					fclose($handle);
				}
				else{
					die("ERROR: Cannot write cache file. Cache directory needs write permission.");
				}
			}
			foreach($valueArr as $key => $val){
				${$key} = $val;
			}
			if($disMode){
				include($cacheFilePath);
			}
			else{
				ob_start();
				include($cacheFilePath);
				$disIncFileCont = ob_get_contents();
				ob_end_clean();
				return $disIncFileCont;
			}
		}
		else{
			$tempStr = file_get_contents($tempFilePath);
			foreach($valueArr as $key => $val){
				$tempStr = str_replace("[__{$key}__]", $val, $tempStr);
			}
			if($disMode)echo $tempStr;
			else return $tempStr;
		}		
	}
	else{
		die("ERROR: Templete File Not Found.. Check File path");
	}
}

function repValToStr($key, $defStr, $value, $imgPathPrefix="", $navValId = ""){
	if($key == "Text" || $key == "Paragraph"){
		return str_replace("[#text#]", $value, $defStr);
	}
	elseif($key == "Image"){
		return str_replace("[#imgpath#]", $imgPathPrefix.$navValId."_".$value, $defStr);
	}
	elseif($key == "URL"){
		$expVals = explode("|", $value);
		$str = str_replace("[#urllink#]", $expVals[0], $defStr);
		return str_replace("[#urllabel#]", $expVals[1], $str);
	}
}

function genHTMLImage($src, $width = "", $height = "", $link = "", $target = "", $border = 0, $alt = ""){
	$str = "<img src=\"{$src}\" border=\"{$border}\"";
	if($width)$str .= " width=\"{$width}\"";
	if($height)$str .= " height=\"{$height}\"";
	if($alt)$str .= " alt=\"{$alt}\"";
	$str .= ">";
	if($link)$str = genHTMLLink($link,$str,$target);
	return $str;
}

function genHTMLLink($link, $label ="", $target = "", $style = ""){
	$str = "<a href=\"{$link}\"";
	if($style)$str .= " class=\"$style\"";
	$str .= ($target)?" target=\"{$target}\">":">";
	$str .= ($label)?$label:$link;
	$str .= "</a>";
	return $str;
}

function genHTMLTable($noRows, $noColumns, $cellValues, $width = "", $height = "", $cellPad = 0, $cellSpace = 0, $border = 0, $align = "", $style = ""){		
	$str = "<table";
	if($width)$str .= " width=\"$width\"";
	if($height)$str .= " height=\"$height\"";
	$str .= " cellpadding=\"$cellPad\"";
	$str .= " cellspacing=\"$cellSpace\"";
	$str .= " border=\"$border\"";
	if($align)$str .= " align=\"$align\"";
	if($style)$str .= " class=\"$style\"";
	$str .= ">\n";
		
	for($i = 0;$i < $noRows;$i++){
		$str .= "<tr>\n";
		for($j = 0;$j < $noColumns;$j++){
			$str .= "<td";
			if(isset($cellValues[$i][$j]["width"]))$str .= " width=\"".$cellValues[$i][$j]["width"]."\"";
			if(isset($cellValues[$i][$j]["height"]))$str .= " height=\"".$cellValues[$i][$j]["height"]."\"";
			if(isset($cellValues[$i][$j]["style"]))$str .= " class=\"".$cellValues[$i][$j]["style"]."\"";
			if(isset($cellValues[$i][$j]["align"]))$str .= " align=\"".$cellValues[$i][$j]["align"]."\"";
			if(isset($cellValues[$i][$j]["valign"]))$str .= " valign=\"".$cellValues[$i][$j]["valign"]."\"";
			$str .= ">";
			$str .= (isset($cellValues[$i][$j]["value"]))?$cellValues[$i][$j]["value"]:"&nbsp;";
			$str .= "</td>\n";
		}
		$str .= "</tr>\n";
	}
		
	$str .= "</table>\n";		
	return $str;		
}

function genHTMLDiv($align = "", $style = "",$cellValues)
{		
	$str = "<div";
	if($align)$str .= " align=\"$align\"";
	if($style)$str .= " class=\"$style\"";
	$str .= ">\n";
		
	for($i = 0;$i < count($cellValues);$i++)
		{
			$str .= "<div>";
			$str .= (isset($cellValues[$i]["value"]))?$cellValues[$i]["value"]:"&nbsp;";
			$str .= "</div>\n";
		}
			
	return $str;		
}

function genDivStyle($str, $style){
	return "<div class=\"$style\">$str</div>";
}

function genFlash($src, $width = "", $height = "", $link = ""){
	$str = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=3,0,0,0\"";
	if($width)$str .= " width=\"{$width}\"";
	if($height)$str .= " height=\"{$height}\"";
	$str .= ">";
	$str .= "<param name=\"movie\" value=\"$src\">";
	$str .= "<param name=\"quality\" value=\"high\">";
	$str .= "<embed src=\"$src\" quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\""; 
	if($width)$str .= " width=\"{$width}\"";
	if($height)$str .= " height=\"{$height}\"";
	$str .= "></embed></object>";
	if($link)$str = genHTMLLink($link,$str,$target);
	return $str;
}
function resizeImage($fileName, $newWidth = 100 , $newHeight = 100, $cropType = 0, $outputFileName = "", $quality = 75){
    $imgCreateFun=array(1 => "imagecreatefromgif", 2 => "imagecreatefromjpeg", 3 => "imagecreatefrompng");
    $imgOutputFun=array(1 => "imagegif", 2 => "imagejpeg", 3 => "imagepng");
    
    if(file_exists($fileName)){
        $myFileInfo = getimagesize($fileName);
        $imgWidth = $myFileInfo[0];
        $imgHeight = $myFileInfo[1];
		
        $resCords = getPropSizes($imgWidth, $imgHeight, $newWidth, $newHeight, $cropType);
        
        $imgType = $myFileInfo[2];
        if(in_array($imgType,array_keys($imgCreateFun)))
		{
            $image_p = imagecreatetruecolor($newWidth, $newHeight);
            $image = $imgCreateFun[$imgType]($fileName);
            imagecopyresampled($image_p, $image, 0, 0, $resCords["srcX"], $resCords["srcY"], $newWidth, $newHeight, $resCords["srcW"], $resCords["srcH"]);                
            if(!file_exists($outputFileName))
			{
                if(!$outputFileName)
				{
                    header("Content-type: ".$myFileInfo["mime"]);
                }
				
				/*chmod($outputFileName, 0644);*/
                $imgOutputFun[$imgType]($image_p, $outputFileName, $quality);
				
                imagedestroy($image_p);
                imagedestroy($image);    
            }
            else
			{
				unlink($outputFileName);
				$imgOutputFun[$imgType]($image_p, $outputFileName, $quality);
                imagedestroy($image_p);
                imagedestroy($image);
                //die("Cannot write output image - Filename already exists");
				//$errMsg ="Cannot write output image - Filename already exists";
            }
            
        }
        else{
            die("Image Type not supported");
        }
    }
    else{
        die("Source file not found");
    }
}

function getPropSizes($orgWidth, $orgHeight, $setWidth, $setHeight, $cropType = 0){    
    $divXFac = $orgWidth / $setWidth;
    $divYFac = $orgHeight / $setHeight;
    $divFac = ($divXFac <= $divYFac)?$divXFac:$divYFac;
    $newWidth = $orgWidth / $divFac;
    $newHeight = $orgHeight / $divFac;
    $orgWidthDef = ($newWidth - $setWidth) * $divFac;
    $orgHeightDef = ($newHeight - $setHeight) * $divFac;
    if($cropType == -1){
        $retArr["srcX"] = 0;
        $retArr["srcY"] = 0;
    }
    elseif($cropType == 0){
        $retArr["srcX"] = $orgWidthDef/2;
        $retArr["srcY"] = $orgHeightDef/2;
    }
    elseif($cropType == 1){
        $retArr["srcX"] = $orgWidthDef;
        $retArr["srcY"] = $orgHeightDef;
        
    }    
    $retArr["srcW"] = $orgWidth - $orgWidthDef;
    $retArr["srcH"] = $orgHeight - $orgHeightDef;
    return $retArr;
} 

function getAjaxJS(){
return <<<EOT
<script language="JavaScript" type="text/javascript">
function createRequestObject() {
    var ro;
    var browser = navigator.appName;
    if(browser == "Microsoft Internet Explorer"){
        ro = new ActiveXObject("Microsoft.XMLHTTP");
    }else{
        ro = new XMLHttpRequest();
    }
    return ro;
}

var http = createRequestObject();

function sndReq(action) {
    http.open('get', 'rpc.php?action='+action);
    http.onreadystatechange = handleResponse;
    http.send(null);
}

function handleResponse() {
    if(http.readyState == 4){
        var response = http.responseText;
        var update = new Array();
        if(response.indexOf('|' != -1)) {
            update = response.split('|');
            document.getElementById(update[0]).innerHTML = update[1];
        }
    }
}
</script>
EOT;
}

function getAjaxJS2(){
return <<<EOT
<script language="JavaScript" type="text/javascript">
function createRequestObject() {
    var ro;
    var browser = navigator.appName;
    if(browser == "Microsoft Internet Explorer"){
        ro = new ActiveXObject("Microsoft.XMLHTTP");
    }else{
        ro = new XMLHttpRequest();
    }
    return ro;
}

var http = createRequestObject();

function sndReq(action) {
    http.open('get', 'rpc.php?action='+action);
    http.onreadystatechange = handleResponse;
    http.send(null);
}

function handleResponse() {
    if(http.readyState == 4){
        var response = http.responseText;
        var update = new Array();
        if(response.indexOf('|*|' != -1)) {
            update = response.split('|*|');
            document.getElementById(update[0]).innerHTML = update[1];
			document.getElementById(update[2]).innerHTML = update[3];
        }
    }
}
</script>
EOT;
}

function getClickHandleJS(){
return <<<EOT
<script language="JavaScript" type="text/javascript">
function listClickHandle(){
	var myVal = document.form1.imglist.value;
	sndReq('img',myVal);
}
</script>

EOT;
}

function getMetaData($keyWords = "", $desc = ""){
$str = <<<EOT
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

EOT;
if($keyWords != ""){
$str .= <<<EOT
<META NAME="KEYWORDS" CONTENT="$keyWords">

EOT;
}
if($desc != ""){
$str .= <<<EOT
<META NAME="DESCRIPTION" CONTENT="$desc">

EOT;
}
return $str;
}

function showHeaderHTML($title = " - - ", $style = "", $jScript = "", $keyWords = "", $desc = ""){
$str = <<<EOT
<html>
<head>
<title>$title</title>

EOT;
$str .= getMetaData($keyWords, $desc)."\n";
if($style != ""){
$str .= <<<EOT
<link href="$style" rel="stylesheet" type="text/css">

EOT;
}
$str .= <<<EOT
$jScript
</head>
<body>
EOT;
echo $str;
}

function showFooterHTML(){
echo <<<EOT

</body>
</html>
EOT;
}
?>
