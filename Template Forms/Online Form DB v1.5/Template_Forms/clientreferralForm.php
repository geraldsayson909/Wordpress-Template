<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Client Referral Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Your_Name']) ||
		empty($_POST['Your_Organization']) ||
		empty($_POST['First_Name']) ||
		empty($_POST['Tel_No']) ||				
		empty($_POST['Clients_Last_Name']) ||				
		empty($_POST['First_Name']) ||				
		empty($_POST['Tel_No_2']) ||				
		empty($_POST['Contact_Person']) ||				
		empty($_POST['Contact_Persons_Tel_No']) ||				
		empty($_POST['Clients_Address']) ||				
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
		
		
		$name = $_POST['Your_Name'];
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

$ii = array('- Please Select -','MEDICARE','PUBLIC AIDE','PRIVATE INSURANCE','SELF PAY');
$month = array('Month','January','February','March','April','May','June','July','August','September','October','November','December');
$lives = array('- Please Select -','House/Apartment','Assisted/Supportive Living','Senior Housing','Group Home','Rented Room','None of the Above');

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
			Your_Name: "required",
			Your_Organization: "required",
			First_Name: "required",
			Tel_No: "required",
			Clients_Last_Name: "required",
			First_Name: "required",
			Tel_No_2: "required",
			Contact_Person: "required",
			Contact_Persons_Tel_No: "required",
			Clients_Address: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		
		messages: {
			Your_Name: "Required",
			Your_Organization: "Required",
			First_Name: "Required",
			Tel_No: "Required",
			Clients_Last_Name: "Required",
			First_Name: "Required",
			Tel_No_2: "Required",
			Contact_Person: "Required",
			Contact_Persons_Tel_No: "Required",
			Clients_Address: "Required",
			Email: "Enter a valid Email",
			secode: ""
		}
	});
	
	var curr_year = new Date().getFullYear();		
	$('#DATE,#Date,#Date_of_Birth').datepick({
        yearRange: "1900:"+curr_year+"",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top: -30px;float: right; margin-right: 10px;" />'
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
						<label for="Referrer"><strong><u>Referrer</u></strong></label>						
					</div>		
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="Your Name">Your Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Your_Name', 'text','Name','placeholder="Enter your name here"'); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="Your Organization">Your Organization <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Your_Organization', 'text','Your_Organization','placeholder="Enter your organization here"'); 
						?>						
					</div>						
				</div>	
				<div class="field">
					<div class="input textarea">
							<label for="Tel No">Telephone Number <span>*</span></label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Tel_No', 'text','Tel_No','placeholder="Enter telephone number here"'); 
							?>						
					</div>		
				</div>
				
				<hr />
				
				<div class="field">
					<div class="input textarea">
						<label for="Client's Last Name">Client's Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Clients_Last_Name', 'text','Clients_Last_Name','placeholder="Enter client\'s last name here"'); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="First Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','First_Name','placeholder="Enter first name here"'); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="Tel. No.">Telephone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Telephone_Number_2', 'text','Telephone_Number_2','placeholder="Enter telephone number here"'); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="Contact Person">Contact Person <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_Person', 'text','Contact_Person','placeholder="Enter contact person here"'); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="Contact Person's Tel. No.">Contact Person's Telephone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_Persons_Tel_No', 'text','Contact_Persons_Tel_No','placeholder="Enter contact persons telephone number here"'); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="Client's Address">Client's Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Clients_Address', 'text','Clients_Address','placeholder="Enter clients address here"'); 
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
					<div class="input f-left">
						<label for="Insurance Information">Insurance Information</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Insurance_Information', 'select',$ii); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label for="Client's Date of Birth">Client's Date of Birth</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Clients_Date_of_Birth', 'text','DATE','placeholder="Enter clients date of birth here"'); 
						?>	
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label for="Client's Medicare Number">Client's Medicare Number</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Clients_Medicate_Number', 'text','Clients_Medicate_Number','placeholder="Enter clients medicare number here"'); 
						?>	
					</div>
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="Has the client ever recieved home health care service in the past">Has the client ever received home health care service in the past?  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Has_the_client_ever_received_home_health_care_service_in_the_past', array('YES','NO')); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input f-left">
						<label for="Client lives in a">Client lives in a</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Client_lives_in_a', 'select',$lives); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="Is the client able to drive a car safely on a regular basis?">Is the client able to drive a car safely on a regular basis?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Is_the_client_able_to_drive_a_car_safely_on_a_regular_basis', array('YES','NO')); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Does the client use any type of assistive device e.g. cane, walker, wheelchair?">Does the client use any type of assistive device e.g. cane, walker, wheelchair?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Does_the_client_use_any_type_of_assistive_device', array('YES','NO')); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Is the client willing to receive home health services?">Is the client willing to receive home health services?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Is_the_client_willing_to_receive_home_health_services', array('YES','NO')); 
						?>						
					</div>
				</div>
				<hr/>
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