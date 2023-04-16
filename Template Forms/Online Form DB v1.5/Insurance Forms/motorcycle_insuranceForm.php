<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Motorcycle Insurance Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Email']) ||				
		empty($_POST['Phone']) ||
		empty($_POST['secode'])) {
	
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
$status = array('- Please Select -','Single','Married','Divorced','Widowed');
$best_day = array('- Please Select -','Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$best_time = array('- Please Select -','Anytime','Morning','Afternoon','Evening');
$collision_deductible = array('Please select a choice.','$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');
$custom_equipment = array('Please select a choice.','$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');
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
			Email: {
				required: true,
				email: true
			},
			Phone: "required",
			secode: "required"		
		},
		messages: {
			First_Name: "Required",
			Last_Name: "Required",
			Email: "Enter a valid Email",
			Phone: "Required",
			secode: ""
		}
	});
	
	
	var curr_year = new Date().getFullYear();
	$('#Motorcycle_License_Endorsement_Date').datepick({
        yearRange: "1900:"+curr_year+"",
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
			<form id="submitform" name="contact" enctype="multipart/form-data" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">CUSTOMER/DRIVER INFORMATION<input type="hidden" name="Customer/Driver Information" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="First_Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name','text','First_Name','placeholder="Enter first name here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Last_Name">Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name','text','Last_Name','placeholder="Enter last name here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Marital_Status">Marital Status</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Marital_Status', 'select',$status); 
						?>
					</div>
					<div class="input f-right">
						<label for="Occupation">Occupation</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Occupation','text','Occupation','placeholder="Enter occupation here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Address">Address</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address','text','Address','placeholder="Enter address here"'); 
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
							$input->select('State', 'select',$state); 
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
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email','text','Email','placeholder="Enter email here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone','text','Phone','placeholder="Enter phone here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Best_day_to_contact">Best day to contact</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Best_day_to_contact', 'select',$best_day); 
						?>
					</div>
					<div class="input f-right">
						<label for="Best_time_to_contact">Best time to contact</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Best_time_to_contact', 'select',$best_time); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Drivers_License">Drivers License</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_License','text','Drivers_License','placeholder="Enter drivers license here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Licensed_State">Licensed State</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Licensed_State','text','Licensed_State','placeholder="Enter licensed state here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Year_Licensed">Year Licensed</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year_Licensed','text','Year_Licensed','placeholder="Enter year licensed here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Licensed_State">Licensed State</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Licensed_State','text','Licensed_State','placeholder="Enter licensed state here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Motorcycle_License_Endorsement_Date">Motorcycle License Endorsement Date</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Motorcycle_License_Endorsement_Date','text','Motorcycle_License_Endorsement_Date','placeholder="Enter date here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Has_license_been_suspended_revoked_or_canceled_in_the_last_3_years">Has license been suspended, revoked or canceled in the last 3 years?</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Has_license_been_suspended_revoked_or_canceled_in_the_last_3_years',array('Yes','No'));
						?>	
					</div>
					<div class="input f-right">
						<label for="Have_you_completed_an_accident_prevention_course_approved_by_the_motor_vehicle_department">Have you completed an accident prevention course approved by the motor vehicle department</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Have_you_completed_an_accident_prevention_course_approved_by_the_motor_vehicle_department',array('Yes','No'));
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Have_you_had_any_accidents_and_violations_in_the_past_3_years">Have you had any accidents and violations in the past 3 years?</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Have_you_had_any_accidents_and_violations_in_the_past_3_years',array('Yes','No'));
						?>	
					</div>
					<div class="input f-right">
						<label for="Have_you_been_convicted_of_a_DUI_in_the_past_10_years">Have you been convicted of a DUI in the past 10 years?</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Have_you_been_convicted_of_a_DUI_in_the_past_10_years',array('Yes','No'));
						?>	
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">MOTORCYCLE INFORMATION<input type="hidden" name="Motorcycle Information" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Year">Year</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year','text','Year','placeholder="Enter year here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Make">Make (ex. Honda, Suzuki)</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Make','text','Make','placeholder="Enter make here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Model">Model</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Model','text','Model','placeholder="Enter model here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="VIN">VIN</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('VIN','text','VIN','placeholder="Enter VIN here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Engine_Size_CC">Engine Size CC</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Engine_Size_CC','text','Engine_Size_CC','placeholder="Enter engine size cc here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Annual_Miles">Annual Miles</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Annual_Miles','text','Annual_Miles','placeholder="Enter annual miles here"'); 
						?>	
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">COVERAGE REQUESTED/DESIRED<input type="hidden" name="Coverage Requested/Desired" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Bodily_Injury">Bodily Injury</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Bodily_Injury','text','Bodily_Injury','placeholder="Enter bodily injury here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Property_Damage">Property Damage</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_Damage','text','Property_Damage','placeholder="Enter property damage here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Uninsured_or_Under-insured_Motorist_Bodily_Injury">Uninsured/Under-insured Motorist Bodily Injury</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Uninsured_or_Under-insured_Motorist_Bodily_Injury','text','Uninsured_or_Under-insured_Motorist_Bodily_Injury','placeholder="Enter uninsured/under-insured motorist bodily injury here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Uninsured_or_Under-insured_Property_Damage">Uninsured/Under-insured Property Damage</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Uninsured_or_Under-insured_Property_Damage','text','Uninsured_or_Under-insured_Property_Damage','placeholder="Enter uninsured/under-insured property damage  here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Medical_Payments">Medical Payments</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Medical_Payments','text','Medical_Payments','placeholder="Enter medical payments here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Comprehensive_Deductible">Comprehensive Deductible</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Comprehensive_Deductible','text','Comprehensive_Deductible','placeholder="Enter comprehensive deductible here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Collision_Deductible_">Collision Deductible</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Collision_Deductible_', 'select',$collision_deductible); 
						?>
					</div>
					<div class="input f-right">
						<label for="Custom_Equipment">Custom Equipment</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Custom_Equipment', 'select',$custom_equipment); 
						?>
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional_Comments">Additional Comments</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Comments', '','Additional_Comments','placeholder="Enter additional comments here" cols="88"'); 
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