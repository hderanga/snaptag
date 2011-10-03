<?php

//if(!isset($_SESSION['loged_user'])){
//    echo "not log user";  
//}
if(isset($_POST['forgot'])){
    $err= null;
    $email = $_POST['email'];
    if($email == ""){      
        $err.="Please type your email address";
    }
    else if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$",$email)){
        $err.="Please enter valid Email address <br />";
    }
    echo $err;
    if($err==""){
        $user_obj = User::FindAll("email ='".$email."'");
        if(count($user_obj)==1){
            $status = User::resetPasswd($email);
            if($status){
                echo "Resset password complete successfully please check your email";
            }
            else{
                echo "There is an system erro";
            }
        }
        else{
            echo "Email not in our records";
        }
        
    }
}

?>

<form method="post" action="">
Email :<input name="email" type="text" /></p>
<input name="forgot" type="submit" id="buttonsub" value="Submit"/>
</form>