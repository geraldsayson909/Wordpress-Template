<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Make a Reservation Form';
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
	
	if(empty($_POST['Full_Name']) ||
		empty($_POST['When_do_you_need_our_services']) ||
		empty($_POST['Time']) ||
		empty($_POST['Email_Address']) ||
		empty($_POST['Address'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}else{

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
		require_once 'config.php';
		require_once 'swiftmailer/mail.php';
		// save data form on database
		include 'savedb.php';


		// save data form on database
		$subject = $formname ;
		$attachments = array();

	 	//name of sender
		$name = $_POST['Full_Name'];
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
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.min.css" />

		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>
			@media only screen and (max-width : 1165px) {
				.resp-field .form_box_col2{-webkit-column-count: 1; -moz-column-count: 1; column-count: 1;}
			}

			@media only screen and (max-width : 900px) {
				.form_box.left.full-width { column-count: 2; }
				.full-width  { width: 100% !important; }
			}

			@media only screen and (max-width : 730px) {
				.form_box.left.full-width { column-count: 1; }
				.form_box.left, .form_box.right{width:100%; float:none;}
				#services { margin: 0; }
			}

		</style>
	</head>
<body>
	<div class="clearfix">
		<div class = "wrapper">
			<div id = "contact_us_form_1" class = "template_form">
				<div class = "form_frame_b">
					<div class = "form_content">
					<form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">
					<?php if($testform):?><div class="test-mode"><i class="fas fa-info-circle"></i><span>You are in test mode!</span></div><?php endif;?>
					<?php echo $prompt_message; ?>
							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Full Name', '*', 'form_field','Enter full name here','Full_Name');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('When do you need our services?', '*', 'form_field Date','Enter date here','When_do_you_need_our_services');
									?>
									<div class="group">
										<div class="form_label" id="services">
											<label class="text_uppercase">
												<strong>&nbsp;<span class="required_filed"></span></strong>
											</label>
										</div>
										<?php
											$input->fields('Time', 'form_field', 'Time', 'placeholder="Enter time here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box left ">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Email Address', '*', 'form_field','Enter email address here','Email_Address');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Address', '*', 'form_field','Enter address here','Address');
									?>
								</div>
								<div class="form_box_col1">
								<?php
									$input->masterfield('What services do you need?', '', 'form_field');
								?>
								</div>
							</div>
							<div class="form_box right">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Additional details about your reservation');
											// @param field name, class, id and attribute
											$input->textarea('Additional_details_about_your_reservation', 'text form_field','Additional_details_about_your_reservation','placeholder="Enter additional details about your reservation here" style="height: 172px;"');
										?>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>

							<div class="form_box left">
								<div class="form_box_col1">

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
			Full_Name: "required",
			When_do_you_need_our_services: "required",
			Time: "required",
			Email_Address: {
				required: true,
				email: true
			},
			Address: "required"
		},
		messages: {
			Full_Name: "",
			When_do_you_need_our_services: "",
			Time: "",
			Email_Address: "",
			Address: ""
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
