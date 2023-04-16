<?php 
require_once 'connect.php';
if(isset($_POST['id'])){
	$id = $_POST['id'];
	$subject = $wpdb->get_row('SELECT * FROM formdatabase_emails WHERE form_id ='.$id);

	if(!empty($subject->form_subject) || !empty($subject->form_from)){		
		$data = " <div class='subject-wrapper'>
					<h1>$subject->form_subject
					<span>$subject->form_from</span></h1>
					</div>";		
		echo json_encode(array('message' => $data));
	}	
}
?>
