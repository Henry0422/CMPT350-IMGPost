<?php
include_once("php_includes/check_login_status.php");
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
	$gallery_list = "This user has not uploaded any photos yet.";
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$gallery = $row["gallery"];
		$countquery = mysqli_query($db_connect, "SELECT COUNT(id) FROM photos WHERE user='$u' AND gallery='$gallery'");
		$countrow = mysqli_fetch_row($countquery);
		$count = $countrow[0];
		$filequery = mysqli_query($db_connect, "SELECT filename FROM photos WHERE user='$u' AND gallery='$gallery' ORDER BY RAND() LIMIT 1");
		$filerow = mysqli_fetch_row($filequery);
		$file = $filerow[0];
		$gallery_list .= '<div class="col-md-5"  onclick="showGallery(\''.$gallery.'\',\''.$u.'\')">';
		$gallery_list .=   '<div>';
		$gallery_list .=     '<img class="img-responsive" src="user/'.$u.'/'.$file.'" alt="cover photo">';
		$gallery_list .=   '</div>';
		$gallery_list .=   '<b>'.$gallery.'</b> ('.$count.')';
		$gallery_list .= '</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $u; ?> Photos</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="css/site.min.css">
<style type="text/css">

	
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
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript" src="js/site.min.js"></script>
<script>
function showGallery(gallery,user){
	_("galleries").style.display = "none";
	_("section_title").innerHTML = user+'&#39;s '+gallery+' Gallery &nbsp; <button onclick="backToGalleries()">Go back to all galleries</button>';
	_("photos").style.display = "block";
	_("photos").innerHTML = 'loading photos ...';
	var ajax = ajaxObj("POST", "php_parsers/photo_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			_("photos").innerHTML = '';
			var pics = ajax.responseText.split("|||");
			for (var i = 0; i < pics.length; i++){
				var pic = pics[i].split("|");
				pics[i] = pics[i].replace(/\s+/g, '');
				_("photos").innerHTML += '<div class="col-md-4"><img class="img-responsive" onclick="photoShowcase(\''+pics[i]+'\')" src="user/'+user+'/'+pic[1]+'" alt="pic"><div>';
			}
			_("photos").innerHTML += '<p style="clear:left;"></p>';
		}
	}
	ajax.send("show=galpics&gallery="+gallery+"&user="+user);
}
function backToGalleries(){
	_("photos").style.display = "none";
	_("section_title").innerHTML = "<?php echo $u; ?>&#39;s Photo Galleries";
	_("galleries").style.display = "block";
}
function photoShowcase(picdata){
	var data = picdata.split("|");
	_("section_title").style.display = "none";
	_("photos").style.display = "none";
	_("picbox").style.display = "block";
	_("picbox").innerHTML = '<button onclick="closePhoto()" class="pull-right">x</button>';
	_("picbox").innerHTML += '<img class="img-responsive" src="user/<?php echo $u; ?>/'+data[1]+'" alt="photo">';
	if("<?php echo $isOwner ?>" == "yes"){
		_("picbox").innerHTML += '<p id="deletelink"><a href="#" onclick="return false;" onmousedown="deletePhoto(\''+data[0]+'\')">Delete this Photo <?php echo $u; ?></a></p>';
	}
}
function closePhoto(){
	_("picbox").innerHTML = '';
	_("picbox").style.display = "none";
	_("photos").style.display = "block";
	_("section_title").style.display = "block";
}
function deletePhoto(id){
	var conf = confirm("Press OK to confirm the delete action on this photo.");
	if(conf != true){
		return false;
	}
	_("deletelink").style.visibility = "hidden";
	var ajax = ajaxObj("POST", "php_parsers/photo_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "deleted_ok"){
				alert("This picture has been deleted successfully. We will now refresh the page for you.");
				window.location = "photos.php?u=<?php echo $u; ?>";
			}
		}
	}
	ajax.send("delete=photo&id="+id);
}
</script>
</head>
<body style="background-color: #48CFAD">
<?php include_once("template_pageTop.php"); ?>
<div class="container" >
	<div class="row">
	
		<div class="col-md-4">
			<h3 class="txt-white"><?php echo $u; ?></h3>
			<div class="panel panel-primary">
				
				<div class="panel-body">
					<?php echo $photo_form; ?>
				</div>
				
			</div>
			
			
			
		</div>
		
		<div class="col-md-8 col-xs-12">
			<h3 id="section_title" class="txt-white"><?php echo $u; ?>&#39;s Galeries</h3>
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
<!--
<div id="pageMiddle">
  <div id="photo_form"><?php //echo $photo_form; ?></div>
  <h2 id="section_title"><?php// echo $u; ?>&#39;s Photo Galleries</h2>
  <div id="galleries"><?php //echo $gallery_list; ?></div>
  <div id="photos"></div>
  <div id="picbox"></div>
  <p style="clear:left;">These photos belong to <a href="user.php?u=<?php //echo $u; ?>"><?php //echo $u; ?></a></p>
</div> -->

<div class="container foot" >
	<?php include_once("template_pageBottom.php");?>
</div>
</body>
</html>