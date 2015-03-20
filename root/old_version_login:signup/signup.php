<?php
session_start();
// If user is logged in, header them away
if(isset($_SESSION["username"])){
	header("location: message.php?msg=You have logged in");
    exit();
}
?>

<?php
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){
	include_once("php_includes/db_connect.php");
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
	    echo '<strong style="color:#009900;">' . $username . ' is OK</strong>';
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
	include_once("php_includes/db_connect.php");
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
		$from = "auto_responder@skchn.ca";
		$subject = 'IMGPost Account Activation';
		$message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>IMGPost Message</title></head><body style="margin:0px; font-family:Tahoma, Geneva, sans-serif;"><div style="padding:10px; background:#333; font-size:24px; color:#CCC;"><a href="http://www.skchn.ca"><img src="http://www.skchn.ca/images/logo.png" width="36" height="30" alt="IMGPost" style="border:none; float:left;"></a>IMGPost Account Activation</div><div style="padding:24px; font-size:17px;">Hello '.$u.',<br /><br />Click the link below to activate your account when ready:<br /><br /><a href="http://www.skchn.ca/activation.php?id='.$uid.'&u='.$u.'&e='.$e.'&p='.$p_hash.'">Click here to activate your account now</a><br /><br />Login after successful activation using your:<br />* E-mail Address: <b>'.$e.'</b></div></body></html>';
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

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sign up / Log in</title>
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/signup_normalize.css">
    <link rel="stylesheet" href="css/signup_style.css" media="screen" type="text/css" />
    
    <style>
        #terms {
            border:#CCC 1px solid;
            background: #F5F5F5;
            padding: 12px;
        }
    </style>

    <script src="js/main.js"></script>
    <script src="js/ajax.js"></script>
    <script>
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
            _("signupbtn").style.display = "none";
            status.innerHTML = 'please wait ...';
            var ajax = ajaxObj("POST", "signup.php");
            ajax.onreadystatechange = function() {
                if(ajaxReturn(ajax) == true) {
//                    alert(ajax.responseText);
                    if(ajax.responseText.trim() != "signup_success"){
                        status.innerHTML = ajax.responseText;
                    } 
                    else {
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

<body>

  <div class="form" name="entireform" id="entireform" onsubmit="return false;">

      <ul class="tab-group">
        <li class="tab active"><a href="#signup">Sign Up</a></li>
        <li class="tab"><a href="#login">Log In</a></li>
      </ul>

      <div class="tab-content">
        <div id="signup">   
          <h1>Sign Up for Free</h1>

          <form action="/" method="post" name="signupform" id="signupform" onsubmit="return false;">

          <div class="field-wrap">
              <label>
                User Name<span class="req">*</span>
              </label>
              <input id="username" type="text" required autocomplete="off" maxlength="16" onblur="checkusername()" onkeyup="restrict('username')"/>
              <span id="unamestatus"></span>
          </div>

          <div class="field-wrap">
            <label>
              Email Address<span class="req">*</span>
            </label>
            <input id="email" type="email" required autocomplete="off" maxlength="88" onfocus="emptyElement('status')" onkeyup="restrict('email')"/>
          </div>

          <div class="field-wrap">
            <label>
              Set A Password<span class="req">*</span>
            </label>
            <input id="pass1" type="password" required autocomplete="off"  maxlength="100" onfocus="emptyElement('status')"/>
          </div>
              
          <div class="field-wrap">
            <label>
              Confirmed Password<span class="req">*</span>
            </label>
            <input id="pass2" type="password" required autocomplete="off" maxlength="100" onfocus="emptyElement('status')"/>
          </div>
              
          <div>
            <a href="#" onclick="return false" onmousedown="openTerms()">View the Terms of Use</a>    
          </div>
              
          <div id="terms" style="display:none;">
              <h3>Spot Posting Term of Use</h3> 
              <p>1. No Porn and Violence</p>
              <p>2. No Personal Abuse</p>
          </div>      

          <button id="signupbtn" type="submit" class="button button-block" onclick="signup()"/>Get Started</button>
          <span id="status"></span>
          </form>

        </div>

        <div id="login">   
          <h1>Welcome Back!</h1>

          <form action="/" method="post">

            <div class="field-wrap">
            <label>
              Email Address<span class="req">*</span>
            </label>
            <input type="email"required autocomplete="off"/>
          </div>

          <div class="field-wrap">
            <label>
              Password<span class="req">*</span>
            </label>
            <input type="password"required autocomplete="off"/>
          </div>

          <p class="forgot"><a href="#">Forgot Password?</a></p>

          <button class="button button-block"/>Log In</button>

          </form>

        </div>

      </div><!-- tab-content -->

</div> <!-- /form -->

  <script src='http://code.jquery.com/jquery-2.1.3.min.js'></script>

  <script src="js/signup.js"></script>

</body>

</html>