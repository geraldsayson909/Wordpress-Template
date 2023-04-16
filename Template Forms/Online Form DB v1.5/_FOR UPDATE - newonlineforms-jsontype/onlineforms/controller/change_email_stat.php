<?php
require_once 'connect.php';
$dateTime = new DateTime();
if( !empty($_POST['checklist']) && isset($_POST['email_type']) ){
	$email_type = $_POST['email_type'];
	foreach ($_POST['checklist'] as $id) {
		$data = array(
			'email_type' => $email_type,
			'date_deleted' => date("Y-m-d  H:i:s")
			);
		$where = array(
			'form_id' => $id
			);
		$wpdb->update('formdatabase_emails',$data,$where);
	}
}
elseif( !empty($_POST['id']) && isset($_POST['status']) ){
	$status = $_POST['status'];
	$id = $_POST['id'];
	
	$data = array( 'status' => $status );
	$where = array( 'form_id' => $id );

	$wpdb->update('formdatabase_emails',$data,$where);

}

?>
