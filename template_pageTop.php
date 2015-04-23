<?php
// It is important for any file that includes this file, to have
// check_login_status.php included at its very top.
$envelope = '<li></li>';
$loginLink = '<li><a id="login" href="login.php">Log In</a></li><li> <a id="signup" href="signup.php">Sign Up</a></li>';
if(isset($_GET["fp"])){
	$loginLink .= '<li> <a class="current" id="forgot" href="forgot_pass.php">Forgot Password</a></li>';
}

if($user_ok == true) {
	$sql = "SELECT notescheck FROM users WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_connect, $sql);
	$row = mysqli_fetch_row($query);
	$notescheck = $row[0];
	$sql = "SELECT id FROM notifications WHERE username='$log_username' AND date_time > '$notescheck' LIMIT 1";
	$query = mysqli_query($db_connect, $sql);
	$numrows = mysqli_num_rows($query);
    if ($numrows == 0) {
		//$envelope = '<li><a class="nav-link" href="notifications.php" title="Your notifications and friend requests"><img src="images/note_still.jpg" width="22" height="12" alt="Notes"></a></li>';
		$envelope = '<li><a id="notif" class="nav-link" href="notifications.php?u='.$log_username.'" title="Your notifications and friend requests"><img src="images/noti_white.png" width="22" height="12" alt="Notes"></a></li>';
    } else {
		//$envelope = '<li><a class="nav-link" href="notifications.php" title="You have new notifications"><img src="images/note_flash.gif" width="22" height="12" alt="Notes"></a></li>';
		$envelope = '<li><a id="notif" class="nav-link" href="notifications.php?u='.$log_username.'" title="You have new notifications"><img src="images/noti_green.png" width="22" height="12" alt="Notes"></a></li>';
	}
    $loginLink = '<li><a id="user" class="nav-link" href="user.php?u='.$log_username.'">'.$log_username.'</a> </li><li> <a id="friends" class="nav-link" href="friends.php?u='.$log_username.'">Friends</a> </li><li> <a class="nav-link" href="logout.php">Log Out</a></li>';
}
?>
<div class="docs-header">
<nav class="navbar navbar-default navbar-custom" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><span class="sr-only">Toggle navigation</span>  <span class="icon-bar"></span>  <span class="icon-bar"></span>  <span class="icon-bar"></span>
                    </button>
					
                    <a class="navbar-brand" href="index.php">
                        <img  src="images/logo.png" height="40">
                    </a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <?php 
						echo  $envelope;
						echo $loginLink;
							?>
                    </ul>
                </div>
            </div>
</nav>
</div>

