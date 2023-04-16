<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Refill RX Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Last_Name']) ||
		empty($_POST['First_Name']) ||
		empty($_POST['Phone_Number']) ||				
		empty($_POST['Rx_1']) ||	
		empty($_POST['secode'])) {
				
	
	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';	
	$prompt_message = '<div id="error">'.$asterisk . ' Required Fields are empty</div>';
	}
	// else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))))
		// { $prompt_message = '<div id="error">Please enter a valid email address</div>';}
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
			
			$name = $_POST['First_Name'].' '.$_POST['Last_Name'];
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
$choices = array('No, thanks','Yes, via phone');
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
			Last_Name: "required",
			First_Name: "required",
			Rx_1: "required",
			Phone_Number: "required",
			secode: "required"		
		},
		messages: {
			Last_Name: "Required",
			First_Name: "Required",
			Phone_Number: "Required",
			Rx_1: "Required",
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
						<span style="color:#000; font-weight:bold;">Who is this prescription for?</span><input type="hidden" name="Who is this prescription for?" value=":" />
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Last_Name">Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','Last_Name','placeholder="Enter last name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="First_Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','First_Name','placeholder="Enter first name here"'); 
						?>						
					</div>					
				</div>
				<div class="field">
					<div class="input">
						<label for="Phone_Number">Phone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone_Number', 'text','Phone_Number','placeholder="Enter phone number here"'); 
						?>						
					</div>				
				</div>
				<div class="field">
					<div class="input">
						<span style="color:#000; font-weight:bold;">RX REFILL NUMBERS</span><input type="hidden" name="RX Refill Numbers" value=":" />			
					</div>				
				</div>
				<div class="field">
					<div class="input3">
						<label for="Rx_1">1 <span>*</span></label>
					</div>
					<div class="input4">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Rx_1','text','Rx_1','placeholder="Enter rx refill number here"'); 
						?>						
					</div>				
				</div>
				<div class="field">
					<div class="input3">
						<label for="Rx_2">2</label>
					</div>
					<div class="input4">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Rx_2','text','Rx_2','placeholder="Enter rx refill number here"'); 
						?>						
					</div>				
				</div>
				<div class="field">
					<div class="input3">
						<label for="Rx_3">3</label>
					</div>
					<div class="input4">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Rx_3','text','Rx_3','placeholder="Enter rx refill number here"'); 
						?>						
					</div>				
				</div>
				<div class="field">
					<div class="input3">
						<label for="Rx_4">4</label>
					</div>
					<div class="input4">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Rx_4','text','Rx_4','placeholder="Enter rx refill number here"'); 
						?>						
					</div>				
				</div>
				<div class="field">
					<div class="input3">
						<label for="Rx_5">5</label>
					</div>
					<div class="input4">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Rx_5','text','Rx_5','placeholder="Enter rx refill number here"'); 
						?>						
					</div>				
				</div>
				<div class="field">
					<div class="input">
						<span style="color:#000; font-weight:bold;">ADD MORE PRESCRIPTIONS</span><input type="hidden" name="More Prescriptions" value=":" /><br/>
						<span style="color:#000; font-style:italic; font-size:11px;">OVER THE COUNTER ITEM</span>
					</div>				
				</div>
				<div class="field">
					<div class="input3">
						&nbsp;
					</div>
					<div class="input5" style="text-align:center;">
						<span style="color:#000; font-weight:bold;">Name</span>
					</div>	
					<div class="input5" style="text-align:center;">
						<span style="color:#000; font-weight:bold;">Quantity</span>					
					</div>						
				</div>
				<div class="field">
					<div class="input3">
						<label for="1">1</label>
					</div>
					<div class="input5" style="padding-right:10px;">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Name_1','text','Prescription_Name_1','placeholder="Enter name here"'); 
						?>						
					</div>	
					<div class="input5">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Qty_1','text','Prescription_Qty_1','placeholder="Enter quantity here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input3">
						<label for="2">2</label>
					</div>
					<div class="input5" style="padding-right:10px;">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Name_2','text','Prescription_Name_2','placeholder="Enter name here"'); 
						?>						
					</div>	
					<div class="input5">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Qty_2','text','Prescription_Qty_2','placeholder="Enter quantity here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input3">
						<label for="3">3</label>
					</div>
					<div class="input5" style="padding-right:10px;">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Name_3','text','Prescription_Name_3','placeholder="Enter name here"'); 
						?>						
					</div>	
					<div class="input5">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Qty_3','text','Prescription_Qty_3','placeholder="Enter quantity here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input3">
						<label for="4">4</label>
					</div>
					<div class="input5" style="padding-right:10px;">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Name_4','text','Prescription_Name_4','placeholder="Enter name here"'); 
						?>						
					</div>	
					<div class="input5">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Qty_4','text','Prescription_Qty_4','placeholder="Enter quantity here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input3">
						<label for="5">5</label>
					</div>
					<div class="input5" style="padding-right:10px;">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Name_5','text','Prescription_Name_5','placeholder="Enter name here"'); 
						?>						
					</div>	
					<div class="input5">
						<!--label class="f-left" style="padding:0 5px 0 0;" for="Phone_Number">1 <span>*</span></label-->
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prescription_Qty_5','text','Prescription_Qty_5','placeholder="Enter quantity here"'); 
						?>						
					</div>						
				</div>
				<div class="field">				
					<div class="input textarea">
						<label class="block" for="PICK_UP_OR_DELIVERY?">PICK UP OR DELIVERY?</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('PICK_UP_OR_DELIVERY?',array('Pickup','Delivery'));
						?>	
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Would_you_like_us_to_notify_you_when_your_prescriptions_are_ready?" style="display: block;">Would you like us to notify you when your prescription(s) are ready?</label>
						<?php 
							// @param field name, class, option, id and attribute
							$input->select('Would_you_like_us_to_notify_you_when_your_prescriptions_are_ready?', 'select',$choices,'','style="width:50% !important;"');
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