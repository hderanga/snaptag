<table width="250" border="1" cellspacing="8" cellpadding="0" style="margin:auto;">
    <tr>
        <td colspan="2" align="center"><?php
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $err = "";
    if ($email == "" || $password == "") {
        $err.="<strong class='error'>Please enter correct email and password</strong>";
    }
    echo $err;
    if ($err == "") {
        $user_status = User::FindAll("email ='" . $email . "' and password  ='" . md5($password) . "'", "*", array(), "A", 0, 0, array(_ACTIVE));
        if (count($user_status) == 1) {
            foreach ($user_status as $user_obj) {
                echo $_SESSION['loged_user'] = $user_obj->getUserId();
                $date = User::dateTime();
                $user_o = User::Find($user_obj->getUserId());
                $user_o->setLastlog($date);
                $user_o->Update();
                echo "<strong class='success'>Login success</strong>";
                echo '<SCRIPT langueage="JavaScript">
                  window.location="index.php?page=home";
                    </SCRIPT>';
            }
        } else {
            $user_status2 = User::FindAll("email ='" . $email . "' and password  ='" . md5($password) . "'", "*", array(), "A", 0, 0, array(_INACTIVE));
            if (count($user_status2) == 1) {
                echo "<strong class='error'>Confirm your email first</strong>";
            } else {
                echo "<strong class='error'>Login Failed</strong>";
            }
        }
    }
}
?><div class="clear" style="height:7px;"></div></td>
    </tr>
    <form method="post" action="">
        <tr>
            <td>Email :</td>
            <td><input name="email" type="text" class="floatr" /></td>
        </tr>
        <tr>
            <td>Password :</td>
            <td><input type="password" name="password" class="floatr" /></td>
        </tr>
        <tr>
            <td colspan="2"><a href="index.php?page=forgot_passwd" style="font-style:italic;font-weight:100;font-size:11px;margin-top:7px;float:left;">Forgot Password?</a><input name="login" type="submit" id="buttonsub" value="Login" style="float:right;" /></td>
        </tr>
    </form>
</table>