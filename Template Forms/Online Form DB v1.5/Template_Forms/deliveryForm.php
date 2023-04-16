<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Delivery Areas Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Phone_Number']) ||				
		empty($_POST['Event_Location']) ||				
		empty($_POST['Rental_Date']) ||				
		empty($_POST['Drop_Off_Time']) ||				
		empty($_POST['When_would_you_like_us_to_pick_up']) ||				
		empty($_POST['Pick_Up_Time']) ||				
		empty($_POST['Email']) ||	
		empty($_POST['secode'])
		) {
				
	
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
		$name = $_POST['First_Name'];
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
$dropoff = array('- Please Select -','8:00am','8:30am','9:00am','9:30am','10:00am','10:30am','11:00am','11:30am','12:00pm','12:30pm','1:00pm','1:30pm','2:00pm','2:30pm','3:00pm');
$day = array('- Please Select -','Same Day','+1 day(s)','+2 day(s)','+3 day(s)','+4 day(s)','+5 day(s)','+6 day(s)','+7 day(s)','+8 day(s)','+9 day(s)','+10 day(s)');
$pickup = array('- Please Select -','12:00pm','12:30pm','1:00pm','1:30pm','2:00pm','2:30pm','3:00pm','3:30pm','4:00pm','4:30pm','5:00pm','5:30pm','6:00pm','6:30pm','7:00pm','7:30pm','8:00pm');
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
			Phone_Number: "required",
			Event_Location: "required",
			Rental_Date: "required",
			Drop_Off_Time: "required",
			When_would_you_like_us_to_pick_up: "required",
			Pick_Up_Time: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			First_Name: "Required",
			Last_Name: "Required",
			Phone_Number: "Required",
			Rental_Date: "Required",
			Drop_Off_Time: "Required",
			When_would_you_like_us_to_pick_up: "Required",
			Pick_Up_Time: "Required",
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
	$('#Rental_Date').datepick({
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
					<div class="input f-left">
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
					<div class="input f-left">
						<label for="Phone_Number">Phone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone_Number', 'text','Phone_Number','placeholder="Enter phone number here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email here"'); 
						?>						
					</div>	
				</div>
				<div class="field">
					<div class="input f-left">
						<label for="Event_Location">Event Location <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Event_Location', 'text','Event_Location','placeholder="Enter event location here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Rental_Date">Rental Date <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Rental_Date', 'text','Rental_Date','placeholder="Enter rental date here"'); 
						?>						
					</div>	
				</div>
				<div class="field">
				<div class="input f-left">
						<label for="Drop_Off_Time">Drop-Off Time <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Drop_Off_Time', 'select',$dropoff);
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input f-left">
						<label for="When_would_you_like_us_to_pick_up">When would you like us to pick up? <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('When_would_you_like_us_to_pick_up', 'select',$day);
						?>						
					</div>
					<div class="input f-right">
						<label for="Pick_Up_Time">Pick-Up Time <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Pick_Up_Time', 'select',$pickup);
						?>						
					</div>
				</div>
				<div class="field">
				<div class="input textarea">
						<label for="Additional_Information">Additional Information <span></span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Information', 'text','Additional_Information','placeholder="Enter additional information here"'); 
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