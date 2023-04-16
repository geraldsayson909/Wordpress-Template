<?php 
require_once 'connect.php';
if( !empty($_POST['checklist']) ){
	foreach ($_POST['checklist'] as $id) {
		echo $id." ";
		$result = $wpdb->get_results('DELETE FROM formdatabase_emails WHERE form_id ='.$id);
	}
}
else{
	echo "No selected mail(s) to delete.";
}
?>

