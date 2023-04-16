<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Schedule Pick-up and Drop-off Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php';
if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Phone_Number']) ||
		empty($_POST['Number_of_Passengers']) ||
		empty($_POST['Pick_Up_Location']) || 
		empty($_POST['Email_Address'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}else{

		 // for email notification
		 require_once 'config.php';
		 require_once 'swiftmailer/mail.php';

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

		// save data form on database
		include 'savedb.php';

		// save data form on database
		$subject = $formname ;
		$attachments = array();

	 	//name of sender
		$name = $_POST['First_Name'].' '. $_POST['Last_Name'];
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
$dropoff = array('- Please Select -','8:00am','8:30am','9:00am','9:30am','10:00am','10:30am','11:00am','11:30am','12:00pm','12:30pm','1:00pm','1:30pm','2:00pm','2:30pm','3:00pm');
$day = array('- Please Select -','Same Day','+1 day(s)','+2 day(s)','+3 day(s)','+4 day(s)','+5 day(s)','+6 day(s)','+7 day(s)','+8 day(s)','+9 day(s)','+10 day(s)');
$pickup = array('- Please Select -','12:00pm','12:30pm','1:00pm','1:30pm','2:00pm','2:30pm','3:00pm','3:30pm','4:00pm','4:30pm','5:00pm','5:30pm','6:00pm','6:30pm','7:00pm','7:30pm','8:00pm');
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

		<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="dist/bootstrap-clockpicker.min.css">
		<link rel="stylesheet" type="text/css" href="assets/css/github.min.css">

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<link rel="stylesheet" href="css/proweaverPhone.css" type="text/css"/>
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>

		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>
<body>
	<div class="clearfix">
		<div class = "wrapper">
			<div id = "contact_us_form_1" class = "template_form">
				<div class = "form_frame_b">
					<div class = "form_content">
						<?php if($testform):?><div class="test-mode"><i class="fas fa-info-circle"></i><span>You are in test mode!</span></div><?php endif;?>
						<form id="submitform" name="contact" method="post" action="">
							<?php echo $prompt_message; ?>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('First Name', '*');
											// @param field name, class, id and attribute
											$input->fields('First_Name', 'form_field','First_Name','placeholder="Enter first name here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Last Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Last_Name', 'form_field','Last_Name','placeholder="Enter last name here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Email Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Email_Address', 'form_field','Email_Address','placeholder="Enter email address here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Phone Number', '*');
											// @param field name, class, id and attribute
											$input->phoneInput('Phone_Number', 'form_field','Phone_Number','placeholder="Enter phone number here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Number of Passengers', '*');
											// @param field name, class, id and attribute
											$input->fields('Number_of_Passengers', 'form_field','Number_of_Passengers','placeholder="Enter number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Pick-Up Location', '*');
											// @param field name, class, id and attribute
											$input->fields('Pick_Up_Location', 'form_field','Pick_Up_Location','placeholder="Enter location here"');
										?>
									</div>
								</div>
							</div>

							
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Pick-Up Date', '*');
											// @param field name, class, id and attribute
											$input->fields('Pick_Up_Date', 'form_field Date','Pick_Up_Date','placeholder="Enter date here"');
										?>
									</div>

									<div class="group">
										<?php $input->label('Pick-Up Time', '*'); ?>
										<div class="input-group clockpicker" data-align="left" data-donetext="Done">
										<input type="text" class="form-control" name="Pick_Up_Time" placeholder="Enter time here" style="height: 63px;">
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-time"></span>
											</span>
										</div>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
											<?php
												// @param label-name, if required
												$input->label('Drop-Off Location', '*');
												// @param field name, class, id and attribute
												$input->fields('Drop_Off_Location', 'form_field','Drop_Off_Location','placeholder="Enter location here"');
											?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Drop-Off Date', '*');
											// @param field name, class, id and attribute
											$input->fields('Drop_Off_Date', 'form_field Date','Drop_Off_Date','placeholder="Enter date here"');
										?>
									</div>

									<div class="group">
										<?php $input->label('Drop-Off Time', '*'); ?>
										<div class="input-group clockpicker" data-align="left" data-donetext="Done">
										<input type="text" class="form-control" name="Drop_Off_Time" placeholder="Enter time here" style="height: 63px;">
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-time"></span>
											</span>
										</div>
									</div>
								</div>
							</div>

							<div class = "form_box5 secode_box">
								<div class = "group">
									<div class = "group">
										<div class="inner_form_box1 recapBtn">
											<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
											<div class="btn-submit"><input type = "submit" class = "form_button" value = "SUBMIT" /></div>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $input->phone(true); ?>
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script src = "js/jquery.mask.min.js"></script>
	<script src = "js/proweaverPhone.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="dist/bootstrap-clockpicker.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.min.js"></script>

	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			First_Name:'required',
			Last_Name:'required',
			Phone_Number:'required',
			Number_of_Passengers:'required',
			Pick_Up_Location:'required',
			Pick_Up_Date:'required',
			Pick_Up_Time:'required',
			Drop_Off_Location:'required',
			Drop_Off_Date:'required',
			Drop_Off_Time:'required',
			Email_Address: {
				required: true,
				email: true
			}
		},
		messages: {
			First_Name:'',
			Last_Name:'',
			Phone_Number:'',
			Number_of_Passengers:'',
			Pick_Up_Location:'',
			Pick_Up_Date:'',
			Pick_Up_Time:'',
			Drop_Off_Location:'',
			Drop_Off_Date:'',
			Drop_Off_Time:'',
			Email_Address: ''
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

		$('.clockpicker').clockpicker()
	.find('input').change(function(){
		console.log(this.value);
	});

	</script>
</body>
</html>
