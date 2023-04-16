<?php 
require_once 'connect.php';
if( !empty($_POST['id']) ){

	$id = $_POST['id'];
	$wpdb->get_results('UPDATE formdatabase_emails SET email_type="trash" WHERE form_id ='.$id);
}
?>

