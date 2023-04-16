<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Recommended';
$prompt_message = '<span class="required-info">* Required Information</span>';
$message = 'Take a look at this website:';

error_reporting(0); // Turn off all error reporting

define('MAIL_TYPE', 1); // 1 - html, 2 - txt
require_once 'config.php';
$link = ' '.MAIL_DOMAIN;

if ($_POST){

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "secret={$recaptcha_privite}&response={$_POST['g-recaptcha-response']}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	$result = json_decode($server_output);
	curl_close ($ch);

	if(empty($_POST['To_Email']) ||
		empty($_POST['To_Name']) ||
		empty($_POST['From_Name']) ||
		empty($_POST['From_Email'])) {

	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';

	$asteriskEmail = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
	}
	else if((!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['To_Email'])))) || (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['From_Email'])))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}else{


        require_once 'swiftmailer/mail.php';

        $body = '<div align="left" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px;">
            <div style="border:8px double #c3c3d0; padding:12px;">
            <div align="center" style="font-size:22px; font-family:Times New Roman, Times, serif; color:#051d38;">'.COMP_NAME.'</div>
            <div align="center" style="color:#990000; font-style:italic; font-size:13px; font-family:Arial;">('.$formname.')</div>
            <p>&nbsp;</p>
            <table width="90%" cellspacing="2" cellpadding="5" align="center" style="font-family:Verdana; font-size:13px">
                ';

            foreach($_POST as $key => $value){
                if($key == 'submit') continue;
                elseif($key == 'To_Name') continue;
                elseif($key == 'To_Email') continue;
                elseif($key == 'From_Name') continue;
                elseif($key == 'From_Email') continue;
                elseif($key == 'Subject') continue;
				elseif($key == 'g-recaptcha-response') continue;

                if(!empty($value)){
					$body .= '<tr style="text-align:center;"><td><strong>Take a look at this website :</strong>'.$link.'</td></tr>';
                }
            }
            $body .= '
            </table>

            </div>
            </div>';

        $subject = COMP_NAME . " [" . $formname . "]";

        $templateVars = array('{tablelist}' => $body);
		$to_email = $_POST['To_Email'];
        $sent = Mail::Send($template, $subject, $templateVars, $to_email, $to_name, $from_email, $from_name, $attachments);

        if($sent == 1 || $sent == true) {
			$prompt_message = '<div id="success"><div class="message"><span>THANK YOU</span><br/> <span>for recommending a friend.</span><br/><span>We will be in touch with them soon.</span><p class="close">x</p></div></div>';
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
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('To (Name)', '*', 'form_field','Enter recipient\'s name here','To_Name');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('To (Email)', '*', 'form_field','Enter recipient\'s email here','To_Email');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('From (Name)', '*', 'form_field','Enter sender\'s name here','From_Name');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('From (Email)', '*', 'form_field','Enter sender\'s email here','From_Email');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Message');
											// @param field name, class, id and attribute
											$input->textarea('Message', 'text form_field','Message','readonly="readonly" cols="88"','Take a look at this website:'.$link);
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
			To_Name: "required",
			To_Email: {
				required: true,
				email: true
			},
			From_Name: "required",
			From_Email: {
				required: true,
				email: true
			},
			secode: "required"
		},
		messages: {
			To_Name: "",
			To_Email: "",
			From_Name: "",
			From_Email: "",
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
