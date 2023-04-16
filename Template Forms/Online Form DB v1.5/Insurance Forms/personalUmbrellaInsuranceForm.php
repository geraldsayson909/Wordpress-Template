<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Personal Insurance Form';
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
// $state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
// $contact_options = array('Phone','Fax','Email');
// $best_time = array('Anytime','Morning at Home','Morning at Work','Afternoon at Home','Afternoon at Work','Evening at Home','Evening at Work');
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
    $('.Date').datepick({
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
					<div class="input">
						<label for="First_Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','First_Name','placeholder="Enter full first name here"'); 
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
						<label for="Date_of_Birth">Date of Birth</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_of_Birth', 'text Date','Date_of_Birth','placeholder="Enter date of Birth here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Marital_Status">Marital Status</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Marital_Status','width: 250px;',array('- Please Select -','Single','Married','Divorced','Widowed'));
						?>	
					</div>
				</div>							
				<div class="field">
					<div class="input">
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Phone">Phone  <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','Phone','placeholder="Enter phone here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Best_day_to_contact">Best day to contact</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_day_to_contact','width: 250px;',array('- Please Select -','Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'));
						?>						
					</div>
					<div class="input f-right">
						<label for="Best_time_to_contact">Best time to contact</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_time_to_contact','width: 250px;',array('- Please Select -','Anytime','Morning','Afternoon','Evening'));
						?>	
					</div>
				</div>
				<hr />
				<div><strong>Dwelling Information</strong></div><br/>
				<div class="field">
					<div class="input">
						<label for="Number_of_dwellings_you_own_occupy">Number of dwellings you own/occupy</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_dwellings_you_own_occupy ', 'text','Number_of_dwellings_you_own_occupy ','placeholder="Enter number of dwellings here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Total_unit_count_of_all_rental_dwellings">Total unit count of all rental dwellings</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Total_unit_count_of_all_rental_dwellings', 'text','Total_unit_count_of_all_rental_dwellings','placeholder="Enter total unit count here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Number_of_vehicles_owned">Number of vehicles owned</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_vehicles_owned', 'text','Number_of_vehicles_owned','placeholder="Enter number of vehicles owned here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Number_of_company_provided_autos">Number of company provided autos</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_company_provided_autos', 'text','Number_of_company_provided_autos','placeholder="Enter number of company provided autos here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Number_of_watercraft_vehicles_owned">Number of watercraft vehicles owned (ex. Powerboats, sailboats,etc.)</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_watercraft_vehicles_owned', 'text','Number_of_watercraft_vehicles_owned','placeholder="Enter full number of watercraft vehicles owned here"'); 
						?>						
					</div>
				</div>	
				<hr />
				<div><strong>Driver Information</strong></div><br/>
				<div class="field">
					<div class="input">
						<label for="Number_of_drivers_in_the_household">Number of drivers in the household</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_drivers_in_the_household', 'text','Number_of_drivers_in_the_household','placeholder="Enter number of drivers here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="List_all_ages_of_all_drivers">List all ages of all drivers</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('List_all_ages_of_all_drivers', 'text','List_all_ages_of_all_drivers','placeholder="Enter list all ages here"'); 
						?>	
					</div>
				</div>
				<hr />
				<div><strong>Property Information</strong></div><br/>
				<div class="field">
					<div class="input">
						<label for="Number_of_propertyties_owned">Number of property(ties) owned</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_propertyties_owned', 'text','Number_of_propertyties_owned','placeholder="Enter Number of property(ties) owned here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Number_of_propertyties_rented_to_others">Number of property(ties) rented to others</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_propertyties_rented_to_others', 'text','Number_of_propertyties_rented_to_others','placeholder="Enter number of property(ties) rented to others here"'); 
						?>	
					</div>
				</div>
				<hr />
				<div><strong>Coverage Requested/Desired</strong></div><br/>
				<div class="field">
					<div class="input">
						<label for="Limit_of_Coverage">Limit of Coverage</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Limit_of_Coverage','width: 250px;',array('- Please Select -','$1 Million','$2 Million','$3 Million','$4 Million','$5 Million'));
						?>						
					</div>
					<div class="input f-right">
						<label for="Underlying_Auto_Limits">Underlying Auto Limits</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Underlying_Auto_Limits','width: 250px;',array('- Please Select -','$100,000','$200,000','$300,000','$400,000','$500,000','$600,000','$700,000','$800,000','$900,000','$1,000,000'));
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Underlying_Homeowners_Limits">Underlying Homeowners Limits</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Underlying_Homeowners_Limits','width: 250px;',array('- Please Select -','$100,000','$200,000','$300,000','$400,000','$500,000','$600,000','$700,000','$800,000','$900,000','$1,000,000'));
						?>						
					</div>
					<div class="input f-right">
						<label for="Underlying_Rental_Dwelling_Limits">Underlying Rental Dwelling Limits</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Underlying_Rental_Dwelling_Limits','width: 250px;',array('- Please Select -','$100,000','$200,000','$300,000','$400,000','$500,000','$600,000','$700,000','$800,000','$900,000','$1,000,000'));
						?>	
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Comments">Comments</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Comments', '','Comments','placeholder="Enter your questions or comments here" cols="88"'); 
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