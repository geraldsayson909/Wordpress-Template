<?php
require_once 'connect.php';
require '../mypdf.php';

if( !empty($_POST['id']) ){
	$id = $_POST['id'];
	$email = $wpdb->get_row('SELECT * FROM formdatabase_emails WHERE form_id ='.$id);

	//For PDF
	$date = date('m_d_Y_hms', time());
	$subject = str_replace(' ', '_',$email->form_subject);
	$mypdf = new MYPDF();
	$attachment_path = '../pdf/'.$subject.'_'.$date.'.pdf';
	$attachment_name = basename($attachment_path);
	$mypdf->createPdf($email->form_subject, $email->form_content, $attachment_path);

	//for localhost testing
	//$url = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/') -10).'/pdf/'.$attachment_name;

	//for live
	$url = home_url().'/onlineforms/pdf/'.$attachment_name;
	echo $url;

}
?>
