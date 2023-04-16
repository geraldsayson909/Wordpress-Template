<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Client Referral Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php'; 

if ($_POST){

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "secret={$recaptcha_privite}&response={$_POST['g-recaptcha-response']}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	$result_recaptcha = json_decode($server_output);
	curl_close ($ch);

	if(empty($_POST['Your_Name']) ||
		empty($_POST['Your_Organization']) ||
		empty($_POST['Telephone_Number']) ||
		empty($_POST['Clients_Last_Name']) ||
		empty($_POST['Clients_First_Name']) ||
		empty($_POST['Telephone_Number_']) ||
		empty($_POST['Contact_Person']) ||
		empty($_POST['Contact_Persons_Telephone_Number']) ||
		empty($_POST['Clients_Address']) ||
		empty($_POST['Email'])) { 

	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(!$result_recaptcha->success){
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
		$name = $_POST['Your_Name'];
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
		unset($_POST);
	}

}
/*************declaration starts here************/
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');

$ii = array('- Please Select -','MEDICARE','PUBLIC AIDE','PRIVATE INSURANCE','SELF PAY');
$month = array('- Please select month -','January','February','March','April','May','June','July','August','September','October','November','December');
$lives = array('- Please Select -','House/Apartment','Assisted/Supportive Living','Senior Housing','Group Home','Rented Room','None of the Above');

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
								<p class="strong_head" >REFERRER</p><input type="hidden" name="REFERRER" value=":" />
						</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Your Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Your_Name', 'form_field','Your_Name','placeholder="Enter your name"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Your Organization', '*');
											// @param field name, class, id and attribute
											$input->fields('Your_Organization', 'form_field','Your_Organization','placeholder="Enter your organization here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Telephone Number','*');
											// @param field name, class, id and attribute
											$input->fields('Telephone_Number', 'form_field','Telephone_Number','placeholder="Enter telephone number here"');
										?>
									</div>
								</div>
							</div>

				<hr />

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Client\'s Last Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Clients_Last_Name', 'form_field','Clients_Last_Name','placeholder="Enter client\'s last name here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('First Name','*');
											// @param field name, class, id and attribute
											$input->fields('Clients_First_Name', 'form_field','Clients_First_Name','placeholder="Enter first name here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Telephone Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Telephone_Number_', 'form_field','Telephone_Number_','placeholder="Enter telephone number here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Contact Person','*');
											// @param field name, class, id and attribute
											$input->fields('Contact_Person', 'form_field','Contact_Person','placeholder="Enter contact person here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Contact Person\'s Telephone Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Contact_Persons_Telephone_Number', 'form_field','Contact_Persons_Telephone_Number','placeholder="Enter contact person\'s telephone number here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Client\'s Address','*');
											// @param field name, class, id and attribute
											$input->fields('Clients_Address', 'form_field','Clients_Address','placeholder="Enter client\'s address here"');
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
											$input->fields('Email', 'form_field','Email','placeholder="Enter email address here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Insurance Information', '');
											// @param field name, class, id and attribute
											$input->select('Insurance_Information', 'form_field', $ii);
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Client\'s Date of Birth');
											// @param field name, class, id and attribute
											$input->fields('Clients_Date_of_Birth', 'form_field Date','Clients_Date_of_Birth','placeholder="Enter clients date of birth here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Client\'s Medicare Number', '');
											// @param field name, class, id and attribute
											$input->fields('Clients_Medicare_Number', 'form_field','Clients_Medicare_Number','placeholder="Enter clients medicare number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Has the client ever recieved home health care service in the past', '');
											// @param field name, class, id and attribute
											$input->radio('Has_the_client_ever_received_home_health_care_service_in_the_past',array('Yes','No'),'','class="two-radio"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Client lives in a', '');
											// @param field name, class, id and attribute
											$input->select('Client_lives_in_a', 'form_field', $lives);
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Is the client able to drive a car safely on a regular basis?', '');
											// @param field name, class, id and attribute
											$input->radio('Is_the_client_able_to_drive_a_car_safely_on_a_regular_basis',array('Yes','No'),'','class="two-radio"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Does the client use any type of assistive device e.g. cane, walker, wheelchair?', '');
											// @param field name, class, id and attribute
											$input->radio('Does_the_client_use_any_type_of_assistive_device',array('Yes','No'),'','class="two-radio"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Is the client willing to receive home health services?', '');
											// @param field name, class, id and attribute
											$input->radio('Is_the_client_willing_to_receive_home_health_services',array('Yes','No'),'','class="two-radio"');
										?>
									</div>
								</div>
							</div>
				<hr/>
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
			Your_Name: "required",
			Your_Organization: "required",
			Telephone_Number: "required",
			Clients_Last_Name: "required",
			Clients_First_Name: "required",
			Telephone_Number_: "required",
			Contact_Person: "required",
			Contact_Persons_Telephone_Number: "required",
			Clients_Address: "required",
			Email: {
				required: true,
				email: true
			}
		},

		messages: {
			Your_Name: "",
			Your_Organization: "",
			Telephone_Number: "",
			Clients_Last_Name: "",
			Clients_First_Name: "",
			Telephone_Number_: "",
			Contact_Person: "",
			Contact_Persons_Telephone_Number: "",
			Clients_Address: "",
			Email: ""
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
