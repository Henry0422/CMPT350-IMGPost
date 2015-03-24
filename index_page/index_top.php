<?php
    $loginLink = '<li><a id="login" href="login.php">Log In</a></li><li> <a id="signup" href="signup.php">Sign Up</a></li>';
    if(isset($_GET["fp"])){
	   $loginLink .= '<li> <a class="current" id="forgot" href="forgot_pass.php">Forgot Password</a></li>';
    }
?>

<div class="navigation">
	 <div class="container">
		 <div class="fixed-header">
			 <div class="top-menu">
				 <ul>
					 <li ><a class="scroll" href="#home"><b>IMGPost</b></a></li>
					 <li><a class="scroll" href="#about">ABOUT</a></li>
					 <li><a class="scroll" href="#gallery">GALLERY</a></li>
					 <li><a class="scroll" href="#contact">CONTACT</a></li>
				 </ul>			
			 </div>
			 <div class="right-msg">
                 <ul>
                    <?php echo $loginLink; ?>
                 </ul>
			 </div>
			 <div class="clearfix"></div>
		 </div>
		 <script>
			$("span.menu").click(function(){
				$(".top-menu ul").slideToggle(500, function(){
				});
			});
			</script>

				<!-- script for menu -->
					<script type="text/javascript">
					jQuery(document).ready(function($) {
						$(".scroll").click(function(event){		
							event.preventDefault();
							$('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
						});
					});
					</script>

				<!-- script for menu -->
				<script>
			 $(document).ready(function() {
				 var navoffeset=$(".navigation").offset().top;
				 $(window).scroll(function(){
					var scrollpos=$(window).scrollTop(); 
					if(scrollpos >=navoffeset){
						$(".navigation").addClass("fixed");
					}else{
						$(".navigation").removeClass("fixed");
					}
				 });
				 
			 });
			 </script>
	 </div>
</div>