<table width="300" border="1" cellspacing="8" cellpadding="0" style="margin:auto;">
  <tr>
    <td colspan="2" align="center"><?php
if(isset($_GET['sec_key']) && $_GET['sec_key']){
    $userid=base64_decode($_GET['sec_key'])-678;
    //$user_objs = User::FindAll("id ='".$userid."'", "*", array(),  "A",  0,  0, array(_ACTIVE));
}    
    if(isset($_SESSION['loged_user']) && $_SESSION['loged_user']){
        $userid = $_SESSION['loged_user'];
    }  
    if(isset($_POST['update'])){
            $err=null;
            $firstname = $_POST['f_name'];
            $lastname = $_POST['l_name'];
            $company = $_POST['c_name'];           
            $password = $_POST['passwd'];
            $con_passwd = $_POST['con_passwd'];
	         
	if($firstname==""){
		$err.="<strong class='error'>First name can't be blank</strong>";
	}
        if($lastname==""){
		$err.="<strong class='error'>Last name can't be blank</strong>";
	}
         if($company==""){
		$err.="<strong class='error'>Comapany can't be blank</strong>";
	}      
        if(!$password==""){
            if($con_passwd=="" or $password!=$con_passwd){
                $err.="<strong class='error'>Please confirm your password</strong>";
            }          
	}
        $updating_user = User::FindAll("userid ='".$userid."'", "*", array(),  "A",  0,  0, array(_ACTIVE));
        $updating_user_obj = $updating_user[0];                
        $updating_user_obj->setFirstname($firstname);
        $updating_user_obj->setLastname($lastname);
        $updating_user_obj->setComapny($company);
       
        if(!$password==""){
            $pass = $updating_user_obj->setPassword($password); 
            if(isset($pass)&& $pass == FALSE){
                $err.="<strong class='error'>Password want to be more than six characters</strong>";
            }
        }
        
        echo $err;
            if($err==""){                               
                    $updated_user=$updating_user_obj->Update();
                if($updated_user){
                    $msg = "<strong class='success'>Update successfully</strong>";
                    echo '<SCRIPT langueage="JavaScript">
                    window.location="index.php?page=home";
                    </SCRIPT>';
                }
             echo $msg;   
            }
        }
    $user_objs = User::FindAll("userid ='".$userid."'", "*", array(),  "A",  0,  0, array(_ACTIVE));
    if(count($user_objs)==1){
        foreach ($user_objs as $user_obj) {
            $user_id = $user_obj->getUserId();
            $f_name=$user_obj->getFirstname();
            $l_name = $user_obj->getLastname();
            $company = $user_obj->getCompany(); 
                        
        }
    }
    
?></td>
  </tr>
<form name="register_frm" method="post" action="">
  <tr>
    <td>First Name <strong style="color:#F00;">*</strong></td>
    <td><input name="f_name" type="text" value="<?=$f_name ?>" class="floatr"></td>
  </tr>
  <tr>
    <td>Last Name <strong style="color:#F00;">*</strong></td>
    <td><input name="l_name" type="text" value="<?=$l_name ?>" class="floatr"></td>
  </tr>
  <tr>
    <td>Company Name <strong style="color:#F00;">*</strong></td>
    <td><input name="c_name" type="text" value="<?=$company ?>" class="floatr"></td>
  </tr>
  <tr>
    <td>Password </td>
    <td><input name="passwd" type="password"></td>
  </tr>
  <tr>
    <td>Confirm Password <strong style="color:#F00;">*</strong></td>
    <td><input name="con_passwd" type="password" class="floatr"></td>
  </tr>
   <tr>
    <td>&nbsp;</td>
    <td><input name="update" type="submit" value="Update" class="floatr"></td>
  </tr>
</form>
</table>