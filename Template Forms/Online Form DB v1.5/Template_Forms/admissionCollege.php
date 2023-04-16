<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Admission Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Full_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||				
		empty($_POST['State']) ||
		empty($_POST['Phone']) ||
		empty($_POST['Date_of_Birth']) ||
		empty($_POST['Age']) ||
		empty($_POST['Marital_Status']) ||
		empty($_POST['School_Last_Attended']) ||
		empty($_POST['City_of_School']) ||
		empty($_POST['School_State']) ||
		empty($_POST['Graduation_Date']) ||
		empty($_POST['Highest_Education_Level_Attained']) ||
		empty($_POST['GED_Scores']) ||
		empty($_POST['GED_School']) ||
		empty($_POST['GED_Date']) ||
		empty($_POST['Person_to_contact_Name']) ||
		empty($_POST['Person_to_contact_Address']) ||
		empty($_POST['Person_to_contact_Phone_Number']) ||
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
		
		
		$name = $_POST['Full_Name'];
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
$marital= array('- Please Select -','Single','Married','Divorced','Widowed');
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
			Full_Name: "required",
			Address: "required",
			City: "required",
			State: "required",
			Phone: "required",
			Date_of_Birth: "required",
			Age: "required",
			Marital_Status: "required",
			School_Last_Attended: "required",
			City_of_School: "required",
			School_State: "required",
			Graduation_Date: "required",
			Highest_Education_Level_Attained: "required",
			GED_Scores: "required",
			GED_School: "required",
			GED_Date: "required",
			Person_to_contact_Name: "required",
			Person_to_contact_Address: "required",
			Person_to_contact_Phone_Number: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			Full_Name: "Required",
			Address: "Required",
			City: "Required",
			State: "Required",
			Phone: "Required",
			Date_of_Birth: "Required",
			Age: "Required",
			Marital_Status: "Required",
			School_Last_Attended: "Required",
			City_of_School: "Required",
			School_State: "Required",
			Graduation_Date: "Required",
			Highest_Education_Level_Attained: "Required",
			GED_Scores: "Required",
			GED_School: "Required",
			GED_Date: "Required",
			Person_to_contact_Name: "Required",
			Person_to_contact_Address: "Required",
			Person_to_contact_Phone_Number: "Required",
			Email: "Enter a valid Email",
			secode: ""
		}
	});

     var curr_year = new Date().getFullYear();
    
    $('#DATE,#Date,#Date_of_Birth,#Graduation_Date,#GED_Date').datepick({
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
						<span style="color:#000; font-weight:bold;">Applicant's Information</span><input type="hidden" name="Applicant's Information" value=":" />
					</div>		
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Full_Name">Full Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Full_Name', 'text','Full_Name','placeholder="Enter full name here"'); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label for="Address">Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="City">City <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"'); 
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
						<label for="Email">Email Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email address here"'); 
						?>
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','Phone','placeholder="Enter phone here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Date_of_Birth">Date of Birth <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_of_Birth','text','Date_of_Birth','placeholder="Enter date of birth here"'); 
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
						<label for="Marital_Status">Marital Status <span>*</span></label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Marital_Status', 'select',$marital);
						?>
					</div>		
				</div>
				
				<div class="field">	
					<div class="input textarea">	
						<span style="color:#000; font-weight:bold;">Educational Background</span><input type="hidden" name="Educational Background" value=":" /><br/><span style="font-style:italic; font-size:10px; color:#000;">Please prepare official documents, transcripts, and/or GED test results which may be used during the evaluation</span>
					</div>		
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="School_Last_Attended">School Last Attended <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('School_Last_Attended', 'text','School_Last_Attended','placeholder="Enter school last attended here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="City_of_School">City <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City_of_School', 'text','City_of_School','placeholder="Enter city of the school here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="School_State">State <span>*</span></label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('School_State', 'select',$state); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Graduation_Date">Graduation Date <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Graduation_Date', 'text','Graduation_Date','placeholder="Enter graduation date here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Highest_Education_Level_Attained">Highest Education Level Attained <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Highest_Education_Level_Attained', 'text','Highest_Education_Level_Attained','placeholder="Enter highest education level attained here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="GED_Scores">GED Scores <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('GED_Scores', 'text','GED_Scores','placeholder="Enter GED scores here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="GED_School">GED School <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('GED_School', 'text','GED_School','placeholder="Enter GED school here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="GED_Date">GED Date <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('GED_Date', 'text','GED_Date','placeholder="Enter GED date here"'); 
						?>
					</div>
				</div>
				
				<div class="field">	
					<div class="input textarea">	
						<span style="color:#000; font-weight:bold;">Person to Contact in Case of Emergency</span><input type="hidden" name="Person to Contact in Case of Emergency" value=":" />
					</div>		
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Person_to_contact_Name">Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Person_to_contact_Name', 'text','Person_to_contact_Name','placeholder="Enter name here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Person_to_contact_Address">Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Person_to_contact_Address', 'text','Person_to_contact_Address','placeholder="Enter address here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Person_to_contact_Phone_Number">Phone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Person_to_contact_Phone_Number', 'text','Person_to_contact_Phone_Number','placeholder="Enter phone number here"'); 
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