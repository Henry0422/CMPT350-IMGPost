<?php
    include_once("php_includes/check_login_status.php");
    // If user is already logged in, header that weenis away
    if($user_ok == true){
		alert("user ok fired");
        header("location: user.php?u=".$_SESSION["username"]);
        exit();
    }
?>

<?php
include_once("php_includes/db_connect.php");
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){
	//include_once("php_includes/db_connect.php");
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_connect, $sql); 
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
	    echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
	    exit();
    }
	if (is_numeric($username[0])) {
	    echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
	    exit();
    }
    if ($uname_check < 1) {
	    echo '<strong style="color:#009900;">Username: ' . $username . ' is available.</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
	    exit();
    }
}
?>

<?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	//include_once("php_includes/db_connect.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
	$e = mysqli_real_escape_string($db_connect, $_POST['e']);
	$p = $_POST['p'];
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_connect, $sql); 
	$u_check = mysqli_num_rows($query);
	// -------------------------------------------
	$sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_connect, $sql); 
	$e_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($u == "" || $e == "" || $p == ""){
		echo "The form submission is missing values.";
        exit();
	} else if ($u_check > 0){ 
        echo "The username you entered is alreay taken";
        exit();
	} else if ($e_check > 0){ 
        echo "That email address is already in use in the system";
        exit();
	} else if (strlen($u) < 6 || strlen($u) > 16) {
        echo "Username must be between 6 and 16 characters";
        exit(); 
    } else if (is_numeric($u[0])) {
        echo 'Username cannot begin with a number';
        exit();
    } else {
	// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database
		// Hash the password and apply your own mysterious unique salt
        $p_hash = md5($p);
		// Add user info into the database table for the main site table
		$sql = "INSERT INTO users (username, email, password, ip, signup, lastlogin, notescheck)       
		        VALUES('$u','$e','$p_hash','$ip',now(),now(),now())";
		$query = mysqli_query($db_connect, $sql); 
		$uid = mysqli_insert_id($db_connect);
		// Establish their row in the useroptions table
		$sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
		$query = mysqli_query($db_connect, $sql);
		// Create directory(folder) to hold each user's files(pics, MP3s, etc.)
		if (!file_exists("user/$u")) {
			mkdir("user/$u", 0755);
		}
		// Email the user their activation link
		$to = "$e";							 
		$from = "auto_responder@theimgpost.com";
		$subject = 'IMGPost Account Activation';
		$message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>IMGPost Message</title></head><body style="margin:0px; font-family:Tahoma, Geneva, sans-serif;"><div style="padding:10px; background:#333; font-size:24px; color:#CCC;"><a href="http://www.theimgpost.com"><img src="http://www.theimgpost.com/images/logo.png" width="36" height="30" alt="IMGPost" style="border:none; float:left;"></a>IMGPost Account Activation</div><div style="padding:24px; font-size:17px;">Hello '.$u.',<br /><br />Click the link below to activate your account when ready:<br /><br /><a href="http://www.theimgpost.com/activation.php?id='.$uid.'&u='.$u.'&e='.$e.'&p='.$p_hash.'">Click here to activate your account now</a><br /><br />Login after successful activation using your:<br />* E-mail Address: <b>'.$e.'</b></div></body></html>';
		$headers = "From: $from\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";
		mail($to, $subject, $message, $headers);
		echo "signup_success";
		exit();
	}
	exit();
}
?>




