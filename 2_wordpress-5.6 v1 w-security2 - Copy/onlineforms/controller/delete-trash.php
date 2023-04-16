<?php 
require_once 'connect.php';
if( !empty($_POST['id']) ){
	$id = $_POST['id'];
	$result = $wpdb->get_results('DELETE FROM formdatabase_emails WHERE form_id ='.$id);
}
?>

