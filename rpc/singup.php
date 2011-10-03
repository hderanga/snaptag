<?php
session_start();
ini_set('display_errors','0');
include 'settings.php';
require 'class/DBAccess.class.php';
require 'class/User.class.php';
require 'class/Fbuser.class.php';

?>
<?php

$email = $_POST['email'];
//echo $email;
if($email){
    $user = new User();
    $r_email = $user->checkEmail($email);
    if(count($r_email)>=1){
        echo "1";
    }
}

?>
