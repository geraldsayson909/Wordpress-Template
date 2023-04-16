<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Commercial Auto Insurance Form';
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
						<label for="First Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','First_Name','placeholder="Enter first name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Last Name">Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','Last_Name','placeholder="Enter last name here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Date of Birth"> Date of Birth </label>
						
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
						<label for="Marital Status">Marital Status </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Marital_Status',array('Single','Married and lives with spouse','Married but separated','Divorced','Widowed'));
						?>	
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
						<label for="Best day to contact"> Best day to contact </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Best_day_to_contact',array('Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'));
						?>		
					</div>	
				</div>

				<div class="field">				
					<div class="input textarea">
						<label for="Best time to contact">Best time to contact </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Best_time_to_contact',array('Anytime','Morning','Afternoon','Evening'));
						?>	
					</div>	
				</div>
				<div class="input textarea" style="background:#DEDEDE; color:#000; font-size:14px; font-weight:bold;text-align:center;">Business Information<input type="hidden" name="Business Information" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Business Name"> Business Name </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Business_Name', 'text','Business_Name','placeholder="Enter business name here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="Address">Address </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="City">City </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label for="State">State </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State', 'select',$state); 
						?>							
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Zip">Zip </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip', 'text','Zip','placeholder="Enter zip here"'); 
						?>							
					</div>	
					<div class="input f-right">
						<label></label>
												
					</div>						
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Year Business was established">Year Business was established  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year_Business_was_established', 'text','Year_Business_was_established','placeholder="Enter year business was established here"'); 
						?>	
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Describe Business Operation">Describe Business Operation </label>
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Describe_Business_Operation', '','Describe_Business_Operation','placeholder="Enter describe business operation here" cols="88"'); 
						?>
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Estimated Annual Gross">Estimated Annual Gross </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Estimated_Annual_Gross', 'text','Estimated_Annual_Gross','placeholder="Enter estimated annual gross here"'); 
						?>	
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Do you have more than one location?">Do you have more than one location? </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Do_you_have_more_than_one_location?',array('Yes','No'));
						?>	
					</div>	
				</div>
				<div class="input textarea" style="background:#DEDEDE; color:#000; font-size:14px; font-weight:bold;text-align:center;"><strong>Vehicle Information</strong><br /><i>(include all cars you or your business owns or leases)</i><input type="hidden" name="Vehicle Information" value=":"/>
				</div>
			
				<div class="input textarea" style="background:none; color:#000; font-size:14px; font-weight:bold;text-align:center;">1<input type="hidden" name="1" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Vehicle	Year">Vehicle Year </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Year1', 'text','Vehicle_Year1','placeholder="Enter vehicle year here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Make">Make </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Make1', 'text','Vehicle_Make1','placeholder="Enter vehicle make here"'); 
						?>						
					</div>						
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Model">Model</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Model1', 'text','Vehicle_Model1','placeholder="Enter vehicle model here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Vehicle_ID_no"> Vehicle ID#(VIN) </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_ID_no1', 'text','Vehicle_ID_no1','placeholder="Enter vehicle ID#(VIN) here"'); 
						?>						
					</div>						
				</div>
				<div class="input textarea" style="background:none; color:#000; font-size:14px; font-weight:bold;text-align:center;">2<input type="hidden" name="2" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Vehicle	Year">Vehicle Year </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Year2', 'text','Vehicle_Year2','placeholder="Enter vehicle year here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Make">Make </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Make2', 'text','Vehicle_Make2','placeholder="Enter vehicle make here"'); 
						?>						
					</div>						
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Model">Model</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Model2', 'text','Vehicle_Model2','placeholder="Enter vehicle model here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Vehicle_ID_no"> Vehicle ID#(VIN) </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_ID_no2', 'text','Vehicle_ID_no2','placeholder="Enter vehicle ID#(VIN) here"'); 
						?>						
					</div>						
				</div>
				<div class="input textarea" style="background:none; color:#000; font-size:14px; font-weight:bold;text-align:center;">3<input type="hidden" name="3" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Vehicle	Year">Vehicle Year </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Year3', 'text','Vehicle_Year3','placeholder="Enter vehicle year here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Make">Make </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Make3', 'text','Vehicle_Make3','placeholder="Enter vehicle make here"'); 
						?>						
					</div>						
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Model">Model</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Model3', 'text','Vehicle_Model3','placeholder="Enter vehicle model here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Vehicle_ID_no"> Vehicle ID#(VIN) </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_ID_no3', 'text','Vehicle_ID_no3','placeholder="Enter vehicle ID#(VIN) here"'); 
						?>						
					</div>						
				</div>
				<div class="input textarea" style="background:none; color:#000; font-size:14px; font-weight:bold;text-align:center;">4<input type="hidden" name="4" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Vehicle	Year">Vehicle Year </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Year4', 'text','Vehicle_Year4','placeholder="Enter vehicle year here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Make">Make </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Make4', 'text','Vehicle_Make4','placeholder="Enter vehicle make here"'); 
						?>						
					</div>						
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Model">Model</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_Model4', 'text','Vehicle_Model4','placeholder="Enter vehicle model here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Vehicle_ID_no"> Vehicle ID#(VIN) </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicle_ID_no4', 'text','Vehicle_ID_no4','placeholder="Enter vehicle ID#(VIN) here"'); 
						?>						
					</div>						
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="If_vehicle_is_kept_at_an_address_other_than_that_listed_above_please_indicate_location">If vehicle is kept at an address other than that listed above, please indicate location </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('If_vehicle_is_kept_at_an_address_other_than_that_listed_above_please_indicate_location', 'text','If_vehicle_is_kept_at_an_address_other_than_that_listed_above_please_indicate_location','placeholder="Enter indicate location here"'); 
						?>	
					</div>	
				</div>
				<div class="field">
					<div class="input">
						<label for="Full Coverage">Full Coverage </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Full_Coverage',array('Yes','No'));
						?>							
					</div>	
					<div class="input f-right">
						<label for="Seasonal Use">Seasonal Use </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Seasonal_Use', 'text','Seasonal_Use','placeholder="Enter seasonal use here"'); 
						?>						
					</div>						
				</div>
				<div class="input textarea" style="background:#DEDEDE; color:#000; font-size:14px; font-weight:bold;text-align:center;"><strong>Driver Information</strong><br /><i>(include all licensed drivers in your Business)</i><input type="hidden" name="Driver Information" value=":"/>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="Vehicles Used for">Vehicles Used for </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Vehicles_Used_for', 'text','Vehicles_Used_for','placeholder="Enter vehicles used for here"'); 
						?>
					</div>	
				</div>
				<div class="input textarea" style="background:none; color:#000; font-size:14px; font-weight:bold;text-align:center;">Driver 1<input type="hidden" name="Driver 1" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Drivers_Full_Name1">Full Name </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Full_Name1', 'text','Drivers_Full_Name1','placeholder="Enter full name here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_License_Number1">License Number </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_License_Number1', 'text','Drivers_License_Number1','placeholder="Enter license number here"'); 
						?>						
					</div>						
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Drivers_Years_Licensed1">Years Licensed</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Years_Licensed1', 'text','Drivers_Years_Licensed1','placeholder="Enter years licensed here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_Licensed_State1"> Licensed State </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Drivers_Licensed_State1', 'select',$state); 
						?>	
									
					</div>						
				</div>

				<div class="field">
					<div class="input">
						<label for="Drivers_Date_of_Birth1"> Date of Birth </label>
						
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Date_of_Birth1', 'text','DATE','placeholder="Enter date of birth here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_Gender1">Gender </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Drivers_Gender1',array('Yes','No'));
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Drivers_Marital_Status1">Marital Status </label>
						
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Marital_Status1', 'text','Drivers_Marital_Status1','placeholder="Enter marital status here"'); 
						?>								
					</div>	
					<div class="input f-right">
						<label></label>
										
					</div>						
				</div>
				<div class="input textarea" style="background:none; color:#000; font-size:14px; font-weight:bold;text-align:center;">Driver 2<input type="hidden" name="Driver 2" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Drivers_Full_Name2">Full Name </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Full_Name2', 'text','Drivers_Full_Name2','placeholder="Enter full name here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_License_Number2">License Number </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_License_Number2', 'text','Drivers_License_Number2','placeholder="Enter license number here"'); 
						?>						
					</div>						
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Drivers_Years_Licensed2">Years Licensed</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Years_Licensed2', 'text','Drivers_Years_Licensed2','placeholder="Enter years licensed here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_Licensed_State2"> Licensed State </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Drivers_Licensed_State2', 'select',$state); 
						?>	
									
					</div>						
				</div>

				<div class="field">
					<div class="input">
						<label for="Driver_Date_of_Birth2"> Date of Birth </label>
						
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Driver_Date_of_Birth2', 'text','DATE','placeholder="Enter date of birth here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_Gender2">Gender </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Drivers_Gender2',array('Yes','No'));
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Drivers_Marital_Status2">Marital Status </label>
						
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Marital_Status2', 'text','Drivers_Marital_Status2','placeholder="Enter marital status here"'); 
						?>								
					</div>	
					<div class="input f-right">
						<label></label>
										
					</div>						
				</div>
				<div class="input textarea" style="background:none; color:#000; font-size:14px; font-weight:bold;text-align:center;">Driver 3<input type="hidden" name="Driver 3" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Drivers_Full_Name3">Full Name </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Full_Name3', 'text','Drivers_Full_Name3','placeholder="Enter full name here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_License_Number3">License Number </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_License_Number3', 'text','Drivers_License_Number3','placeholder="Enter license number here"'); 
						?>						
					</div>						
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Drivers_Years_Licensed3">Years Licensed</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Years_Licensed3', 'text','Drivers_Years_Licensed3','placeholder="Enter years licensed here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_Licensed_State3"> Licensed State </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Drivers_Licensed_State3', 'select',$state); 
						?>	
									
					</div>						
				</div>

				<div class="field">
					<div class="input">
						<label for="Driver_Date_of_Birth3"> Date of Birth </label>
						
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Driver_Date_of_Birth3', 'text','DATE','placeholder="Enter date of birth here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_Gender3">Gender </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Drivers_Gender3',array('Yes','No'));
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Drivers_Marital_Status3">Marital Status </label>
						
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Marital_Status3', 'text','Drivers_Marital_Status3','placeholder="Enter marital status here"'); 
						?>								
					</div>	
					<div class="input f-right">
						<label></label>
										
					</div>						
				</div>
				<div class="input textarea" style="background:none; color:#000; font-size:14px; font-weight:bold;text-align:center;">Driver 4<input type="hidden" name="Driver 4" value=":"/>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Drivers_Full_Name4">Full Name </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Full_Name4', 'text','Drivers_Full_Name4','placeholder="Enter full name here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_License_Number4">License Number </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_License_Number4', 'text','Drivers_License_Number4','placeholder="Enter license number here"'); 
						?>						
					</div>						
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Drivers_Years_Licensed4">Years Licensed</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Years_Licensed4', 'text','Drivers_Years_Licensed4','placeholder="Enter years licensed here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_Licensed_State4"> Licensed State </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Drivers_Licensed_State4', 'select',$state); 
						?>	
									
					</div>						
				</div>

				<div class="field">
					<div class="input">
						<label for="Driver_Date_of_Birth4"> Date of Birth </label>
						
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Driver_Date_of_Birth4', 'text','DATE','placeholder="Enter date of birth here"'); 
						?>									
					</div>	
					<div class="input f-right">
						<label for="Drivers_Gender4">Gender </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Drivers_Gender4',array('Yes','No'));
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Drivers_Marital_Status4">Marital Status </label>
						
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_Marital_Status4', 'text','Drivers_Marital_Status4','placeholder="Enter marital status here"'); 
						?>								
					</div>	
					<div class="input f-right">
						<label></label>
										
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