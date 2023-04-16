<?php
@session_start();
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
  require '../../wp-config.php';
}else{
	require '../wp-config.php';
}

// $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// if($conn){

// }
// else{
// 	die ('Unable to connect to server.');
// }
?>
