<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Health Insurance Form';
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
$status = array ('- Please Select -','Single','Married','Divorced','Widowed');
$daycontact = array ('- Please Select -','Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$timecontact = array ('- Please Select -','Anytime','Morning','Afternoon','Evening');
$tobacco = array('- Please Select -','Current User','Within past year','over 1 year ago','over 2 years ago','over 3 years ago','over 4 years ago','over 5 years ago');
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

			Last_Name: "required",
			First_Name: "required",

			Phone: "required",

			Email: {

				required: true,

				email: true

			},

			secode: "required"		

		},

		messages: {

			Last_Name: "Required",
			First_Name: "Required",

			Phone: "Required",

			Email: "Enter a valid Email",

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
    $('.Date').datepick({
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
					<div class="input textarea">
						<div style="background:#0065b1; border-radius:5px;color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Customer Information<input type="hidden" name="Customer Information" value=":"/></div>  
					</div>	
				</div>

				<div class="field">

					<div class="input textarea">

						<label for="First_Name">First Name <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('First_Name', 'text','First_Name','placeholder="Enter first name here"'); 

						?>						

					</div>		

				</div>	
			<div class="field">

					<div class="input textarea">

						<label for="Last_Name">Last Name <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Last_Name', 'text','Last_Name','placeholder="Enter last name here"'); 

						?>						

					</div>		

				</div>	
		<div class="field">
			<div class="input textarea">

						<label for="Marital_Status">Marital Status</label>

						<?php 

							// @param field name, class, id and attribute

							$input->select('Marital_Status', 'select',$status); 

						?>						

					</div>					

				</div>	
				<div class="field">				

					<div class="input textarea">

						<label for="Occupation">Occupation</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Occupation', 'text','Occupation','placeholder="Enter occupation here"'); 

						?>	

					</div>	

				</div>
				<div class="field">				

					<div class="input textarea">

						<label for="Address">Address</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 

						?>	

					</div>	

				</div>
					<div class="field">				

					<div class="input textarea">

						<label for="City">City</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('City', 'text','City','placeholder="Enter city here"'); 

						?>	

					</div>	

				</div>
			<div class="field">

					<div class="input textarea">

						<label for="State">State</label>

						<?php 

							// @param field name, class, id and attribute

							$input->select('State', 'select',$state); 

						?>						

					</div>
				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Zip">Zip</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Zip', 'text','Zip','placeholder="Enter zip here"'); 

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

					<div class="input textarea">

						<label for="Phone">Phone <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Phone', 'text','Phone','placeholder="Enter phone here"'); 

						?>

					</div>		

				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Best_day_to_contact">Best day to contact</label>

						<?php 

							// @param field name, class, id and attribute

							$input->select('Best_day_to_contact', 'select',$daycontact); 

						?>						

					</div>
				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Best_time_to_contact">Best time to contact</label>

						<?php 

							// @param field name, class, id and attribute

							$input->select('Best_time_to_contact', 'select',$timecontact); 

						?>						

					</div>
				</div>
				<hr/>
			<div class="field">

					<div class="input textarea">

						<label for="Date_of_Birth">Date of Birth <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Date_of_Birth', 'text Date','Date_of_Birth','placeholder="Enter date of birth here"'); 

						?>	

					</div>

				</div>	
				<div class="field">

					<div class="input textarea">

						<label for="Gender">Gender <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->radio('Gender', array('Male','Female')); 
						?>		
					</div>
				</div>	
		<div class="field">

					<div class="input textarea">

						<label for="Weight">Weight </label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Weight', 'text','Weight','placeholder="Enter weight here"'); 

						?>	

					</div>

				</div>	

				<div class="field">		

					<div class="input textarea">

						<label for="Height">Height</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Height', 'text','Height','placeholder="Enter height here"'); 

						?>

					</div>		
				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Tobacco_/_Nicotine_Use">Tobacco/Nicotine Use</label>

						<?php 

							// @param field name, class, id and attribute

							$input->select('Tobacco_/_Nicotine_Use', 'select',$tobacco); 

						?>						

					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Medications_currently_prescribed_and_any_health_history">Please list any medications currently prescribed and any health history </label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Medications_currently_prescribed_and_any_health_history', '','Medications_currently_prescribed_and_any_health_history','placeholder="Enter medications and any health history here" cols="88"'); 
						?>
					</div>		
				</div>
				<hr/>
				<div class="field">				
					<div class="input textarea">
						<div style="background:#0065b1; border-radius:5px;color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Spouse Information<input type="hidden" name="Spouse Information" value=":"/></div>  
					</div>	
				</div>
				<div class="field">

					<div class="input textarea">

						<label for="First_Name_">First Name</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('First_Name_', 'text','First_Name_','placeholder="Enter first name here"'); 

						?>						

					</div>		

				</div>	
				<div class="field">

					<div class="input textarea">

						<label for="Middle_Initial">Middle Initial</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Middle_Initial', 'text','Middle_Initial','placeholder="Enter middle initial here"'); 

						?>						

					</div>		

				</div>	
				<div class="field">

					<div class="input textarea">

						<label for="Last_Name_">Last Name</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Last_Name_', 'text','Last_Name_','placeholder="Enter last name here"'); 

						?>						

					</div>		

				</div>	
				<div class="field">

					<div class="input textarea">

						<label for="Date_of_Birth_">Date of Birth</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Date_of_Birth_', 'text Date','Date_of_Birth_','placeholder="Enter date of birth here"'); 

						?>	

					</div>

				</div>	
				<div class="field">				
					<div class="input textarea">
						<div style="background:#0065b1; border-radius:5px;color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Dependent Information<input type="hidden" name="Dependent Information" value=":"/></div>  
					</div>	
				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Number_of_children_to_be_covered">Number of children to be covered</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Number_of_children_to_be_covered', 'text','Number_of_children_to_be_covered','placeholder="Enter number of children here"'); 

						?>						

					</div>		

				</div>	
					<div class="field">

					<div class="input textarea">

						<label for="Ages_separated_by_comma">Ages separated by comma</label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Ages_separated_by_comma', 'text','Ages_separated_by_comma','placeholder="Enter ages here"'); 

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