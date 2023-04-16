<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Condo Insurance Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Phone']) ||				
		empty($_POST['Email']) ||	
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
$Marital_Status = array('- Please Select -','Single','Married and lives with spouse','Married but separated','Divorced','Widowed');
$contact = array('- Please Select -','Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$time = array('- Please Select -','Anytime','Morning','Afternoon','Evening');
$SecuritySystem =  array('None','Monitored','Unmonitored');
$FireAlarm =  array('- Please Select -','None','Monitored','Unmonitored');
$Liability_Protection = array('- Please Select -','$100,000','$200,000','$300,000','$400,000','$500,000','$600,000','$700,000','$800,000','$900,000','$1,000,000');
$Deductible= array('- Please Select -','$1,000-$5,000','$5,000-$10,000','$10,000-$15,000','$15,000-$20,000','$20,000-$25,000','$25,000-$30,000','$30,000-$35,000','$35,000-$40,000','$40,000-$45,000','$45,000-$50,000');
$Personal_Property = array('- Please Select -','$1,000-$5,000','$5,000-$10,000','$10,000-$15,000','$15,000-$20,000','$20,000-$25,000','$25,000-$30,000','$30,000-$35,000','$35,000-$40,000','$40,000-$45,000','$45,000-$50,000');
$Loss_of_Use = array('- Please Select -','$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');


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
	$("#submitform").submit(function(){
		if($(this).valid()){
			self.parent.$('html, body').animate(
				{ scrollTop: self.parent.$('#myframe').offset().top },
				500
			);
		}
	});
	
	
	var curr_year = new Date().getFullYear();
	$('#Date_of_Birth').datepick({
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
					<div class="input">
						<label>First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','','placeholder="Enter first name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','','placeholder="Enter last name here"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Date of Birth</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_of_Birth', 'text','Date_of_Birth','placeholder="Enter date of birth here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Gender</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Gender', array('Male','Female')); 
						?>						
					</div>
				</div>


				<div class="field">
					<div class="input">
						<label>Marital Status</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Marital_Status', 'select', $Marital_Status); 
						?>						
					</div>
					<div class="input f-right">
						<label>Occupation</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Occupation', 'text','','placeholder="Enter occupation here"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','','placeholder="Enter email here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','','placeholder="Enter phone here"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Best day to contact</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_day_to_contact', 'select', $contact); 
						?>						
					</div>
					<div class="input f-right">
						<label>Best time to contact </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_time_to_contact', 'select', $time); 
						?>						
					</div>
				</div>

				<hr />
				
				<div class="field">
					<p style="font-weight: bold;">Dwelling Information</p> <input type="hidden" value=":" name="Dwelling Information">
				</div>

				<div class="field">
					<div class="input">
						<label>Approximate Year Built</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Approximate_Year_Built', 'text','','placeholder="Enter approximate year built here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Approximate Square Footage </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Approximate_Square_Footage', 'text','','placeholder="Enter approximate square footage here"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Address</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','','placeholder="Enter address here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>City </label> 
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','','placeholder="Enter licence class here"'); 
						?>						
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label>State</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State', 'select', $state); 
						?>						
					</div>
					<div class="input f-right">
						<label>Zip</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip', 'text','','placeholder="Enter zip shere"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Number of Units</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Units', 'text','','placeholder="Enter number of units here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Number of Stories</label> 
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Stories', 'text','','placeholder="Enter number of stories here"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Number of Bedrooms</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Bedrooms', 'text','','placeholder="Enter number of bedrooms here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Number of Bathrooms</label> 
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Bathrooms', 'text','','placeholder="Enter number of bathrooms here"'); 
						?>						
					</div>
				</div>		

				<div class="field">
					<div class="input">
						<label>Security System</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Security_System', 'select', $SecuritySystem); 
						?>						
					</div>
					<div class="input f-right">
						<label>Fire Alarm</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Fire_Alarm', 'select', $FireAlarm); 
						?>					
					</div>
				</div>	
				<hr />
				
				<div class="field">
					<p style="font-weight: bold;">Coverage Desired/Requested</p> <input type="hidden" value=":" name="Coverage Desired/Requested">
				</div>
				
				<div class="field">
					<div class="input">
						<label>Liability Protection</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Liability_Protection', 'select', $Liability_Protection) 
						?>						
					</div>
					<div class="input f-right">
						<label>Deductible</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Deductible', 'select', $Deductible) 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label>Personal Property</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Personal_Property', 'select', $Personal_Property) 
						?>						
					</div>
					<div class="input f-right">
						<label>Loss of Use</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Loss_of_Use', 'select', $Loss_of_Use) 
						?>						
					</div>
				</div>	
				
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional Comments">Additional Comments</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Comments', '','Additional_Comments','placeholder="Enter additional comments here" cols="88"'); 
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