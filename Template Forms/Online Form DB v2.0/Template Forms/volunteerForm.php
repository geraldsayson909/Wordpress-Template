<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Volunteer Form';
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

	if( empty($_POST['How_did_you_first_hear_about_our_program']) ||
		empty($_POST['Please_list_any_of_your_special_skills_and_other_languages_spoken']) ||
		empty($_POST['Previous_volunteer_experience_please_describe_it_here']) ||
		empty($_POST['How_would_you_like_to_receive_information_from_us']) ||
		empty($_POST['Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['Telephone']) ||
		empty($_POST['Interested_in_helping']) ||
		empty($_POST['Email_Address'])
		) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
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
		$name = $_POST['Name'];
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
$areasNeed = array('Please select','Fundraising','PR/Marketing','Office Assistance','Volunteer Recruitment','Special Events');
?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>

		<link rel="stylesheet" href="style.min.css?ver23asas">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
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
											$input->label('How did you first hear about our program?', '*');
											// @param field name, class, id and attribute
											$input->fields('How_did_you_first_hear_about_our_program', 'form_field','How_did_you_first_hear_about_our_program','placeholder="Enter detail here"');
										?>
									</div>

								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Please list any of your special skills and other languages spoken', '*');
											// @param field name, class, id and attribute
											$input->textarea('Please_list_any_of_your_special_skills_and_other_languages_spoken', 'text form_field','Please_list_any_of_your_special_skills_and_other_languages_spoken','placeholder="Enter special skills and other languages spoken here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('If you have previous volunteer experience please describe it here', '*');
											// @param field name, class, id and attribute
											$input->textarea('Previous_volunteer_experience_please_describe_it_here', 'text form_field','Previous_volunteer_experience_please_describe_it_here','placeholder="Enter previous volunteer experience and description here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('How would you like to receive information from us?', '*');
											// @param field name, class, id and attribute
											$input->fields('How_would_you_like_to_receive_information_from_us', 'form_field','How_would_you_like_to_receive_information_from_us','placeholder="Enter detail here"');
										?>
									</div>
								</div>
							</div>

							<p class="strong_head">Contact Information</p>
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
												$input->fields('Address', 'form_field','Address','placeholder="Enter address here"');
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
											$input->fields('Telephone', 'form_field','Telephone','placeholder="Enter telephone number here"');
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
								<div class="form_box_col">
									<div class="group">
										<?php
											$input->label('If you are interested in helping in any of the following areas of need, please select one', '*');
											// @param field name, class, id and attribute
											$input->select('Interested_in_helping', 'form_field',$areasNeed);

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
	<script src = "js/jquery-1.9.0.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script src = "js/plugins.min.js"></script>


	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			How_did_you_first_hear_about_our_program: "required",
			Please_list_any_of_your_special_skills_and_other_languages_spoken: "required",
			Previous_volunteer_experience_please_describe_it_here: "required",
			How_would_you_like_to_receive_information_from_us: "required",
			Name: "required",
			Address: "required",
			Telephone: "required",
			Interested_in_helping: "required",
			Email_Address: {
				required: true,
				email: true
			},
		},
		messages: {
			How_did_you_first_hear_about_our_program: "",
			Please_list_any_of_your_special_skills_and_other_languages_spoken: "",
			Previous_volunteer_experience_please_describe_it_here: "",
			How_would_you_like_to_receive_information_from_us: "",
			Name: "",
			Address: "",
			Telephone: "",
			Interested_in_helping: "",
			Email_Address: "",
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

});
</script>
</body>
</html>
