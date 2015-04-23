<?php
include_once("check_login_status.php");
// If user is already logged in, header that weenis away
if($user_ok == true){
	header("location: user.php?u=".$_SESSION["username"]);
    exit();
}
?>

<?php
include_once("php_includes/db_connect.php");
// AJAX CALLS THIS CODE TO EXECUTE
if(isset($_POST["e"])){
	//include_once("php_includes/db_connect.php");
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
?>

<?php
// EMAIL LINK CLICK CALLS THIS CODE TO EXECUTE
if(isset($_GET['u']) && isset($_GET['p'])){
	//include_once("php_includes/db_connect.php");
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
    <meta charset="utf-8">
    <title>Documentation - Bootflat</title>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="smartaddon-verification" content="936e8d43184bc47ef34e25e426c508fe">
    <meta name="keywords" content="Flat UI Design, UI design, UI, user interface, web interface design, user interface design, Flat web design, Bootstrap, Bootflat, Flat UI colors, colors">
    <meta name="description" content="The complete style of the Bootflat Framework.">
    <link rel="shortcut icon" href="favicon_16.ico">
    <link rel="bookmark" href="favicon_16.ico">
    <link rel="stylesheet" href="css/site.min.css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,800,700,400italic,600italic,700italic,800italic,300italic" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]><script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script><![endif]-->
    <script type="text/javascript" src="js/site.min.js"></script>
	
	<style>
		.vertical-center {
			  min-height: 100%;  /* Fallback for browsers do NOT support vh unit */
			  min-height: 80vh; /* These two lines are counted as one :-)       */

			  display: flex;
			  align-items: center;
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
			var ajax = ajaxObj("POST", "forgot_pass.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					var response = ajax.responseText.trim();
					if(response == "success"){
						_("recover").innerHTML = '<p>Check your email inbox in a few minutes</br>You can close this window or tab if you like.</p>';
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

<body style="background-color: #48CFAD">
	<?php include_once("template_pageTop.php");?>
  <div class="container documents">
		<div class="row vertical-center">
			<div class="col-md-3">
			</div>
			<div class="col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading text-center" style='color:white;'>
							<h4>Password Recovery</h4>
						</div>
						<div class="panel-body">
							<div class="form-horizontal"  id="forgotform">
								<div id="recover">   

								  <form name="forgotpassform" id="forgotpassform" onsubmit="return false;">
										<div class="form-group">
											
											<div class=" col-xs-12">
												<input type="email" class="form-control" onfocus="emptyElement('status')" onkeyup="restrict('email')" maxlength="88" required autocomplete="off" name="email" id="email" placeholder="Enter email"/>
											</div>
										</div>
										<div class="form-group">
											<div class="col-xs-12">
												<button id="forgotpassbtn"  class="btn btn-login btn-block" onclick="forgotpass()"/>Recover Password</button>
											</div>
										</div>
										<div class="text-center">
											<span id="status" style="display: none"></span>
										</div>
								  </form>
								</div>
								<div id="blank"></div>
						</div> <!-- /form -->
					  </div>
				  </div>
				</div> <!--/col-md-6-->
			<div class="col-md-3">
			</div>
		</div>
  </div>
  <div class="container" >
	<?php include_once("template_pageBottom.php");?>
</div>
  
   
</body>

</html>