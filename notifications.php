<?php
include_once("php_includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: http://www.yoursite.com");
    exit();
}
$u = "";
$sex = "male";
$userlevel = "";
$profile_pic = "";
$profile_pic_btn = "";
$avatar_form = "";
$country = "";
$joindate = "";
$lastsession = "";

if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: http://www.skchn.ca/index.html");
    exit();	
}

// Select the member from the users table
$sql = "SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_connect, $sql);
// Now make sure that user exists in the table
$numrows = mysqli_num_rows($user_query);
if($numrows < 1){
	echo "That user does not exist or is not yet activated, press back";
    exit();	
}
// Check to see if the viewer is the account owner
$isOwner = "no";
if($u == $log_username && $user_ok == true){
	$isOwner = "yes";
}

// Fetch the user row from the query above
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$profile_id = $row["id"];
	$gender = $row["gender"];
	$userlevel = $row["userlevel"];
	$avatar = $row["avatar"];
	$signup = $row["signup"];
	$lastlogin = $row["lastlogin"];
	$joindate = strftime("%b %d, %Y", strtotime($signup));
	$lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
	if($gender == "f"){
		$sex = "Female";
	}
	$profile_pic = '<img class="img-responsive" src="user/'.$u.'/'.$avatar.'" alt="'.$u.'">';
	if($avatar == NULL){
		$profile_pic = '<img class="img-responsive" src="images/avatardefault.png" alt="'.$user1.'">';
	}
}




$notification_list = "";
$sql = "SELECT * FROM notifications WHERE username LIKE BINARY '$log_username' ORDER BY date_time DESC";
$query = mysqli_query($db_connect, $sql);
$numrows = mysqli_num_rows($query);
if($numrows < 1){
	$notification_list = "You do not have any notifications";
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$noteid = $row["id"];
		$initiator = $row["initiator"];
		$app = $row["app"];
		$note = $row["note"];
		$date_time = $row["date_time"];
		$date_time = strftime("%b %d, %Y", strtotime($date_time));
		$notification_list .= "<p><a href='user.php?u=$initiator'>$initiator</a> | $app<br />$note</p>";
	}
}
mysqli_query($db_connect, "UPDATE users SET notescheck=now() WHERE username='$log_username' LIMIT 1");
?><?php
$friend_requests = "";
$sql = "SELECT * FROM friends WHERE user2='$log_username' AND accepted='0' ORDER BY datemade ASC";
$query = mysqli_query($db_connect, $sql);
$numrows = mysqli_num_rows($query);
if($numrows < 1){
	$friend_requests = 'No friend requests';
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$reqID = $row["id"];
		$user1 = $row["user1"];
		$datemade = $row["datemade"];
		$datemade = strftime("%B %d", strtotime($datemade));
		$thumbquery = mysqli_query($db_connect, "SELECT avatar FROM users WHERE username='$user1' LIMIT 1");
		$thumbrow = mysqli_fetch_row($thumbquery);
		$user1avatar = $thumbrow[0];
		$user1pic = '<img src="user/'.$user1.'/'.$user1avatar.'" alt="'.$user1.'" class="user_pic">';
		if($user1avatar == NULL){
			$user1pic = '<img src="images/avatardefault.png" alt="'.$user1.'" class="user_pic">';
		}
		//$friend_requests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
		$friend_requests .= '<div  id="friendreq_'.$reqID.'" class="panel panel-primary">';
		$friend_requests .= '<div class="panel-body">';
		$friend_requests .= '<a href="user.php?u='.$user1.'">'.$user1pic.'</a>';
		$friend_requests .= '<div class="user_info" id="user_info_'.$reqID.'">'.$datemade.' <a href="user.php?u='.$user1.'">'.$user1.'</a> requests friendship<br /><hr />';
		$friend_requests .= '<button onclick="friendReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">accept</button> or ';
		$friend_requests .= '<button onclick="friendReqHandler(\'reject\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">reject</button>';
		$friend_requests .= '</div>';
		$friend_requests .= '</div>';
		$friend_requests .= '</div>';
		//$friend_requests .= '</div>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Notifications</title>
	 <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
	<link rel="stylesheet" href="css/site.min.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<script type="text/javascript" src="js/site.min.js"></script>
	
	<style type="text/css">
	div#notesBox{float:left; width:430px; border:#F0F 1px dashed; margin-right:60px; padding:10px;}
	div#friendReqBox{float:left; width:430px; border:#F0F 1px dashed; padding:10px;}
	div.friendrequests{height:74px; border-bottom:#CCC 1px solid; margin-bottom:8px;}
	img.user_pic{float:left; width:68px; height:68px; margin-right:8px;}
	div.user_info{float:left; font-size:14px;}
	
	.foot {
			padding-bottom: 0px;
			position:fixed;
			bottom:0;
		}
		.foot{
			position:fixed;
			height:60px;
			bottom:0px;
			left:0px;
			right:0px;
			margin-bottom:0px;
		}
		
		body{
			margin-bottom:60px;
		}
		
		.txt-white {
			color: white;
		}
	</style>

	<script type="text/javascript">
	$( document ).ready(function() {
		$('#notif').addClass('current');
	});
	function friendReqHandler(action,reqid,user1,elem){
		var conf = confirm("Press OK to '"+action+"' this friend request.");
		if(conf != true){
			return false;
		}
		_(elem).innerHTML = "processing ...";
		var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
		ajax.onreadystatechange = function() {
			if(ajaxReturn(ajax) == true) {
				if(ajax.responseText == "accept_ok"){
					_(elem).innerHTML = "<b>Request Accepted!</b><br />Your are now friends";
				} else if(ajax.responseText == "reject_ok"){
					_(elem).innerHTML = "<b>Request Rejected</b><br />You chose to reject friendship with this user";
				} else {
					_(elem).innerHTML = ajax.responseText;
				}
			}
		}
		ajax.send("action="+action+"&reqid="+reqid+"&user1="+user1);
	}
	</script>
</head>
<body style="background-color: #48CFAD">
<?php include_once("template_pageTop.php"); ?>

<div class="container" >
	<div class="row ">
		<div class="col-md-2 col-xs-12">
			<h3 class="txt-white"><?php echo $u; ?></h3>
			<div class="panel panel-primary">
				<div class="panel-body">
					<div class="col-sm-12 col-xs-4">
						<?php echo $profile_pic; ?>
					</div>
					<div class="col-sm-12 col-xs-8">
						<!-- <p>Is the viewer the page owner, logged in and verified? <b><?php echo $isOwner; ?></b></p> -->
						<p>Gender: <?php echo $sex; ?></p>
						<p>User Level: <?php echo $userlevel; ?></p>
						<p>Join Date: <?php echo $joindate; ?></p>
						<p>Last Session: <?php echo $lastsession; ?></p>
					</div>
				</div>
				
			</div>  
		</div>
		<div class="col-md-6 col-xs-12">
			<h3 class="txt-white">Notifications</h3>
			<div class="panel panel-primary">
				<div class="panel-body">
					<?php echo $notification_list; ?>
				</div>
			</div>
		</div>
		
		<div class="col-md-4 col-xs-12">
			<h3 class="txt-white">Friend Requests</h3>
			<?php echo $friend_requests; ?>
		</div>
	</div>
	<div class="row">
		
	</div>
</div>
<div class="container foot" style="background-color: #48CFAD">
	<?php include_once("template_pageBottom.php"); ?>
</div>
</body>
</html>