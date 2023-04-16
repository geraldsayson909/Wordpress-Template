<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Flood Insurance Form';
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
		empty($_POST['Last_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||
		empty($_POST['Phone_Number']) ||
		empty($_POST['Fax']) ||
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
$best_contact = array('- Please Select -','Phone','Fax','Email');
$Marital_Status = array('- Please Select -','Married','Unmarried','Divorced');
$bathrooms = array('- Please Select -','None','1','2','3','4','5');
$fireplace = array('- Please Select -','None','1','2','3','4','5');
$units = array('- Please Select -','Condo','Single family residence','Douplex','Triplex','Fourplex','5 or more');
$garagetype = array('- Please Select -','Attached','Detached','Built-in');
$contstructiontype = array('- Please Select -','Frame','Brick/Masonry','Log','Adobe','Other');
$roof_type = array('- Please Select -','Asphalt Shingle','Wood Shingle','Tile','Concrete');
$roof_age = array('- Please Select -','1-10 years','11-20 years','over 20 years');
$exterior_type = array('- Please Select -','Wood Siding','Stucco on Frame','Stucco on Masonry','Paint on Masonry','Solid Brick','Other');
$foundation = array('- Please Select -','Slab','Raised');
$liability = array('- Please Select -','$100,000','$300,000','$500,000','$1,000,000');
$deductible = array('- Please Select -','500','750','1000','1500','200','2500','5000');
$alarmsystem = array('- Please Select -','None','Just at my home','Alert Monitoring Services','Notifies Policies/Fire Dept');
$distance = array('- Please Select -','0-3 miles','4-6 miles','7-10 miles');
$subsoil = array('- Please Select -','Filled land','Clay','Sand','Marsh','Rock','Others');
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


							<?php $input->info('Personal Information');?>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('First Name', '*', 'form_field');
										$input->masterfield('Zip Code', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Last Name', '*', 'form_field');
										$input->masterfield('Phone Number', '*', 'form_field');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Address', '*', 'form_field');
										$input->masterfield('Fax', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('City', '*', 'form_field');
										$input->masterfield('Email Address', '*', 'form_field');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('State', '', 'form_field', $state);
										$input->masterselect('Best way to contact', '', 'form_field', $best_contact);
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Age', '', 'form_field');
										$input->masterselect('Marital Status', '', 'form_field', $Marital_Status);
									?>
								</div>
							</div>

							<div class="form_box left">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masteradio('Gender', '', array('Male','Female'));
									?>
								</div>
							</div>
							<div class="clear"></div>

							<?php $input->info('About the property');?>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Property Type', '*', 'form_field');
										$input->masterselect('No. of Bathrooms', '', 'form_field', $bathrooms);
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masterselect('No. of fireplace', '', 'form_field', $fireplace);
										$input->masterselect('No. of units', '', 'form_field', $units);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masterfield('Living sq. footage', '', 'form_field');
										$input->masterfield('No. of levels', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masteradio('Swimming Pool', '', array('Yes','No'));
										$input->masteradio('Spa', '', array('Yes','No'));
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masteradio('AC', '', array('Yes','No'));
										$input->masteradio('Deck', '', array('Yes','No'));
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masteradio('Porch', '', array('Yes','No'));
										$input->masteradio('Year Built', '', array('Yes','No'));
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Year Home was Purchased', '', 'form_field');
										$input->masterfield('No. of Car Garage', '', 'form_field');
									?>
								</div>
							</div>



							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Type of Garage', '', 'form_field', $garagetype);
										$input->masterselect('Construction Type', '', 'form_field', $contstructiontype);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Roof Type', '', 'form_field', $roof_type);
										$input->masterselect('Roof Age', '', 'form_field', $roof_age);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Exterior Type', '', 'form_field', $exterior_type);
										$input->masterselect('Foundation', '', 'form_field', $foundation);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masteradio('Any erosion in the area?', '', array('Yes','No'));
										$input->masterselect('Nature of Subsoil', '', 'form_field', $subsoil);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masteradio('Type of Topography', '', array('Yes','No'));
										$input->masterselect('Distance to the closest fire department', '', 'form_field', $distance);
									?>
								</div>
							</div>

							<?php $input->info('Coverage');?>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Liability Requested', '', 'form_field', $liability);
										$input->masterselect('Deductible', '', 'form_field', $deductible);
									?>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Alarm System', '', 'form_field', $alarmsystem);
										$input->masteradio('Any losses during the last 5 years?', '', array('Yes','No'));
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Dwelling', '', 'form_field');
										$input->masterfield('Other Structure', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Personal Property', '', 'form_field');
										$input->masterfield('Loss of Use', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Personal Liability', '', 'form_field');
										$input->masterfield('Medical Payments', '', 'form_field');
									?>
								</div>
							</div>

							<?php $input->info('Insurance Information');?>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Prio / Current Carrier', '', 'form_field');
										$input->masterfield('No. of Claims (In last 3 years)', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('1. Type of Claim', '', 'form_field');
										$input->masterfield('Amount of Claim', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('2. Type of Claim', '', 'form_field');
										$input->masterfield('Amount of Claim', '', 'form_field','','Amount_of_claim_');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('3. Type of Claim', '', 'form_field');
										$input->masterfield('Amount of Claim', '', 'form_field','','Amount_of_claim__');
									?>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->mastertextarea('Additional Information (Please include any losses for the last 5 years)', '', 'form_field','','Additional_Information');
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
			First_Name: "required",
			Last_Name: "required",
			Address: "required",
			City: "required",
			Phone_Number: "required",
			Fax: "required",
			Email_Address: {
				required: true,
				email: true
			}
		},
		messages: {
			First_Name: "",
			Last_Name: "",
			Address: "",
			City: "",
			Phone_Number: "",
			Fax: "",
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
