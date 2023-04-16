<?php 
require_once 'connect.php';

		
if( !empty($_POST['checklist']) ){
	
	foreach ($_POST['checklist'] as $id) {
		// echo $id." ";
		$email = $wpdb->get_row('SELECT * FROM formdatabase_emails WHERE form_id ='.$id);
		echo json_encode(array('message' => $email->form_content));

	}
	
}
else{
	echo json_encode(array('message' => "No selected mail(s) to delete."));
}
?>

