<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( is_front_page() ) { ?>
			<h1><span>Welcome to </span>Teen Drug Addiction</h1>
		<?php }elseif(is_page('sitemap')){ ?>
			<h1><?php the_title(); ?></h1>
			<ul class="bullet2 col2">
				<?php wp_list_pages(array('title_li' => '')); ?>
			</ul>
		<?php } else { ?>
			<h1><?php the_title(); ?></h1>
			<?php if($post->post_content=="") { ?>
					<p>We are in the process of updating our website with contents. Please check back next time.</p>
			<?php } ?>
		<?php } ?>
		<!--Default WordPress
		the_post_thumbnail( 'thumbnail' );     // Thumbnail (150 x 150 hard cropped)
		the_post_thumbnail( 'medium' );        // Medium resolution (300 x 300 max height 300px)
		the_post_thumbnail( 'medium_large' );  // Medium Large (added in WP 4.4) resolution (768 x 0 infinite height)
		the_post_thumbnail( 'large' );         // Large resolution (1024 x 1024 max height 1024px)
		the_post_thumbnail( 'full' );          // Full resolution (original size uploaded)-->
		<?php if ( has_post_thumbnail() ) {?>
			<?php the_post_thumbnail('full', array('class' => 'thumb_right_dd'));?>
		<?php }?>
		<div class="entry-content">
			<?php the_content(); ?>			
		
			<?php if(is_page('173')) { ?>
				<p><iframe id="myframe" style="border:0px; width:100%;" src="<?php bloginfo('template_url'); ?>/forms/newsletterForm.php"></iframe>
				<script type="text/javascript">
				//<![CDATA[ 
				document.getElementById('myframe').onload = function(){
				calcHeight();
				};
				//]]>
				</script>
				</p>
			<?php } else if(is_page('10')) { ?>
				<p><iframe id="myframe" style="border:0px; width:100%;" src="<?php bloginfo('template_url'); ?>/forms/GetQuoteForm.php"></iframe>
				<script type="text/javascript">
				//<![CDATA[ 
				document.getElementById('myframe').onload = function(){
				calcHeight();
				};
				//]]>
				</script>
				</p>
			<?php } else if(is_page('11')) { ?>
				<p><iframe id="myframe" style="border:0px; width:100%;" src="<?php bloginfo('template_url'); ?>/forms/contactForm.php"></iframe>
				<script type="text/javascript">
				//<![CDATA[ 
				document.getElementById('myframe').onload = function(){
				calcHeight();
				};
				//]]>
				</script>
				</p>
			<?php } else if(is_page('page_ID_or_permalink')) { ?> <!-- 2.1 -->
				<?php comments_template( '', true ); ?>
			<?php } ?>
			
			<?php if(is_page('page_ID_or_permalink')) { comments_template( '', true ); } ?> <!-- 2.2 -->
			
			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
			<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-## -->
<?php endwhile; // end of the loop. ?>