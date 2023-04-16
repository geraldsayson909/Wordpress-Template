<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Admission College Form';
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

	if(empty($_POST['Full_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||
		 empty($_POST['State']) ||
		empty($_POST['Phone_Number']) ||
		empty($_POST['Date_of_Birth']) ||
		empty($_POST['Age']) ||
		empty($_POST['Marital_Status']) ||
		empty($_POST['Graduation_Date']) ||
		empty($_POST['Highest_Education_Level_Attained']) ||
		empty($_POST['GED_Scores']) ||
		empty($_POST['GED_School']) ||
		empty($_POST['GED_Date']) ||
		empty($_POST['Person_to_contact_Name']) ||
		empty($_POST['Person_to_contact_Address']) ||
		empty($_POST['Person_to_contact_Phone_Number']) ||
		empty($_POST['Email_Address']) ) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(!$result_recaptcha->success){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}else{

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
		include 'send_email_curl.php';

		// save data form on database
		include 'savedb.php';

		// save data form on database
		$subject = $formname ;
		$attachments = array();

	 	//name of sender
		$name = $_POST['Full_Name'];
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
$marital= array('- Please Select -','Single','Married','Divorced','Widowed');
$preferToContact = array('- Please Select -','Phone','Fax','Email');
$marital_status = array('- Please Select -','Single','Married','Divorced','Widowed');

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
			@media only screen and (max-width : 780px) {
				.strong_head{margin-top: 20px !important;}
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
								<p class="strong_head" >Applicant's Information</p><input type="hidden" name="Applicants_Information" value=":" />
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Full Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Full_Name', 'form_field','Full_Name','placeholder="Enter full name here"');
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
											// @param label-name, if required
											$input->label('State', '*');
											// @param field name, class, id and attribute
											$input->select('State', 'form_field', $state);
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Email Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Email_Address', 'form_field','Email_Address','placeholder="Enter email address here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Phone Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Phone_Number', 'form_field','Phone_Number','placeholder="Enter phone number here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Date of Birth','*');
											// @param field name, class, id and attribute
											$input->fields('Date_of_Birth', 'form_field Date','Date_of_Birth','placeholder="Enter date of birth here"');
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
											// @param label-name, if required
											$input->label('Marital Status', '*');
											// @param field name, class, id and attribute
											$input->select('Marital_Status', 'form_field', $marital_status);
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<p class="strong_head" >Educational Background</p><input type="hidden" name="Educational_Background" value=":" />
							</div>

							<div class="information">
								Please prepare official documents, transcripts, and/or GED test results which may be used during the evaluation
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('School Last Attended', '*');
											// @param field name, class, id and attribute
											$input->fields('School_Last_Attended', 'form_field','School_Last_Attended','placeholder="Enter school last attended here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('City', '*');
											// @param field name, class, id and attribute
											$input->fields('City_', 'form_field','City_','placeholder="Enter city here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('State', '*');
											// @param field name, class, id and attribute
											$input->select('State_', 'form_field', $state);
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Graduation Date','*');
											// @param field name, class, id and attribute
											$input->fields('Graduation_Date', 'form_field Date','Graduation_Date','placeholder="Enter graduation date here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Highest Education Level Attained', '*');
											// @param field name, class, id and attribute
											$input->fields('Highest_Education_Level_Attained', 'form_field','Highest_Education_Level_Attained','placeholder="Enter highest education level attained here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('GED Scores', '*');
											// @param field name, class, id and attribute
											$input->fields('GED Scores', 'form_field','GED Scores','placeholder="Enter GED scores here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('GED School', '*');
											// @param field name, class, id and attribute
											$input->fields('GED School', 'form_field','GED School','placeholder="Enter GED school here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('GED Date','*');
											// @param field name, class, id and attribute
											$input->fields('GED_Date', 'form_field Date','GED_Date','placeholder="Enter GED date here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<p class="strong_head" >Person to Contact in Case of Emergency</p><input type="hidden" name="Person_to_Contact_in_Case_of_Emergency" value=":" />
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Person_to_contact_Name', 'form_field','Person_to_contact_Name','placeholder="Enter name here"');
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
											$input->fields('Person_to_contact_Address', 'form_field','Person_to_contact_Address','placeholder="Enter address here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Phone Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Person_to_contact_Phone_Number', 'form_field','Person_to_contact_Phone_Number','placeholder="Enter phone number here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
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
			Full_Name: "required",
			Address: "required",
			City: "required",
			City_: "required",
			State: "required",
			State_: "required",
			Phone: "required",
			Date_of_Birth: "required",
			Age: "required",
			Marital_Status: "required",
			School_Last_Attended: "required",
			City_of_School: "required",
			School_State: "required",
			Graduation_Date: "required",
			Highest_Education_Level_Attained: "required",
			GED_Scores: "required",
			GED_School: "required",
			GED_Date: "required",
			Person_to_contact_Name: "required",
			Person_to_contact_Address: "required",
			Person_to_contact_Phone_Number: "required",
			Phone_Number: "required",
			Email_Address: {
				required: true,
				email: true
			},
		},
		messages: {
			Full_Name: "",
			Address: "",
			City: "",
			City_: "",
			State: "",
			State_: "",
			Phone: "",
			Date_of_Birth: "",
			Age: "",
			Marital_Status: "",
			School_Last_Attended: "",
			City_of_School: "",
			School_State: "",
			Graduation_Date: "",
			Highest_Education_Level_Attained: "",
			GED_Scores: "",
			GED_School: "",
			GED_Date: "",
			Person_to_contact_Name: "",
			Person_to_contact_Address: "",
			Person_to_contact_Phone_Number: "",
			Email_Address: "",
			Phone_Number: ""
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
