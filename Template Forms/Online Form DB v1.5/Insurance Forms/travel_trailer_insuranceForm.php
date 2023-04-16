<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Travel Trailer Insurance Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Phone']) ||
		empty($_POST['Address']) ||
		empty($_POST['Fax']) ||
		empty($_POST['City']) ||
		empty($_POST['Email']) ||				
		empty($_POST['Zip_Code']) ||
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
$garage = array('- Please Select -','Built in','Attached to home','Not attached to home','Carport','No Garage');
$security = array('- Please Select -','None','Monitored','Unmonitored');
$fire = array('- Please Select -','None','Monitored','Unmonitored');
$ownership_status = array('- Please Select -','Paid For','Financed','Leased');
$bodily = array('- Please Select -','$10,000-$50,000','$50,000-$100,000','$100,000-$150,000','$150,000-$200,000','$200,000-$250,000','$250,000-$300,000','$300,000-$350,000','$400,000-$450,000','$450,000-$500,000');
$property = array('- Please Select -','$10,000-$50,000','$50,000-$100,000','$100,000-$150,000','$150,000-$200,000','$200,000-$250,000','$250,000-$300,000','$300,000-$350,000','$400,000-$450,000','$450,000-$500,000');
$uninsured_bodily = array('- Please Select -','$10,000-$50,000','$50,000-$100,000','$100,000-$150,000','$150,000-$200,000','$200,000-$250,000','$250,000-$300,000','$300,000-$350,000','$400,000-$450,000','$450,000-$500,000');
$uninsured_property = array('- Please Select -','$10,000-$50,000','$50,000-$100,000','$100,000-$150,000','$150,000-$200,000','$200,000-$250,000','$250,000-$300,000','$300,000-$350,000','$400,000-$450,000','$450,000-$500,000');
$comprehensive = array('- Please Select -','$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');
$collision = array('- Please Select -','$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');
$custom_equipment = array('- Please Select -','$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');
$Best_way = array('- Please Select -','Phone','Fax','Email');
$loss = array('- Please Select -','$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');

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
			Zip_Code: "required",
			Last_Name: "required",
			Phone: "required",
			Address: "required",
			Fax: "required",
			City: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			First_Name: "Required",
			Zip_Code: "Required",
			Last_Name: "Required",
			Phone: "Required",
			Address: "Required",
			Fax: "Required",
			City: "Required",
			Email: "Enter a valid Email",
			secode: ""
		}
	});

	var curr_year = new Date().getFullYear();
	$('#DATE,#Date,#Date_of_Birth,#Current_Policy_Expiration_Date,#Birth_Date, .Date').datepick({
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
					<div class="input">
						
						<label for="First_Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name','text','First_Name','placeholder="Enter first name here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Zip_Code">Zip Code <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip_Code','text','Zip_Code','placeholder="Enter zip code here"'); 
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
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone','text','Phone','placeholder="Enter phone here"'); 
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
						<label for="Fax">Fax <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax','text','Fax','placeholder="Enter fax here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="City">City <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City','text','City','placeholder="Enter city here"'); 
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
						<label for="State">State </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Licensed_State', 'select',$state); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Best_way_to_contact_you">Best way to contact you </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_way_to_contact_you', 'select',$Best_way); 
						?>	
					</div>
				</div>
				<hr/>
				
				<div class="field">
					<div class="input">
						<label for="Current_Insurance_Company">Current Insurance Company </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Insurance_Company','text','Current_Insurance_Company','placeholder="Enter current insurance company here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Current_Policy_Expiration_Date">Current Policy Expiration Date </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Policy_Expiration_Date','text','Current_Policy_Expiration_Date','placeholder="Enter date here" '); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Number_of_Years_Insured">Number of Years Insured </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Years_Insured','text','Number_of_Years_Insured','placeholder="Enter number of years insured here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label></label>
						
					</div>
				</div>
				<hr/>
				<div class="field">
					<div class="input">
						<label for="Travel_Trailer_Year">Travel Trailer - Year </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Travel_Trailer_Year','text','Travel_Trailer_Year','placeholder="Enter travel trailer - year here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Travel_Trailer_Make">Travel Trailer - Make </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Travel_Trailer_Make','text','Travel_Trailer_Make','placeholder="Enter travel trailer - make here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Travel_Trailer_Model">Travel Trailer - Model </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Travel_Trailer_Model','text','Travel_Trailer_Model','placeholder="Enter travel trailer - model here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Serial_Number_of_Boat">Serial number of boat </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Serial_Number_of_Boat','text','Serial_Number_of_Boat','placeholder="Enter serial number of boat here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Travel_Trailer_Length">Travel Trailer - Length (in feet) </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Travel_Trailer_Length','text','Travel_Trailer_Length','placeholder="Enter travel trailer - length (in feet) here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Current_Boat_Value">Current Boat Value </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Boat_Value','text','Current_Boat_Value','placeholder="Enter current boat value here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Year_of_Engine_One">Year of Engine One </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year_of_Engine_One','text','Year_of_Engine_One','placeholder="Enter year of engine one here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Travel_Trailer_purchase_price">Travel Trailer - purchase price </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Travel_Trailer_purchase_price','text','Travel_Trailer_purchase_price','placeholder="Enter travel trailer - purchase price here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Engine_size">Engine size (Horsepower) </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Engine_size','text','Engine_size','placeholder="Enter engine size here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Location_Boat">Location Boat </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Location_Boat','text','Location_Boat','placeholder="Enter location boat here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Year_of_Engine_Two">Year of Engine Two </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year_of_Engine_Two','text','Year_of_Engine_Two','placeholder="Enter year of engine Two here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Primary_Stored">Primary Stored </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Primary_Stored','text','Primary_Stored','placeholder="Enter primary stored here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Engine_Two_size">Engine Two size </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Engine_Two_size','text','Engine_Two_size','placeholder="Enter engine two size here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Stored_A_float">Stored A float </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Stored_A_float','text','Stored_A_float','placeholder="Enter stored a float here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label></label>

					</div>
					<div class="input f-right">
						<label for="Range_of_Navigation">Range of Navigation </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Range_of_Navigation','text','Range_of_Navigation','placeholder="Enter range of navigation here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Trailor_Year">Trailor Year </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Trailor_Year','text','Trailor_Year','placeholder="Enter trailor year here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Trailor_Model">Trailor Model </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Trailor_Model','text','Trailor_Model','placeholder="Enter trailor model here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Trailor_Make">Trailor Make </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Trailor_Make','text','Trailor_Make','placeholder="Enter trailor make here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Tailor_Current_Value">Tailor Current Value </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Tailor_Current_Value','text','Tailor_Current_Value','placeholder="Enter tailor current value here"'); 
						?>	
						
					</div>
				</div>
				<hr/>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:none; color:#000; font-size:12px; font-weight:bold; padding:3px 10px;">Driver 1<input type="hidden" name="Driver 1" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Name_of_Driver">Name of Driver </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Name_of_Driver','text','Name_of_Driver','placeholder="Enter name of driver here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Driver_Last_Name ">Last Name  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Driver_Last_Name','text','Driver_Last_Name','placeholder="Enter last name here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Birth_Date">Birth Date </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Birth_Date','text Date','Birth_Date','placeholder="Enter birth date here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Gender">Gender  </label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Gender',array('Female','Male'));
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Years_Boat_Ownership">Years Boat Ownership </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Years_Boat_Ownership','text','Years_Boat_Ownership','placeholder="Enter years boat ownership here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Years_Boating_Experience">Years Boating Experience  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Years_Boating_Experience','text','Years_Boating_Experience','placeholder="Enter years boating experience here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Have_any_operators_completed_a_Boating_safety_course">Have any operators completed a boating safety course</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Have_any_operators_completed_a_Boating_safety_course','text','Have_any_operators_completed_a_Boating_safety_course','placeholder=""'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Describe_all_marine_insurance_loses">Describe all marine insurance loses</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Describe_all_marine_insurance_loses','text','Describe_all_marine_insurance_loses','placeholder="Describe all marine insurance loses here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Drivers_License_Number">Driver's License Number </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_License_Number','text','Drivers_License_Number','placeholder="Enter driver\'s license number here"'); 
						?>	
						
					</div>
				</div>
				<hr/>
				<div class="field">
					<div class="input textarea">
						<div style="background:none; color:#000; font-size:12px; font-weight:bold; padding:3px 10px;">Driver 2<input type="hidden" name="Driver 2" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Name_of_Driver_">Name of Driver </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Name_of_Driver_','text','Name_of_Driver_','placeholder="Enter name of driver here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Driver_Last_Name_">Last Name  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Driver_Last_Name_','text','Driver_Last_Name_','placeholder="Enter last name here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Birth_Date_">Birth Date </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Birth_Date_','text Date','Birth_Date_','placeholder="Enter birth date here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Gender_">Gender  </label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Gender_',array('Female','Male'));
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Years_Boat_Ownership_">Years Boat Ownership </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Years_Boat_Ownership_','text','Years_Boat_Ownership_','placeholder="Enter years boat ownership here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Years_Boating_Experience_">Years Boating Experience  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Years_Boating_Experience_','text','Years_Boating_Experience_','placeholder="Enter years boating experience here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Have_any_operators_completed_a_Boating_safety_course_">Have any operators completed a boating safety course</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Have_any_operators_completed_a_Boating_safety_course_','text','Have_any_operators_completed_a_Boating_safety_course_','placeholder=""'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Describe_all_marine_insurance_loses_">Describe all marine insurance loses</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Describe_all_marine_insurance_loses_','text','Describe_all_marine_insurance_loses_','placeholder="Describe all marine insurance loses here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Drivers_License_Number_">Driver's License Number </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Drivers_License_Number_','text','Drivers_License_Number_','placeholder="Enter driver\'s license number here"'); 
						?>	
						
					</div>
				</div>
				
				<hr/>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:none; color:#000; font-size:12px; font-weight:bold; padding:3px 10px;">Coverage Request<input type="hidden" name="Coverage Request" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Liability_Limit">Liability Limit</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Liability_Limit','text','Liability_Limit','placeholder="Enter liability limit here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Property_Damage">Property Damage </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_Damage','text','Property_Damage','placeholder="Enter property damage here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Medical_Payment">Medical Payment</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Medical_Payment','text','Medical_Payment','placeholder="Enter medical payment here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Collision_Deductible">Collision Deductible </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Collision_Deductible','text','Collision_Deductible','placeholder="Enter collision deductible here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Comprehensive_Deductible">Comprehensive Deductible</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Comprehensive_Deductible','text','Comprehensive_Deductible','placeholder="Enter comprehensive deductible here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label></label>
						
					</div>
				</div>
				<hr/>
			
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional_Information ">Additional Information </label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Information', '','Additional_Information','placeholder="Enter additional information here" cols="88"'); 
						?>
					</div>		
				</div>
				<hr/>
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