<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Builders Risk Insurance Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Contact_First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Email']) ||				
		empty($_POST['Phone_Number']) ||	
		empty($_POST['Address_of_property']) ||	
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
		$name = $_POST['Contact_First_Name'].' '.$_POST['Last_Name'];
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
$property_features = array('- Please Select -','Dead Bolts','Fire Extinguisher','Trampoline','Covered Deck/Patio','Swimming Pool');
$policyperiod = array('- Please Select -','3 months','6 months','1 year');
$deductible = array('- Please Select -','1000','2000','3000','4000','5000','10000');
$homebuilder = array('- Please Select -','Home Owner','Builder','General Contractor');


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
			Contact_First_Name: "required",
			Last_Name: "required",
			Phone_Number: "required",
			Address_of_property: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		
		messages: {
			First_Name: "Required",
			Last_Name: "Required",
			Email: "Enter a valid Email",
			Phone_Number: "Required",
			Address_of_property: "Required",
			secode: ""
		}
	});
	$('#DATE,#Date,#Date_of_Birth').datepick({
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
						<label for="Contact_First_Name">Contact First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_First_Name','text','Contact_First_Name','placeholder="Enter first name here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="State">State</label>
							<?php 
								// @param field name, class, optname, id and attribute
								$input->select('State', 'select',$state); 
							?>
							
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Last_Name">Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name','text','Last_Name','placeholder="Enter last name here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Zip_Code">Zip Code</label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Zip_Code','text','Zip_Code','placeholder="Enter zip code here"'); 
							?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Business_Name">Business Name</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->fields('Business_Name','text','Business_Name','placeholder="Enter business name here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Phone_Number">Phone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
								$input->fields('Phone_Number','text','Phone_Number','placeholder="Enter phone number here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="DBA">DBA</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('DBA','text','DBA','placeholder="Enter DBA here"'); 
						?>	
					</div>
					<div class="input f-right">
							<label for="Fax_Number">Fax Number</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax_Number','text','Fax_Number','placeholder="Enter fax number here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Address_of_property">Address of property <span>*</span></label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Address_of_property','text','Address_of_property','placeholder="Enter address here"'); 
							?>	
					</div>
					<div class="input f-right">
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email','text','Email','placeholder="Enter email here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="City">City</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->fields('City','text','City','placeholder="Enter city here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Best_way_to_contact">Best way to contact</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Best_way_to_contact', 'select',$best_time); 
						?>
					</div>
				</div>
			
			<hr/>
				<div class="field">
					<div class="input">
						<label for="Are_you_the_Home_Owner,_a_Builder,_or_General_Contractor">Are you the Home Owner, a Builder, or General Contractor</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Are_you_the_Home_Owner,_a_Builder,_or_General_Contractor', 'select', $homebuilder); 
						?>	
					</div>
					<div class="input f-right">
						<label for="New_Constuction_or_Remodel"><br/>New Constuction or Remodel</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('New_Constuction_or_Remodel',array('New Construction','Remodel')); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Residential_or_Commerial">Residential or Commerial</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Residential_or_Commerial',array('Residential','Commerial'));
						?>	
					</div>
					<div class="input f-right">
						<label for="How_long_will_the_project_last">How long will the project last</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
								$input->fields('How_long_will_the_project_last','text','How_long_will_the_project_last','placeholder="Enter estimated schedule here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="What_will_be_the_total_value_of_the_compleated_project">What will be the total value of the compleated project</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('What_will_be_the_total_value_of_the_compleated_project','text','What_will_be_the_total_value_of_the_compleated_project','placeholder="Enter total value here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Constuction_Type"><br/>Constuction Type</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Constuction_Type',array('CBS','Frame')); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="What_is_the_value_of_Building_material_left_on_the_job_sight_at_any_time">What is the value of Building material left on the job sight at any time</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('What_is_the_value_of_Building_material_left_on_the_job_sight_at_any_time','text','What_is_the_value_of_Building_material_left_on_the_job_sight_at_any_time','placeholder="Enter value here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Policy period"><br/>Policy period</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Policy_period', 'select',$policyperiod); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Deductible">Deductible</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Deductible','select', $deductible); 
						?>	
					</div>
				</div>
				
				<div class="field">
					&nbsp;
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Do_you_want_Hurricane_coverage?">Do you want Hurricane coverage?</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->radio('Do_you_want_Hurricane_coverage?',array('Yes','No')); 
						?>
					</div>
					<div class="input f-right">
						<label for="Do_you_want_Liability_Insurance_for_the_project?">Do you want Liability Insurance for the project</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->radio('Do_you_want_Liability_Insurance_for_the_project?',array('Yes','No')); 
						?>
					</div>
				</div>
				
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional_Information">Additional Information</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Information', '','Additional_Information','placeholder="Enter additional information here" cols="88"'); 
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