<!--?php @session_start();
get_includes('head');
get_includes('header');
get_includes('nav');
get_includes('banner');
?-->
<!--?php if ( is_front_page() ) { get_includes('middle'); }?-->
<!DOCTYPE html>
<!--[if lt IE 10]>      <html class="no-js lt-ie11 lt-ie10 lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 10]>         <html class="no-js lt-ie11 lt-ie10 lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 11]>         <html class="no-js lt-ie11"> <![endif]-->
<!--[if gt IE 11]><!--> <html class="no-js" lang="en-US"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->

	<title><?php //echo get_bloginfo('name');?>CompanyName</title>


	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="css/hamburgers.min.css">
	<link rel="stylesheet" href="css/media.css">
	<link rel="stylesheet" href="css/rslides.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/animations.min.css">
	<link rel="stylesheet" href="css/skitter.styles.min.css">

	<!--?php wp_head(); ?-->
</head>
	<body>
		<div class="protect-me">
		<div class="clearfix">

<!-- Header -->
  <header>
    <div class="wrapper">
			<div class="header_con">
        <div class="main_logo">
          <a href="<?php //echo get_home_url(); ?>"><figure><img src="images/main-logo.png" alt="<?php //echo get_bloginfo('name');?>"/></figure></a>
        </div>

				<div class="head_info">
					<div class="header_info">
						<ul>
							<li><address>123 Street Name, Lorem Ipsum
								Orlando, Florida 1234</address>
								<a href="">View Map and Directions &raquo;</a></li>
							<li>Phone: <mark>602-358-7106</mark></li>
							<li>Email: <a href="mailto:noncep@agchurchconsulting.com">noncep@agchurchconsulting.com</a></li>
						</ul>
						<!--?php dynamic_sidebar('header_info')?> -->
					</div>
					<div class="social_media">
						<ul>
							<li><a href="https://www.facebook.com" target="_blank"><figure><img src="images/fb-icon.png" alt="facebook"/></figure></a></li>
							<li><a href="https://www.twitter.com" target="_blank"><figure><img src="images/twitter-icon.png" alt="twitter"/></figure></a></li>
						</ul>
					</div>
				</div>
			</div>
      <div class="clearfix"></div>
    </div>
  </header>
<!-- End Header -->

<!-- Navigation -->
	<div id="nav_area">
    <div class="nav_toggle_button">
			<div class="logo_wrap"></div>
			<div class="toggle_holder">
				<div class="hamburger hamburger--spin-r">
				  <div class="hamburger-box">
					<div class="hamburger-inner"></div>
				  </div>
				</div>
				<small>Menu</small>
			</div>
			<div class="clearfix"></div>
	  </div>

		<div class="toggle_right_nav">
		  <nav class="page_nav">
				<div class="menu_slide_right">
					<a href="<?php //echo get_home_url(); ?>" class="logo_slide_right"><figure><img src="images/main-logo.png" alt="<?php //echo get_bloginfo('name');?>"/></figure></a>
					<div class="toggle_holder">
						<div class="hamburger hamburger--spin-r">
						  <div class="hamburger-box">
							<div class="hamburger-inner"></div>
						  </div>
						</div>
						<small>Close</small>
					</div>
					<div class="clearfix"></div>
				</div>

				<div class="wrapper">
					<!--?php wp_nav_menu( array( 'container_class' => 'nav-menu', 'theme_location' => 'primary', 'after' => '<span><i class="fa fa-2x">&nbsp;&nbsp;&nbsp;&nbsp;</i></span>') ); ?-->
					<ul>
						<li class="current_page_item"><a href="">Home</a></li>
						<li><a href="">About Us</a></li>
						<li><a href="">Services</a></li>
						<li><a href="">Careers</a></li>
						<li><a href="">Resources</a></li>
						<li><a href="">Contact Us</a></li>
					</ul>
				</div>
		  </nav>
			<div class="toggle_nav_close"></div>
		</div>
	</div>
<!-- End Navigation -->

<!-- Banner -->
	<div id="banner">
		<div class="wrapper">
			<div class="bnr_con">

				<div class="slider">
					<!-- <div class="box_skitter box_skitter_large">
						<ul>
							<li><figure><img src="images/slider/1.jpg" alt="" class="random"/></figure></li>
							<li><figure><img src="images/slider/2.jpg" alt="" class="random"/></figure></li>
							<li><figure><img src="images/slider/3.jpg" alt="" class="random"/></figure></li>
						</ul>
			    </div> -->
					<ul class="rslides">
						<li><figure><img src="images/slider/1.jpg" alt=""/></figure></li>
						<li><figure><img src="images/slider/2.jpg" alt=""/></figure></li>
						<li><figure><img src="images/slider/3.jpg" alt=""/></figure></li>
					</ul>
				</div>

				<div class="bnr_info">
					<h3>xxx</h3>
					<h2>xxx <span>xxx</span></h2>
					<p>xxxx</p>
					<a href="javascript:;">xxxx</a>
					<!--?php dynamic_sidebar('bnr_info')?> -->
				</div>

			</div>
		</div>
	</div>
