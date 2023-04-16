<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Reservation Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Contact_Number']) ||
		empty($_POST['Your_Name']) ||
		empty($_POST['secode'])) {
				
	
	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';	
	$prompt_message = '<div id="error">'.$asterisk . ' Required Fields are empty</div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))))
		{ $prompt_message = '<div id="error">Please enter a valid email address</div>';}
	else if($_SESSION['security_code'] != htmlspecialchars(trim($_POST['secode']), ENT_QUOTES)){
		$prompt_message = '<div id="error">Invalid Security Code</div>';
	}else{
		
		// for email notification
		require_once 'config.php';
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
	
		$subject = COMP_NAME . " [" . $formname . "]";	
		
		$templateVars = array('{tablelist}' => $body);	
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
$transpo = array('Point to Point','Hourly Transportation','Pickup from Airport','Drop Off at Airport');
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
<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
<link rel="stylesheet" href="css/jquery.datepick.css" type="text/css" />
<script type="text/javascript">
$(document).ready(function() {	
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Contact_Number: "required",
			Your_Name: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"
		},
		messages: {
			Contact_Number: "Required",
			Your_Name: "Required",
			Email: "Required",
			secode: "Required",
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
			<form id="submitform" name="contact" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				<div class="field">
					<div class="input textarea">
						<label for="Your_Name">Your Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Your_Name', 'text','Your_Name','placeholder="Enter name here"'); 
						?>						
					</div>		
				</div>	
				<div class="field">				
					<div class="input textarea">
						<label for="Contact_Number">Contact Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_Number', 'text','Contact_Number','placeholder="Enter contact number here"'); 
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
						<label for="Travel_Destination_Tour_Package">Travel Destination/Tour Package</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Travel_Destination_Tour_Package', 'text','Travel_Destination_Tour_Package','placeholder="Enter travel destination / tour package here"'); 
						?>
					</div>		
				</div>
				<div class="field">		
					<div class="input textarea">
						<label for="Travel_Date">Travel Date(s)</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Travel_Date', 'text thisDate','Travel_Date','placeholder="Enter travel date(s) here"'); 
						?>
					</div>		
				</div>				
				<div class="field">	
					<div class="input textarea">	
						<label for="Special_Requests">Special Requests </label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Special_Requests', '','Special_Requests','placeholder="Enter special requests here" cols="88"'); 
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