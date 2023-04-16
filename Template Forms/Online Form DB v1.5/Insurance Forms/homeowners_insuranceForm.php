<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Homeowners Insurance Form';
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
$status = array('- Please Select -','Single','Married and lives with spouse','Married but separated','Divorced','Widowed');
$best_day = array('- Please Select -','Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$best_time = array('- Please Select -','Anytime','Morning','Afternoon','Evening');
$construction_type = array('- Please Select -','Mostly wood','Mostly brick','Stucco','Others');
$roof_type = array('- Please Select -','Asphalt Shingle','Wood Shingle','Tile','Concrete');
$primary_heating = array('- Please Select -','Gas','Electric','Hot water/steam','Coal/Oil/Kerosene','Propane','Stove');
$garage_type = array('- Please Select -','Built In','Attached to Home','Not Attached to Home','Carport','No Garage');
$security_system = array('- Please Select -','None','Monitored','Unmonitored');
$fire_alarm = array('- Please Select -','None','Monitored','Unmonitored');
$property_features = array('Dead Bolts','Fire Extinguisher','Trampoline','Covered Deck/Patio','Swimming Pool');
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
			<form id="submitform" name="contact" enctype="multipart/form-data" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">CUSTOMER INFORMATION<input type="hidden" name="Customer Information" value=":"/></div>					
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
						<label for="Date_of_Birth">Date of Birth</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_of_Birth','text','Date_of_Birth','placeholder="Enter date of birth here" '); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Gender">Gender</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Gender',array('Male','Female'));
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
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">PROPERTY/HOME DETAILS<input type="hidden" name="Property/Home Details" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Property_Type">Property Type</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_Type','text','Property_Type','placeholder="Enter property type here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Approximate_Year_Built">Approximate Year Built</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Approximate_Year_Built','text','Approximate_Year_Built','placeholder="Enter approximate year built here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Do_you_own_or_rent_this_property">Do you own or rent this property?</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Do_you_own_or_rent_this_property',array('Yes','No'));
						?>	
					</div>
					<div class="input f-right">
						<label for="Do_you_live_in_this_property">Do you live in this property?</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Do_you_live_in_this_property',array('Yes','No'));
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Property_Address">Property Address</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_Address','text','Property_Address','placeholder="Enter property address here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Property_apartment_or_unit">Property apt/unit</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_apartment_or_unit','text','Property_apartment_or_unit','placeholder="Enter property apt/unit here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Property_City">Property City</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_City','text','Property_City','placeholder="Enter property city here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Property_State">Property State</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Property_State', 'select',$state); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Property_Zip_Code">Property Zip Code</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_Zip_Code','text','Property_Zip_Code','placeholder="Enter property zip code here"'); 
						?>	
					</div>
				</div>
				
				<div class="field">
					&nbsp;
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Construction_Type">Construction Type</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Construction_Type', 'select',$construction_type); 
						?>
					</div>
					<div class="input f-right">
						<label for="Roof_Type">Roof Type</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Roof_Type', 'select',$roof_type); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Primary_Heating_System">Primary Heating System</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Primary_Heating_System', 'select',$primary_heating); 
						?>
					</div>
					<div class="input f-right">
						<label for="Number_of_Bedrooms">Number of Bedrooms</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Bedrooms','text','Number_of_Bedrooms','placeholder="Enter number of bedrooms here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Number_of_Bathrooms">Number of Bathrooms</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Bathrooms','text','Number_of_Bathrooms','placeholder="Enter number of bathrooms here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Number_of_Stories">Number of Stories</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Stories','text','Number_of_Stories','placeholder="Enter number of stories here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Garage_Type">Garage Type</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Garage_Type', 'select',$garage_type); 
						?>
					</div>
					<div class="input f-right">
						<label for="Approximate_square_footage">Approximate square footage</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Approximate_square_footage','text','Approximate_square_footage','placeholder="Enter approximate square footage here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Security_System">Security System</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Security_System', 'select',$security_system); 
						?>
					</div>
					<div class="input f-right">
						<label for="Fire_Alarm">Fire Alarm</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Fire_Alarm', 'select',$fire_alarm); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Additional_Property_Features">Select any additional property features that apply (optional):</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->chkbox('Additional_Property_Features',$property_features,'','',2);
						?>	
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">COVERAGE<input type="hidden" name="Coverage" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Liability_Limits">Liability Limits</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Liability_Limits','text','Liability_Limits','placeholder="Enter liability limits here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Deductible">Deductible</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Deductible','text','Deductible','placeholder="Enter deductible here"'); 
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