<!-- End Banner -->

<!-- Middle -->
<div id="middle">
	<div class="wrapper">
		<div class="middle_con">
			<div class="mid_info">
				<h3>xxxx</h3>
				<h2>xxx <span>xxxx</span></h2>
				<p>xxx</p>
				<a href="#">xxx</a>
				<!--?php dynamic_sidebar('mid_info')?> -->
			</div>
			<div class="middle_boxes">

				<section class="mid_box1">
					<h2>xxx <span>xxx</span></h2>
					<p>xxx</p>
					<a href="javascript:;">xxxx</a>
				</section>

				<section class="mid_box2">
					<h2>xxx <span>xxx</span></h2>
					<p>xxx</p>
					<a href="javascript:;">xxxx</a>
				</section>

				<section class="mid_box3">
					<h2>xxx <span>xxx</span></h2>
					<p>xxx</p>
					<a href="javascript:;">xxxx</a>
				</section>

			</div>
		</div>
	</div>
</div>
<!-- End Middle -->

<!-- Main -->
	<div id="main_area">
  	<div class="wrapper">
			<div class="main_con">
				 <figure class="main_img">
						<img src="images/main-img.jpg" alt="">
					</figure>
				<main>
					<h1 class="h3_title">xxx</h1>
					<h1 class="h1_title">Welcome to <span>xxxx</span></h1>
					<p class="main_p">xxxx</p>
				</main>

			</div>

			<div class="clearfix"></div>
  	</div>
	</div>
<!-- End Main -->

<!-- Bottom -->
  <div id="bottom1">
    <div class="wrapper">
      <div class="btm1_con">
				<figure class="btm1_img">
					<img src="images/btm1-img.jpg" alt="">
				</figure>
				<div class="btm1_info">
					<h3>xxxx</h3>
					<h2>xxx <span>xxxx</span></h2>
					<p>xxx</p>
					<a href="#">xxx</a>
					<!--?php dynamic_sidebar('btm1_info')?> -->
				</div>
				<div class="btm1_boxes">

					<section class="btm1_box1">
						<h2>xxx</h2>
						<p>xxx</p>
						<a href="javascript:;">xxx</a>
						<!--?php dynamic_sidebar('btm1_box1')?> -->
					</section>

					<section class="btm1_box2">
						<h2>xxx</h2>
						<p>xxx</p>
						<a href="javascript:;">xxx</a>
						<!--?php dynamic_sidebar('btm1_box2')?> -->
					</section>

					<section class="btm1_box3">
						<h2>xxx</h2>
						<p>xxx</p>
						<a href="javascript:;">xxx</a>
						<!--?php dynamic_sidebar('btm1_box3')?> -->
					</section>

					<section class="btm1_box4">
						<h2>xxx</h2>
						<p>xxx</p>
						<a href="javascript:;">xxx</a>
						<!--?php dynamic_sidebar('btm1_box4')?> -->
					</section>

				</div>
      </div>
    </div>
  </div>

	<div id="bottom2">
		<div class="wrapper">
			<div class="btm2_con">
				<figure class="btm2_img">
					<img src="images/btm2-img.jpg" alt="">
				</figure>
				<div class="btm2_info">
					<h3>xxxx</h3>
					<h2>xxx <span>xxxx</span></h2>
					<p>xxx</p>
					<a href="#">xxx</a>
					<!--?php dynamic_sidebar('btm2_info')?> -->
				</div>
				<div class="btm2_boxes">
					<section class="btm2_box1">
						<h2>xxx <span>xx</span></h2>
						<p>xxx</p>
						<a href="#">xxx</a>
						<!--?php dynamic_sidebar('btm2_box1')?> -->
					</section>
					<section class="btm2_box2">
						<h2>xxx <span>xxx</span></h2>
						<p>xxx</p>
						<a href="#">xxx</a>
						<!--?php dynamic_sidebar('btm3_box2')?> -->
					</section>
					<section class="btm2_box3">
						<h2>xxx <span>xxx</span></h2>
						<p>xxx</p>
						<a href="#">xxx</a>
						<!--?php dynamic_sidebar('btm3_box3')?> -->
					</section>
				</div>
			</div>
		</div>
	</div>
	<div id="bottom3">
		<div class="wrapper">
			<div class="btm3_con">
				<figure class="btm3_img">
					<img src="images/btm3-img.jpg" alt="">
				</figure>
				<div class="btm3_info">
					<h3>xxxx</h3>
					<h2>xxx <span>xxxx</span></h2>
					<p>xxx</p>
					<a href="#">xxx</a>
					<!--?php dynamic_sidebar('btm3_info')?> -->
				</div>
				<div class="btm3_boxes">
					<section class="btm3_box1">
						<h2>xxx <span>xx</span></h2>
						<p>xxx</p>
						<a href="#">xxx</a>
						<!--?php dynamic_sidebar('btm3_box1')?> -->
					</section>
					<section class="btm3_box2">
						<h2>xxx <span>xxx</span></h2>
						<p>xxx</p>
						<a href="#">xxx</a>
						<!--?php dynamic_sidebar('btm3_box2')?> -->
					</section>
					<section class="btm3_box3">
						<h2>xxx <span>xxx</span></h2>
						<p>xxx</p>
						<a href="#">xxx</a>
						<!--?php dynamic_sidebar('btm3_box3')?> -->
					</section>
				</div>
			</div>
		</div>
	</div>
	<div id="bottom4">
		<div class="wrapper">
			<div class="btm4_con">
				<figure class="btm4_img">
					<img src="images/btm4-img.jpg" alt="">
				</figure>
				<div class="btm4_info">
					<h3>xxx</h3>
					<h2>xxx <span>xxx</span></h2>
					<p>xxx</p>
					<!--?php dynamic_sidebar('btm4_info')?> -->
				</div>
				<form action="#" method="post">
					<input type="text" name="Full_Name" value="" placeholder="Full Name*" required>
					<input type="email" name="Email_Address" value="" placeholder="Email Address*" required>
					<textarea name="Question_or_Comment" placeholder="Message(s)"></textarea>
					<input type="submit" name="submit_btn" value="Submit">
				</form>
			</div>
		</div>
	</div>
