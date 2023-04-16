<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Client Satisfaction Survey Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['secode'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error">'.$asterisk . ' Required Fields are empty</div>';
	}
	// else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))))
		// { $prompt_message = '<div id="error">Please enter a valid email address</div>';}
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
				 elseif($key == 'Email') continue;
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


		$name = "Message From Your Site";
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
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			secode: "required"
		},
		messages: {
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
					<div class="input textarea">

						<input type="hidden" name="Email" value="sample@domain.com" />
					</div>
				</div>

				<div class="field">
					<div class="input textarea">
						<label class="block" for="Please_rate_the_quality_of_the_services_you_received_from_us">1. Please rate the quality of the services you received from us:</label>
						<?php
							// @param field name, value, id and attribute
							$input->radio('Please_rate_the_quality_of_the_services_you_received_from_us',array('Excellent','Good','Fair','Poor'));
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label class="block" for="Please_rate_the_information_we_provided_on_our_website">2. Please rate the information we provided on our website:</label>
						<?php
							// @param field name, value, id and attribute
							$input->radio('Please_rate_the_information_we_provided_on_our_website',array('Excellent','Good','Fair','Poor'));
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label class="block" for="Please_rate_our_staff_in_terms_of_efficiency">3. Please rate our staff in terms of efficiency:</label>
						<?php
							// @param field name, value, id and attribute
							$input->radio('Please_rate_our_staff_in_terms_of_efficiency',array('Excellent','Good','Fair','Poor'));
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label class="block" for="Please_rate_our_responsiveness_to_feedback">4. Please rate our responsiveness to feedback:</label>
						<?php
							// @param field name, value, id and attribute
							$input->radio('Please_rate_our_responsiveness_to_feedback',array('Excellent','Good','Fair','Poor'));
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label class="block" for="Please_rate_our_responsiveness_to_feedback">5. Please rate your overall experience with our services:</label>
						<?php
							// @param field name, value, id and attribute
							$input->radio('Please_rate_your_overall_experience_with_our_services',array('Excellent','Good','Fair','Poor'));
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label class="block" for="Would_you_recommend_us_to_friends_and_family">6. Would you recommend us to friends and family?</label>
						<?php
							// @param field name, value, id and attribute
							$input->radio('Would_you_recommend_us_to_friends_and_family',array('Yes','No'));
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Reason">Reason:</label>
						<?php
							// @param field name, class, id and attribute
							$input->textarea('Reason', '','Reason','placeholder="Enter your reason here" cols="88"');
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
