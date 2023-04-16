<?php 
require_once 'connect.php';

if( isset($_POST['pass']) ) {
	$user = $_SESSION['user_login'];
	$pass = $_POST['pass'];
	$result = $wpdb->get_results("SELECT * FROM formdatabase_users WHERE user_name = '".$user."' AND user_pass='".$pass."' ");
	if(!empty($result)){
		$r['message'] = "success";
	}
	else{
		$r['message'] = "bogo!";
	}
	echo json_encode($r);
}
?>
