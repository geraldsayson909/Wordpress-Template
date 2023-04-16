<?php 
require_once 'connect.php';

if( isset($_POST['pass']) ) {
	$user = $_SESSION['user_login'];
	$pass = $_POST['pass'];

	$data = array(
		'user_pass' => $pass
		);
	$where = array(
		'user_name' => $user
		);

	$result = $wpdb->update('formdatabase_users',$data,$where);
	if($result){
		$r['message'] = "success";
	}
	else{
		$r['message'] = "bogo!";
	}
	echo json_encode($r);
}
?>
