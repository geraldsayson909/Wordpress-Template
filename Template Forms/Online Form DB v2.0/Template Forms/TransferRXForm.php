<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Transfer Prescription Form';
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

	if( empty($_POST['First_Name'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
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

		 // for email notification
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
		<link rel="stylesheet" type="text/css" href="css/dd.min.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>
			[type="radio"]:checked + label::after, [type="radio"]:not(:checked) + label::after {left: 3px;}
			[type="radio"]:checked + label::before, [type="radio"]:not(:checked) + label::before {left: 0;}
			.form_head {border-radius: 10px; }
			.form_head p.title_head:nth-child(1) { background: #ff3f3f;  margin: 0;  padding: 10px;  color: #fff;  font-weight: bold;  border-top-right-radius: 8px;  border-top-left-radius: 8px;}
			.form_head .form_box .form_box_col1 p { margin-bottom: 4px; }
			.form_head .form_box { margin: 0; padding: 25px 28px; border: 2px solid #ddd; border-top: none;  border-bottom-right-radius: 8px;  border-bottom-left-radius: 8px;}
			.form_box5.secode_box {margin: 15px 0 0 0;}
			.form_box.medsinpput .form_box_col1 { margin: 0 0 15px 0; }
			.strong_heading {font-weight: 700; text-transform: uppercase;}
			@media only screen and (min-width: 100px) and (max-width : 780px) {
				.form_head {     margin: 10px auto; }
			}
			@media only screen and (max-width : 400px) {
				.form_head .form_box{padding: 15px 15px 25px;}
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
											$input->label('Date of Birth', '*');
											// @param field name, class, id and attribute
											$input->datepicker('Date_of_Birth', 'form_field','placeholder="Enter date of birth here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Phone Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Phone_Number', 'form_field','Phone_Number','placeholder="Enter phone number here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Address', 'form_field','Address','placeholder="Enter address here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('City', '*');
											// @param field name, class, id and attribute
											$input->fields('City', 'form_field','City','placeholder="Enter city here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('State');
											// @param field name, class, id and attribute
											$input->select('State', 'form_field', $state);
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Zip/Postal Code', '*');
											// @param field name, class, id and attribute
											$input->fields('Zip_or_Postal_Code', 'form_field','Zip_or_Postal_Code','placeholder="Enter zip or postal code here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Pharmacy Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Pharmacy_Name', 'form_field','Pharmacy_Name','placeholder="Enter pharmacy name here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Pharmacy Phone','*');
											// @param field name, class, id and attribute
											$input->fields('Pharmacy_Phone', 'form_field','Pharmacy_Phone','placeholder="Enter pharmacy phone here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_head">
								<p class="title_head">Prescription to be transferred</p>
								<div class="form_box">
									<div class="form_box_col1">
										<div class="group">
											<p>If you would like to transfer all prescription, simply check the box below.</p>
											<?php
												// @param field name, class, id and attribute
												$input->checktxt('Transfer_all_my_prescriptions', 'Yes','Transfer all my prescriptions');
											?>
										</div>
									</div>



									<p>If you would like to selectively transfer your prescription, use the option below.</p>
									<p class="strong_heading">List Specific Prescription to be transferred</p>


									<div class="grouping">
										<div class="form_box_col2">
											<div class="group">
											<div class="form_head">
												<p class="title_head">Medication Name</p>
												<div class="form_box medsinpput">
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx1 Med Name');
																// @param field name, class, id and attribute
																$input->fields('Rx1_Med_Name', 'form_field','Rx1_Med_Name','placeholder="Enter medication name here"');
															?>
														</div>
													</div>
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx2 Med Name');
																// @param field name, class, id and attribute
																$input->fields('Rx2_Med_Name', 'form_field','Rx2_Med_Name','placeholder="Enter medication name here"');
															?>
														</div>
													</div>
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx3 Med Name');
																// @param field name, class, id and attribute
																$input->fields('Rx3_Med_Name', 'form_field','Rx3_Med_Name','placeholder="Enter medication name here"');
															?>
														</div>
													</div>
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx4 Med Name');
																// @param field name, class, id and attribute
																$input->fields('Rx4_Med_Name', 'form_field','Rx4_Med_Name','placeholder="Enter medication name here"');
															?>
														</div>
													</div>
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx5 Med Name');
																// @param field name, class, id and attribute
																$input->fields('Rx5_Med_Name', 'form_field','Rx5_Med_Name','placeholder="Enter medication name here"');
															?>
														</div>
													</div>
												</div>
											</div>
											</div>

											<div class="group">
											<div class="form_head">
												<p class="title_head">Prescription Number from current pharmacy</p>
												<div class="form_box medsinpput">
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx1 Number');
																// @param field name, class, id and attribute
																$input->fields('Rx1_Number', 'form_field','Rx1_Number','placeholder="Enter prescription number here"');
															?>
														</div>
													</div>
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx2 Number');
																// @param field name, class, id and attribute
																$input->fields('Rx2_Number', 'form_field','Rx2_Number','placeholder="Enter prescription number here"');
															?>
														</div>
													</div>
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx3 Number');
																// @param field name, class, id and attribute
																$input->fields('Rx3_Number', 'form_field','Rx3_Number','placeholder="Enter prescription number here"');
															?>
														</div>
													</div>
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx4 Number');
																// @param field name, class, id and attribute
																$input->fields('Rx4_Number', 'form_field','Rx4_Number','placeholder="Enter prescription number here"');
															?>
														</div>
													</div>
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Rx5 Number');
																// @param field name, class, id and attribute
																$input->fields('Rx5_Number', 'form_field','Rx5_Number','placeholder="Enter prescription number here"');
															?>
														</div>
													</div>
												</div>
											</div>
											</div>
										</div>
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
			First_Name: "required",
			Last_Name: "required",
			Date_of_Birth: "required",
			Phone_Number: "required",
			Address: "required",
			City: "required",
			Zip_or_Postal_Code: "required",
			Pharmacy_Name: "required",
			Pharmacy_Phone: "required"
		},
		messages: {
			First_Name: "",
			Last_Name: "",
			Date_of_Birth: "",
			Phone_Number: "",
			Address: "",
			City: "",
			Zip_or_Postal_Code: "",
			Pharmacy_Name: "",
			Pharmacy_Phone: ""
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


   $("input[name='Transfer_all_my_prescriptions']").change(function(event){
      if (this.checked){
        	$('.medsinpput input').attr('disabled',true);
      } else {
         	$('.medsinpput input').attr('disabled',false);
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
