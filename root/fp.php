<?php
include_once("check_login_status.php");
// If user is already logged in, header that weenis away
if($user_ok == true){
	header("location: user.php?u=".$_SESSION["username"]);
    exit();
}
?><?php
// AJAX CALLS THIS CODE TO EXECUTE
if(isset($_POST["e"])){
	include_once("php_includes/db_connect.php");
	$e = mysqli_real_escape_string($db_connect, $_POST['e']);
	$sql = "SELECT id, username FROM users WHERE email='$e' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_connect, $sql);
	$numrows = mysqli_num_rows($query);
	if($numrows > 0){
		while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
			$id = $row["id"];
			$u = $row["username"];
		}
		$emailcut = substr($e, 0, 4);
		$randNum = rand(10000,99999);
		$tempPass = "$emailcut$randNum";
		$hashTempPass = md5($tempPass);
		$sql = "UPDATE useroptions SET temp_pass='$hashTempPass' WHERE username='$u' LIMIT 1";
	    $query = mysqli_query($db_connect, $sql);
		$to = "$e";
		$from = "auto_responder@skchn.ca";
		$headers ="From: $from\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$subject ="imgpost Temporary Password";
		$msg = '<h2>Hello '.$u.'</h2><p>This is an automated message from yoursite. If you did not recently initiate the Forgot Password process, please disregard this email.</p><p>You indicated that you forgot your login password. We can generate a temporary password for you to log in with, then once logged in you can change your password to anything you like.</p><p>After you click the link below your password to login will be:<br /><b>'.$tempPass.'</b></p><p><a href="http://www.skchn.ca/forgot_pass.php?u='.$u.'&p='.$hashTempPass.'">Click here now to apply the temporary password shown below to your account</a></p><p>If you do not click the link in this email, no changes will be made to your account. In order to set your login password to the temporary password you must click the link above.</p>';
		if(mail($to,$subject,$msg,$headers)) {
			echo "success";
			exit();
		} else {
			echo "email_send_failed";
			exit();
		}
    } else {
        echo "no_exist";
    }
    exit();
}
?><?php
// EMAIL LINK CLICK CALLS THIS CODE TO EXECUTE
if(isset($_GET['u']) && isset($_GET['p'])){
	include_once("php_includes/db_connect.php");
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
	$temppasshash = preg_replace('#[^a-z0-9]#i', '', $_GET['p']);
	if(strlen($temppasshash) < 10){
		exit();
	}
	$sql = "SELECT id FROM useroptions WHERE username='$u' AND temp_pass='$temppasshash' LIMIT 1";
	$query = mysqli_query($db_connect, $sql);
	$numrows = mysqli_num_rows($query);
	if($numrows == 0){
		header("location: message.php?msg=There is no match for that username with that temporary password in the system. We cannot proceed.");
    	exit();
	} else {
		$row = mysqli_fetch_row($query);
		$id = $row[0];
		$sql = "UPDATE users SET password='$temppasshash' WHERE id='$id' AND username='$u' LIMIT 1";
	    $query = mysqli_query($db_connect, $sql);
		$sql = "UPDATE useroptions SET temp_pass='' WHERE username='$u' LIMIT 1";
	    $query = mysqli_query($db_connect, $sql);
	    header("location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
 <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/signup_normalize.css">
    <link rel="stylesheet" href="css/signup_style.css" media="screen" type="text/css" />
	
	<style>
	#status {
            border:#CCC 1px solid;
            background: #F5F5F5;
            padding: 12px;
        }
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script>
function forgotpass(){
	var e = _("email").value;
	if(e == ""){
		_("status").innerHTML = "Type in your email address";
	} else {
		_("forgotpassbtn").style.display = "none";
		_("status").innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "fp.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
				var response = ajax.responseText.trim();
				if(response == "success"){
					_("forgotpassform").innerHTML = '<h3>Step 2. Check your email inbox in a few minutes</h3><p>You can close this window or tab if you like.</p>';
				} else if (response == "no_exist"){
					_("status").innerHTML = "Sorry that email address is not in our system";
					_("status").style.display = "block"
					_("forgotpassbtn").style.display = "block";
				} else if(response == "email_send_failed"){
					_("status").innerHTML = "Mail function failed to execute";
				} else {
					_("status").innerHTML = "An unknown error occurred";
				}
	        }
        }
        ajax.send("e="+e);
	}
}

 function emptyElement(x){
        _(x).innerHTML = "";
		_(x).style.display = "none";
    }  

function restrict(elem){
        var tf = _(elem);
        var rx = new RegExp;
        if(elem == "email"){
            rx = /[' "]/gi;//restrict for email; no ', " or space
        } else if(elem == "username"){
            rx = /[^a-z0-9]/gi;
        }
        tf.value = tf.value.replace(rx, "");
    }	
</script>
</head>
<body>
	<div class="form">

		  <ul class="tab-group">
			<li class="tab active"><a href="#recover">Recover Password</a></li>
		  </ul>

		  <div class="tab-content">
			<div id="recover">   
			  <h1>Generate Temporary Password</h1>

			  <form name="forgotpassform" id="forgotpassform" onsubmit="return false;">
				  <div class="field-wrap">
					<label>
					  Enter your Email Address<span class="req">*</span>
					</label>
					<input id="email" type="email" required autocomplete="off" maxlength="88" onfocus="emptyElement('status')" onkeyup="restrict('email')"/>
				  </div>
				  
				  <button id="forgotpassbtn"  class="button button-block" onclick="forgotpass()"/>Generate Temp Pwd</button>
				  <span id="status" style="display: none"></span>
			  </form>
			</div>

		  </div><!-- tab-content -->

	</div> <!-- /form -->


	<script src='http://code.jquery.com/jquery-2.1.3.min.js'></script>
	<script src="js/signup.js"></script>

</body>
</html>