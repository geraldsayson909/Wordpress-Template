<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Enrollment Form';
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

	if( empty($_POST['Full_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||
		empty($_POST['State']) ||
		empty($_POST['Phone']) ||
		empty($_POST['Date_of_Birth']) ||
		empty($_POST['Age']) ||
		empty($_POST['Marital_Status']) ||
		empty($_POST['School_Last_Attended']) ||
		empty($_POST['City_']) ||
		empty($_POST['State_']) ||
		empty($_POST['Graduation_Date']) ||
		empty($_POST['Highest_Education_Level_Attained']) ||
		empty($_POST['Name']) ||
		empty($_POST['Address_']) ||
		empty($_POST['Phone_Number']) ||
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
		unset($_POST);

	}

}
/*************declaration starts here************/
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
$status= array('Please select','Single','Married','Divorced','Widowed');

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

							<?php $input->info('Applicant\'s Name'); ?>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masterfield('Full Name', '*', 'form_field');
										$input->masterfield('Address', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, options, id, attribute
										$input->masterfield('City', '*', 'form_field');

										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterselect('State', '*', 'form_field', $state);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masterfield('Email Address', '*', 'form_field');
										$input->masterfield('Phone', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col3">
									<?php
										$input->masterdatepicker('Date of Birth', '*', 'form_field');
										$input->masterfield('Age', '*', 'form_field');
										$input->masterselect('Marital Status', '*', 'form_field', $status);
									?>
								</div>
							</div>

							<?php $input->info('Education Background'); ?>
							<small><i>Please prepare official documents, transcripts, and/or GED test results which may be used during the evaluation</i></small>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										$input->masterfield('School Last Attended', '*', 'form_field');
										$input->masterfield('City_', '*', 'form_field','Enter city here');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col3">
									<?php
										$input->masterselect('State_', '*', 'form_field', $state);
										$input->masterdatepicker('Graduation Date', '*', 'form_field');
										$input->masterfield('Highest Education Level Attained', '*', 'form_field');
									?>
								</div>
							</div>

							<?php $input->info('Person to Contact in Case of Emergency'); ?>
							<div class="form_box">
								<div class="form_box_col3">
									<?php
										$input->masterfield('Name', '*', 'form_field');
										$input->masterfield('Address_', '*', 'form_field','Enter address here');
										$input->masterfield('Phone Number', '*', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Additional Information');
											// @param field name, class, id and attribute
											$input->textarea('Additional_Information', 'text form_field','Additional_Information','placeholder="Enter additional information here"');
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
			Full_Name: "required",
			Address: "required",
			City: "required",
			State: "required",
			Phone: "required",
			Date_of_Birth: "required",
			Age: "required",
			Marital_Status: "required",
			School_Last_Attended: "required",
			City_: "required",
			State_: "required",
			Graduation_Date: "required",
			Highest_Education_Level_Attained: "required",
			Name: "required",
			Address_: "required",
			Phone_Number: "required",
			Email_Address: {
				required: true,
				email: true
			},
			secode: "required"
		},
		messages: {
			Full_Name: "",
			Address: "",
			City: "",
			State: "",
			Phone: "",
			Date_of_Birth: "",
			Age: "",
			Marital_Status: "",
			School_Last_Attended: "",
			City_: "",
			State_: "",
			Graduation_Date: "",
			Highest_Education_Level_Attained: "",
			Name: "",
			Address_: "",
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
			$('.g-recaptcha').addClass('errors').attr('id','recaptcha');
		  }
	});

	$('.Date').datepicker();
	$('.Date').attr('autocomplete', 'off');

});

</script>
</body>
</html>
