<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Commercial Insurance Form';
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
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Therapist_Email_Address']))))
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
$marital= array('- Please Select -','Single','Married','Separated/Divorced','Remarried');
$occupancy = array('- Please Select -','Own','Rent');
$bestday = array('- Please Select -','Anyday', 'Weekdays', 'Weekend', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
$besttime = array('- Please Select -','Anytime', 'Morning', 'Afternoon', 'Evening');
$exterior_walls = array('- Please Select -','Double brick', 'Brick veneer', 'Timber', 'Weatherboard', 'Steel', 'Concrete', 'Fibro','Stone/Sandstone', 'Polystyrene'. 'Asbestos cement', 'Mud brick', 'Other construction');	
$roof = array('- Please Select -','Concrete tiles', 'Fibro', 'Metal/iron', 'Slate', 'tile', 'Copper', 'Tin', 'Shingle', 'Thatched', 'Other');
$property_type = array('- Please Select -','House', 'Villa/Townhouse', 'Unit', 'Caravan', 'Hotel/Motel/Hostel', 'Mobile Home','Retirement Village Unit/Villa', 'Nursing Home Unit/Villa', 'Guest House/Boarding House', 'Granny Flat', 'Other');
$security =array('- Please Select -','None', 'Monitored', 'Unmonitored');
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
    $('#Date_of_Birth').datepick({
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
				<div class="field">				
					<div class="input textarea">
						<div style="background:#0064b4; border-radius:5px;color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Customer Information<input type="hidden" name="Customer Information" value=":"/></div>  
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
						<label for="Date_of_Birth ">Date of Birth </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_of_Birth','text','Date_of_Birth','placeholder="Enter date of birth here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Gender">Gender</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Gender',array('Male','Female')); 
						?>						
					</div>					
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Marital_Status">Marital Status</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Marital_Status', 'select',$marital); 
						?>								
					</div>	
					<div class="input f-right">
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
								$input->fields('Phone','text','Phone','placeholder="Enter phone number here"'); 
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
					<div class="input">
						<label for="Best_day_to_contact">Best day to contact</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_day_to_contact', 'select',$bestday); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Best_time_to_contact">Best time to contact</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_time_to_contact', 'select',$besttime); 
						?>						
					</div>					
				</div>
				<div class="field">				
					<div class="input textarea">
						<div style="background:#0064b4; border-radius:5px;color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Property Information<input type="hidden" name="Property Information" value=":"/></div>  
					</div>	
				</div>
				<div class="field">
					<div class="input">
						<label for="Address">Address</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="City">City</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"'); 
						?>						
					</div>					
				</div>
				<div class="field">
					<div class="input">
						<label for="State">State</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State', 'select',$state); 
						?>						
					</div>		
					<div class="input f-right">
						<label for="Zip">Zip</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip', 'text','Zip','placeholder="Enter zip here"'); 
						?>							
					</div>	
				</div>	
				<div class="field">
					<div class="input">
						<label for="Year_building/property_was_built">Year building/property was built</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year_building/property_was_built', 'text','Year_building/property_was_built','placeholder="Enter year here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Occupancy">Occupancy</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Occupancy', 'select',$occupancy); 
						?>						
					</div>					
				</div>
				<div class="field">
					<div class="input">
						<label for="Approximate_Square_Footage">Approximate Square Footage</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Approximate_Square_Footage', 'text','Approximate_Square_Footage','placeholder="Enter square footage here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Number_of_Stories">Number of Stories</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Stories', 'text','Number_of_Stories','placeholder="Enter number of stories here"'); 
						?>						
					</div>					
				</div>
				<div class="field">
					<div class="input">
						<label for="Property_Type">Property Type</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Property_Type', 'select',$property_type); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Number_of_Stories">Exterior Walls</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Exterior_Walls', 'select',$exterior_walls); 
						?>						
					</div>					
				</div>
				<div class="field">
					<div class="input">
						<label for="Roof">Roof</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Roof', 'select',$roof); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Security_System">Security System</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Security_System', 'select',$security); 
						?>						
					</div>					
				</div>
				<div class="field">
					<div class="input">
						<label for="Is_your_property_managed_by_a_Licensed_Property_Management_agent?">Is your property managed by a Licensed Property Management agent?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Is_your_property_managed_by_a_Licensed_Property_Management_agent?', array('Yes','No')); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Is_your_property_part_of_a_Strata_title_place?">Is your property part of a Strata title place?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Is_your_property_part_of_a_Strata_title_place?', array('Yes','No')); 
						?>						
					</div>					
				</div>
				<div class="field">				
					<div class="input textarea">
						<div style="background:#0064b4; border-radius:5px;color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Additional Information<input type="hidden" name="Additional Information" value=":"/></div>  
					</div>	
				</div>
				<div class="field">
					<div class="input">
						<label for="Prior_Insurance">Prior Insurance</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Prior_Insurance', 'text','Prior_Insurance','placeholder="Enter prior insurance here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Length_of_Coverage">Length of Coverage</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Length_of_Coverage', 'text','Length_of_Coverage','placeholder="Enter length of coverage here"'); 
						?>						
					</div>					
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Describe_any_additional_requirements_or_specifics_about_your_insurance_needs">Please describe any additional requirements or specifics about your insurance needs. The more information you can provide here, the more accurately our vendors can be in providing quotes</label>
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Describe_any_additional_requirements_or_specifics_about_your_insurance_needs', '','Describe_any_additional_requirements_or_specifics_about_your_insurance_needs','placeholder="Enter description here" cols="88"'); 
						?>						
					</div>	
				</div>
				
				<div class="field">	
					<div class="verification">
						<img src="../securitycode/SecurityImages.php?characters=5" border="0" id ="securiryimage" alt="Security code" />
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