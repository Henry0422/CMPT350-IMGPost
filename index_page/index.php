<?php
include_once("php_includes/check_login_status.php");
?>

<!DOCTYPE html>
<html>
<head>
<title>IMGPost - Photo Sharing Notworks</title>
<link href="index_css/bootstrap.css" rel='stylesheet' type='text/css'/>
<link href="index_css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="index_css/style.css" rel="stylesheet" type="text/css" media="all"/>
 <script src="index_js/jquery.easing.min.js"></script>
<link href='http://fonts.googleapis.com/css?family=Raleway:400,100,300,500,700,800,900,600,200' rel='stylesheet' type='text/css'>
 <meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="keywords" content="Agro Agency Responsive web template, Bootstrap Web Templates, Flat Web Templates, Andriod Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<script src="index_js/jquery.min.js"> </script>
<!---- start-smoth-scrolling---->
		<script type="text/javascript" src="index_js/move-top.js"></script>
		<script type="text/javascript" src="index_js/easing.js"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(".scroll").click(function(event){		
					event.preventDefault();
					$('html,body').animate({scrollTop:$(this.hash).offset().top},900);
				});
			});
		</script>
 	<!---- start-smoth-scrolling---->

<!----//requred-js-files---->
		<script type="text/javascript" 	src="index_js/jquery.smint.js"></script>
		<script type="text/javascript">
			$(document).ready( function() {
			    $('.subMenu').smint({
			    	'scrollSpeed' : 1000
			    });
			});
		</script>

</head>
<body>
<!--body-->
<?php include_once("index_top.php"); ?>

<div id="home" class="banner">
	 <div class="container">
		 <div class="banner-info">
			 <div class="logo">
				 <a href="index.php"><img src="index_images/logo.png" alt=""/></a>
			 </div>
             <h1><b>Share your photo here</b></h1>
             <p><b>Easy Sign up today and get connect with others</b></p>
			 <a class="hvr-shutter-out-vertical button" href="#">Sign Up</a>
		</div>
	 </div>
</div>
<!---->

<!---->
<div id="about" class="top-grid">
	 <div class="container">
		 <div class="top-grid-section">
			 <div class="col-md-3 top-grids">
				 <span class="glyphicon glyphicon-camera"></span>
				 <h3>Shutter Release</h3>
				 <p>Using your mobile devices to record the wonderful moment in your life. </p>
			 </div>
			 <div class="col-md-3 top-grids">
                 <span class="glyphicon glyphicon-upload"></span>
				 <h3>Upload to IMGPost</h3>
				 <p>Select their favorite photos and simply upload photos to IMGPost using all kinds of mobile devices.</p>
			 </div>
			 <div class="col-md-3 top-grids">
				 <span class="glyphicon glyphicon-share"></span>
				 <h3>Share & Manage</h3>
				 <p>Join the our photography community, discoverbeautiful photos and share your own. Manage your photos in your personal webpage</p>
			 </div>
			 <div class="col-md-3 top-grids">
				 <span class="glyphicon glyphicon-thumbs-up"></span>
				 <h3>Like & Commment</h3>
				 <p>Explore IMGPost to easily find everything you're interested in. Click like button and perpaer to relax.</p>
			 </div>
			 <div class="clearfix"></div>
		 </div>
	 </div>
</div>
<!---->
		<!---testmonials---->
		<div  class="testmonials section s3">
			<div class="container">
			<div class="bs-example">
			    <div id="myCarousel" class="carousel slide" data-interval="3000" data-ride="carousel">
			    	<!-- Carousel indicators -->
			        <ol class="carousel-indicators pagenate-icons">
			            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
			            <li data-target="#myCarousel" data-slide-to="1"></li>
			            <li data-target="#myCarousel" data-slide-to="2"></li>
			        </ol>   
			       <!-- Carousel items -->
			        <div class="carousel-inner">
			            <div class="active item">
			               <img src="index_images/feature-likeshare.jpg" title="name" />
			                <div class="carousel-caption caption">
			                  <h3>Get social with your photos </h3>
			                  <p>Your photo is your namecard to let other people know your life, your work and your mood. </p>
			                </div>
			            </div>
			            <div class="item">
			               <img src="index_images/feature-people.jpg" title="name" />
			                <div class="carousel-caption caption">
			                  <h3>Share your photos with friends</h3>
			                  <p>Your posting will showing on other people's personal mainpage It is a good way to tell your friend. </p>
			                </div>
			            </div>
			            <div class="item">
			                <img src="index_images/feature-photos.jpg" title="name" />
			                <div class="carousel-caption caption">
			                  <h3>High Res Picture</h3>
			                  <p>All your photo is uploaded and stored in its high resolution format, ready for share with others. </p>
			                </div>
			            </div>
			        </div>
			        <!-- Carousel nav -->
			    </div>
		</div>
		</div>
		</div>
		<!---testmonials---->
