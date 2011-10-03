<html>
    <head>         
         <script type="text/javascript" src="js/jquery.js"></script>
         <script type="text/javascript" src="js/singup.js"></script> 
    </head>
<body>
    
<table width="300" border="1" cellspacing="8" cellpadding="0" style="margin:auto;">
  <tr>
    <td align="center" colspan="2">
<?php
    $err="";
    $msg="";
    $firstname = null;
    $lastname = null;
    $company = null;
    $email = null;
    $dateTime = date("F j, Y, g:i a");
    
    if(isset($_GET['email']) && $_GET['email']){
        $request_email = $_GET['email'];
        if(isset($_GET['key']) && strlen($_GET['key'])==32){
            $key = $_GET['key'];           
            $email_obj = User::FindAll("email ='".$request_email."' and act_code ='".$key."'", "*", array(),  "A",  0,  0, array(_INACTIVE));             
            if(count($email_obj)==1){
                foreach ($email_obj as $email_objs)                   
//                $email_objs->setStatus(_ACTIVE);                
//                $return_id= $email_objs->Update(); 
                   $user_o = User::Find($email_objs->getUserId());
                   $user_o->setStatus(_ACTIVE);
                   $return_id=$user_o->Update();
                if($return_id){
                    echo "Registation complete";                    
                }
            }
            else{
                echo "There is an error maybe some one took your email";
            }
        }              
    }       
    if(isset($_POST['register'])){
        
        $firstname = $_POST['f_name'];
        $lastname = $_POST['l_name'];
        $company = $_POST['c_name'];
        $email = $_POST['email'];
        $password = $_POST['passwd'];
        $con_passwd = $_POST['con_passwd'];
	         
	if($firstname==""){
		$err.="First name can't be blank";
	}
        if($lastname==""){
		$err.="Last name can't be blank";
	}
         if($company==""){
		$err.="Comapany can't be blank";
	}
        if($email==""){
		$err.="Email can't be blank";
	}
	else if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$",$email)){
		$err.="Please enter valid Email address";
	}
        if($password==""){
		$err.="Password field can't be empty";
	}
        else if($password!=$con_passwd){
            $err.="Password didn't match";
        }
	
        $my_user = new User('','','', $company, $email, $password, $reg_date, $last_log, $status, $act_code);
            $my_user->setFirstname($firstname);
            $my_user->setLastname($lastname);
            $my_user->setComapny($company);
            $act_code=$my_user->gen_actcode();
            $my_user->setAct_code($act_code);
            $my_user->setStatus(_INACTIVE); 
            $my_user->setRegdate($dateTime);
            $my_user->setLastlog(null);
                       
            $valemail=$my_user->checkEmail($email);           
            if($_POST['email']!=""){
                if(isset($valemail) && $valemail=$email){                
                    $my_user->setEmail($email);
                }
                else{
                    $err.="Email already exits";
                }
            }
        $pass = $my_user->setPassword($password);            
        if($password!=""){
             if(isset($pass) && $pass==FALSE){
                $err.="Password needs to be more than 6 characters";
            }
        }
        echo $err;    
	if($err==""){                                      
            $userid=$my_user->Insert();         
            if($userid){              
                $emailstat=$my_user->sendEmail($email,$act_code);
                
                if($emailstat){
                  $msg.="Registration Succesfully Completed";
                }                
            }
            else{
                $msg.="Registration Fail";
            }
            echo $msg;
	}           
    }
?></td>
  </tr>
<form name="register_frm" method="post" action="">
    
  <tr>
    <td>First Name <strong style="color:#F00;">*</strong></td>
    <td><input name="f_name" id="f_name" type="text" value="<?=$firstname ?>" class="floatr"></td>
    <td><span id="err_fname"></span></td>
  </tr>
  <tr>
    <td>Last Name <strong style="color:#F00;">*</strong></td>
    <td><input name="l_name" id="l_name" type="text" value="<?=$lastname ?>" class="floatr"></td>
    <td><span id="err_lname"></span></td>
  </tr>
  <tr>
    <td>Company Name <strong style="color:#F00;">*</strong></td>
    <td><input name="c_name" id="c_name" type="text" value="<?=$company ?>" class="floatr"></td>
    <td><span id="err_cname"></span></td>
  </tr>
  <tr>
    <td>Email <strong style="color:#F00;">*</strong></td>
    <td><input name="email" id="email" type="text" value="<?=$email ?>" class="floatr"></td>
    <td><span id="err_email"></span></td>
  </tr>
  <tr>
  <tr>
    <td>Password <strong style="color:#F00;">*</strong></td>
    <td><input name="passwd" id="pass" type="password" class="floatr"></td>
    <td><span id="err_pass"></span></td>
  </tr>
  <tr>
    <td>Confirm Password <strong style="color:#F00;">*</strong></td>
    <td><input name="con_passwd" id="con_pass" type="password" class="floatr"></td>
    <td><span id="err_conpass"></span></td>
  </tr>
   <tr>
    <td>&nbsp;</td>
    <td><input name="register" type="submit" onclick="return validateform()" value="Register" class="floatr"></td>
  </tr>
 
</form>
</table>
    </body>
    </html>