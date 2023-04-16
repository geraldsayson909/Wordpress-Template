<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Job Seekers Form';
$prompt_message = '<span class="required-info">* Required Information</span>';

// for email notification
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


	if( empty($_POST['First_Name']) ||
		empty($_POST['Job_Applying_for']) ||
		empty($_POST['MI']) ||
		empty($_POST['Social_Security_Number']) ||
		empty($_POST['Street_Address']) ||
		empty($_POST['City']) ||
		empty($_POST['State']) ||
		empty($_POST['Zip']) ||
		empty($_POST['Telephone']) ||
		empty($_POST['Email'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
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
			$prompt_message = '<div id="success"><div class="message"><span>THANK YOU</span><br/> <span>for sending us a message!</span><br/><span>We will be in touch with you soon.</span><p class="close">x</p></div></div>';
				unset($_POST);
		}else {
			$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
		}
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
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.min.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>
			.form_head {border-radius: 10px; }
			.form_head p.title_head:nth-child(1) { background: #ff3f3f;  margin: 0;  padding: 10px;  color: #fff;  font-weight: bold;  border-top-right-radius: 8px;  border-top-left-radius: 8px;}
			.form_head .form_box .form_box_col1 p { margin-bottom: 4px; }
			.form_head .form_box { margin: 0; padding: 25px 28px; border: 2px solid #ddd; border-top: none;  border-bottom-right-radius: 8px;  border-bottom-left-radius: 8px;}

			.amount{
			  padding: 10px 90px;
			}
			#icon {
				position: absolute;
				padding: 10px 39px 10px 10px;
				background: #616161;
				height: 62px;
				color: #fff;
				font-size: 31px;
			}
			.fa-dollar-sign::before {
				content: "\f155";
				position: relative;
				left: 13px;
				top: 5px;
			}

			[type="radio"]:checked + label, [type="radio"]:not(:checked) + label { padding: 10px 31px 10px 3px; }

			@media only screen and (min-width: 780px) and (max-width : 1530px) {
				.email_shift{column-count:1;}
			}
			@media only screen and (min-width: 100px) and (max-width : 1000px) {
				.time-mobi tr td{width:100%!important;}
			}
			@media only screen and (min-width: 100px) and (max-width : 780px) {
				.email_shift tr td{width:100%!important;}
			}
			@media only screen and (min-width: 110px) and (max-width : 1490px) {
				.radio tr td{width: 100%; margin-right: 0;}
				.radio tr td:last-child {width: 100%; margin-right: 0; }
			}
			@media only screen and (max-width : 740px) {
				.radio tr td, .radio tr td:last-child{width:100%; display:block;}
				.radioLine div {width: 100%; float: none; }
			}

		</style>
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
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Job Applying for?', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('First Name', '*', 'form_field');
										$input->masterfield('MI', '*', 'form_field','Enter middle initial here');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Last Name', '*', 'form_field');
										$input->masterfield('Social Security Number', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterdatepicker('Date of Birth', '', 'form_field');
										$input->masterfield('Street Address', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('City', '*', 'form_field');
										$input->masterselect('State', '*', 'form_field', $state);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Zip', '*', 'form_field');
										$input->masterfield('Telephone', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box email_shift">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Email', '*', 'form_field');
										$input->masteradio('Education', '', array('High School','Vo-tech','College','Post Graduate'),'','','4');
									?>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Desired Wage');
											$input->amount('Desired_Wage', '','placeholder="Enter desired wage here"');
										?>
									</div>
									<?php
										$period = array('Select Period','Per Hour','Per Year');
										$input->masterselect('Select Period', '', 'form_field', $period);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, options, id, attribute
										$input->mastertextarea('Skills (Separate with comma)', '', 'form_field','Enter your skills here');
									?>
								</div>
							</div>

							<div class="form_box  time-mobi">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masteradio('Full Time Week Days', '', array('1st','2nd','3rd'),'','','3');
										$input->masteradio('Marital Status', '', array('Single','Married','Exemptions'),'','','3');
										$input->label('&nbsp;');
										$input->fields('Exemptions_', 'form_field','Exemptions_','placeholder="Exemptions"');
									?>

								</div>
							</div>

							<div class="form_box left ">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masteradio('Weekends', '', array('1st','2nd','3rd'),'','','3');


									?>
								</div>
							</div>

							<div class = "form_box5 secode_box">
								<div class="inner_form_box1 recapBtn">
									<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
									<div class="btn-submit"><input type = "submit" class = "form_button" value = "SUBMIT" /></div>
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
			First_Name: "required",
			Job_Applying_for: "required",
			MI: "required",
			Last_Name: "required",
			Social_Security_Number: "required",
			Street_Address: "required",
			City: "required",
			State: "required",
			Zip: "required",
			Telephone: "required",
			Email: "required"
		},
		messages: {
			First_Name: "",
			Job_Applying_for: "",
			MI: "",
			Last_Name: "",
			Social_Security_Number: "",
			Street_Address: "",
			City: "",
			State: "",
			Zip: "",
			Telephone: "",
			Email: ""
		}
	});

	$('#Exemptions_').hide();
	$('input[name="Marital_Status"]').change(function(){
		if($(this).val() == "Exemptions"){
			$('#Exemptions_').slideToggle();
			$('#Exemptions_').attr('disabled',false);
		}
		else{
			$('#Exemptions_').slideUp();
			$('#Exemptions_').attr('disabled',true);
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
