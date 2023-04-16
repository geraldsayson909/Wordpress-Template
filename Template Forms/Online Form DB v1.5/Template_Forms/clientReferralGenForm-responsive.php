<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Referral Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>'; 

if ($_POST){
	if(empty($_POST['Date_of_Referral']) ||		
		empty($_POST['Referrers_Name']) ||	
		empty($_POST['Referrers_Email']) ||	
		empty($_POST['Referrers_Phone']) ||	
		empty($_POST['Name']) ||	
		empty($_POST['secode'])) {
				
	
	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';	
	$prompt_message = '<div id="error">'.$asterisk . ' Required Fields are empty</div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))) && !preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Referrers_Email']))))
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
		
		$name = $_POST['Referrers_Name'];
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
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
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
			Date_of_Referral: "required",	
			Referrers_Name: "required",
			Referrers_Phone: "required",
			Name: "required",
			Referrers_Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			Date_of_Referral: "Required",	
			Referrers_Name: "Required",
			Referrers_Phone: "Required",
			Name: "Required",
			Referrers_Email: "Enter a valid Email",
			secode: ""
		}
	});
	$('#DATE,#Date,#Date_of_Referral').datepick({
        yearRange: "1970:2014",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top:8px; float:right;" />'
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
			<form id="submitform" name="contact" enctype="multipart/form-data" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				
				<div class="field">
					<div class="input">
						<label for="Date_of_Referral">Date of Referral </label>
							<input type="text" name="Date_of_Referral" class="text" style="background:#dedede;" value="<?php echo date("F d,Y") ;?>" readonly/>
					</div>
					<div class="input f-right">
						<label for="Referrer_Name">Referrer's Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Referrers_Name', 'text','Referrers_Name','placeholder="Enter referrer name here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Referrers_Email">Referrer's Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Referrers_Email','text','Referrers_Email','placeholder="Enter referrer email here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Referrers_Phone">Referrer's Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Referrers_Phone', 'text','Referrers_Phone','placeholder="Enter referrer phone here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Contact_Preference">Contact Preference</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->chkbox('Contact_Preference',array('Contact by email','Contact by phone'));
						?>	
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Client Details<input type="hidden" name="Client Details" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Name">Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Name','text','Name','placeholder="Enter name here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Address_1">Address 1</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address_1', 'text','Address_1','placeholder="Enter address 1 here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Address_2">Address 2</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address_2','text','Address_2','placeholder="Enter address 2 here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="City">City</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City','text','City','placeholder="Enter city here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="State">State</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('State', 'select', $state); 
						?>
					</div>
					<div class="input f-right">
						<label for="Zip">Zip</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip','text','Zip','placeholder="Enter zip here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Email">Email</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email','text','Email','placeholder="Enter email here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Phone">Phone</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone','text','Phone','placeholder="Enter phone here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Mobile">Mobile</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Mobile','text','Mobile','placeholder="Enter mobile here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Service_Request">Service Request</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Service_Request','text','Service_Request','placeholder="Enter service request here"'); 
						?>	
					</div>
				</div>
				
				<div class="field">	
					<div class="input textarea">	
						<label for="Statement/reason_for_referral">Your statement/reason for referral</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Statement/reason_for_referral', '','Statement/reason_for_referral','placeholder="Enter statement/reason for referral here" cols="88"'); 
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