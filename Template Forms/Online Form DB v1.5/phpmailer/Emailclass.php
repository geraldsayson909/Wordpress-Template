<?php
require("class.phpmailer.php");
class Emailclass{
	private $asterisk = '<font color="#ff0000">* </font>';
	private $dcomp = "SOUTHWEST ESCROW CORPORATION";
	private $toclient = 'proweav@aol.com';
	public $notice;

	function theMailer($formname,$namefrom,$emailfrom){
	
		$host='mail.proweaver.net';
		$username='johnmorton@proweaver.net';
		$password='proweaver';
		$senderEmail=$emailfrom;
		$senderName=$namefrom;
		
		
		$mail = new PHPMailer();
		
		$mail->IsSMTP();                                           // set mailer to use SMTP (required)
		$mail->Host = $host;                   // specify main and backup server (required)
		$mail->SMTPAuth = true;                                    // turn on SMTP authentication (required)
		$mail->Username = $username;            // SMTP username (required)
		$mail->Password = $password;                             // SMTP password (required)
		
		$mail->From = $emailfrom;  //$senderEmail;					   // sender email (required)	 
		$mail->FromName = $senderName; 						   // sender name (required)
		$mail->AddAddress($this->toclient, $formname); //email = required, name = optional
		//$mail->AddBCC('proweav@aol.com','Mailcheck');         //email = required, name = optional
		
		$mail->WordWrap = 50;                                       // set word wrap to 50 characters (optional)
		$mail->IsHTML(true);                                        // set email format to HTML  (required)
		//$mail->IsHTML(false);                                     // set email format to Plain Text 
	
		
		$message = '<span style="color:#003399; font-size:22px;">' . $this->dcomp . ' [' . $formname . ']</span>' .
				  '<table width="500" border="0" bordercolor="#999999" cellpadding="3" ' .
				  'cellspacing="3" style="font-size:12px;">';
		foreach($_POST as $key => $value){
			if($key == 'Submit' || 
			$key == 'Submit2' || 
			$key == 'submit' ||
			$key == 'reset' ||
			$key == 'Reset' ||
			$key == 'getquote'
			
			) continue;


			if(!empty($value)){
				$key = str_replace('_', ' ', $key);
				
				if($key == $value){
					$message .= '<tr><td colspan="2" bgcolor="#E3E3E3">' . $value . '</td></tr>';
				
				}else if($value == 'borderline'){
					$message .= '<tr><td colspan="2" ><hr color="#cccccc" size="1" noshade="noshade"/></td></tr>';
				}else{
					$message .= '<tr><td width="50%" style="color:#0066ff;">' . $key . '</td><td width="50%">' . $value . '</td></tr>';
				}
			}
		}

		$mail->Subject =  $this->dcomp . ' [' . $formname . ']';				// Subject (required)
		$mail->Body    = $message; // Body / message (required)
		
		//$mail->AddAttachment('class.phpmailer.php');  // Attachment (oprional)
		
		if(!$mail->Send()){
		   $this->prompt =  'Message could not be sent.<br>';
		   $this->prompt .=  'Mailer Error: ' . $mail->ErrorInfo;
		}
		$this->notice = '
		<div style="width:auto; height:auto; paddingtop:15px; margin:0 auto 0 auto; border:2px #cc0000 solid;"><p align="center" style="color:#FF0000; font-size:12px;">Successfully submitted, thank you. We will get in touch with you as soon as possible.</p></div>';
		return $this->notice;
	}
}					/***************** for more info visit @ http://phpmailer.codeworxtech.com/index.php *****************/
?>
