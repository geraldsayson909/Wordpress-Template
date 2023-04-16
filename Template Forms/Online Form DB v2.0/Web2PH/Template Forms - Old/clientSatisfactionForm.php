<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Client Satisfaction Survey Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php';
if ($_POST){
		if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}elseif(empty($_POST['Quality_of_the_services_you_received_from_us']) and empty($_POST['Information_we_provided_on_our_website']) and
	empty($_POST['Staff_in_terms_of_efficiency']) and
	empty($_POST['Responsiveness_to_feedback']) and
	empty($_POST['Would_you_recommend_us_to_friends_and_family']) and
	empty($_POST['Reason'])){
			$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Your feedback is helpful. Please answer at least one question.</span><br/><p class="rclose">x</p></div></div>';
	}
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
		$name = "Message From Your Site";
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

		<link rel="stylesheet" href="style.min.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<script src='https://www.google.com/recaptcha/api.js'></script>
		
		<link rel="stylesheet" href="css/proweaverPhone.css" type="text/css"/>
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>
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
											// @param label-name, if required
											$input->label('1. Please rate the quality of the services you received from us:');
											// @param field name, class, id and attribute
											$input->radio('Quality_of_the_services_you_received_from_us',array('Excellent','Fair','Good','Poor'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('2. Please rate the information we provided on our website:');
											// @param field name, class, id and attribute
											$input->radio('Information_we_provided_on_our_website',array('Excellent','Fair','Good','Poor'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('3. Please rate our staff in terms of efficiency:');
											// @param field name, class, id and attribute
											$input->radio('Staff_in_terms_of_efficiency',array('Excellent','Fair','Good','Poor'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('4. Please rate our responsiveness to feedback:');
											// @param field name, class, id and attribute
											$input->radio('Responsiveness_to_feedback',array('Excellent','Fair','Good','Poor'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('5. Please rate your overall experience with our services:');
											// @param field name, class, id and attribute
											$input->radio('Overall_experience_with_our_services',array('Excellent','Fair','Good','Poor'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('6. Would you recommend us to friends and family?');
											// @param field name, class, id and attribute
											$input->radio('Would_you_recommend_us_to_friends_and_family',array('Yes','No'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Reason');
											// @param field name, class, id and attribute
											$input->textarea('Reason', 'text form_field','Reason','placeholder="Enter your reason here"');
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
/* 			First_Name: "required",
			Last_Name: "required",
			Address: "required",
			Question_Comment: "required",
			Email_Address: {
				required: true,
				email: true
			},
			secode: "required"	 */
		},
		messages: {
/* 			First_Name: "",
			Last_Name: "",
			Address: "",
			Question_Comment: "",
			Email_Address: "",
			secode: "" */
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
