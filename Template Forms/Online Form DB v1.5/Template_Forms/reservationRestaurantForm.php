<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Reservation Restaurant Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Your_Name']) ||
		empty($_POST['Contact_Number']) ||	
		empty($_POST['secode'])) {
				
	
	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';	
	$prompt_message = '<div id="error">'.$asterisk . ' Required Fields are empty</div>';
	}
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
			Contact_Number: "required",
			secode: "required"		
		},
		messages: {
			Your_Name: "Required",
			Contact_Number: "Required",
			secode: ""
		}
	});
	
	var curr_year = new Date().getFullYear();
    $('#Date').datepick({
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
			<form id="submitform" name="contact" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				<div class="field">
					<div class="input">
						<label>Your Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Your_Name', 'text','Your_Name','placeholder="Enter your name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Contact Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_Number', 'text','Last_Name','placeholder="Enter your contact number here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>When will you be dining with us?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('When_will_you_be_dining_with_us?', 'text','Date','placeholder="Enter date here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Number of Adults in your party</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Adults_in_your_party', 'text','Number_of_Adults_in_your_party','placeholder="Enter the number of adults in your party here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Number of Children in your party</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Children_in_your_party', 'text','Date','placeholder="Enter the number of children in your party here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Time</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Time', 'text','Time','placeholder="Enter your time here"'); 
						?>						
					</div>
				</div>
				
				
				<div class="field">
					<div class="input textarea">
						<label>Special Requests</label>
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Special_Requests', '','Message','placeholder="Enter your special requests here" cols="88"'); 
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
						<button type='submit' class="button medButton">Submit</button>						
					</div>	
				</div>
			</form>	
			<div class="clearfix"></div>			
		</div>
	</div>
</body>	
</html>