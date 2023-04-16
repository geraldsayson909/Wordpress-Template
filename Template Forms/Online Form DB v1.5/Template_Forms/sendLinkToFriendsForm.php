<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Recommended';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';
$message = 'Take a look at this website:';

error_reporting(0); // Turn off all error reporting

define('MAIL_TYPE', 1); // 1 - html, 2 - txt
require_once 'config.php';
$link = ' '.MAIL_DOMAIN;

if ($_POST){

	if(empty($_POST['To_Email']) ||
		empty($_POST['To_Name']) ||
		empty($_POST['From_Name']) ||				
		empty($_POST['From_Email']) ||	
		empty($_POST['secode'])) {	
	
	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$asteriskEmail = '<span style="color:#FF0000;">Please enter a valid email address</span>';
	$prompt_message = $asterisk . '<span style="color:#FF0000;"> Required Fields are empty</span>';
	}
	else if((!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['To_Email'])))) || (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['From_Email'])))))
		{ $prompt_message = '<span style="color:#FF0000;">Please enter a valid email address</span>';}
	else if($_SESSION['security_code'] != htmlspecialchars(trim($_POST['secode']), ENT_QUOTES)){
		$prompt_message = '<span style="color:#CC0000;">Invalid Security Code</span>';
	}else{
	
		
        require_once 'swiftmailer/mail.php';
		
        $body = '<div align="left" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px;">
            <div style="border:8px double #c3c3d0; padding:12px;">
            <div align="center" style="font-size:22px; font-family:Times New Roman, Times, serif; color:#051d38;">'.COMP_NAME.'</div>
            <div align="center" style="color:#990000; font-style:italic; font-size:13px; font-family:Arial;">('.$formname.')</div>
            <p>&nbsp;</p>
            <table width="90%" cellspacing="2" cellpadding="5" align="center" style="font-family:Verdana; font-size:13px">
                ';
        
            foreach($_POST as $key => $value){
                if($key == 'secode') continue;
                elseif($key == 'submit') continue;
                elseif($key == 'To_Name') continue;
                elseif($key == 'To_Email') continue;
                elseif($key == 'From_Name') continue;
                elseif($key == 'From_Email') continue;
                elseif($key == 'Subject') continue;
                
                if(!empty($value)){            
					$body .= '<tr style="text-align:center;"><td><strong>Take a look at this website :</strong>'.$link.'</td></tr>';
                }
            }
            $body .= '
            </table>

            </div>
            </div>';    
    
        $subject = COMP_NAME . " [" . $formname . "]";    
        
        $templateVars = array('{tablelist}' => $body);    
		$to_email = $_POST['To_Email'];
        $sent = Mail::Send($template, $subject, $templateVars, $to_email, $to_name, $from_email, $from_name, $attachments);    
        
        if($sent == 1 || $sent == true) {
                $prompt_message = '<div id="success">Your message has been submitted.  We will get in touch with you as soon as possible.<br/>Thank you for your time.</div>';
                unset($_POST);
        }else {
                $prompt_message = '<div id="error">Failed to send email. Please try again.<br />Error: '.$sent.'</div>';                
        }
	}
		
		
}
/*************declaration starts here************/
?>


<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $formname; ?></title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {	
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			To_Name: "required",
			To_Email: {
				required: true,
				email: true
			},
			From_Name: "required",
			From_Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			To_Name: "Required",
			To_Email: "Enter a valid Email",
			From_Name: "Required",
			From_Email: "Enter a valid Email",
			secode: ""
		}
	});
	$("#submitform").submit(function(){
		if($(this).valid()){
			self.parent.$('html, body').animate(
				{ scrollTop: self.parent.$('#myframe').offset().top },
				500
			);
		}
	});	
});
</script>
</head>
<body>
	<div id="container" class="rounded-corners">
		<div id="content" class="rounded-corners">
			<form id="submitform" name="contact" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				<div class="field">
					<div class="input">
						<label for="To_Name">To (Name) <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('To_Name', 'text','To_Name','placeholder="Enter recipient\'s name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="To_Email">To (Email) <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('To_Email', 'text','To_Email','placeholder="Enter recipient\'s email here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="From_Name">From (Name) <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('From_Name', 'text','From_Name','placeholder="Enter sender\'s name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="From_Email">From (Email) <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('From_Email', 'text','From_Email','placeholder="Enter sender\'s email here"'); 
						?>						
					</div>						
				</div>	
				<div class="field">
					<div class="input">
						<label for="Subject">Subject</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Subject', 'text','Subject','placeholder="Enter subject here"'); 
						?>						
					</div>					
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Message">Message</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Message','','Message','readonly="readonly" cols="88"','Take a look at this website'.$link); 
						?>
					</div>		
				</div>
				<div class="field">	
					<div class="verification">
						<img src="../forms/securitycode/SecurityImages.php?characters=5" border="0" id ="securiryimage" alt="Security code" />
						<?php 
							// @param field name, class, id and attribute
							$input->fields('secode', 'text','secode','placeholder="Enter security code here" title="This confirms you are a human user and not a spam-bot." maxlength="5"'); 
						?>	
						<button type='submit' class="button">Submit</button>						
					</div>	
				</div>
			</form>	
			<div class="clearfix"></div>			
		</div>
	</div>
</body>	
</html>