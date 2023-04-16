<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Online Job Order Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Company']) ||
		empty($_POST['Contact_Person']) ||
		empty($_POST['Telephone']) ||	
        empty($_POST['Job_Location']) ||
        empty($_POST['Employee_Rate']) ||		
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
		
		
		$name = $_POST['Contact_Person'];
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
			Company: "required",
			Contact_Person: "required",
			Telephone: "required",
			Job_Location: "required",
			Employee_Rate: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			Company: "Required",
			Contact_Person: "Required",
			Telephone: "Required",
			Job_Location: "Required",
			Employee_Rate: "Required",
			Email: "Enter a valid Email",
			secode: ""
		}  

	});
	     var curr_year = new Date().getFullYear();
	    $('.thisDate').datepick({
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
<style type="text/css">
.hidden-data{background:#04A8E7;color:#FFF;font-size:18px;padding:7px;border-radius:7px;}
</style>
</head>
<body>
	<div id="container" class="rounded-corners">
		<div id="content" class="rounded-corners">
			<form id="submitform" name="contact" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				<div class="field">
					<div class="input textarea">
						<label>Company <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Company', 'text','','placeholder="Enter company here"'); 
						?>						
					</div>		
				</div>	
				<div class="field">				
					<div class="input textarea">
						<label>Contact Person <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_Person', 'text','','placeholder="Enter contact person here"'); 
						?>						
					</div>
				</div>
				
				<div class="field">				
					<div class="input textarea">
						<label>Telephone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Telephone', 'text','','placeholder="Enter telephone here"'); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Fax</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax', 'text','','placeholder="Enter fax here"'); 
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
						<label>Job Location <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Job_Location', 'text','','placeholder="Enter job location here"'); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Position (Job Title)</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Position_Job_Title', 'text','','placeholder="Enter position (job title) here"'); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Number of Workers</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Workers', 'text','','placeholder="Enter number of workers here"'); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Start Date</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Start_Date', 'text thisDate','','placeholder="Enter start date here"'); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Duration (Weeks)</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Duration_Weeks', 'text','','placeholder="Enter duration (weeks) here"'); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Job Classification</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Job_Classification', 'text','','placeholder="Enter job classification here"'); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<div class="hidden-data">
						   <input type="hidden" value=":" name="Shift Information"/> Shift Information
                        </div>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Shift Start Time</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Shift_Start_Time', 'text','','placeholder="Enter shift start time here"'); 
						?>						
					</div>
				</div>
				
				<div class="field">				
					<div class="input textarea">
						<label>Shift End Time</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Shift_End_Time', 'text','','placeholder="Enter shift end time here"'); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Shift Preference</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Shift_Preference', 'text','','placeholder="Enter shift preference here"'); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Shift Preference</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Shift_Preference_', array('Weekend','Flex','Job Share')); 
						?>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<div class="hidden-data">
						   <input type="hidden" value=":" name="Wage Information"/> Wage Information
                        </div>						
					</div>
				</div>
				<div class="field">				
					<div class="input textarea">
						<label>Employee Rate <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Employee_Rate', 'text','','placeholder="Enter employee rate here"'); 
						?>						
					</div>
				</div>			
				<div class="field">	
					<div class="input textarea">	
						<label>Job Description and Comments</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Job_Description_and_Comments', '','Comments','placeholder="Enter job description and comments here" cols="88"'); 
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