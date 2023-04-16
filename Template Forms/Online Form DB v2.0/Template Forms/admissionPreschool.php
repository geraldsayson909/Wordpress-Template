<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Admission Preschool Form';
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

	if(empty($_POST['Childs_Full_Name']) ||
		empty($_POST['Birth_Date']) ||
		empty($_POST['Age']) ||
		empty($_POST['Sex']) ||
		empty($_POST['Parent_or_Guardians_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['State_']) ||
		empty($_POST['Email']) ||
		empty($_POST['Phone_Number']) ||
		empty($_POST['Name']) ||
		empty($_POST['Address_']) ||
		empty($_POST['Name_of_Childs_Doctor']) ||
		empty($_POST['Phone_Number_']) ||
		empty($_POST['Hospital_Preference']) ||
		empty($_POST['Please_list_down_any_allergies_or_dietary_restrictions_of_the_child'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
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

		// for email notification
		require_once 'config.php';
		require_once 'swiftmailer/mail.php';
		// save data form on database
		include 'savedb.php';

		// save data form on database
		$subject = $formname ;
		$attachments = array();

	 	//name of sender
		$name = $_POST['Childs_Full_Name'];
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

						<?php $input->label('Child\'s Information'); ?>

						<div class="form_box">
							<div class="form_box_col2">
								<div class="group">
									<?php
										// @param label-name, if required
										$input->label('Child\'s Full Name', '*');
										// @param field name, class, id and attribute
										$input->fields('Childs_Full_Name', 'form_field','Childs_Full_Name','placeholder="Enter child\'s full name here"');
									?>
								</div>
								<div class="group">
									<?php
										// @param label-name, if required
										$input->label('Birth Date', '*');
										// @param field name, class, id and attribute
										$input->fields('Birth_Date', 'form_field Date','Birth_Date','placeholder="Enter birth date here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Age', '*');
										// @param field name, class, id and attribute
										$input->fields('Age', 'form_field','Age','placeholder="Enter age here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Sex', '*');
										// @param field name, class, id and attribute
										$input->radio('Sex',array('Male','Female'));
									?>
								</div>
							</div>
						</div>

						<?php $input->label('Parent/Guardian\'s Information'); ?>

						<div class="form_box">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Parent/Guardian\'s Name ', '*');
										// @param field name, class, id and attribute
										$input->fields('Parent_or_Guardians_Name', 'form_field','Parent_or_Guardians_Name','placeholder="Enter parent or guardians name here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Address', '*');
										// @param field name, class, id and attribute
										$input->fields('Address', 'form_field','Address','placeholder="Enter address here"');
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
										// @param label-name, if required
										$input->label('State', '*');
										// @param field name, class, id and attribute
										$input->select('State_', 'form_field', $state);
									?>
								</div>
							</div>
						</div>
						<div class="form_box">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Email', '*');
										// @param field name, class, id and attribute
										$input->fields('Email', 'form_field','Email','placeholder="Enter email here"');
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

						<?php $input->label('Person to Contact in Case of Emergency if Parent/Guardian Cannot be Reached'); ?>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Name', 'form_field','Name','placeholder="Enter name here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Address_', 'form_field','Address_','placeholder="Enter address here"');
										?>
									</div>
								</div>
							</div>


						<?php $input->label('Person Health Information'); ?>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Name of Child\'s Doctor', '*');
											// @param field name, class, id and attribute
											$input->fields('Name_of_Childs_Doctor', 'form_field','Name_of_Childs_Doctor','placeholder="Enter name of child\'s doctor here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Phone Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Phone_Number_', 'form_field','Phone_Number_','placeholder="Enter phone number here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Hospital Preference', '*');
											// @param field name, class, id and attribute
											$input->fields('Hospital_Preference', 'form_field','Hospital_Preference','placeholder="Enter hospital preference here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Please list down any allergies or dietary restrictions of the child','*');
											// @param field name, class, id and attribute
											$input->textarea('Please_list_down_any_allergies_or_dietary_restrictions_of_the_child', 'text form_field','Please_list_down_any_allergies_or_dietary_restrictions_of_the_child','placeholder="Enter any allergies or dietary restrictions of the child here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Additional Information');
											// @param field name, class, id and attribute
											$input->textarea('Additional_Information', 'text form_field','Additional_Information','placeholder="Enter additional information here"');
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
			Childs_Full_Name: "required",
			Birth_Date: "required",
			Age: "required",
			Sex: "required",
			Parent_or_Guardians_Name: "required",
			Address: "required",
			City: "required",
			State_: "required",
			Phone_Number: "required",
			Name: "required",
			Address_: "required",
			Name_of_Childs_Doctor: "required",
			Phone_Number_: "required",
			Hospital_Preference: "required",
			Email: {
				required: true,
				email: true
			},
			Please_list_down_any_allergies_or_dietary_restrictions_of_the_child: "required",
			secode: "required"
		},
		messages: {
			Childs_Full_Name: "",
			Birth_Date: "",
			Age: "",
			Sex: "",
			Parent_or_Guardians_Name: "",
			Address: "",
			City: "",
			State_: "",
			Phone_Number: "",
			Email: "",
			Name: "",
			Address_: "",
			Name_of_Childs_Doctor: "",
			Phone_Number_: "",
			Hospital_Preference: "",
			Please_list_down_any_allergies_or_dietary_restrictions_of_the_child: "",
			secode: ""
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
