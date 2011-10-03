 <?php 

   $app_id = "179177588818529";
   $app_secret = "0ec5c23806835de2dec61bf8b5879e5a";
   $my_url = "http://www.geekville.co/beta/Sample/index.php?page=test3";

   session_start();
   $code = isset ( $_REQUEST["code"])?$_REQUEST['code']:null;
   
   if(empty($code)) {
     $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
     $dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
       . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
       . $_SESSION['state'];

     echo("<script> top.location.href='" . $dialog_url . "'</script>");
   }

   if(isset ($_REQUEST['state']) && $_REQUEST['state'] == $_SESSION['state']) {
     $token_url = "https://graph.facebook.com/oauth/access_token?"
       . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
       . "&client_secret=" . $app_secret . "&code=" . $code;

     $response = file_get_contents($token_url);
     $params = null;
     parse_str($response, $params);

     $graph_url = "https://graph.facebook.com/me?access_token=" 
       . $params['access_token'];

     $user = json_decode(file_get_contents($graph_url));
     //print_r($user);
     
     $fb_userid = $user->id;
     $fb_user = $user->name;
     $fb_user_firstname = $user->first_name;
     $fb_user_lastname = $user->last_name; 
     
     $user_status = Fbuser::valUser($fb_userid); 
     
     $fb_user_obj = new Fbuser($fb_userid=$fb_userid, $fb_username=null, $fb_firstname=null, $fb_lastname=null, $status=null);
     $fb_user_obj->setFb_userid($fb_userid);    
     $fb_user_obj->setFb_username($fb_user);
     $fb_user_obj->setFb_firstname($fb_user_firstname);
     $fb_user_obj->setFb_lastname($fb_user_lastname);
     $fb_user_obj->setStatus(_ACTIVE);
     
     
     if($user_status==TRUE){
         echo "inserting";
         $fb_return_id = $fb_user_obj->Insert();
         echo $fb_return_id;
         $_SESSION['fb_user']=$fb_return_id;
         
     }
    else{
          $_SESSION['fb_user']=$fb_userid;
     }
                   
    // echo("Hello " . $user->name);
     echo '<SCRIPT langueage="JavaScript">
                   window.location="index.php?page=home";
                   </SCRIPT>';
   }
   else {
     echo("The state does not match. You may be a victim of CSRF.");
   }

 ?>
