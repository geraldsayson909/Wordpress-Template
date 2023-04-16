<?php
require_once 'connect.php';
if(isset($_POST['id'])){
	$id = $_POST['id'];
	$subject = $wpdb->get_row('SELECT * FROM formdatabase_emails WHERE form_id ='.$id);

	if(!empty($subject->form_subject) || !empty($subject->form_from)){
		$data['message'] = " <div class='subject-wrapper'>
					<h1>$subject->form_subject
					<span>$subject->form_from</span></h1>					<p>Please do not reply to this email. This is only a notification from your website online forms. To contact the person who filled out your online form, kindly use the email which is inside the form below.</p>
					</div>";
		echo json_encode($data);
	}
}
?>
