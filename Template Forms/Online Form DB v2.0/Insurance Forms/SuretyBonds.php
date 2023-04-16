<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Garage Liability Form';
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

	if( empty($_POST['Contact_First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Applicants_Full_Name']) ||
		empty($_POST['Business_Address_Street']) ||
		empty($_POST['City']) ||
		empty($_POST['Email_Address'])
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

			require_once 'swiftmailer/mail.php';
			// save data form on database
			include 'savedb.php';


			// save data form on database
			$subject = $formname ;
			$attachments = array();

			//name of sender
			$name = $_POST['Contact_First_Name'].' '.$_POST['Last_Name'];
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
$contact_options = array('- Please Select -','Phone','Fax','Email');
$best_way = array('- Please Select -','Phone','Fax','Email');
$YN = array('- Please Select -','Yes','No');
$business = array('- Please Select -','Solo Proprietor','Partnership','Corporation','LLC');
$insurance = array('- Please Select -','$100,000/200,000','$300,000/600,000','$500,000/1,000,000','1,000,000/2,000,000');
$limits = array('- Please Select -','1 Million','2 Million','3 Million','4 Million','5 Million');
?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>

		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
		<link rel="stylesheet" href="style.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/datepicker.css">
		<link rel="stylesheet" href="css/jquery.datepick.css" type="text/css" media="screen" />

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

							<div class="infobox">
									<div class="input textarea" ><input type="hidden" value=":" name="Whether what you need is commercial surety such as probate and federal bonds, or fidelity bonds to protect your business against employee theft and dishonesty, we can find the surety bond that you are looking for. Fill out the online form below as completely as possible. Our agents will get back to you for your quote."/>
									Whether what you need is commercial surety such as probate and federal bonds, or fidelity bonds to protect your business against employee theft and dishonesty, we can find the surety bond that you are looking for. Fill out the online form below as completely as possible. Our agents will get back to you for your quote.
									</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Contact First Name', '*', 'form_field');
										$input->masterfield('Last Name', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Applicant\'s Full Name as it appears on bond', '*', 'form_field','','Applicants_Full_Name');
										$input->masterfield('Federal I.D. Number', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Business Address Street', '*', 'form_field');
										$input->masterfield('City', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('State', '', 'form_field', $state);
										$input->masterfield('Zip Code', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Phone Number', '', 'form_field');
										$input->masterfield('Fax Number', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Email Address', '*', 'form_field');
										$input->masterselect('Best way to contact', '', 'form_field', $best_way);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterdatepicker('Date Business Establish', '', 'form_field');
										$input->masterselect('Type of Business', '', 'form_field', $business);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Do you have Business Insurance', '', 'form_field');
										$input->masterfield('Liability Limits', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterdatepicker('Property Damage Limits', '', 'form_field');
										$input->masterselect('Have your ever had a business fall?', '', 'form_field', $YN);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Have your ever had a business files bankruptcy?', '', 'form_field', $YN);
										$input->masterselect('Has the owner of the business ever filed for bankruptcy?', '', 'form_field', $YN);
									?>
								</div>
							</div><hr/>


							<?php $input->info('Bond Information');?>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Nature of Bond Required', '', 'form_field');
										$input->masterfield('Obligee (To whom bond is to be given)', '', 'form_field','','Obligee');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Amouint of Bond $', '', 'form_field');
										$input->masterdatepicker('Effective Date', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Term of Bond', '', 'form_field');
										$input->masterfield('Has applicant been declined for a bond', '', 'form_field');
									?>
								</div>
							</div><hr/>

							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->mastertextarea('Additional Information', '', 'form_field');
									?>
								</div>
							</div>


							<div class = "form_box5 secode_box">
								<div class="inner_form_box1 recapBtn">
									<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
									<div class="btn-submit"><button class = "form_button">Submit</button></div>
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
	<script src = "js/plugins.js"></script>



	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Contact_First_Name: "required",
			Last_Name: "required",
			Applicants_Full_Name: "required",
			Business_Address_Street: "required",
			City: "required",
			Email_Address: {
				required: true,
				email: true
			}
		},
		messages: {
			Contact_First_Name: "",
			Last_Name: "",
			Applicants_Full_Name: "",
			Business_Address_Street: "",
			City: "",
			Email_Address: ""
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
