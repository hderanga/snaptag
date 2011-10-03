<?php
echo "ok";
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script src="../js/jquery.js" type="text/javascript"></script>
<script src="../js/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready(function() {
    
	// validate the comment form when it is submitted
	
	// validate signup form on keyup and submit
	$("#register_frm").validate({
		rules: {
			f_name: "required",
			l_name: "required"
                        //,
//			c_name: {
//				required: true,
//				minlength: 2
//			},
//                        email: {
//				required: true,
//				email: true
//			},
//			pass: {
//				required: true,
//				minlength: 6
//			},
//			con_pass: {
//				required: true,
//				minlength: 6,
//				equalTo: "#password"
//			}
                },			
		messages: {
			f_name: "Please enter your firstname",
			l_name: "Please enter your lastname"
                        //,
//			c_name: {
//				required: "Please enter a company name",
//				minlength: "Your username must consist of at least 2 characters"
//			},
//			password: {
//				required: "Please provide a password",
//				minlength: "Your password must be at least 5 characters long"
//			},
//			confirm_password: {
//				required: "Please provide a password",
//				minlength: "Your password must be at least 5 characters long",
//				equalTo: "Please enter the same password as above"
//			},
//			email: "Please enter a valid email address"
			
		}
	});
        
        
        
});
</script>
<table>
<form name="register_frm" method="get" onsubmit="return false" id="register_frm" action="">
    
  <tr>
    <td>First Name <strong style="color:#F00;">*</strong></td>
    <td><input name="f_name" id="f_name" type="text" value="" class="floatr"></td>
    <td><span id="err_fname"></span></td>
  </tr>
  <tr>
    <td>Last Name <strong style="color:#F00;">*</strong></td>
    <td><input name="l_name" id="l_name" type="text" value="" class="floatr"></td>
    <td><span id="err_lname"></span></td>
  </tr>
  <tr>
    <td>Company Name <strong style="color:#F00;">*</strong></td>
    <td><input name="c_name" id="c_name" type="text" value="" class="floatr"></td>
    <td><span id="err_cname"></span></td>
  </tr>
  <tr>
    <td>Email <strong style="color:#F00;">*</strong></td>
    <td><input name="email" id="email" type="text" value="" class="floatr"></td>
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
    <td><input name="register" type="submit" onclick="" value="Register" class="floatr"></td>
  </tr>
 
</form>