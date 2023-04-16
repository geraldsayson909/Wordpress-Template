<?php 
@session_start();
$_SESSION['user_login'] = '';
$_SESSION['login_status'] = 0;
session_destroy();
header('Location:../index.php');

?>