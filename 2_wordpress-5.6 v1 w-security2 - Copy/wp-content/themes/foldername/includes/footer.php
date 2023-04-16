<!-- Start Footer -->
  <footer>
    <div class="footer_top">
      <div class="wrapper">
        <div class="footer_top_main">
          <div class="contact_info">
            <?php dynamic_sidebar('contact_info');?>
            content
          </div>

          <div class="footer_logo">
            <a href="<?php echo get_home_url(); ?>"><figure><img src="<?php bloginfo('template_url');?>/images/footer-logo.png" alt="<?php echo get_bloginfo('name');?>"/></figure></a>
          </div>

          <!-- <div class="visitorCounter">
          <span style="color:#005177;">Visitor No.</span> <span style="color:#CC6600;"><script src="counter.php?page=counter"></script></span>
          </div> -->

        </div>
      </div>
    </div>

    <div class="footer_btm">
      <div class="wrapper">
        <div class="footer_btm_main">
          <div class="footer_nav">
            <?php wp_nav_menu( array( 'theme_location' => 'secondary') ); ?>
          </div>

          <div class="copyright">
            &copy; Copyright
            <?php
            $start_year = '2020';
            $current_year = date('Y');
            $copyright = ($current_year == $start_year) ? $start_year : $start_year.' - '.$current_year;
            echo $copyright;
            ?>
            <span class="footer_comp"><?php echo get_bloginfo('name');?></span>
             Designed by <a href="http://proweaver.com" target="_blank" rel="nofollow">Proweaver</a>
          </div>
        </div>
      </div>
      </div>
  </footer>

<span class="back_top"></span>

</div> <!-- End Clearfix -->
</div> <!-- End Protect Me -->

<?php get_includes('ie');?>

<!--
Solved HTML5 & CSS IE Issues
-->
<script src="<?php bloginfo('template_url');?>/js/modernizr-custom-v2.7.1.min.js"></script>
<script src="<?php bloginfo('template_url');?>/js/jquery-2.1.1.min.js"></script>

<!--
Solved Psuedo Elements IE Issues
-->

<!-- <script>
function googleTranslateElementInit() {
new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
}
</script> -->

<!-- <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script> -->


<script src="<?php bloginfo('template_url');?>/js/calcheight.min.js"></script>
<script src="<?php bloginfo('template_url');?>/js/jquery.easing.1.3.js"></script>
<script src="<?php bloginfo('template_url');?>/js/jquery.skitter.min.js"></script>
<script src="<?php bloginfo('template_url');?>/js/css3-animate-it.min.js"></script>
<!-- <script src="<?php bloginfo('template_url');?>/js/owlcarousel/owl.carousel.min.js"></script> -->
<script src="<?php bloginfo('template_url');?>/js/responsiveslides.min.js"></script>
<script src="<?php bloginfo('template_url');?>/js/plugins.min.js"></script>
<?php wp_footer(); ?>
</body>
</html>
<!-- End Footer -->
