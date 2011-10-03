<?php
if(isset($_SESSION['loged_user']) && $_SESSION['loged_user']){   
    $userid = $_SESSION['loged_user'];
    $user_objs = User::FindAll("userid ='".$userid."'", "*", array(),  "A",  0,  0, array(_ACTIVE));
    foreach($user_objs as $user_obj){
        $firstname=$user_obj ->getFirstname();
        $lastname=$user_obj ->getLastname();
        $company=$user_obj ->getCompany();
    }
} 
if(isset($_SESSION['fb_user'])&& $_SESSION['fb_user']){
        $userid = $_SESSION['fb_user'];
        $fb_user_objs = Fbuser::FindAll("fb_userid ='".$userid."'", "*", array(),  "A",  0,  0, array(_ACTIVE));        
        foreach ($fb_user_objs as $fb_user_obj) {
            $fb_firstname=$fb_user_obj->getFb_firstname();
            $fb_lastname =$fb_user_obj->getFb_lastname();
        }
    }
    
?>
<table width="350" border="1" cellspacing="8" cellpadding="0" style="margin:auto;">
  <tr>
    <td align="center" colspan="2"><?=$firstname ?><?=$fb_firstname ?>'s Profile <?php if(!$fb_userid){?>
      <a href="index.php?page=profile_edit" class="floatr">Edit profile</a></td>          
   <?php }  ?>        
  </tr>
  <tr>
  	<td><strong>First name :</strong></td>
    <td><?=$firstname ?><?=$fb_firstname ?></td>
  </tr>
  <tr>
  	<td><strong>Last name :</strong></td>
    <td><?=$lastname ?><?=$fb_lastname ?></td>
  </tr>
  <tr>
  	<td><strong>Company name :</strong></td>
    <td><?=$company ?></td>
  </tr>
</table>
<table width="350" border="1" cellspacing="8" cellpadding="0" style="margin:auto;">
            <tr>
                <td align="center" colspan="2">App name</td>
                <td align="center" colspan="2">Current status</td>
            </tr>   
     
        


<?php 
if($userid){
    //echo "ok";
    $app_objs = Campain_app::FindAll("userid ='".$userid."'and status='1'");
    foreach ($app_objs as $app_obj) {
        $appnames=$app_obj->getApp_name();
        $status = $app_obj->getApp_status();
        if($status==1){
            $status_str = "Active";
        }
        elseif ($status==0) {
            $status_str = "Deactive";
        }
        ?> 

        
            <tr>
                <td align="center" colspan="2"><?=$appnames ?></td>
                <td align="center" colspan="2"><?=$status_str ?></td>
            </tr>   
     
        </table>
    <?php }
}
?>