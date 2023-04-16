<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Transfer RX Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Date_of_Birth']) ||
		empty($_POST['Phone_Number']) ||	
		empty($_POST['Address']) ||	
		empty($_POST['City']) ||		
		empty($_POST['Zip_or_Postal_Code']) ||	
		empty($_POST['Pharmacy_Name']) ||	
		empty($_POST['Pharmacy_Phone']) ||	
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

	 	//name of sender
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
			First_Name: "required",
			Last_Name: "required",
			Date_of_Birth: "required",
			Phone_Number: "required",
			Address: "required",
			City: "required",
			Zip_or_Postal_Code: "required",
			Pharmacy_Name: "required",
			Pharmacy_Phone: "required",
			secode: "required"		
		},
		messages: {
			First_Name: "Required",
			Last_Name: "Required",
			Date_of_Birth: "Required",
			Phone_Number: "Required",
			Address: "Required",
			City: "Required",
			Zip_or_Postal_Code: "Required",
			Pharmacy_Name: "Required",
			Pharmacy_Phone: "Required",
			secode: ""
		}
	});
	$('#DATE,#Date,#Date_of_Birth').datepick({
        yearRange: "1970:2014",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top: 8px; float: right; position: absolute; right: 10px; top: 36px;" />'
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
						<span style="color:#000; font-weight:bold;">Patient Details</span><input type="hidden" name="Patient Details" value=":" />
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="First_Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','First_Name','placeholder="Enter first name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Last_Name">Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','Last_Name','placeholder="Enter last name here"'); 
						?>						
					</div>	
				</div>
				<div class="field">
					<div class="input">
						<label for="Date_of_Birth">Date of Birth <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_of_Birth','text','Date_of_Birth','placeholder="Enter date of birth here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Phone_Number">Phone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone_Number', 'text','Phone_Number','placeholder="Enter phone number here"'); 
						?>						
					</div>	
				</div>
				<div class="field">
					<div class="input">
						<label for="Address">Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address','text','Address','placeholder="Enter address here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="City">City <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"'); 
						?>						
					</div>	
				</div>
				<div class="field">
					<div class="input">
						<label for="State">State </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State', 'select',$state); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Zip_or_Postal_Code">Zip/Postal Code <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip_or_Postal_Code', 'text','Zip_or_Postal_Code','placeholder="Enter zip or postal code here"'); 
						?>						
					</div>	
				</div>
				<div class="field">
					<div class="input">
						<label for="Pharmacy_Name">Pharmacy Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Pharmacy_Name','text','Pharmacy_Name','placeholder="Enter pharmacy name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Pharmacy_Phone">Pharmacy Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Pharmacy_Phone', 'text','Pharmacy_Phone','placeholder="Enter pharmacy phone here"'); 
						?>						
					</div>	
				</div>
				<div class="field">
					<div class="input">
						<span style="color:#000; font-weight:bold;">Prescriptions to be transferred</span><input type="hidden" name="Prescriptions to be transferred" value=":" />
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<span style="font-style:italic; font-size:11px; color:#000;">If you would like to transfer all prescriptions, simply check the box below.</span>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<input type="checkbox" name="Transfer_all_my_prescriptions" value="Yes"/> Transfer all my prescriptions
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<span style="font-style:italic; font-size:11px; color:#000;">If you would like to selectively transfer your prescriptions, simply start typing to find your medication.</span>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<span style="color:#000;">List specific prescriptions to be transferred</span><input type="hidden" name="List specific prescriptions to be transferred" value=":" />
					</div>
				</div>
				<div class="field">
					<div class="input">
						<div class="field f-height">
							<div class="input textarea" style="text-align:center;">
								<span style="color:#000; font-weight:bold;">MEDICATION NAME</span>
							</div>
						</div>
						<div class="field">
							<div class="input6">
								<label for="Rx1_Med_Name">Rx1 Med Name</label>
							</div>
							<div class="input7">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx1_Med_Name', 'text','Rx1_Med_Name','placeholder="Enter medication name here"'); 
								?>
							</div>
						</div>
						<div class="field">
							<div class="input6">
								<label for="Rx2_Med_Name">Rx2 Med Name</label>
							</div>
							<div class="input7">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx2_Med_Name', 'text','Rx2_Med_Name','placeholder="Enter medication name here"'); 
								?>
							</div>
						</div>
						<div class="field">
							<div class="input6">
								<label for="Rx3_Med_Name">Rx3 Med Name</label>
							</div>
							<div class="input7">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx3_Med_Name', 'text','Rx3_Med_Name','placeholder="Enter medication name here"'); 
								?>
							</div>
						</div>
						<div class="field">
							<div class="input6">
								<label for="Rx4_Med_Name">Rx4 Med Name</label>
							</div>
							<div class="input7">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx4_Med_Name', 'text','Rx4_Med_Name','placeholder="Enter medication name here"'); 
								?>
							</div>
						</div>
						<div class="field">
							<div class="input6">
								<label for="Rx5_Med_Name">Rx5 Med Name</label>
							</div>
							<div class="input7">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx5_Med_Name', 'text','Rx5_Med_Name','placeholder="Enter medication name here"'); 
								?>
							</div>
						</div>
					</div>
					<div class="input f-right">
						<div class="field">
							<div class="input textarea" style="text-align:center;">
								<span style="color:#000; font-weight:bold;">PRESCRIPTION NUMBER<br/>FROM CURRENT PHARMACY</span>					
							</div>	
						</div>
						<div class="field">
							<div class="input8">
								<label for="Rx1_#">Rx 1 #</label>
							</div>
							<div class="input9">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx1_#', 'text','Rx1_#','placeholder="Enter prescription number here"'); 
								?>
							</div>
						</div>
						<div class="field">
							<div class="input8">
								<label for="Rx2_#">Rx 2 #</label>
							</div>
							<div class="input9">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx2_#', 'text','Rx2_#','placeholder="Enter prescription number here"'); 
								?>
							</div>
						</div>
						<div class="field">
							<div class="input8">
								<label for="Rx3_#">Rx 3 #</label>
							</div>
							<div class="input9">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx3_#', 'text','Rx3_#','placeholder="Enter prescription number here"'); 
								?>
							</div>
						</div>
						<div class="field">
							<div class="input8">
								<label for="Rx4_#">Rx 4 #</label>
							</div>
							<div class="input9">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx4_#', 'text','Rx4_#','placeholder="Enter prescription number here"'); 
								?>
							</div>
						</div>
						<div class="field">
							<div class="input8">
								<label for="Rx5_#">Rx 5 #</label>
							</div>
							<div class="input9">
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Rx5_#', 'text','Rx5_#','placeholder="Enter prescription number here"'); 
								?>
							</div>
						</div>
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