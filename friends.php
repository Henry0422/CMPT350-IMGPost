<?php
include_once("php_includes/check_login_status.php");

if($user_ok != true || $log_username == ""){
	header("location: http://www.skchn.ca/index.php");
    exit();
}
// Initialize any variables that the page might echo
$u = "";
$sex = "male";
$userlevel = "";
$profile_pic = "";
$profile_pic_btn = "";
$avatar_form = "";
$country = "";
$joindate = "";
$lastsession = "";
// Make sure the _GET username is set, and sanitize it
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
?>

<?php
    $isFriend = false;
    $ownerBlockViewer = false;
    $viewerBlockOwner = false;
    if($u != $log_username && $user_ok == true){
        $friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' OR user1='$u' AND user2='$log_username' AND accepted='1' LIMIT 1";
        if(mysqli_num_rows(mysqli_query($db_connect, $friend_check)) > 0){
            $isFriend = true;
        }
        $block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1";
        if(mysqli_num_rows(mysqli_query($db_connect, $block_check1)) > 0){
            $ownerBlockViewer = true;
        }
        $block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
        if(mysqli_num_rows(mysqli_query($db_connect, $block_check2)) > 0){
            $viewerBlockOwner = true;
        }
    }
?>

<?php 
    $friend_button = '<button disabled>Request As Friend</button>';
    $block_button = '<button disabled>Block User</button>';
    // LOGIC FOR FRIEND BUTTON
    if($isFriend == true){
        $friend_button = '<button onclick="friendToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')">Unfriend</button>';
    } else if($user_ok == true && $u != $log_username && $ownerBlockViewer == false){
        $friend_button = '<button onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Request As Friend</button>';
    }
    // LOGIC FOR BLOCK BUTTON
    if($viewerBlockOwner == true){
        $block_button = '<button onclick="blockToggle(\'unblock\',\''.$u.'\',\'blockBtn\')">Unblock User</button>';
    } else if($user_ok == true && $u != $log_username){
        $block_button = '<button onclick="blockToggle(\'block\',\''.$u.'\',\'blockBtn\')">Block User</button>';
    }
?>

<?php

//HANDLES LISTING OF FRIENDS
$friendsHTML = '';
$friends_view_all_link = '';
$sql = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u' AND accepted='1'";
$query = mysqli_query($db_connect, $sql);
$query_count = mysqli_fetch_row($query);
$friend_count = $query_count[0];
if($friend_count < 1){
	$friendsHTML = $u." has no friends yet";
} else {
	$max = 18;
	$all_friends = array();
	$sql = "SELECT user1 FROM friends WHERE user2='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
	$query = mysqli_query($db_connect, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user1"]);
	}
	$sql = "SELECT user2 FROM friends WHERE user1='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
	$query = mysqli_query($db_connect, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user2"]);
	}
	$friendArrayCount = count($all_friends);
	if($friendArrayCount > $max){
		array_splice($all_friends, $max);
	}
	if($friend_count > $max){
		$friends_view_all_link = '<a href="view_friends.php?u='.$u.'">view all</a>';
	}
	$orLogic = '';
	foreach($all_friends as $key => $user){
			$orLogic .= "username='$user' OR ";
	}
	$orLogic = chop($orLogic, "OR ");
	$sql = "SELECT username, avatar FROM users WHERE $orLogic";
	$query = mysqli_query($db_connect, $sql);
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$friend_username = $row["username"];
		$friend_avatar = $row["avatar"];
		if($friend_avatar != ""){
			$friend_pic = 'user/'.$friend_username.'/'.$friend_avatar.'';
		} else {
			$friend_pic = 'images/avatardefault.png';
		}
		
		$friendsHTML .= '<div class="col-sm-4 ">';
		$friendsHTML .= '<a href="user.php?u='.$friend_username.'"><img class="img-responsive" src="'.$friend_pic.'" alt="'.$friend_username.'" title="'.$friend_username.'"></a>';
		$friendsHTML .= '<div  id="user_info_'.$reqID.'"><a href="user.php?u='.$friend_username.'">'.$friend_username.'</a></div><hr /> ';
		$friendsHTML .= '</div>';
	}
}
?>

<?php
//HANDLES NOTIFICATIONS
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
?>

