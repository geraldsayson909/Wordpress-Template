<?php 
require_once 'connect.php';
$counter = $wpdb->get_var('SELECT COUNT(*) FROM formdatabase_emails WHERE email_type="inbox" AND status ="new"');
echo json_encode(array('message' => '<span> Inbox ('.$counter.')</span>'));
?>
