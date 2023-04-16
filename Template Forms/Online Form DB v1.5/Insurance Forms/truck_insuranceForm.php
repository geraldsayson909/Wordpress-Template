<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Truck Insurance Form';
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
$CarryingCapacity  = array('- Please Select -','2 to 5 tonnes','5 to 10 tonnes','more than 10 tonnese');
$type = array('- Please Select -','Rigid','Primemover');
$LicenceClass = array('- Please Select -','C','LR','MR','HR','HC');
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
	$("#submitform").submit(function(){
		if($(this).valid()){
			self.parent.$('html, body').animate(
				{ scrollTop: self.parent.$('#myframe').offset().top },
				500
			);
		}
	});
	
	
	
	var curr_year = new Date().getFullYear();
	$('#Renewal_Date').datepick({
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
					<div class="input">
						<label>First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','','placeholder="Enter first name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','','placeholder="Enter last name here"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','','placeholder="Enter phone here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','','placeholder="Enter email here"'); 
						?>						
					</div>
				</div>


				<div class="field">
					<div class="input">
						<label>Year of Manufacture </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year_of_Manufacture', 'text','','placeholder="Enter year of manufacture here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Manufacturer</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Manufacturer', 'text','','placeholder="Enter manufacturer here"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Model</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Model', 'text','','placeholder="Enter model here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Type</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Type', 'select', $type); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Carrying Capacity</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Carrying_Capacity', 'select', $CarryingCapacity); 
						?>						
					</div>
					<div class="input f-right">
						<label>Sum Insured ($) </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Sum_Insured_($)', 'text','','placeholder="Enter sum insured ($) here"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Postcode Parked At Night </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Postcode_Parked_At_Night', 'text','','placeholder="Enter postcode parked at night here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Suburb Parked At Night </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Suburb_Parked_At_Night', 'text','','placeholder="Enter suburb parked at night here"'); 
						?>						
					</div>
				</div>	

				<div class="field">
					<div class="input">
						<label>Current Insurer</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Insurer', 'text','','placeholder="Enter current insurer here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Licence Class </label> 
						<?php 
							// @param field name, class, id and attribute
							$input->select('Licence_Class', 'select', $LicenceClass); 
						?>						
					</div>
				</div>		
				<div class="field">
					<div class="input">
						<label>Renewal Date</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Renewal_Date', 'text','Renewal_Date','placeholder="Enter renewal date here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Preferred Time To Call</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Preferred_Time_To_Call', 'text','','placeholder="Enter preferred time to call here"'); 
						?>						
					</div>
				</div>		

				<div class="field">	
					<div class="input textarea">	
						<label>Claims History In The Past 5 Years (if any)</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Claims_History_In_The_Past_5_Years_(if_any)', '','','placeholder="Enter claims history In the past 5 years (if any) here" cols="88"'); 
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