<?php
//HANDLES FRIEND REQUESTS
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
			$user1pic = '<img src="images/avatardefault.png" alt="'.$user1.'" class="img-responsive">';
		}
		$friend_requests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
		$friend_requests .= '<div class="panel panel-primary">';
		$friend_requests .= '<div class="panel-body">';
		$friend_requests .= '<div class="col-sm-4 col-xs-4"><a href="user.php?u='.$user1.'">'.$user1pic.'</a></div>';
		$friend_requests .= '<div class="col-sm-8 col-xs-8"><div class="user_info" id="user_info_'.$reqID.'">'.$datemade.' <a href="user.php?u='.$user1.'">'.$user1.'</a> requests friendship<br /><hr />';
		$friend_requests .= '<button onclick="friendReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">accept</button> or ';
		$friend_requests .= '<button onclick="friendReqHandler(\'reject\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">reject</button>';
		$friend_requests .= '</div>';
		$friend_requests .= '</div>';
		$friend_requests .= '</div>';
		$friend_requests .= '</div>';
		$friend_requests .= '</div>';
	}
}
?>

<?php
    session_start(); 
    $_SESSION['previous_location'] = 'user.php?u='.$u;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $u; ?>'s Friends</title>
	 <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
	<link rel="stylesheet" href="css/site.min.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<script type="text/javascript" src="js/site.min.js"></script>

<style>
		.vertical-center {
			  min-height: 80%;  /* Fallback for browsers do NOT support vh unit */
			  min-height: 80vh; /* These two lines are counted as one :-)       */

			  display: flex;
			  align-items: center;
		}
		
		
		.foot{
			position:fixed;
			height:60px;
			bottom:0px;
			left:0px;
			right:0px;
			margin-bottom:0px;
			background-color: #48CFAD
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
		$('#friends').addClass('current');
	});

function friendToggle(type,user,elem){
	var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $u; ?>.");
	if(conf != true){
		return false;
	}
	_(elem).innerHTML = 'please wait ...';
	var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
	ajax.onreadystatechange = function() {
       alert(ajaxReturn(ajax));
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText.trim() == "friend_request_sent"){
				_(elem).innerHTML = 'OK Friend Request Sent';
			} 
            else if(ajax.responseText.trim() == "unfriend_ok"){
				_(elem).innerHTML = '<button onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request As Friend</button>';
			} 
            else {
				//alert(ajax.responseText.trim());
				_(elem).innerHTML = 'Try again later';
			}
		}
	}
	ajax.send("type="+type+"&user="+user);
}
function blockToggle(type,blockee,elem){
	var conf = confirm("Press OK to confirm the '"+type+"' action on user <?php echo $u; ?>.");
	if(conf != true){
		return false;
	}
	var elem = document.getElementById(elem);
	elem.innerHTML = 'please wait ...';
	var ajax = ajaxObj("POST", "php_parsers/block_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText.trim() == "blocked_ok"){
				elem.innerHTML = '<button onclick="blockToggle(\'unblock\',\'<?php echo $u; ?>\',\'blockBtn\')">Unblock User</button>';
			} else if(ajax.responseText.trim() == "unblocked_ok"){
				elem.innerHTML = '<button onclick="blockToggle(\'block\',\'<?php echo $u; ?>\',\'blockBtn\')">Block User</button>';
			} else {
				alert(ajax.responseText);
				elem.innerHTML = 'Try again later';
			}
		}
	}
	ajax.send("type="+type+"&blockee="+blockee);
}

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
		<div class="col-md-4 col-xs-12">
			<h3 class="txt-white"><?php echo $u; ?></h3>
			<div class="panel panel-primary">
				
				<div class="panel-body">
					<div class="col-sm-12 col-xs-4">
						<?php echo $profile_pic; ?>
					</div>
					<div class="col-sm-12 col-xs-4">
						<!--<p>Is the viewer the page owner, logged in and verified? <b><?php echo $isOwner; ?></b></p> -->
						<p>Gender: <?php echo $sex; ?></p>
						<p>User Level: <?php echo $userlevel; ?></p>
						<p>Join Date: <?php echo $joindate; ?></p>
						<p>Last Session: <?php echo $lastsession; ?></p>
					</div>
				</div>
				
				<?php
					if($isOwner == 'no'){
						echo '<hr />';
						echo '<p>Friend Button: <span id="friendBtn">';
						echo $friend_button;
						echo '</span></p><p>Friend Button: <span id="blockBtn">';
						echo $block_button.'</span></p>';
					}
				?>
			</div>
		</div>
		<div class="col-md-8 col-xs-12">
			<h3 class="txt-white">Friends</h3>
			<div class="panel panel-primary">
				
				<div class="panel-body">
					<?php echo $friendsHTML; ?>
				</div>
			</div>
		</div>
		
		<!--<div class="col-md-4 ">
			<h3 class="txt-white">Friend Requests</h3>
			<?php echo $friend_requests; ?>
		</div> -->
		
	</div>
	<div class="row">
		
	</div>
</div>
<div class="container foot" >
	<?php include_once("template_pageBottom.php");?>
</div>
</body>
</html>