<? /*************** Regular Testimonial Activation ***************/

01. Copy comments.php from wp-includes/theme-compat and upload the file to wp-content/themes/company_name folder
	//this is done to "synchronize" comments.php file with the current WordPress version of the website


02. In your loop-page.php copy the code below: //(see loop-page.php for reference; line 58)
	add the code: comments_template( '', true );
	// you may add it on the existing if else statements (2.1)
	<?php } else if(is_page('page_ID_or_permalink')) { ?>
		<?php comments_template( '', true ); ?>
	<?php } ?>
	//or you may create another if statement (2.2)
	<?php if(is_page('page_ID_or_permalink')) { comments_template( '', true ); } ?>
	//make sure to enclose the code inside php tags "<?php ... ?>" since it is a php function


03. Go to wp_includes and open comment-template.php
	3.1 find "Website" and comment out the texts starting from "'url'" to "</p>'," //(see comment-template.php for reference; line 2203)
		//to remove label and textfield for Website

	3.2 find the code: $comment_fields = array( 'comment' => $args['comment_field'] ) + (array) $args['fields'];
		and alter the code so that the comment field will be placed below other text fields (Name and Email)
		it should then be: $comment_fields =  (array) $args['fields'] + array( 'comment' => $args['comment_field'] );
		//Note: the Comment field should always be placed at the bottom part of the form, below the name and email fields but above the submit button.

	3.3 find "<form" and remove "<?php echo $html5 ? ' novalidate' : ''; ?>"
		//this is done so that the required attibute on the form fields will work

	3.4 find the code: "<label for="comment">' . _x( 'Comment', 'noun' ) . '</label>" and add "*" beside "Comment"
	it should then be: <label for="comment">' . _x( 'Comment *', 'noun' ) . '</label>

04. upload testimonial.css to wp-content/themes/company_name/css folder and add the following inside head.php

	<?php if(is_page('page_ID_or_permalink')) { ?>
	<link rel="stylesheet" href="<?php bloginfo("template_url") ?>/css/testimonial.css">
	<?php }?>

05. Copy the code below and paste it on /themes/company_name/js/plugins.js file of the site.
		//---------------------- CODE --------------------------------------
		// COMMENTS STYLE PLUGIN //
		$('.commentlist li:last-child').css('background','none');
		$('.commentlist li ul li').css('background','none');
		$('.commentlist li ul li:last-child').css('border-bottom','none');
		//---------------------- END OF CODE -------------------------------


//REMINDERS:
» Always remove Website Text Field
» Always jud naa sa ubos ang Comment Field
» Dapat walay Reply functionality
» Dapat walay avatar
» Dapat wala to ang "Leave a Reply" nga text
» Dapat required ang mga fields
