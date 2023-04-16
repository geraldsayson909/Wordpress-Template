<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);
define('COMP_EMAIL', 'qatest@proweaver.net'); // clients email

define('MAIL_METHOD', 'PHPMAIL'); // SMTP or PHPMAIL (PHP Mail Function)
define('SMTP_SERVER', ''); // SMTP server
define('SMTP_USER', ''); // SMTP username
define('SMTP_PASSWD', ''); // SMTP password

define('SMTP_ENCRYPTION', 'off'); // TLS, SSL or off
define('SMTP_PORT', 587); // SMPT port number 587 or default
define('COMP_NAME', 'COMPANYNAME'); // company name
define('MAIL_TYPE', 2); // 1 - html, 2 - txt
define('MAIL_DOMAIN', 'web4.proweaverlinks.com/arlingtonpharmacy'); // company domain

$recaptcha_sitekey = '6LeoXdwZAAAAAAOsa6EC54VCLJNnuz-TZXZhMMYc'; // Update it using a working google Site key
$recaptcha_privite = '6LeoXdwZAAAAAMDRk3bhiW3D1V0OlyifEKZPlRGN'; // Update it using a working google Privite key

//for from email
if(!empty($_POST['Email'])){
	$from = $_POST['Email'];
}else if(!empty($_POST['Email_Address'])){
	$from = $_POST['Email_Address'];
}else{
	$from = NULL;
}

// do not edit
$subject = COMP_NAME . " [" . $formname . "]";
$template = 'template';
$to_name = NULL;
$from_email = $from;
$from_name = 'Message From Your Site';
$attachments = array();

// testing here
$testform = true;
if($testform){
	$to_email 	= 'qa@proweaver.net';
	$cc = '';
	$bcc = '';
}else{
	$to_email 	= 'qatest@proweaver.net';
	$cc = '';
	$bcc = '';
}
