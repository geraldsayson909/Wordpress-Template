<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Hotel Reservation Form';
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

	if( empty($_POST['Check_In']) ||
		empty($_POST['Check_Out']) ||
		empty($_POST['Rooms']) ||
		empty($_POST['Guest_per_room']) ||
		empty($_POST['Special_Rates']) ||
		empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Email_Address']) ||
		empty($_POST['Telephone_Number']) ||
		empty($_POST['Mobile_Number'])
		) {

	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
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
		$name = $_POST['First_Name'] . ' '.$_POST['Last_Name'];
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
$contact_options = array('Phone','Fax','Email');
$best_time = array('Please select', 'Anytime','Morning at Home','Morning at Work','Afternoon at Home','Afternoon at Work','Evening at Home','Evening at Work');

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
		<style>
			.js-labelFile {
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
				padding: 18px 10px;
				cursor: pointer;
			}
			.radioLine div {
				display: inline-block;
			}
			input[type="radio"] + label.error + label {
				padding-left: 28px;
			}
			label.error + label { position: relative; bottom: 0; }
			label.error { height: 0 !important; padding: 0!important;}
		</style>
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
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterdatepicker('Check In', '*', 'form_field', 'Enter check in date here');
										$input->masterdatepicker('Check Out', '*', 'form_field', 'Enter check out date here');
									?>
								</div>
							</div>

							<?php $input->info('Guest Information');?>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Rooms', '*', 'form_field');

										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Guest per room', '*', 'form_field');
									?>
								</div>
							</div>



							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Special Rates', '*');
											// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->radioline('Special Rates', array ('None', 'Senior/Discount', 'Military', 'Corporate/Promotional Code', 'Group Code'));
										?>
									</div>
								</div>
							</div>


							<?php $input->info('Personal Details');?>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('First Name', '*', 'form_field');
										$input->masterfield('Last Name', '*', 'form_field');
									?>

								</div>
							</div>

							<?php $input->info('Contact Details');?>
							<p>Let us know where we can send your booking confimation. We will not use your details for marketing promotions.</p>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Email Address', '*', 'form_field');

										// @param field name, required, class, options, id, attribute
										$input->masterfield('Telephone Number', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box left">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Mobile Number', '*', 'form_field');
									?>
								</div>
							</div>

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
			Check_In: "required",
			Check_Out: "required",
			Rooms: "required",
			Guest_per_room: "required",
			Special_Rates: "required",
			First_Name: "required",
			Last_Name: "required",
			Email_Address: "required",
			Telephone_Number: "required",
			Mobile_Number: "required"
		},
		messages: {
			Check_In: "",
			Check_Out: "",
			Rooms: "",
			Guest_per_room: "",
			Special_Rates: "",
			First_Name: "",
			Last_Name: "",
			Email_Address: "",
			Telephone_Number: "",
			Mobile_Number: ""
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
</script>
</body>
</html>
