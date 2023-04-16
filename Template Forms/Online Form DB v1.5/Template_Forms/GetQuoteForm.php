<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Request a Quote Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Daytime_Telephone']) ||				
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
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $formname; ?></title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {	
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			First_Name: "required",
			Last_Name: "required",
			Daytime_Telephone: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			First_Name: "Required",
			Last_Name: "Required",
			Daytime_Telephone: "Required",
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
							$input->fields('First_Name', 'text','First_Name','placeholder="Enter first name here"'); 
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
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Daytime_Telephone">Daytime Telephone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Daytime_Telephone', 'text','Daytime_Telephone','placeholder="Enter daytime telephone here"'); 
						?>						
					</div>
				</div>							
				<div class="field">
					<div class="input">
						<label for="Evening_Telephone">Evening Telephone</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Evening_Telephone', 'text','Evening_Telephone','placeholder="Enter evening telephone here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Best_Time_To_Reach_You">Best Time To Reach You</label>
						<?php 
							// @param field name, class, id and attribute
							$selections = array('- Please Select -', 'Mornings', 'Afternoons', 'Evening', 'Weekends', 'Anytime');
							$input->select('Best_Time_To_Reach_You', 'select',$selections); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Address">Address</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="City">City</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="State">State</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('State', 'select',$state); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Zip_Code">Zip Code</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip_Code', 'text','Zip_Code','placeholder="Enter zip code here"'); 
						?>						
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Any_Comments_or_Questions">Any Comments or Questions?</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Any_Comments_or_Questions', '','Any_Comments_or_Questions','placeholder="Enter comments or questions here" cols="88"'); 
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