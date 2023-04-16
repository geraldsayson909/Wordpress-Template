<?php @session_start();
get_includes('head');
get_includes('header');
get_includes('nav');
get_includes('banner');
?>

<?php if ( is_front_page() ) { get_includes('middle'); } ?>

<!-- Main -->
<div id="main_area">
  <div class="wrapper">
    <div class="main_holder animatedParent animateOnce">
      <main>
        <?php if(!is_front_page()) : ?><div class="breadcrumbs">
        <?php
        if(function_exists('bcn_display'))
        {
        bcn_display();
        }?>
        </div><?php endif; ?>
        <?php get_template_part( 'loop', 'home' );?>
      </main>

    </div>
      <?php if ( is_front_page() ) { get_includes('sidebar'); } ?>
    <div class="clearfix"></div>
  </div>
</div>
<!-- End Main -->
<?php if ( is_front_page() ) { get_includes('bottom'); } ?>
<?php get_includes('footer');?>
