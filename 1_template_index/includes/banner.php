<?php if (is_front_page() ) { ?>
<div id="banner">
  <div class="wrapper">
    <div class="slider">
      <!-- <div class="box_skitter box_skitter_large">
        <ul>
          <li><img src="images/slider/1.jpg" alt="" class="random"/></li>
          <li><img src="images/slider/2.jpg" alt="" class="random"/></li>
          <li><img src="images/slider/3.jpg" alt="" class="random"/></li>
        </ul>
      </div> -->
      <ul class="rslides">
        <li><img src="images/slider/1.jpg" alt=""/></li>
        <li><img src="images/slider/2.jpg" alt=""/></li>
        <li><img src="images/slider/3.jpg" alt=""/></li>
      </ul>
    </div>

    <img src="images/slider/1.jpg" alt="" class="mobi_ban">

    <div class="bnr_info">
      <div class="slogan">
        <h2>SloganHere</h2>
      </div>
    </div>
  </div>
</div>
<?php } else { ?>
<div class="non_ban">
<div class="non_ban_img">
<?php if(is_home() && is_author() && is_category() && is_tag() && is_single()) { ?>
  <?php if (has_post_thumbnail() ) {?>
      <?php the_post_thumbnail('full');?>
  <?php }else{ ?>
      <figure><img src="<?php bloginfo('template_url');?>/images/slider/nonhome-1229921.jpg" alt="a group of nurse and doctors smiling" /></figure>
  <?php } ?>
  <?php } elseif (has_post_thumbnail() ) { ?>
        <?php the_post_thumbnail('full');?>
  <?php } else { ?>
    <img src="<?php bloginfo('template_url'); ?>/images/slider/nonhome-1229921.jpg" alt="a group of nurse and doctors smiling">
  <?php } ?>
</div>

<div class="page_title">
  <?php if(!is_home() && !is_author() && !is_category() && !is_tag() && !is_single()) { ?>
    <h1 class="h1_title"><?php the_title(); ?></h1>
    <?php echo do_shortcode("[short_title id='" . get_the_ID() . "']"); ?>
  <?php } else { ?>
    <h1 class="h1_title">Blog</h1>
  <?php } ?>
</div>
</div>
<?php }?>
