<?php 
require 'connect.php';
$result = $wpdb->get_results("SELECT * FROM formdatabase_users ");
$r = array();
if(!empty($result)){
	$r['message'] = 'success';
}else{
	$r['message'] = 'failed';
}
die(json_encode($r));
?>
