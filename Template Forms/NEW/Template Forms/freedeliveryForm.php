<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Free Delivery Form';
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

	if( empty($_POST['Name_of_Company_or_Individual']) ||
		empty($_POST['Consignee']) ||
		empty($_POST['Address']) ||
		empty($_POST['Phone_Number']) ||
		empty($_POST['Notify_when_products_are_ready']) ||
		empty($_POST['Email_Address'])
		) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
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
					}else if($key == "docu1"){
						$body .= '<tr><td colspan="2" line-height:30px"><input type="checkbox" checked disabled /> Yes, I want free pick-up and delivery.</td></tr>';
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
		$name = $_POST['Name_of_Company_or_Individual'];
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

?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>
		 <link rel="stylesheet" href="css/intlTelInput.css">
		<link rel="stylesheet" href="style.min.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />
		<link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">

		<link rel="stylesheet" href="css/proweaverPhone.css" type="text/css"/>
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>

		<script src='https://www.google.com/recaptcha/api.js'></script>

		<style media="screen">
		body { font-family: Muli,sans-serif; }
		::placeholder { font-family: Muli,sans-serif;}
		::-moz-placeholder { font-family: Muli,sans-serif; }
		::-webkit-input-placeholder { font-family: Muli,sans-serif; }
		:-ms-input-placeholder { font-family: Muli,sans-serif;}
		select.form_field { font-family: Muli,sans-serif;}

		.custom-ack{font-weight:bold;font-size: 17px;margin-bottom: 0px !important;}
		input[type=checkbox] {transform: scale(1.5);-webkit-appearance: checkbox;-moz-appearance:checkbox;appearance:checkbox;}
		#icon { position: absolute; padding:10px 30px 5px 5px; background: #ccc; height: 62px; color: #fff; font-size: 31px; }

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

							<p style="background:#ccc;padding:10px;font-size:18px;">Who is this delivery for?</p>
							<input type="hidden" name="Who is this delivery for?" value=":">

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Name of Company or Individual', '*');
											// @param field name, class, id and attribute
											$input->fields('Name_of_Company_or_Individual', 'form_field','Name_of_Company_or_Individual','placeholder="Enter name here"');
										?>
									</div>
										<div class="group">
											<?php
												$input->label('Consignee', '*');
												// @param field name, class, id and attribute
												$input->fields('Consignee', 'form_field','Consignee','placeholder="Enter full name here"');
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
												$input->label('Phone Number', '*');
												// @param field name, class, id and attribute
												$input->fields('Phone_Number', 'form_field','Phone_Number','placeholder="Enter number here"');
											?>
										</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
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
										<p class="custom-ack"> <input type="checkbox" name="docu1"> Yes, I want free pick-up and delivery.</p>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Would you like us to notify you when your product(s) are ready?', '*');
											// @param field name, class, id and attribute
											$input->select('Notify_when_products_are_ready', 'form_field', array('- Please select -','No, thanks','Yes, by email','Yes, by phone'));
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
	<!--?php $input->phone(true); ?-->
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>
	<script src = "js/jquery.mask.min.js"></script>
	<script src = "js/proweaverPhone.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.min.js"></script>

	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Name_of_Company_or_Individual: "required",
			Consignee: "required",
			Address: "required",
			Phone_Number: "required",
			Notify_when_products_are_ready: "required",
			docu1: "required",
			Email_Address: {
				required: true,
				email: true
			}
		},
		messages: {
			Name_of_Company_or_Individual: "",
			Consignee: "",
			Address: "",
			Phone_Number: "",
			Notify_when_products_are_ready: "",
			Email_Address: "",
			docu1: ""
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
			$('.g-recaptcha').addClass('errors').attr('id','recaptcha');
		  }
	});

});

</script>
</body>
</html>
