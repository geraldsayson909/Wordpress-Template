<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Make a Reservation Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Full_Name']) ||
		empty($_POST['Email']) ||	
		empty($_POST['Address']) ||			
		empty($_POST['secode'])
		) {
				
	
	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';	
	$prompt_message = '<div id="error">'.$asterisk . ' Required Fields are empty</div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))))
		{ $prompt_message = '<div id="error">Please enter a valid email address</div>';}
	else if($_SESSION['security_code'] != htmlspecialchars(trim($_POST['secode']), ENT_QUOTES)){
		$prompt_message = '<div id="error">Invalid Security Code</div>';
	}else{

		$body = '<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px; margin: 0 auto;">
			<div style="border:8px double #c3c3d0; padding:12px;">
			<div align="center" style="color:#990000; font-style:italic; font-size:20px; font-family:Arial; margin:bottom: 15px;">('.$formname.')</div>
			
			<table width="90%" cellspacing="2" cellpadding="5" align="center" style="font-family:Verdana; font-size:13px">
				';
		
			foreach($_POST as $key => $value){
				if($key == 'secode') continue;
				elseif($key == 'submit') continue;
				
				if(!empty($value)){
					$key2 = str_replace('_', ' ', $key);
					if($value == ':') {
						$body .= '<tr><td colspan="2" style="background:#F0F0F0; line-height:30px"><b>'.$key2.'</b></td></tr>';
					}else {				
						$body .= '<tr><td><b>'.$key2.'</b>:</td> <td>'.htmlspecialchars(trim($value), ENT_QUOTES).'</td></tr>';
					}
				}
			}
			$body .= '
			</table>

			</div>
			</div>';	
	
		// for email notification
		require_once 'config.php';
		require_once 'swiftmailer/mail.php';
		// save data form on database
		include 'savedb.php';
		
		
		// save data form on database
		$subject = $formname ;		
		$attachments = array();

	 	//name of sender
		$name = $_POST['Full_Name'];
		$result = insertDB($name,$subject,$body,$attachments);		
		
		$templateVars = array('{link}' => get_home_url().'/onlineforms/'.$_SESSION['token'], '{company}' => COMP_NAME);

		Mail::Send($template, 'New Message Notification', $templateVars, $to_email, $to_name, $from_email, $from_name, $cc, $bcc);

		if($result){
			$prompt_message = '<div id="success">Your message has been submitted.  We will get in touch with you as soon as possible.<br/>Thank you for your time.</div>';
				unset($_POST);
		}else {
			$prompt_message = '<div id="error">Failed to send email. Please try again.</div>';
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
<link rel="stylesheet" href="css/jquery.datepick.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {	
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Full_Name: "required",
			Email: {
				required: true,
				email: true
			},
			Address: "required",
			secode: "required"		
		},
		messages: {
			Full_Name: "Required",
			Email: "Enter a valid Email",
			Address: "Required",
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
	
	var curr_year = new Date().getFullYear();
    $('.thisDate').datepick({
        yearRange: "1900:"+curr_year+"",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top: 8px; float: right; position: absolute; right: 10px; top: 36px;" />'
    });
});
</script>
</head>
<body>
	<div id="container" class="rounded-corners">
		<div id="content" class="rounded-corners">
			<form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				<div class="field">
					<div class="input textarea">
						<label for="Full_Name">Full Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Full_Name', 'text','Full_Name','placeholder="Enter full name here"'); 
						?>						
					</div>		
				</div>	
				<div class="field">				
					<div class="input textarea">
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email here"'); 
						?>
					</div>	
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Address">Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
						?>	
					</div>
				</div>	
				<div class="field">		
					<div class="input textarea">
						<label for="What_services_do_you_need">What services do you need? </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('What_services_do_you_need', 'text','What_services_do_you_need','placeholder="Enter services needed here"'); 
						?>
					</div>		
				</div>
				<div class="field">		
					<div class="input">
						<label for="Date">When do you need our services?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date', 'text thisDate','Date','placeholder="Enter date here"'); 
						?>
					</div>	
					<div class="input f-right">
						<label for="Time">&nbsp;</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Time', 'text','Time','placeholder="Enter time here"'); 
						?>
					</div>		
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional_details_about_your_reservation">Additional details about your reservation</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_details_about_your_reservation', '','Additional_details_about_your_reservation','placeholder="Enter additional details about your reservation here" cols="88"'); 
						?>
					</div>		
				</div>
				<div class="field">	
					<div class="verification">
						<img src="securitycode/SecurityImages.php?characters=5" border="0" id ="securiryimage" alt="Security code" />
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