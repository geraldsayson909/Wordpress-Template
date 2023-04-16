<?php 
require_once 'connect.php';
if(isset($_POST['emailtype'])){
	$emailtype = $_POST['emailtype'];
	$totalemail = $wpdb->get_var('SELECT COUNT(*) FROM formdatabase_emails WHERE email_type='.$emailtype.'');
	echo json_encode(array('message' => $totalemail));
}

?>
