<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Land Lord Insurance Form';
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
		empty($_POST['Phone_Number']) ||
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
$contact_options = array('- Please Select -','Phone','Fax','Email');
$best_time = array('- Please Select -','Anytime','Morning at Home','Morning at Work','Afternoon at Home','Afternoon at Work','Evening at Home','Evening at Work');
$property = array('- Please Select -','house','villa/townhouse','unit','caravan','hotel/motel/hostel','mobile home','retirement village unit/villa','nursing home unit/villa','guest house/boarding house','granny flat','other');
$exterior = array('- Please Select -','double brick','brick veneer','timber','weather board','steel','concrete','fibro','stone/sandstone','polystyrene',' Asbestos cement','Mud brick','other construction');
$roof = array('- Please Select -','concrete tiles','fibro','metal/iron','slate','tile','copper','tin','shingle','thatched','other');
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
								// @param field name, required, class, replaceholder, rename, id, attrib
								$input->masterdatepicker('Date of Birth', '', 'form_field');
								$input->masteradio('Gender', '', array('Male','Female'));
							  ?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib

									$input->masterselect('Marital_Status','', 'form_field',  array('Single','Married and lives with spouse','Married but separeted','Divorced','Widowed'));
									$input->masterfield('Occupation', '', 'form_field');
								?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterfield('Email Address', '*', 'form_field');
									$input->masterfield('Phone Number', '*', 'form_field');
								?>

							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterselect('Best_day_to_contact', '', 'form_field',  array('- Please Select -','Any day','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thurdsay','Friday','Saturday','Sunday'),'','','2');
								?>
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterselect('Best_time_to_contact', '', 'form_field',  array('- Please Select -','Any time','Morning','Afternoon','Evening'));
								?>
							</div>
						</div>

						<?php $input->info('Property / Home Details');?>
						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterfield('Approximate Year Built', '', 'form_field');
									$input->masterfield('Approximate square footage', '', 'form_field');
								?>

							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterfield('Address', '', 'form_field');
									$input->masterfield('City', '', 'form_field');
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
									$input->masterselect('Property Type', '', 'form_field', $property);
									$input->masterselect('Exterior Walls', '', 'form_field', $exterior);
								?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterselect('Roof', '', 'form_field', $roof);
									$input->masterfield('Number of Stories', '', 'form_field');
								?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterfield('Number of Bedrooms', '', 'form_field');
									$input->masterfield('Number of Bathrooms', '', 'form_field');
								?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterselect('Security System', '', 'form_field', array('- Please Select -','none','monitored','unmonitored'));
									$input->masterselect('Fire Alarm', '', 'form_field', array('- Please Select -','none','monitored','unmonitored'));
								?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
							  <?php
								// @param field name, required, class, replaceholder, rename, id, attrib
								$input->masteradio('Is your building / property managed by a Licensed Property Management Agent?', '', array('Yes','No'));
								$input->masteradio('Is your building / property part of a Strata title place?', '', array('Yes','No'));
							  ?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterselect('What quantity of contents do you have?', '', 'form_field', array('- Please Select -','Fixtures and fittings only ','Furniture & major appliances in addition to fixtures and fittings','Fully furnished including bedding and kitchenware as well as fixtures and fittings'),'','Quality_of_contents have');
									$input->masterselect('What standard of contents do you have?', '', 'form_field', array('- Please Select -', 'Average - no name brands, basic equipment, self-assembled furniture, etc.','quality - well-known brands, superior equipment, standard furnitures, etc. ','prestige - designer brands, handcrafted furniture, etc. '),'','Standard of contents have');
								?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col1">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->mastertextarea('List any additional features in your property (Ex. Grage, swimming pool, etc.)', '', 'form_field','Enter description here','Additional Features');
								?>
							</div>
						</div>


						<?php $input->info('Coverage Requested / Desired');?>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterselect('Liability Protection', '', 'form_field', array('- Please Select -','$100,000','$200,000','$300,000','$400,000','$500,000','$600,000','$700,000','$800,000','$900,000','$1,000,000'));
									$input->masterselect('Deductible', '', 'form_field', array('- Please Select -','$1,000-$5,000','$5,000-$10,000','$10,000-$15,000','15,000-$20,000','20,000-$25,000','25,000-$30,000','30,000-$35,000','35,000-$40,000','40,000-$45,000','45,000-$50,000'));
								?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col2">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->masterselect('Personal Property', '', 'form_field', array('- Please Select -','$1,000-$5,000','$5,000-$10,000','$10,000-$15,000','15,000-$20,000','20,000-$25,000','25,000-$30,000','30,000-$35,000','35,000-$40,000','40,000-$45,000','45,000-$50,000'));
									$input->masterselect('Loss of Use', '', 'form_field',array('- Please Select -','$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000'));
								?>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col1">
								<?php
									// @param field name, required, class, replaceholder, rename, id, attrib
									$input->mastertextarea('Additional Coverages / Comments', '', 'form_field');
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




});
</script>
</body>
</html>