<!-- End Bottom -->

<!--Footer -->
	<footer>
		<div class="footer_top">
			<div class="wrapper">
				<div class="footer_top_con">
					<div class="contact_info">
		 			 <h3>xxx</h3>
		 			 <h2>xxx <span>xxx</span></h2>
					 <p>xxx</p>
						<ul>
							<li><address>123 Street Name, Lorem Ipsum
								Orlando, Florida 1234</address>
								<a href="">View Map and Directions &raquo;</a></li>
							<li>Phone: <mark>602-358-7106</mark></li>
							<li>Email: <a href="mailto:noncep@agchurchconsulting.com">noncep@agchurchconsulting.com</a></li>
						</ul>
						<!--?php dynamic_sidebar('contact_info')?> -->
					</div>

					<!-- <div class="footer_logo">
						<a href="<?php //echo get_home_url(); ?>"><figure><img src="images/footer_logo.png" alt="<?php //echo get_bloginfo('name');?>"></figure></a>
					</div> -->
				</div>
			</div>
		</div>

    <div class="footer_btm">
      <div class="wrapper">
				<div class="footer_btm_con">
					<div class="footer_nav">
						<ul>
							<li class="current_page_item"><a href="index.php">Home</a></li>
							<li><a href="#">About Us</a></li>
							<li><a href="#">Services</a></li>
							<li><a href="#">Careers</a></li>
							<li><a href="#">Resources</a></li>
							<li><a href="#">Contact Us</a></li>
						</ul>
						<!--?php wp_nav_menu( array('theme_location' => 'secondary' ) ); ?-->
					</div>

					<div class="copyright">
					  &copy; Copyright
							<?php
							$start_year = '2021';
							$current_year = date('Y');
							$copyright = ($current_year == $start_year) ? $start_year : $start_year.' - '.$current_year;
							echo $copyright;
							?>
					  <span class="footer_comp"><?php //echo get_bloginfo('name');?>CompanyName</span>
					  Designed by <a href="http://proweaver.com" target="_blank" rel="nofollow">Proweaver</a>
					</div>
				</div>
			</div>
    </div>
	</footer>

	<span class="back_top"></span>

  </div> <!-- End Clearfix -->
  </div> <!-- End Protect Me -->

  <!--?php get_includes('ie');?-->

  <!--
  Solved HTML5 & CSS IE Issues
  -->
	<script src="js/modernizr-custom-v2.7.1.min.js"></script>
	<script src="js/jquery-2.1.1.min.js"></script>
	<script src="js/jquery.skitter.min.js"></script>
	<script src="js/jquery.easing.1.3.js"></script>
	<script src="js/css3-animate-it.min.js"></script>
	<!--
	Solved Psuedo Elements IE Issues
	-->
	<script src="js/calcheight.min.js"></script>
	<script src="js/responsiveslides.min.js"></script>
	<script src="js/plugins.min.js"></script>
  <!--?php wp_footer(); ?-->
</body>
</html>
<!-- End Footer -->
