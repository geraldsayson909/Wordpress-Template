<?php
require_once 'connect.php';


if( !empty($_POST['id']) ){

	$id =$_POST['id'];

	$email = $wpdb->get_row('SELECT * FROM formdatabase_emails WHERE form_id ='.$id);

	/*For subject*/
	$subject = "<div style='padding: 0 30px;font-family:Open Sans, sans-serif;'>
				<h1 style='font-size: 23px; color:#4baadf; background: #fff; line-height: 30px;'>$email->form_subject
				<span style='font-size: 20px; display: block; color: #333333; font-weight: normal;'>$email->form_from</span></h1>
				<hr style='margin: 20px 0;'></hr>
			  </div>";

	/*For form content*/
	$string = preg_replace(array('/<p[^>]*>(.*)<\/p[^>]*>/i', '/<div[^>]*>(.*)<\/div[^>]*>/i'), '', $email->form_content);
	$email_content = '<div style="font: 14px Open Sans,sans-serif;">'.preg_replace(array('/(<[^>]+) style=".*?"/i'), '$1', $string).'</div>';

	echo $subject.$email_content;

}

?>