<!----start-portfolio---->
		<div id="gallery" class="portfolio section s2">
			<div class="container portfolio-head">
                <p><br><br></p>
				<h3>Gallery</h3>
			</div>
			<!---- start-portfolio-script----->
			<script src="index_js/hover_pack.js"></script>
			<script type="text/javascript" src="index_js/jquery.mixitup.min.js"></script>
			<script type="text/javascript">
				$(function () {
					var filterList = {
						init: function () {
						
							// MixItUp plugin
							// http://mixitup.io
							$('#portfoliolist').mixitup({
								targetSelector: '.portfolio',
								filterSelector: '.filter',
								effects: ['fade'],
								easing: 'snap',
								// call the hover effect
								onMixEnd: filterList.hoverEffect()
							});				
						
						},
						hoverEffect: function () {
							// Simple parallax effect
							$('#portfoliolist .portfolio').hover(
								function () {
									$(this).find('.label').stop().animate({bottom: 0}, 200, 'easeOutQuad');
									$(this).find('img').stop().animate({top: -30}, 500, 'easeOutQuad');				
								},
								function () {
									$(this).find('.label').stop().animate({bottom: -40}, 200, 'easeInQuad');
									$(this).find('img').stop().animate({top: 0}, 300, 'easeOutQuad');								
								}		
							);				
						
						}			
					};
					// Run the show!
					filterList.init();
				});	
			</script>
			<!----//End-portfolio-script----->
					<ul id="filters" class="clearfix">
						<li><span class="filter active" data-filter="app card icon">All</span></li>
						<li><span class="filter" data-filter="app">Quebec City</span></li>
						<li><span class="filter" data-filter="card">Ottawa</span></li>
						<li><span class="filter" data-filter="icon">Banff Town</span></li>
					</ul>
					<div id="portfoliolist">
					<div class="portfolio card mix_all" data-cat="logo" style="display: inline-block; opacity: 1;">
						<div class="portfolio-wrapper">		
							<a data-toggle="modal" data-target=".bs-example-modal-md" href="#" class="b-link-stripe b-animate-go  thickbox">
						     <img src="index_images/p1.jpg" /><div class="b-wrapper"><h2 class="b-animate b-from-left    b-delay03 "><img src="index_images/link-ico.png" alt=""/></h2>
						  	</div></a>
		                </div>
					</div>				
					<div class="portfolio app mix_all" data-cat="app" style="display: inline-block; opacity: 1;">
						<div class="portfolio-wrapper">		
							<a data-toggle="modal" data-target=".bs-example-modal-md" href="#" class="b-link-stripe b-animate-go  thickbox">
						     <img src="index_images/p2.jpg" /><div class="b-wrapper"><h2 class="b-animate b-from-left    b-delay03 "><img src="index_images/link-ico.png" alt=""/></h2>
						  	</div></a>
		                </div>
					</div>		
					<div class="portfolio card mix_all" data-cat="web" style="display: inline-block; opacity: 1;">
						<div class="portfolio-wrapper">		
							<a data-toggle="modal" data-target=".bs-example-modal-md" href="#" class="b-link-stripe b-animate-go  thickbox">
						     <img src="index_images/p3.jpg" /><div class="b-wrapper"><h2 class="b-animate b-from-left    b-delay03 "><img src="index_images/link-ico.png" alt=""/></h2>
						  	</div></a>
		                </div>
					</div>				
					<div class="portfolio card mix_all" data-cat="icon" style="display: inline-block; opacity: 1;">
						<div class="portfolio-wrapper">		
							<a data-toggle="modal" data-target=".bs-example-modal-md" href="#" class="b-link-stripe b-animate-go  thickbox">
						     <img src="index_images/p4.jpg" /><div class="b-wrapper"><h2 class="b-animate b-from-left    b-delay03 "><img src="index_images/link-ico.png" alt=""/></h2>
						  	</div></a>
		                </div>
					</div>	
					<div class="portfolio icon mix_all" data-cat="app" style="display: inline-block; opacity: 1;">
						<div class="portfolio-wrapper">		
							<a data-toggle="modal" data-target=".bs-example-modal-md" href="#" class="b-link-stripe b-animate-go  thickbox">
						     <img src="index_images/p8.jpg" /><div class="b-wrapper"><h2 class="b-animate b-from-left    b-delay03 "><img src="index_images/link-ico.png" alt=""/></h2>
						  	</div></a>
		                </div>
					</div>			
					<div class="portfolio app mix_all" data-cat="card" style="display: inline-block; opacity: 1;">
						<div class="portfolio-wrapper">		
							<a data-toggle="modal" data-target=".bs-example-modal-md" href="#" class="b-link-stripe b-animate-go  thickbox">
						     <img src="index_images/p6.jpg" /><div class="b-wrapper"><h2 class="b-animate b-from-left    b-delay03 "><img src="index_images/link-ico.png" alt=""/></h2>
						  	</div></a>
		                </div>
					</div>	
					<div class="portfolio icon mix_all" data-cat="card" style="display: inline-block; opacity: 1;">
						<div class="portfolio-wrapper">		
							<a data-toggle="modal" data-target=".bs-example-modal-md" href="#" class="b-link-stripe b-animate-go  thickbox">
						     <img src="index_images/p7.jpg" /><div class="b-wrapper"><h2 class="b-animate b-from-left    b-delay03 "><img src="index_images/link-ico.png" alt=""/></h2>
						  	</div></a>
		                </div>
					</div>	
					<div class="portfolio card mix_all" data-cat="icon" style="display: inline-block; opacity: 1;">
						<div class="portfolio-wrapper">		
							<a data-toggle="modal" data-target=".bs-example-modal-md" href="#" class="b-link-stripe b-animate-go  thickbox">
						     <img src="index_images/p5.jpg" /><div class="b-wrapper"><h2 class="b-animate b-from-left    b-delay03 "><img src="index_images/link-ico.png" alt=""/></h2>
						  	</div></a>
		                </div>
					</div>		
					<div class="clearfix"> </div>																																					
				</div>
		</div>
		<!----//End-portfolio---->
		<script src="index_js/bootstrap.min.js"></script>
		<!----start-model-box---->
						<a data-toggle="modal" data-target=".bs-example-modal-md" href="#"> </a>
						<div class="modal fade bs-example-modal-md light-box" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
						  <div class="modal-dialog modal-md">
						    <div class="modal-content light-box-info">
						    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><img src="index_images/close.png" title="close" /></button>
						     <h3>Favriote photos in IMGPost</h3>
						     <p>These photos are most popular photos from our users. They shot these photo across Canada</p>
						    </div>
						  </div>
						</div>
						<!----start-model-box---->
		<!----start-contact---->

<!----start-contact---->
<div id="contact" class="contact section s4">
	 <div class="container">
		 <h4>Contact</h4>
		 <p class="contact-head">Feel free to contact us, fill out the form below and we'll get back to you ASAP.</p>
		  <div class="row contact-form">
				<form>
					<div class="col-md-6 text-box">
						<input type="text" placeholder="Name" />
						<input type="text" placeholder="Email" />
						<input type="text" placeholder="Subject" />
				    </div>
					 <div class="col-md-6 textarea">
							<textarea>Message</textarea>
					  </div>
					  <div class="clearfix"> </div><br />
					  <input class="btn-red-lg" type="submit" value="Submit Message" />
			  </form>
		  </div>
		  <div class="social-media">			
			 <a href="#"><i class="facebook"></i></a>
			 <a href="#"><i class="pinterest"></i></a>
			 <a href="#"><i class="twitter"></i></a>
			 <a href="#"><i class="google"></i></a>
</div>
<div class="copy-right">
		<p>Copyright &#169; 2015 <span>IMGPost</span> All Rights Reserved.</p>
</div>
	 </div>
</div>

				