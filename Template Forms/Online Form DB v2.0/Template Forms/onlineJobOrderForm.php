<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Online Job Order Form';
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

	if(empty($_POST['Company']) ||
		empty($_POST['Contact_Person']) ||
		empty($_POST['Telephone']) ||
        empty($_POST['Job_Location']) ||
        empty($_POST['Employee_Rate']) ||
		empty($_POST['Email_Address'])) {

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


		$name = $_POST['Contact_Person'];
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
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.min.css" />

		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>
			.information, .information2{background: #fee7e3; color: #444444; font-weight: bold;}
			.information:before{content: url(images/info-reTranspo-icon.png)!important;}
			.information2:before{position: absolute; left: 25px; top: 13px; content: url(images/wage-icon.png)!important;}
			.radio tr td{width:33%; margin-right:0;}
			.radio tr td:last-child {width: 33%; margin-right: 0; }
			@media only screen and (min-width: 110px) and (max-width : 1490px) {
				.radio tr td{width: 33%; margin-right: 0;}
			}
			@media only screen and (max-width : 430px) {
				.radio tr td, .radio tr td:last-child{width:100%; display:block;}
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
					<?php echo $prompt_message; ?>
					<hr />
							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Company', '*', 'form_field','Enter company here','Company');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Contact Person', '*', 'form_field','Enter contact person here','Contact_Person');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Telephone', '*', 'form_field','Enter telephone here','Telephone');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Fax Number', '*', 'form_field','Enter fax here','Fax_Number');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Email Address', '*', 'form_field','Enter email address here','Email_Address');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Job Location', '*', 'form_field','Enter job location here','Job_Location');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Position (Job Title)', '*', 'form_field','Enter position here','Position_Job_Title');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Number of Workers', '*', 'form_field','Enter number of workers here','Number_of_Workers');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Start Date', '*', 'form_field Date','Enter start date here','Start_Date');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Duration (Weeks)', '*', 'form_field','Enter durationhere','Duration_Weeks');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Job Classification', '*', 'form_field','Enter job classification here','Job_Classification');
									?>
								</div>
							</div>

							<div class="information">
								Shift Information
								 <input type="hidden" value=":" name="Shift Information" />
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Shift Start Time', '', 'form_field','Enter shift start time here','Shift_Start_Time');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Shift Preference', '', 'form_field','Enter shift preference here','Shift_Preference');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Shift End Time', '', 'form_field','Enter shift end time here','Shift_End_Time');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masteradio('Shift_Preference_', '', array('Weekend','Flex','Job Share'),3);
									?>
								</div>
							</div>

							<div class="information2">
								Wage Information
								 <input type="hidden" value=":" name="Wage Information" />
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Employee Rate', '*', 'form_field','Enter employee rate here','Employee_Rate');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param label-name, if required
										$input->label('Job Description and Comments');
										// @param field name, class, id and attribute
										$input->textarea('Job_Description_and_Comments', 'text form_field','Job_Description_and_Comments','placeholder="Enter job description and comments here"');
									?>
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
			Company: "required",
			Contact_Person: "required",
			Telephone: "required",
			Fax_Number: "required",
			Job_Location: "required",
			Position_Job_Title: "required",
			Number_of_Workers: "required",
			Start_Date: "required",
			Duration_Weeks: "required",
			Job_Classification: "required",
			Employee_Rate: "required",
			Email_Address: {
				required: true,
				email: true
			},
			secode: "required"
		},
		messages: {
			Company: "",
			Contact_Person: "",
			Telephone: "",
			Fax_Number: "",
			Job_Location: "",
			Position_Job_Title: "",
			Number_of_Workers: "",
			Start_Date: "",
			Duration_Weeks: "",
			Job_Classification: "",
			Employee_Rate: "",
			Email_Address: ""
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
