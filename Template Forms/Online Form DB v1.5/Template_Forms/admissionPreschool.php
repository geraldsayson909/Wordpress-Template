<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Admission Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Childs_Full_Name']) ||
		empty($_POST['Birth_Date']) ||
		empty($_POST['Age']) ||				
		empty($_POST['Sex']) ||				
		empty($_POST['Parent_or_Guardians_Name']) ||				
		empty($_POST['Address']) ||				
		empty($_POST['State']) ||
		empty($_POST['Email']) ||			
		empty($_POST['Phone']) ||			
		empty($_POST['Name']) ||				
		empty($_POST['Address_']) ||				
		empty($_POST['Name_of_Childs_Doctor']) ||				
		empty($_POST['Phone_']) ||				
		empty($_POST['Hospital_Preference']) ||				
		empty($_POST['Please_list_down_any_allergies_or_dietary_restrictions_of_the_child']) ||
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
		$name = $_POST['Name'];
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
			Childs_Full_Name: "required",
			Birth_Date: "required",
			Age: "required",
			Sex: "required",
			Parent_or_Guardians_Name: "required",
			Address: "required",
			City: "required",
			State: "required",
			Phone: "required",
			Name: "required",
			Address_: "required",
			Name_of_Childs_Doctor: "required",
			Phone_: "required",
			Hospital_Preference: "required",
			Email: {
				required: true,
				email: true
			},
			Please_list_down_any_allergies_or_dietary_restrictions_of_the_child: "required",
			secode: "required"		
		},
		messages: {
			Childs_Full_Name: "Required",
			Birth_Date: "Required",
			Age: "Required",
			Sex: "Required",
			Parent_or_Guardians_Name: "Required",
			Address: "Required",
			City: "Required",
			State: "Required",
			Phone: "Required",
			Email: "Enter a valid Email",
			Name: "Required",
			Address_: "Required",
			Name_of_Childs_Doctor: "Required",
			Phone_: "Required",
			Hospital_Preference: "Required",
			Please_list_down_any_allergies_or_dietary_restrictions_of_the_child: "Required",
			secode: ""
		}
	});
	
	 var curr_year = new Date().getFullYear();
	 
	$('#Birth_Date').datepick({
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
			<form id="submitform" name="contact" enctype="multipart/form-data" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				
				<div class="field">
					<div class="input textarea">
						<div style="font-weight:bold;">Child's Information<input type="hidden" name="Child's Information" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Childs_Full_Name">Child's Full Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Childs_Full_Name', 'text','Childs_Full_Name','placeholder="Enter child\'s full name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Birth_Date">Birth Date <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Birth_Date', 'text','Birth_Date','placeholder="Enter birth date here"'); 
						?>						
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Age">Age <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Age', 'text','Age','placeholder="Enter age here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Sex">Sex <span>*</span></label>
						<?php 
							// @param field name, value, id, attribute and rows
							$input->radio('Sex',array('Male','Female'));
						?>						
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="font-weight:bold;">Parent/Guardian's Information<input type="hidden" name="Parent/Guardian's Information" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Parent_or_Guardians_Name">Parent/Guardian's Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Parent_or_Guardians_Name', 'text','Parent_or_Guardians_Name','placeholder="Enter parent/guardians name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Address">Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
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
						<label for="State">State <span>*</span></label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('State', 'select',$state); 
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
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','Phone','placeholder="Enter phone here"'); 
						?>					
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="font-weight:bold;">Person to Contact in Case of Emergency if Parent/Guardian Cannot be Reached<input type="hidden" name="Person to Contact in Case of Emergency if Parent/Guardian Cannot be Reached" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Name">Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Name', 'text','Name','placeholder="Enter name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Address">Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address_', 'text','','placeholder="Enter address here"'); 
						?>					
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="font-weight:bold;">Health Information<input type="hidden" name="Health Information" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Name_of_Childs_Doctor">Name of Child's Doctor <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Name_of_Childs_Doctor', 'text','Name_of_Childs_Doctor','placeholder="Enter name of child\'s doctor here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone_', 'text','Phone_','placeholder="Enter phone here"'); 
						?>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Hospital_Preference">Hospital Preference <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Hospital_Preference', 'text','Hospital_Preference','placeholder="Enter hospital preference here"'); 
						?>						
					</div>	
				</div>
				
				<div class="field">	
					<div class="input textarea">	
						<label for="Please_list_down_any_allergies_or_dietary_restrictions_of_the_child">Please list down any allergies or dietary restrictions of the child <span>*</span></label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Please_list_down_any_allergies_or_dietary_restrictions_of_the_child', '','Please_list_down_any_allergies_or_dietary_restrictions_of_the_child','placeholder="Enter any allergies or dietary restrictions of the child here" cols="88"'); 
						?>
					</div>		
				</div>
				
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional_Information">Additional Information </label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Information', '','','placeholder="Enter additional information here" cols="88"'); 
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