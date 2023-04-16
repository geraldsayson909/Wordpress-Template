<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Facility Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Full_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||				
		empty($_POST['Zip']) ||	
		empty($_POST['How_do_you_prefer_to_be_contacted']) ||	
		empty($_POST['Email_Address']) ||	
		empty($_POST['Phone']) ||	
		empty($_POST['Best_time_to_call']) ||	
		empty($_POST['secode'])) {
				
	
	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';	
	$prompt_message = '<div id="error">'.$asterisk . ' Required Fields are empty</div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
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
$contact_options = array('- Please Select -','Phone','Fax','Email');
$best_time = array('- Please Select -','Anytime','Morning at Home','Morning at Work','Afternoon at Home','Afternoon at Work','Evening at Home','Evening at Work');
?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $formname; ?></title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<link rel="stylesheet" href="css/jquery.datepick.css" type="text/css" />
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
			Zip: "required",
			How_do_you_prefer_to_be_contacted: "required",
			Email_Address: {
				required: true,
				email: true
			},
			Phone: "required",
			Best_time_to_call: "required",
			secode: "required"		
		},
		messages: {
			Full_Name: "Required",
			Address: "Required",
			City: "Required",
			Zip: "Required",
			How_do_you_prefer_to_be_contacted: "Required",
			Email_Address: "Enter a valid Email",
			Phone: "Required",
			Best_time_to_call: "Required",
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
		$('#Preferred_date').datepick({
        yearRange: "1900:"+curr_year+"",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top: -30px;float: right; margin-right: 10px;" />'
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
						<label for="Full_Name">Full Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Full_Name', 'text','Full_Name','placeholder="Enter full name here"'); 
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
						<label for="State">State</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State', 'select',$state); 
						?>						
					</div>
				</div>							
				<div class="field">
					
					<div class="input">
						<label for="Zip">Zip <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip', 'text','Zip','placeholder="Enter zip here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="How_do_you_prefer_to_be_contacted">How do you prefer to be contacted? <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('How_do_you_prefer_to_be_contacted', 'select',$contact_options); 
						?>	
					</div>
			 </div>		
			 <div class="field">		
					<div class="input">
						<label for="Email_Address">Email Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email_Address', 'text','Email_Address','placeholder="Enter email address here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Fax">Fax</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax', 'text','Fax','placeholder="Enter fax here"');  
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
						<label for="Best_time_to_call">Best time to call <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_time_to_call', 'select',$best_time); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Preferred_date">Preferred Date</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Preferred_date', 'text','Preferred_date','placeholder="Enter preferred date here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Preferred_time">Preferred Time</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Preferred_time', 'text','Preferred_time','placeholder="Enter preferred time here"');  
						?>	
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Comments">Comments</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Comments', '','Comments','placeholder="Enter your comments here" cols="88"'); 
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