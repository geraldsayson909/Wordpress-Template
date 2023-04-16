<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Boat Insurance Form';
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

	if( empty($_POST['First_Name']) ||
		empty($_POST['Last_Name'])
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
$status = array('Single','Married and lives with spouse','Married but separated','Divorced','Widowed');
$best_day = array('Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$best_time = array('Anytime','Morning','Afternoon','Evening');
$garage = array('Built in','Attached to home','Not attached to home','Carport','No Garage');
$security = array('None','Monitored','Unmonitored');
$fire = array('None','Monitored','Unmonitored');
$ownership_status = array('Paid For','Financed','Leased');
$bodily = array('$10,000 - $50,000','$50,000 - $100,000','$100,000 - $150,000','$150,000 - $200,000','$200,000 - $250,000','$250,000 - $300,000','$300,000 - $350,000','$400,000 - $450,000','$450,000 - $500,000');
$property = array('$10,000 - $50,000','$50,000 - $100,000','$100,000 - $150,000','$150,000 - $200,000','$200,000 - $250,000','$250,000 - $300,000','$300,000 - $350,000','$400,000 - $450,000','$450,000 - $500,000');
$uninsured_bodily = array('$10,000 - $50,000','$50,000 - $100,000','$100,000 - $150,000','$150,000 - $200,000','$200,000 - $250,000','$250,000 - $300,000','$300,000 - $350,000','$400,000 - $450,000','$450,000 - $500,000');
$uninsured_property = array('$10,000 - $50,000','$50,000 - $100,000','$100,000 - $150,000','$150,000 - $200,000','$200,000 - $250,000','$250,000 - $300,000','$300,000 - $350,000','$400,000 - $450,000','$450,000 - $500,000');
$comprehensive = array('$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');
$collision = array('$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');
$custom_equipment = array('$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');
$loss = array('$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');

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

							<?php $input->info('Customer Information');?>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('First Name', '*', 'form_field');
										$input->masterfield('Last Name', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param label-name, if required
										$input->label('Date of Birth');
										// @param field name, class, id and attribute
										$input->fields('Date_of_Birth', 'form_field Date','Date_of_Birth','placeholder="Enter date of birth here"');
									?>
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masteradio('Gender','',array('Male','Female'));
									?>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterselect('Marital Status', '', 'form_field', $status);
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Occupation', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, options, id, attribute
										$input->masterfield('Email Address', '*', 'form_field');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Phone Number', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterselect('Best day to contact', '', 'form_field', $best_day);
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterselect('Best time to contact', '', 'form_field', $best_time);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, options, id, attribute
										$input->masterfield('Driver\'s Lincense', '', 'form_field');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterselect('Licensed State', '', 'form_field', $state);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, options, id, attribute
										$input->masterfield('Year Licensed', '', 'form_field');
									?>
									<?php
										// @param label-name, if required
										$input->masteradio('Have you had any boating experience?', '', array('Yes','No'));
									?>
								</div>
							</div>
							<div class="clear"></div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param label-name, if required
										$input->masteradio('Have you had a coastguard or power squadron course?', '', array('Yes','No'));
									?>
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masteradio('Have you had any accidents and violations in the past 3 years?', '', array('Yes','No'));
									?>
								</div>
							</div>

							<?php $input->info('Vessel Information');?>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param label-name, if required
										$input->masterfield('Year Purchased', '', 'form_field');
									?>
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masterfield('Purchased Price', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param label-name, if required
										$input->masterfield('Make', '', 'form_field');
									?>
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masterfield('Model', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param label-name, if required
										$input->masterfield('Hull ID Number', '', 'form_field');
									?>
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masteradio('Hull', '', array('Fiberglass','Aluminum', 'Inflatable'),'','',3);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masterfield('Mooring Zip Code', '', 'form_field');
									?>
									<?php
										// @param label-name, if required
										$input->masterfield('Number of Motors', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box left">
								<div class="form_box_col1">
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masteradio('Type of Propulsion', '', array('Inboard','Ourboard', 'Jet'),'','',3);
									?>
								</div>
							</div>
							<div class="clear"></div>

							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->mastertextarea('List of all Safety Equipments', '', 'form_field');
									?>
								</div>
							</div>

							<?php $input->info('Coverage Requested / Desired');?>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param label-name, if required
										$input->masterselect('Body Injury', '', 'form_field', $bodily);
									?>
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masterselect('Property Damage', '', 'form_field', $property);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param label-name, if required
										$input->masterselect('Uninsured / Under-insured Motorist Bodily Injury', '', 'form_field', $uninsured_bodily);
									?>
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masterselect('Uninsured / Under-insured Property Damage', '', 'form_field', $uninsured_property);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param label-name, if required
										$input->masterselect('Comprehensive Deductible', '', 'form_field', $comprehensive);
									?>
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masterselect('Collision Deductible', '', 'form_field', $collision);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param label-name, if required
										$input->masterselect('Custom Equipment', '', 'form_field', $custom_equipment);
									?>
									<?php
										// @param field name, required, value, id, attrib, rows
										$input->masterselect('Loss of Use', '', 'form_field', $loss);
									?>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<?php
										$input->label('Additional Comments', '');
										// @param field name, class, id and attribute
										$input->textarea('Additional_Comments', 'text form_field','Additional_Comments','placeholder="Enter additional comments here"');
									?>
								</div>
							</div>


							<div class = "form_box5 secode_box">
								<div class="inner_form_box1 recapBtn">
									<div class="g-recaptcha" data-sitekey="6LfNRm0UAAAAAAx0Lp6Eqx-En2RKFqdULuqR7BjK"></div>
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
			First_Name: "required",
			Last_Name: "required",
			Phone_Number: "required",
			Email_Address: {
				required: true,
				email: true
			}
		},
		messages: {
			First_Name: "",
			Last_Name: "",
			Phone_Number: "",
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
			$('.Date').datepick({
				showTrigger: '<img src="images/calendar.png" alt="Select date" style="position: absolute; right: 16px; top: 20px;" />'
			});

});
</script>
</body>
</html>
