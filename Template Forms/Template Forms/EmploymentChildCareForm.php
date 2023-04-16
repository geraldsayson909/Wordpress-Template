<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Employment Child Care Form';
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

	if( empty($_POST['Full_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||
		empty($_POST['Zip_Code']) ||
		empty($_POST['Phone_Number'])
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
				if($key == 'Verify_Email') continue;
				elseif($key == 'Verify_Password') continue;
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
		
		// when form has attachments, uncomment code below
		if(!empty($_FILES['attachment']['name'])){
			$attachmentsdir = ABSPATH.'onlineforms/attachments/';
			$validextensions = array('pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'); // include file type here
			for($i = 0 ; $i < count($_FILES['attachment']['name']) ; $i++ ){

				$checkfile =  $attachmentsdir.$_FILES['attachment']['name'][$i];
				//$tobeuploadfile = $_FILES['attachment']['tmp_name'][$i];
				$tempfile = pathinfo($_FILES['attachment']['name'][$i]);
				if(in_array(strtolower($tempfile['extension']), $validextensions)){
					if(file_exists($checkfile)){
						$storedfile = $tempfile['filename'].'-'.time().'.'.$tempfile['extension'];
					}else{
						$storedfile = $_FILES['attachment']['name'][$i];
					}

					if( move_uploaded_file($_FILES['attachment']['tmp_name'][$i], $attachmentsdir.$storedfile) ){
						$attachments[] = $storedfile;
					}
				}
			}
		}

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
											$input->label('State');
											// @param field name, class, id and attribute
											$input->select('State', 'form_field',$state);
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
											$input->label('Email Address');
											// @param field name, class, id and attribute
											$input->fields('Email_Address', 'form_field','Email_Address','placeholder="Enter email address here"');
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
											// @param label-name, if required
											$input->label('Are you willing to work full-time or part-time?');
											// @param field name, class, id and attribute
											$input->radio('Willing_to_work_full-time_or_part-time',array('Yes','No'),'','',2);
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Are you fluent in speaking English?');
											// @param field name, class, id and attribute
											$input->radio('Fluent_in_speaking_English',array('Yes','No'),'','',2);
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('What other languages do you speak / write besides English?');
											// @param field name, class, id and attribute
											$input->textarea('Languages_fo_you_speak_or_write_besides_english', 'text form_field','Languages_fo_you_speak_or_write_besides_english','placeholder="Enter other languages here"');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Are you CPR Certified?');
											// @param field name, class, id and attribute
											$input->radio('CPR Certified',array('Yes','No'),'','',2);
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Are you trained with First Aid?');
											// @param field name, class, id and attribute
											$input->radio('Trained_with_first_aid',array('Yes','No'),'','',2);
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Certifications / Educational Background:');
											// @param field name, class, id and attribute
											$input->radioline('Certifications',array('Early Childhood Education Diploma','Early Childhood Education Degree Diploma','Early Childhood Education Assistant','CEGEP','Others please specify'),'','',1);
											$input->fields('Others', 'form_field','Others','placeholder="Others please specify here" disabled');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Have you had previous experience in a Child Care or Day Care facility?');
											// @param field name, class, id and attribute
											$input->textarea('Previous_experience_in_a_Child_Care_or_Day_Care_facility', 'text form_field','Previous_experience_in_a_Child_Care_or_Day_Care_facility','placeholder="Please describe previous work experience in detail here" style="height: 173px;"');
										?>
										<div style="margin: 15px 0 0 0;">
										<?php
											// @param label-name, if required
											$input->label('How soon can you start?');
											// @param field name, class, id and attribute
											$input->fields('How_soon_can_you_start', 'form_field Date','How_soon_can_you_start','placeholder="Enter date here"');
										?>
										</div>
									</div>
								</div>
							</div>

							<div class="form_box left">
								<div class="form_box_col1">

									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Attach Resume');
										?>
										<input type="file" name="attachment[]" id="file" class="input-file" multiple>
										<label for="file" class="btn btn-tertiary js-labelFile">
											<span class="js-fileName">Choose a file</span>
											<span class="icon"><i class="fas fa-plus-circle"></i></span>
										</label>
									</div>
								</div>
							</div>

							<div class="form_box left">
								<div class="form_box_col1">

								</div>
							</div>
							<div class="clearfix"></div>

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
			Full_Name: "required",
			Address: "required",
			City: "required",
			Zip_Code: "required",
			Phone_Number: "required"

		},
		messages: {
			Full_Name: "",
			Address: "",
			City: "",
			Zip_Code: "",
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


	$('input[name="Certifications"]').change(function(){
		if($(this).val() == "Others please specify"){
			$('input[name="Others"]').removeAttr('disabled','disabled');
		}
		else{
			$('input[name="Others"]').attr('disabled','disabled');
		}
	});

});
</script>
</body>
</html>
