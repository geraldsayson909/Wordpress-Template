<!-- Header -->
  <header>
    <div class="wrapper">
      <div class="header_main">
      <div class="main_logo">
        <a href="<?php echo get_home_url(); ?>"><figure><img src="<?php bloginfo('template_url');?>/images/main-logo.png" alt="<?php echo get_bloginfo('name');?>"/></figure></a>
      </div>

      <div class="header_info">

        <div class="head_info">
          <?php dynamic_sidebar('header_info');?>
          <h2>Contact <span>number</span></h2>
        </div>

        <!-- <div class="social_media">
          <ul>
            <li><a href="https://www.facebook.com" target="_blank"><figure><img src="<?php bloginfo('template_url');?>/images/facebook.png" alt="facebook"/></figure></a></li>
            <li><a href="https://www.twitter.com" target="_blank"><figure><img src="<?php bloginfo('template_url');?>/images/twitter.png" alt="twitter"/></figure></a></li>
          </ul>
        </div> -->

        <!--<div id="google_translate_element"></div> -->
      </div>

      <div class="clearfix"></div>
      </div>
    </div>
  </header>
<!-- End Header -->
