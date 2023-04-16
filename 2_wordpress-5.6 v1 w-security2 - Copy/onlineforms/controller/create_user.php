<?php 
require_once 'connect.php';
if( isset($_POST['username']) && isset($_POST['password']) ){

	$username = $_POST['username'];
	$password = $_POST['password'];

	if( !empty($username) && !empty($password) ){
		$checker =$wpdb->get_results("SELECT * FROM formdatabase_users WHERE user_name='".$username."'");
		if( !empty($checker) ) {
			$r['message'] = "User already exists.";
		}else{
			$data = array(
				'user_name' => $_POST['username'],
				'user_pass' => $_POST['password']
				);
			$insert = $wpdb->insert('formdatabase_users',$data);
			if($insert == TRUE){
				unset($_POST);
				$r['message'] = "success";
			}else{
				$r['message'] = "failed";
			}
		}
		
	}
}
echo json_encode($r);
?>
