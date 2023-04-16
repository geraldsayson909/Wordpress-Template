<?php
require_once 'connect.php';

if(isset($_POST['id'])){
	$id = $_POST['id'];
	$data = array(
			'status' => "read"
			);
		$where = array(
			'form_id' => $id
			);
	$wpdb->update('formdatabase_emails',$data,$where);
}
?>
