<?php 
@session_start();
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{require '../../wp-systcon/wp-load.php';}else{require '../wp-systcon/wp-load.php';}
// $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// if($conn){
// }
// else{
// 	die ('Unable to connect to server.');
// }
?>
