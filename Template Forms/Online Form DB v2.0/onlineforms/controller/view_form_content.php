<?php
require_once 'connect.php';
if(isset($_POST['token'])){
	$token= $_POST['token'];
	$email = $wpdb->get_row('SELECT form_content FROM formdatabase_emails WHERE token ="'.$token.'"');
	if(!empty($email)){
		echo json_encode(array('message' => $email->form_content));
	}
}
?>
