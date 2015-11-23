<?php
include_once("php_includes/check_login_status.php");
// Initialize any variables that the page might echo
$u = "";
$sex = "Male";
$userlevel = "";
$profile_pic = "";
$profile_pic_btn = "";
//avatar is profile photo
$avatar_form = "";
$country = "";
$joindate = "";
$lastsession = "";
// Make sure the _GET username is set, and sanitize it
if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: http://www.theimgpost.com/index.php");
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
	$profile_pic_btn = '<a href="#" onclick="return false;" onmousedown="toggleElement(\'avatar_form\')">Edit</a>';
    //avatar is profile photo
	$avatar_form  = '<form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
	$avatar_form .=   '<h4>Change your profile photo</h4>';
	$avatar_form .=   '<input type="file" name="avatar" required>';
	$avatar_form .=   '</br><p><input type="submit" value="Upload"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;                               &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="submit" onmousedown="toggleElement(\'avatar_form\').close()" value="Cancel"></p>';
	$avatar_form .= '</form>';
}
// Fetch the user row from the query above
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$profile_id = $row["id"];
	$gender = $row["gender"];
	$userlevel = $row["userlevel"];
    //avatar is profile photo
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
	$max = 3;
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
		$friends_view_all_link = '<a href="friends.php?u='.$u.'">View All</a>';
	}
	$orLogic = '';
	foreach($all_friends as $key => $user){
			$orLogic .= "username='$user' OR ";
	}
	$orLogic = chop($orLogic, "OR ");
	$sql = "SELECT username, avatar FROM users WHERE $orLogic";
	$query = mysqli_query($db_connect, $sql);
	$cnt = 1;
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$friend_username = $row["username"];
		$friend_avatar = $row["avatar"];
		if($friend_avatar != ""){
			$friend_pic = 'user/'.$friend_username.'/'.$friend_avatar.'';
		} else {
			$friend_pic = 'images/avatardefault.png';
		}
		//$friendsHTML .= '<div class="panel panel-primary">';
		//$friendsHTML .= '<div class="panel-body">';
		$friendsHTML .= '<div class="col-sm-4">';
		$friendsHTML .= '<a href="user.php?u='.$friend_username.'"><img class="img-responsive" src="'.$friend_pic.'" alt="'.$friend_username.'" title="'.$friend_username.'"></a>';
		$friendsHTML .= '<div class="user_info" id="user_info_'.$reqID.'"><a href="user.php?u='.$friend_username.'">'.$friend_username.'</a><hr></div> ';
		$friendsHTML .= '</div>';
		//$friendsHTML .= '</div>';
		//$friendsHTML .= '</div>';
	}
}
?><?php 
$coverpic = "";
$noPic =  '<img class="img-responsive img-tumbnail center-block" src="images/AddMedia.png" alt="pic">';
$sql = "SELECT filename FROM photos WHERE user='$u' ORDER BY RAND() LIMIT 1";
$query = mysqli_query($db_connect, $sql);
if(mysqli_num_rows($query) > 0){
	$row = mysqli_fetch_row($query);
	$filename = $row[0];
	$coverpic = '<img class="img-responsive img-thumbnail center-block" src="user/'.$u.'/'.$filename.'" alt="pic">';
}
?>

<?php
    session_start(); 
    $_SESSION['previous_location'] = 'user.php?u='.$u;
?>

