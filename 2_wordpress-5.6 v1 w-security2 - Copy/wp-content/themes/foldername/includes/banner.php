<!-- Banner -->
<div id="banner">
<div class="wrapper">
	<?php if (is_front_page() ) { ?>
	<div class="bnr_main">
		<div class="slider">
			<div class="box_skitter box_skitter_large">
				<ul>
					<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/1.jpg" class="random"  alt="x1"/></figure></li>
					<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/2.jpg" class="random"  alt="x2"/></figure></li>
					<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/3.jpg" class="random"  alt="x3"/></figure></li>
					<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/4.jpg" class="random"  alt="x4"/></figure></li>
					<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/5.jpg" class="random"  alt="x5"/></figure></li>
					<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/6.jpg" class="random"  alt="x6"/></figure></li>
				</ul>
			</div>
			<ul class="rslides">
				<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/1.jpg" alt="x1"/></figure></li>
				<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/2.jpg" alt="x2"/></figure></li>
				<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/3.jpg" alt="x3"/></figure></li>
				<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/4.jpg" alt="x4"/></figure></li>
				<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/5.jpg" alt="x5"/></figure></li>
				<li><figure><img src="<?php bloginfo('template_url');?>/images/slider/6.jpg" alt="x6"/></figure></li>
			</ul>
	  </div>
			<figure><img src="<?php bloginfo('template_url');?>/images/slider/1.jpg" alt="x1" class="mobi_ban"></figure>

	  <div class="bnr_info">
					<?php dynamic_sidebar('bnr_info');?>
					<h2></h2>
	  </div>
	</div>

	<!-- after bnr_info class -->
<?php } else {?>
 <div class="non_ban">
	 <figure>
	 <?php if(is_home() && is_author() && is_category() && is_tag() && is_single()) { ?>
	 <?php if (has_post_thumbnail() ) {?>
	 <?php the_post_thumbnail('full');?>
	 <?php }else{ ?>
	 <img src="<?php bloginfo('template_url');?>/images/slider/nonhome-default.jpg" alt="lady caregiver with senior people smiling" />
	 <?php } ?>
	 <?php } elseif (has_post_thumbnail() ) { ?>
	 <?php the_post_thumbnail('full');?>
	 <?php } else { ?>
	 <img src="<?php bloginfo('template_url'); ?>/images/slider/nonhome-default.jpg" alt="lady caregiver with senior people smiling">
	 <?php } ?>
	 </figure>
	 <div class="page_title">
		 <?php if(!is_home() && !is_author() && !is_category() && !is_tag() && !is_single()) { ?>
			 <h1 class="h1_title"><?php the_title(); ?></h1>
			 <?php echo do_shortcode("[short_title id='" . get_the_ID() . "']"); ?>
		 <?php } else { ?>
			 <h1 class="headings_title">Blog</h1>
		 <?php } ?>
	 </div>
 </div>

 <?php }?>
	<!-- end of after bnr_info class -->

</div>
</div>
<!-- End Banner -->
