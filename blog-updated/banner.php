<div id="banner">
	<div class="wrapper">
		<div class="slider">
      <?php if (!is_front_page() && !is_home() && !is_author() && !is_category() && !is_tag() && !is_single()) { ?>
        <?php if (has_post_thumbnail()) {?>
          <div class="nh_banner">
            <?php the_post_thumbnail('full');?>
          </div>
        <?php }else {?>
          <div class="nh_banner">
            <img src="<?php bloginfo('template_url');?>/images/slider/nh_banner.jpg" alt="image" />
          </div>
        <?php }?>
      <?php } else if(is_home() || is_author() || is_category() || is_tag() || is_single()) { ?>
				<div class="nh_banner">
					<img src="<?php bloginfo('template_url');?>/images/slider/nh_banner.jpg" alt="image" />
				</div>
			<?php } else { ?>
			<div class="flash">
					<figure><img src="<?php bloginfo('template_url');?>/images/bnr_img.png" alt="oil mine in the sea,cargo truck,fleet of Vehicles"></figure>
			</div>
			<div class="mobi_ban">
				<figure><img src="<?php bloginfo('template_url');?>/images/slider/1.jpg" alt="oil mine in the sea"></figure>
			</div>
    <?php } ?>

    <?php if(is_front_page()){?>
      <div class="bnr_info">
        <?php dynamic_sidebar('bnr_info');?>
      </div>
      <?php } else {?>
        <div class="nh_banner_title">
          <div class="main_heading">
            <?php if(!is_home() && !is_author() && !is_category() && !is_tag() && !is_single()) { ?>
            <h1 class="h1_title"><?php the_title();?></h1>
            <?php echo do_shortcode("[short_title id='" . get_the_ID() . "']"); ?>
            <?php } else { ?>
              <h1 class="h1_title">Blog</h1>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
		</div>
	</div>
</div>
