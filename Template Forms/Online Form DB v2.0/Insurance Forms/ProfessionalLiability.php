<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Professional Liability Form';
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

	if( empty($_POST['Contact_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['Phone']) ||
		empty($_POST['City']) ||
		empty($_POST['Business_Name']) ||
		empty($_POST['Email_Address'])
		) {

	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
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
				if($key == 'secode') continue;
				elseif($key == 'submit') continue;
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

			require_once 'swiftmailer/mail.php';
			// save data form on database
			include 'savedb.php';


			// save data form on database
			$subject = $formname ;
			$attachments = array();

			//name of sender
			$name = $_POST['Contact_Name'];
			$result = insertDB($name,$subject,$body,$attachments);

			$templateVars = array('{link}' => get_home_url().'/onlineforms/'.$_SESSION['token'], '{company}' => COMP_NAME);

			Mail::Send($template, 'New Message Notification', $templateVars, $to_email, $to_name, $from_email, $from_name, $cc, $bcc);

			if($result){
				$prompt_message = '<div id="success"><div class="message"><span>THANK YOU</span><br/> <span>for sending us a message!</span><br/><span>We will be in touch with you soon.</span><p class="close">x</p></div></div>';
					unset($_POST);
			}else {
				$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
			}



	}

}
/*************declaration starts here************/
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
$contact_options = array('- Please Select -','Phone','Fax','Email');
$best_time = array('- Please Select -','Anytime','Morning at Home','Morning at Work','Afternoon at Home','Afternoon at Work','Evening at Home','Evening at Work');
$YN = array('Yes','No');
$rent_own = array('- Please Select -','Rent','Own');
$insurance = array('- Please Select -','$100,000/200,000','$300,000/600,000','$500,000/1,000,000','1,000,000/2,000,000');
$business = array('- Please Select -','Single Proprietorsh','Partnership','Corporation','Association','LLC');
$limits = array('- Please Select -','1 Million','2 Million','3 Million','4 Million','5 Million');
?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>

		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
		<link rel="stylesheet" href="style.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/datepicker.css">
		<link rel="stylesheet" href="css/jquery.datepick.css" type="text/css" media="screen" />

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

							<div class="infobox">
									<div class="input textarea" ><input type="hidden" value=":" name="With Professional Liability Insurance, professionals such as physicians, lawyers, accountants and real estate brokers are protected in the event of liability claims for damage or losses that may be suffered by their clients in the course of their professional services. Fill out the online form below as completely as possible. Our agents will get back to you for your quote."/>
									With Professional Liability Insurance, professionals such as physicians, lawyers, accountants and real estate brokers are protected in the event of liability claims for damage or losses that may be suffered by their clients in the course of their professional services. Fill out the online form below as completely as possible. Our agents will get back to you for your quote.
									</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Contact Name', '*', 'form_field');
										$input->masterfield('FEI Number', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Address', '*', 'form_field');
										$input->masterfield('Zip Code', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('State', '', 'form_field', $state);
										$input->masterfield('Phone', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('City', '*', 'form_field');
										$input->masterfield('Fax', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Business Name', '*', 'form_field');
										$input->masterfield('Email Address', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box left">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('DBA', '', 'form_field');
									?>
								</div>
							</div>
							<div class="clearfix"></div>
							<hr/>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Current Insurance Company', '', 'form_field');
										$input->masterfield('Current Policy Expiry', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Number of Years Insured', '', 'form_field');
										$input->masterfield('Have you had any claims in the last 5 years?', '', 'form_field','','Had_any_claims_in_the_last_5_years');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->mastertextarea('Give us a brief description of your day to day operation', '', 'form_field','','Brief_description_of_your_day_to_day_operation');
									?>
								</div>
							</div>
							<hr/>


							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Type of Business', '', 'form_field', $business);
										$input->masterfield('Category of Business', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Year Established', '', 'form_field');
										$input->masterfield('Number of Office Locations', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box left">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Rent of Own Office', '', 'form_field', $rent_own);
									?>
								</div>
							</div>
							<div class="clearfix"></div>
							<hr/>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Annual Gross Revenue', '', 'form_field');
										$input->masterfield('Number of Employees', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Liability limit requested', '', 'form_field');
										$input->masterfield('Employee Payroll', '', 'form_field');
									?>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->mastertextarea('Additional Information', '', 'form_field');
									?>
								</div>
							</div>


							<div class = "form_box5 secode_box">
								<div class="inner_form_box1 recapBtn">
									<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
									<div class="btn-submit"><button class = "form_button">Submit</button></div>
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
	<script src = "js/plugins.js"></script>



	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Contact_Name: "required",
			Address: "required",
			Phone: "required",
			City: "required",
			Business_Name: "required",
			Email_Address: {
				required: true,
				email: true
			}
		},
		messages: {
			Contact_Name: "",
			Address: "",
			Phone: "",
			City: "",
			Business_Name: "",
			Email_Address: ""
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
		$('.Date').datepick({
			showTrigger: '<img src="images/calendar.png" alt="Select date" style="position: absolute; right: 16px; top: 20px;" />'
		});



});
</script>
</body>
</html>