<?php
// Make sure the _GET "u" is set, and sanitize it
$u = "";
if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: http://www.webintersect.com");
    exit();	
}
$photo_form = "";
// Check to see if the viewer is the account owner
$isOwner = "no";
if($u == $log_username && $user_ok == true){
	$isOwner = "yes";
	$photo_form  = '<form id="photo_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
	$photo_form .=   '<h4>Add a photo to a gallery.</h4>';
	$photo_form .=   '<b>Choose Gallery:</b> ';
	$photo_form .=   '<select name="gallery" required>';
	$photo_form .=     '<option value=""></option>';
	$photo_form .=     '<option value="Myself">Myself</option>';
	$photo_form .=     '<option value="Family">Family</option>';
	$photo_form .=     '<option value="Pets">Pets</option>';
	$photo_form .=     '<option value="Friends">Friends</option>';
	$photo_form .=     '<option value="Random">Random</option>';
	$photo_form .=   '</select>';
	$photo_form .=   ' &nbsp; &nbsp; &nbsp; <b>Choose Photo:</b> ';
	$photo_form .=   '<input type="file" name="photo" accept="image/*" required>';
	$photo_form .=   '<p><input type="submit" value="Upload Photo Now"></p>';
	$photo_form .= '</form>';
}
// Select the user galleries
$gallery_list = "";
$sql = "SELECT DISTINCT gallery FROM photos WHERE user='$u'";
$query = mysqli_query($db_connect, $sql);
if(mysqli_num_rows($query) < 1){
	$gallery_list = "Empty";
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$gallery = $row["gallery"];
		$countquery = mysqli_query($db_connect, "SELECT COUNT(id) FROM photos WHERE user='$u' AND gallery='$gallery'");
		$countrow = mysqli_fetch_row($countquery);
		$count = $countrow[0];
        
		$filequery = mysqli_query($db_connect, "SELECT filename FROM photos WHERE user='$u' AND gallery='$gallery' ORDER BY RAND() LIMIT 1");
		$filerow = mysqli_fetch_row($filequery);
		$file = $filerow[0];
        
        $descriptionquery = mysqli_query($db_connect, "SELECT description FROM photos WHERE user='$u' AND gallery='$gallery'");
        $descriptionrow = mysqli_fetch_row($descriptionquery);
        $description = $descriptionrow[0];
        
		$gallery_list .= '<div class="col-md-5">';
		$gallery_list .=   '<div>';
		$gallery_list .=     '<img class="img-responsive" src="user/'.$u.'/'.$file.'" alt="cover photo">';
        $gallery_list .=     '<p>'.$description.'</p>';
		$gallery_list .=   '</div>';
		$gallery_list .= '</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $u; ?></title>
	 <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
	<link rel="stylesheet" href="css/site.min.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
	<script type="text/javascript" src="js/site.min.js"></script>

<style>
		.vertical-center {
			  min-height: 100%;  /* Fallback for browsers do NOT support vh unit */
			  min-height: 80vh; /* These two lines are counted as one :-)       */

			  display: flex;
			  align-items: center;
		}
		
		.foot{
			height:40px;
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
		.txt-grey {
			color: grey;
		}
		
		div#profile_pic_box{
			//float:right; 
			//border:#999 2px solid;
			//width:200px; 
			//height:200px; 
			//margin:20px 30px 0px 0px; 
			overflow-y:hidden;
		}
		div#profile_pic_box > img{z-index:2000; width:200px;}
		div#profile_pic_box > a {
			display: none;
			//position:absolute; 
			/*margin:140px 0px 0px 120px; */
			z-index:4000;
			background:#D8F08E;
			//border:#81A332 1px solid;
			//border-radius:3px;
			/*padding:5px;*/
			font-size:12px;
			text-decoration:none;
			color:#60750B;
		}
		div#profile_pic_box > form{
			display:none;
			position:absolute;
			z-index:3000;
			padding:10px;
			opacity:.8;
			background:#F0FEC2;
			//width:180px;
			//height:180px;
		}
		div#profile_pic_box:hover a {
			display: block;
		}
		
		
		div.status_boxes{padding:12px; line-height:1.5em;}
		div.status_boxes > div{padding:8px; border:#99C20C 1px solid; background: #F4FDDF;}
		div.status_boxes > div > b{font-size:12px;}
		div.status_boxes > button{padding:5px; font-size:12px;}
		textarea.replytext{width:98%; height:40px; padding:1%; border:#999 1px solid;}
		div.reply_boxes{padding:12px; border:#999 1px solid; background:#F5F5F5;}
		div.reply_boxes > div > b{font-size:12px;}
		
		// Class
		.center-block {
		  display: block;
		  margin-left: auto;
		  margin-right: auto;
		}
	
	</style>
    
<script type="text/javascript">
$( document ).ready(function() {
		$('#user').addClass('current');
	});
function friendToggle(type,user,elem){
	var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $u; ?>.");
	if(conf != true){
		return false;
	}
	_(elem).innerHTML = 'please wait ...';
	var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
            
			if(ajax.responseText.trim() == "friend_request_sent"){
				_(elem).innerHTML = 'OK Friend Request Sent';
			} 
            else if(ajax.responseText.trim() == "unfriend_ok"){
				_(elem).innerHTML = '<button onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request As Friend</button>';
			}
            else if(ajax.responseText.trim() == "<?php echo $u; ?> currently has the maximum number of friends, and cannot accept more."){
                 _(elem).innerHTML = '<?php echo $u; ?> currently has the maximum number of friends, and cannot accept more.';   
            }
            else if(ajax.responseText.trim() == "<?php echo $u; ?> has you blocked, we cannot proceed."){
                 _(elem).innerHTML = '<?php echo $u; ?> has you blocked, we cannot proceed.';   
            }
            else if(ajax.responseText.trim() == "You must first unblock <?php echo $u; ?> in order to friend with them."){
                 _(elem).innerHTML = 'You must first unblock <?php echo $u; ?> in order to friend with them.';   
            }
            else if(ajax.responseText.trim() == "You are already friends with <?php echo $u; ?>."){
                 _(elem).innerHTML = 'You have a pending friend request already sent to <?php echo $u; ?>.';   
            }
            else if(ajax.responseText.trim() == "You have a pending friend request already sent to <?php echo $u; ?>."){
                 _(elem).innerHTML = 'You have a pending friend request already sent to <?php echo $u; ?>.';   
            }
            else if(ajax.responseText.trim() == "<?php echo $u; ?> has requested to friend with you first. Check your friend requests."){
                 _(elem).innerHTML = '<?php echo $u; ?> has requested to friend with you first. Check your friend requests.';   
            }
            else {
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
				elem.innerHTML = 'Try again later';
			}
		}
	}
	ajax.send("type="+type+"&blockee="+blockee);
}
</script>
</head>

<body style="background-color: #48CFAD">
    
<?php include_once("template_pageTop.php"); ?>

<div class="container" >
	<div class="row" style="padding-top: 15px">
	
		<div class="col-md-4">
			<div class="panel panel-primary">
				
				<div class="panel-body">
					<div  class="col-sm-4">
						<?php echo $profile_pic; ?>
						<?php echo $profile_pic_btn; ?>
						<div id="profile_pic_box">
                            <!--avatar is profile photo-->
							<?php echo $avatar_form; ?>
						</div>
					</div>
					<div class="col-sm-8">
						<!--<p>Is the viewer the page owner, logged in and verified? <b><?php echo $isOwner; ?></b></p>-->
						<h4><?php echo $u; ?></h4>
						<p>Gender: <?php echo $sex; ?></p>
						<p>Join Date: <?php echo $joindate; ?></p>
						<p>Last Session: <?php echo $lastsession; ?></p>
					</div>
					<div class="col-sm-12">
						<?php
							if($isOwner == 'no'){
								echo '<hr>';
								echo '<p>Friend Button: <span id="friendBtn">';
								echo $friend_button;
								echo '</span></p><p>Unfriend Button: <span id="blockBtn">';
								echo $block_button.'</span></p>';
							}
						?>
						
					</div>
				</div>
				
			</div>
			
			<h3 class="txt-white">Photos</h3>
			<div class="panel panel-primary">
				
				<div class="panel-body">
					<div class="col-sm-2 col-xs-1"></div>
					<div class="col-sm-8 col-xs-10 center-block">
						<a class="center-block" href="photos.php?u=<?php echo $u; ?>;" title="view <?php echo $u; ?>&#39;s photo galleries">
							<?php 
								if (empty($coverpic)){
									echo $noPic;
								}else{
									echo $coverpic; 
								}
							?>
						</a>
					</div>
					<div class="col-sm-2 col-xs-1"></div>
					
				</div>
			</div>
			
			<h3 class="txt-white">Friends</h3>
			<div class="panel panel-primary">
				<div class="panel-body">
					<?php 
						echo $friendsHTML; 
                    ?>
                </div>
                <div class="panel-body">
                    <?php
						echo $u." has ".$friend_count." friends  ";
						echo $friends_view_all_link; 
					?>
				</div>
			</div>
			
		</div>
		
		<div class="col-md-8 col-xs-12">
			<?php include_once("template_status.php"); ?>
		</div>
		
        <div class="col-md-4">
        </div>
        
        <div class="col-md-8 col-xs-12">
			<div class="panel panel-primary">
				<div class="panel-body">
					<div id="galleries">
						<?php echo $gallery_list; ?>
					</div>
					<div id="photos" class="col-md-12"></div>
					 <div id="picbox"  class="col-md-12"></div>
				</div>
			</div>
		</div>
		
	</div>
</div>
	
	<div class="container foot" >
		<?php include_once("template_pageBottom.php");?>
	</div>
</body>
</html>