<?php
    // AJAX CALLS THIS LOGIN CODE TO EXECUTE
    if(isset($_POST["e"])){
        // CONNECT TO THE DATABASE
        //include_once("php_includes/db_connect.php");
        // GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
        $e = mysqli_real_escape_string($db_connect, $_POST['e']);
        $p = md5($_POST['p']);
        // GET USER IP ADDRESS
        $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
        // FORM DATA ERROR HANDLING
        if($e == "" || $p == ""){
            echo "login_failed";
            exit();
        } else {
        // END FORM DATA ERROR HANDLING
            $sql = "SELECT id, username, password FROM users WHERE email='$e' AND activated='1' LIMIT 1";
            $query = mysqli_query($db_connect, $sql);
            $row = mysqli_fetch_row($query);
            $db_id = $row[0];
            $db_username = $row[1];
            $db_pass_str = $row[2];
            if($p != $db_pass_str){
                echo "login_failed";
                exit();
            } 
            else {
                // CREATE THEIR SESSIONS AND COOKIES
                $_SESSION['userid'] = $db_id;
                $_SESSION['username'] = $db_username;
                $_SESSION['password'] = $db_pass_str;
                setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
                setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
                setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
                // UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
                $sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
                $query = mysqli_query($db_connect, $sql);
                echo $db_username;
                exit();
            }
        }
        exit();
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sign Up</title>
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
	// A $( document ).ready() block.
	$( document ).ready(function() {
		$('#signup').addClass('current');
	});
	
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

    function emptyElement(x){
        _(x).innerHTML = "";
    }    

    function checkusername(){
        var u = _("username").value;
        if(u != ""){
            _("unamestatus").innerHTML = 'checking ...';//here can put a html image tag
            var ajax = ajaxObj("POST", "signup.php");
            ajax.onreadystatechange = function() {
                if(ajaxReturn(ajax) == true) {
                    _("unamestatus").innerHTML = ajax.responseText;
                }
            }
            ajax.send("usernamecheck="+u);
        }
    }
    function signup(){
        var u = _("username").value;
        var e = _("email").value;
        var p1 = _("pass1").value;
        var p2 = _("pass2").value;
        var status = _("status");
        if(u == "" || e == "" || p1 == "" || p2 == ""){
            status.innerHTML = "Fill out all of the form data";
        } else if(p1 != p2){
            status.innerHTML = "Your password fields do not match";
        } else if( _("terms").style.display == "none"){
            status.innerHTML = "Please view the terms of use";
        } else {
//            _("signupbtn").style.display = "none";
            status.innerHTML = 'please wait ...';
            var ajax = ajaxObj("POST", "signup.php");
            ajax.onreadystatechange = function() {
                if(ajaxReturn(ajax) == true) {
					
                    if(ajax.responseText.trim() != "signup_success"){
                        status.innerHTML = ajax.responseText;
                    } else {
                        window.scrollTo(0,0);
                        _("signupform").innerHTML = "OK "+u+", check your email inbox and junk mail box at <u>"+e+"</u> in a moment to complete the sign up process by activating your account. You will not be able to do anything on the site until you successfully activate your account.";
                    }
                }
            }
            ajax.send("u="+u+"&e="+e+"&p="+p1);
        }
    }
	
    function openTerms(){
        _("terms").style.display = "block";
        emptyElement("status");
    }

    </script>
</head>

<body style="background-color: #48CFAD">
	<?php include_once("template_pageTop.php");?>
  <div class="container documents">
		<div class="row vertical-center">
			<div class="col-md-3">
			</div>
			<div class="col-md-6 col-xs-12">
					<div class="panel panel-primary">
						<div class="panel-heading text-center" style='color:white;'>
							<h4>Sign Up</h4>
						</div>
						<div class="panel-body">
							<form class="form-horizontal" id="signupform" onsubmit="return false;">
								<div class="form-group">
                                    <div class=" col-xs-12">
                                        <input class="form-control text-center" id="username" placeholder="Username" type="text" required autocomplete="off" maxlength="16" onblur="checkusername()" onkeyup="restrict('username')"/>
                                        <p class="text-center" id="unamestatus"></p>
                                    </div>  
								</div>

								<div class="form-group">
                                    <div class=" col-xs-12">
									   <input class="form-control text-center" id="email" placeholder="Email" type="email" required autocomplete="off" maxlength="88" onfocus="emptyElement('status')" onkeyup="restrict('email')"/>
                                    </div>
								</div>

								<div class="form-group">
                                    <div class=" col-xs-12">
									   <input class="form-control text-center" id="pass1" type="password" required autocomplete="off"  maxlength="100" placeholder="Enter password"onfocus="emptyElement('status')"/>
                                    </div>
								</div>
									  
								<div class="form-group">
                                    <div class=" col-xs-12">
									   <input class="form-control text-center" id="pass2" type="password" required autocomplete="off" maxlength="100" placeholder="Confirm Password" onfocus="emptyElement('status')"/>
                                    </div>
								</div>
									  
								<div class="text-center">
									<a href="#" onclick="return false" onmousedown="openTerms()">View the Terms of Use</a>    
								</div>
									  
								<div id="terms" class="text-center" style="display:none;">
									<h3>Spot Posting Term of Use</h3> 
									<p>1. No Porn and Violence</p>
									<p>2. No Personal Abuse</p>
								</div>      

								  <button id="signupbtn" type="submit" class="btn btn-login btn-block" onclick="signup()"/>Get Started</button>
								  <span id="status"></span>
						  </form>
					  </div>
				  </div>
			</div>
			<div class="col-md-3">
			</div>
		</div>
  </div>
  <div class="container" >
	<?php include_once("template_pageBottom.php");?>
</div>
  
   
</body>

</html>