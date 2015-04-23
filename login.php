<?php
    include_once("php_includes/check_login_status.php");
    // If user is already logged in, header that weenis away
    if($user_ok == true){
		//alert("user ok fired");
        header("location: user.php?u=".$_SESSION["username"]);
        exit();
    }
?>


<?php
    // AJAX CALLS THIS LOGIN CODE TO EXECUTE
    if(isset($_POST["e"])){
        // CONNECT TO THE DATABASE
        include_once("php_includes/db_connect.php");
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
    <title>Sign In</title>
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
		$('#login').addClass('current');
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
					
                    if(ajax.responseText != "signup_success"){
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

        
    function login(){ 
        var e = _("email").value; 
        var p = _("password").value; 
        if(e == "" || p == ""){ 
          _("status").innerHTML = "Fill out all of the form data"; 
        } 
        else { 
            _("loginbtn").style.display = "none"; 
            _("status").innerHTML = 'please wait ...'; 
            var ajax = ajaxObj("POST", "login.php"); 
            ajax.onreadystatechange = function() { 
                if(ajaxReturn(ajax) == true) {
					var r = ajax.responseText.trim();
                    if(r == "login_failed"){ 
                        _("status").innerHTML = "Login unsuccessful, please try again."; 
                        _("loginbtn").style.display = "block";
						e= "";
						p="";
                    }else{ 
                        window.location = "user.php?u="+ajax.responseText; 
                    } 
                }
            }
            ajax.send("e="+e+"&p="+p); 
        }
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
							<h4>Sign in</h4>
						</div>
						<div class="panel-body">
							<form class="form-horizontal" id="loginform" onsubmit="return false;">
								<div class="form-group">
                                    <div class=" col-xs-12">
										<input type="email" class="form-control text-center" onfocus="emptyElement('status')" maxlength="88" required autocomplete="off" name="email" id="email" placeholder="Enter email"/>
                                    </div>									
								</div>
								<div class="form-group">
                                    <div class=" col-xs-12">
										<input type="password" class="form-control text-center" onfocus="emptyElement('status')" maxlength="100" required autocomplete="off" name="password" id="password" placeholder="Enter password"/>
                                    </div>
								</div>
								<div class="text-center">
									<p class="forgot "><a href="forgot_pass.php?fp='y'">Forgot Password?</a></p>
								</div>
								<div class="form-group">
                                    <div class=" col-xs-12">
									   <button id="loginbtn" class="btn btn-login btn-block" onclick="login()"/>Log In</button>
                                    </div>
								</div>
								<div class="text-center">
									<p id="status"></p>
								</div>
							
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