<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Verify Insurance Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php';
if ($_POST){

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "secret={$recaptcha_privite}&response={$_POST['g-recaptcha-response']}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	$result = json_decode($server_output);
	curl_close ($ch);

	if( empty($_POST['Client_Full_Name'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}
	else{

		$body = '<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px; margin: 0 auto;">
			<div style="border:8px double #c3c3d0; padding:12px;">
			<div align="center" style="color:#990000; font-style:italic; font-size:20px; font-family:Arial; margin:bottom: 15px;">('.$formname.')</div>

			<table width="90%" cellspacing="2" cellpadding="5" align="center" style="font-family:Verdana; font-size:13px">
				';

			foreach($_POST as $key => $value){
				if($key == 'submit') continue;
				elseif($key == 'g-recaptcha-response') continue;

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
		include 'send_email_curl.php';

		// save data form on database
		include 'savedb.php';

		// save data form on database
		$subject = $formname ;
		$attachments = array();

	 	//name of sender
		$name = $_POST['Client_Full_Name'];
		$result = insertDB($name,$subject,$body,$attachments);

		$parameter = array(
			'body' => $body,
			'from' => $from_email,
			'from_name' => $from_name,
			'to' => $to_email,
			'subject' => 'New Message Notification',	
			'attachment' => $attachments	
		);

		$prompt_message = send_email($parameter);

	}

}
/*************declaration starts here************/
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>

		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
		<link rel="stylesheet" href="style.min.css?ver23asas">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.min.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>
<body>
	<div class="clearfix">
		<div class = "wrapper">
			<div id = "contact_us_form_1" class = "template_form">
				<div class = "form_frame_b">
					<div class = "form_content">
					<?php if($testform):?><div class="test-mode"><i class="fas fa-info-circle"></i><span>You are in test mode!</span></div><?php endif;?>

						<form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">
								<?php echo $prompt_message; ?>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Client Full Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Client_Full_Name', 'form_field','Client_Full_Name','placeholder="Enter client full name here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Client Date of Birth', '*');
											// @param field name, class, id and attribute
											$input->datepicker('Client_Date_of_Birth', 'form_field','placeholder="Enter date here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Primary Insured Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Primary_Insured_Name', 'form_field','Primary_Insured_Name','placeholder="Enter primary insured name here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Primary Phone Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Primary_Phone_Number', 'form_field','Primary_Phone_Number','placeholder="Enter primary phone number here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Street Address 1', '*');
											// @param field name, class, id and attribute
											$input->fields('Street_Address_1', 'form_field','Street_Address_1','placeholder="Enter street address here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Street Address 2');
											// @param field name, class, id and attribute
											$input->fields('Street_Address_2', 'form_field','Street_Address_2','placeholder="Enter street address here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('City', '*');
											// @param field name, class, id and attribute
											$input->fields('City', 'form_field','City','placeholder="Enter city here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Zip Code', '*');
											// @param field name, class, id and attribute
											$input->fields('Zip_Code', 'form_field','Zip_Code','placeholder="Enter zip code here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('State', '*');
											// @param field name, class, id and attribute
											$input->select('State', 'form_field', $state);
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Client Primary Phone Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Client_Primary_Phone_Number', 'form_field','Client_Primary_Phone_Number','placeholder="Enter client primary phone number here"');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Insurance Provider', '*');
											// @param field name, class, id and attribute
											$input->fields('Insurance_Provider', 'form_field','Insurance_Provider','placeholder="Enter insurance provider here"');
										?>
									</div>
									<div class="group">
											<?php
												$input->label('Insurance Phone Number', '*');
												// @param field name, class, id and attribute
												$input->fields('Insurance_Phone_Number', 'form_field','Insurance_Phone_Number','placeholder="Enter insurance phone number here"');
											?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Insurance Member ID', '*');
											// @param field name, class, id and attribute
											$input->fields('Insurance_Member_ID', 'form_field','Insurance_Member_ID','placeholder="Enter insurance member id here"');
										?>
									</div>
									<div class="group">
											<?php
												$input->label('Insurance Group ID');
												// @param field name, class, id and attribute
												$input->fields('Insurance_Group_ID', 'form_field','Insurance_Group_ID','placeholder="Enter insurance group id here"');
											?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Type of plan (HMO or PPO)');
											// @param field name, class, id and attribute
											$input->fields('Type_of_plan_HMO_or_PPO', 'form_field','Type_of_plan_HMO_or_PPO','placeholder="Enter type of plan here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Social Security Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Social_Security_Number', 'form_field','Social_Security_Number','placeholder="Enter social security number here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Email', '*');
											// @param field name, class, id and attribute
											$input->fields('Email', 'form_field','Email','placeholder="Enter email here"');
										?>
									</div>
								</div>
							</div>

							<div class = "form_box5 secode_box">
								<div class = "group">
									<div class="inner_form_box1 recapBtn">
										<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
										<div class="btn-submit"><input type = "submit" class = "form_button" value = "SUBMIT" /></div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.min.js"></script>


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
		},
		messages: {
			Client_Full_Name: "",
			Client_Date_of_Birth: "",
			Primary_Insured_Name: "",
			Primary_Phone_Number: "",
			Street_Address_1: "",
			City: "",
			Zip_Code: "",
			State: "",
			Client_Primary_Phone_Number: "",
			Insurance_Provider: "",
			Insurance_Phone_Number: "",
			Insurance_Member_ID: "",
			Social_Security_Number: "",
			Email: "",
		}
	});


	$("#submitform").submit(function(){
		if($(this).valid()){
			$('.load_holder').css('display','block');
			self.parent.$('html, body').animate(
				{ scrollTop: self.parent.$('#myframe').offset().top },
				500
			);
		}
		if(grecaptcha.getResponse() == "") {
			var $recaptcha = document.querySelector('#g-recaptcha-response');
				$recaptcha.setAttribute("required", "required");
				$('.g-recaptcha').addClass('errors').attr('id','recaptcha');
		  }
	});

	$( "input" ).keypress(function( event ) {
		if(grecaptcha.getResponse() == "") {
			var $recaptcha = document.querySelector('#g-recaptcha-response');
			$recaptcha.setAttribute("required", "required");
		  }
	});

	$('.Date').datepicker();
	$('.Date').attr('autocomplete', 'off');


});
$(function() {
  $('.Date, .date').datepicker({
	autoHide: true,
	zIndex: 2048,
  });
});
</script>
</body>
</html>
