<?php
require_once 'connect.php';
if( isset($_POST['user']) && isset($_POST['pass']) ) {
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$result = $wpdb->get_results("SELECT * FROM formdatabase_users WHERE user_name = '".$user."' AND user_pass='".$pass."' ");
	if(!empty($result)){
		$_SESSION['user_login'] = $user;
		$_SESSION['login_status'] = 1;
		$r['message'] = "success";
	}
	else{
		$r['message'] = "bogo!";
	}
	echo json_encode($r);
}
?>
