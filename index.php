<?php
session_start();
ini_set('display_errors','on');
include 'settings.php';
require 'class/DBAccess.class.php';
require 'class/User.class.php';
require 'class/Fbuser.class.php';
require 'class/category.class.php';

?>

<?php 
	if(isset($_GET['logout'])== true){
		unset($_SESSION['loged_user']);
                unset($_SESSION['fb_user']);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Test</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<h3>
</h3>
</head>

<body>
<div id="Wrap">
<table width="600" border="1" cellspacing="8" cellpadding="0" style="margin:auto;">
  <tr>
    <td align="left">  <?php 
if(isset($_SESSION['loged_user']) && $_SESSION['loged_user']){
$username=User::findF_name($_SESSION['loged_user']); 
       echo "Wellcome ".$username; 
}
?></td>
    <td align="right">
      
        <?php if(isset($_SESSION['loged_user']) || isset ($_SESSION['fb_user'])){ 
      ?>
      <a href="index.php?page=login&logout=true">Logout</a> |     
      <a href="index.php?page=profile_view"><?=$username ?></a> | 
  <?php }else{?>
  <a href="index.php?page=home">Home</a> |   
  <a href="index.php?page=login">Login</a> | 
  <a href="index.php?page=singup">Register</a> | 
  <a href="index.php?page=test3">Login with facebook</a>
  &nbsp; &nbsp; 
  <?php } ?></td>
  </tr>
</table>

  <?php
	//Dynamic pages
	if(isset($_GET["page"])){
		$incPage = "include/".$_GET["page"].".php";
	}
	else{
		$incPage = "include/home.php";
	}
	if(isset($incPage)){
		include($incPage);
	}
       
?>
</div>	
</body>
</html>
