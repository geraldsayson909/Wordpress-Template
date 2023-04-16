<?php 
require_once 'connect.php';

if( isset($_POST['user']) ) {
	$user = $_POST['user'];
 	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";;
	$pass = substr( str_shuffle( $chars ), 0, 12 );

	$data = array(
		'user_pass' => $pass
		);
	$where = array(
		'user_name' => $user
		);

	$result = $wpdb->update('formdatabase_users',$data,$where);
	if($result){
		echo $pass;
	}
	else{
		echo "failed";
	}
}
?>