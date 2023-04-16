<?php

require_once 'connect.php';



if(isset($_POST['id'])){

	$id = $_POST['id'];

	$email = $wpdb->get_row('SELECT form_content FROM formdatabase_emails WHERE form_id ='.$id);

	if(!empty($email)){

		// $email_content = preg_replace(array('/<p[^>]*>(.*)<\/p[^>]*>/i', '/(<[^>]+) style=".*?"/i'), '$1', $email->form_content);

		$r['message'] = preg_replace('/<div[^>]*>(.*)<\/div[^>]*>/i', '', $email->form_content);

		 echo json_encode($r);
	}

}

?>

