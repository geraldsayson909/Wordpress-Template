<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);
define('COMP_EMAIL', 'onlineform10@proweaver.net'); // company email

define('MAIL_METHOD', 'SMTP'); // SMTP or PHPMAIL (PHP Mail Function)
define('SMTP_SERVER', 'secure.emailsrvr.com'); // SMTP server
define('SMTP_USER', 'onlineform10@proweaver.net'); // SMTP username
define('SMTP_PASSWD', 'i0f0oRwM5qY@'); // SMTP password


define('SMTP_ENCRYPTION', 'off'); // TLS, SSL or off
define('SMTP_PORT', 587); // SMPT port number 587 or default
define('COMP_NAME', 'Life Touches Home Healthcare LLC'); // company name
define('MAIL_TYPE', 2); // 1 - html, 2 - txt
define('MAIL_DOMAIN', 'web2.proweaverlinks.com/tech/lifetouches'); // company domain

// do not edit
$subject = COMP_NAME . " [" . $formname . "]";
$template = 'template';
$to_name = NULL;
$from_email = NULL;
$from_name = 'Message From Your Site';
$attachments = array();

// testing here
$testform = true;
if($testform){
	$to_email 	= array('pdqapw5@gmail.com');
	$cc = '';
	$bcc = '';
}else{
	$to_email 	= array('tgeorge@lifetoucheshomehealth.com');
	$cc = '';
	$bcc = '';
}
