<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();


$formname = 'Verify Insurance Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Client_Full_Name']) ||
		empty($_POST['Client_Date_of_Birth']) ||
		empty($_POST['Primary_Insured_Name']) ||
		empty($_POST['Primary_Phone_Number']) ||
		empty($_POST['Street_Address_1']) ||
		empty($_POST['City']) ||
		empty($_POST['Zip_Code']) ||
		empty($_POST['State']) ||
		empty($_POST['Client_Primary_Phone_Number']) ||
		empty($_POST['Insurance_Provider']) ||
		empty($_POST['Insurance_Phone_Number']) ||
		empty($_POST['Insurance_Member_ID']) ||
		empty($_POST['Social_Security_Number']) ||
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
		$name = $_POST['Client_Full_Name'];
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
			Client_Full_Name: "required",
			Client_Date_of_Birth: "required",
			Primary_Insured_Name: "required",
			Primary_Phone_Number: "required",
			Street_Address_1: "required",
			City: "required",
			Zip_Code: "required",
			State: "required",
			Client_Primary_Phone_Number: "required",
			Insurance_Provider: "required",
			Insurance_Phone_Number: "required",
			Insurance_Member_ID: "required",
			Social_Security_Number: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			Client_Full_Name: "Required",
			Client_Date_of_Birth: "Required",
			Primary_Insured_Name: "Required",
			Primary_Phone_Number: "Required",
			Street_Address_1: "Required",
			City: "Required",
			Zip_Code: "Required",
			State: "Required",
			Client_Primary_Phone_Number: "Required",
			Insurance_Provider: "Required",
			Insurance_Phone_Number: "Required",
			Insurance_Member_ID: "Required",
			Social_Security_Number: "Required",
			Email: "Enter a valid Email",
			secode: ""
		}
	});
	 var curr_year = new Date().getFullYear();
	$('#DATE,#Date,#Client_Date_of_Birth').datepick({
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
						<label for="Client_Full_Name">Client Full Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Client_Full_Name', 'text','Client_Full_Name','placeholder="Enter client\'s full name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Client_Date_of_Birth">Client Date of Birth <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Client_Date_of_Birth', 'text','Client_Date_of_Birth','placeholder="Enter client\'s date of birth here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Primary_Insured_Name">Primary Insured Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Primary_Insured_Name', 'text','Primary_Insured_Name','placeholder="Enter primary insured name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Primary_Phone_Number">Primary Phone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Primary_Phone_Number', 'text','Primary_Phone_Number','placeholder="Enter primary phone number here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Street_Address_1">Street Address 1 <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Street_Address_1', 'text','Street_Address_1','placeholder="Enter street address 1 here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Street_Address_2">Street Address 2 </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Street_Address_2', 'text','Street_Address_2','placeholder="Enter street address 2 here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="City">City <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Zip_Code">Zip Code <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip_Code', 'text','Zip_Code','placeholder="Enter zip code here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="State">State <span>*</span></label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('State', 'select',$state); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Client_Primary_Phone_Number">Client Primary Phone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Client_Primary_Phone_Number', 'text','Client_Primary_Phone_Number','placeholder="Enter client primary phone number here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Insurance_Provider">Insurance Provider <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Insurance_Provider', 'text','Insurance_Provider','placeholder="Enter insurance provider here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Insurance_Phone_Number">Insurance Phone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Insurance_Phone_Number', 'text','Insurance_Phone_Number','placeholder="Enter insurance phone number here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Insurance_Member_ID">Insurance Member ID <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Insurance_Member_ID', 'text','Insurance_Member_ID','placeholder="Enter insurance member ID here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Insurance_Group_ID">Insurance Group ID </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Insurance_Group_ID', 'text','Insurance_Group_ID','placeholder="Enter insurance group ID here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Type_of_Plan">Type of plan(HMO or PPO) </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Type_of_Plan', 'text','Type_of_Plan','placeholder="Enter type of plan here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Social_Security_Number">Social Security Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Social_Security_Number', 'text','Social_Security_Number','placeholder="Enter social security number here"'); 
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