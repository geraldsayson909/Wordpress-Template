<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Boat Insurance Form';
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
$form_state = 'Florida';	
$state = array('- Please Select -','Florida','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');


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
			Phone: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			First_Name: "Required",
			Last_Name: "Required",
			Phone: "Required",
			Email: "Enter a valid Email",
			secode: ""
		}
	});
    
	
	var curr_year = new Date().getFullYear();
	$('#DATE,#Date,#Date_of_Birth').datepick({
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
			<form id="submitform" name="contact" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				<div class="input textarea" style="background:#DEDEDE; color:#000; font-size:14px; font-weight:bold;text-align:center;">Customer Information <input type="hidden" name="Customer Information" value=":"/>
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
						<label for="Date_of_Birth"> Date of Birth </label>
						
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_of_Birth', 'text','DATE','placeholder="Enter date of birth here"'); 
						?>								
					</div>	
					<div class="input f-right">
						<label for="Gender">Gender </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Gender',array('Male','Female'));
						?>							
					</div>						
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Marital_Status">Marital Status </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Marital_Status',array('Single','Married and lives with spouse','Married but separated','Divorced','Widowed'));
						?>	
					</div>	
				</div>
				<div class="field">
					<div class="input">
						<label for="Occupation">Occupation </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Occupation', 'text','Occupation','placeholder="Enter occupation here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label></label>
											
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Email"> Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email address here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','Phone','placeholder="Enter phone here"'); 
						?>						
					</div>						
				</div>
				
				<div class="field">				
					<div class="input textarea">
						<label for="Best_day_to_contact"> Best day to contact </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Best_day_to_contact',array('Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'));
						?>		
					</div>	
				</div>
				
				
				<div class="field">				
					<div class="input textarea">
						<label for="Best_time_to_contact">Best time to contact </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Best_time_to_contact',array('Anytime','Morning','Afternoon','Evening'));
						?>	
					</div>	
				</div>
				<hr/>
				<div class="field">
					<div class="input">
						<label for="Drivers_License">Driver's License </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_License', 'text','Drivers_License','placeholder="Enter drivers license here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Licensed_State">Licensed State </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Licensed_State', 'select',$state); 
						?>							
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Years_Licensed">Years Licensed </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Years_Licensed', 'text','Years_Licensed','placeholder="Enter years licensed here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label></label>
										
					</div>						
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Have_you_had_any_boating_experience?">Have you had any boating experience?</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Have_you_had_any_boating_experience?',array('Yes','No'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Have_you_had_a_coastguard_or_power_squadron_course?">Have you had a coastguard or power squadron course?</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Have_you_had_a_coastguard_or_power_squadron_course?',array('Yes','No'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Have_you_had_any_accidents_and_violations_in_the_past_3_years?">Have you had any accidents and violations in the past 3 years?</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Have_you_had_any_accidents_and_violations_in_the_past_3_years?',array('Yes','No'));
						?>
					</div>	
				</div>
				<div class="input textarea" style="background:#DEDEDE; color:#000; font-size:14px; font-weight:bold;text-align:center;">Vessel Information<input type="hidden" name="Vessel Information" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Year_Purchased">Year Purchased </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year_Purchased', 'text','Year_Purchased','placeholder="Enter year purchased here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Purchased_Price">Purchased Price </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Purchased_Price', 'text','Purchased_Price','placeholder="Enter purchased price here"'); 
						?>	
										
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Vessel_Make">Make </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vessel_Make', 'text','Vessel_Make','placeholder="Enter vessel make here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Vessel_Model">Model </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vessel_Model', 'text','Vessel_Model','placeholder="Enter vessel model here"'); 
						?>	
										
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Hull_ID">Hull ID #  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Hull_ID', 'text','Hull_ID','placeholder="Enter hull ID here"'); 
						?>	
												
					</div>	
					<div class="input f-right">
						<label for="Hull">Hull </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Hull',array('fiberglass','aluminum','inflatable'));
						?>
										
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Mooring_Zipcode">Mooring Zipcode  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Mooring_Zipcode', 'text','Mooring_Zipcode','placeholder="Enter mooring zipcode here"'); 
						?>	
												
					</div>	
					<div class="input f-right">
						<label for="Number_of_Motors">Number of Motors </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Motors', 'text','Number_of_Motors','placeholder="Enter number of motors here"'); 
						?>	
										
					</div>						
				</div>
				
				<div class="field">				
					<div class="input textarea">
						<label for="Type_of_Propulsion">Type of Propulsion  </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Type_of_Propulsion',array('Inboard','Outboard','Jet'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="List_all_Safety_Equipments">List all Safety Equipments </label>
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('List_all_Safety_Equipments', '','List_all_Safety_Equipments','placeholder="Enter list all safety equipments here" cols="88"'); 
						?>	
					</div>	
				</div>
				<div class="input textarea" style="background:#DEDEDE; color:#000; font-size:14px; font-weight:bold;text-align:center;">Coverage Requested/Desired<input type="hidden" name="Coverage Requested Desired" value=":"/>
				</div>
				
				<div class="field">				
					<div class="input textarea">
						<label for="Bodily_Injury">Bodily Injury </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Bodily_Injury',array('$10,000-$50,000','$50,000-$100,000','$100,000-$150,000','$150,000-$200,000','$200,000-$250,000','$250,000-$300,000','$300,000-$350,000','$400,000-$450,000','$450,000-$500,000'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Property_Damage">Property Damage </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Property_Damage',array('$10,000-$50,000','$50,000-$100,000','$100,000-$150,000','$150,000-$200,000','$200,000-$250,000','$250,000-$300,000','$300,000-$350,000','$400,000-$450,000','$450,000-$500,000'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Uninsured_Under_insured_Motorist_Bodily_Injury">Uninsured/Under-insured Motorist Bodily Injury </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Uninsured_Under_insured_Motorist_Bodily_Injury',array('$10,000-$50,000','$50,000-$100,000','$100,000-$150,000','$150,000-$200,000','$200,000-$250,000','$250,000-$300,000','$300,000-$350,000','$400,000-$450,000','$450,000-$500,000'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Uninsured_Under_insured_Property_Damage">Uninsured/Under-insured Property Damage </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Uninsured_Under_insured_Property_Damage',array('$10,000-$50,000','$50,000-$100,000','$100,000-$150,000','$150,000-$200,000','$200,000-$250,000','$250,000-$300,000','$300,000-$350,000','$400,000-$450,000','$450,000-$500,000'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Comprehensive_Deductible">Comprehensive Deductible </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Comprehensive_Deductible',array('$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Collision_Deductible">Collision Deductible </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Collision_Deductible',array('$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Custom_Equipment">Custom Equipment </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Custom_Equipment',array('$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000'));
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Loss_of_Use">Loss of Use </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Loss_of_Use',array('$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000'));
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
				<hr />
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