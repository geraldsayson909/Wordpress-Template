<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Group Health Insurance Form';
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
$yesno = array('- Please Select -','Yes','No');
$title = array('- Please Select -','Administration','CEO/President/ Owner','CFO','CIO/CTO','Consultant','Customer Service','Engineer/Programmer','Facilities/Operations','Finance/ Accounting Manager','Finance/ Accounting Staff','General Manager','Human Resources','IS/IT Management','IS/ IT Staff','Marketing Manager','Marketing Staff','Partner/Principal','Purchasing Manager','Sales/ Business Dev. Manager','Sales/ Business Dev.','Vice President/Senior Manager');
$Industry = array('- Please Select -','Advertising/Marketing/PR','Agriculture','Biotech/Pharmaceuticals','Computers - Hardware','Computers - Software','Construction/General Contracting','Consulting','Education','Equipment Sales &amp; Service','Financial Services','Government','Healthcare','Information Services','Insurance','Legal','Manufacturing','Media/Entertainment/Publishing','Non-Profit','Other Services','Real Estate','Restaurant','Retail','Telecom/Utilitie','Transportation/Logistics','Travel/Hospitality','Wholesale');
$entity_type= array('- Please Select -','Association', 'Corporation', 'S Corporation', 'Limited Liability Company', 'Limited Liability Partnership', 'Partnership', 'Sole Proprietorship', 'Limited Partnership', 'Professional Corporation', 'Nonprofit Corporation');
?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>
		<?php if(stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")):?>
			<link rel="stylesheet" href="css/mobile.css?ver23asas">
		<?php endif;?>
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
		<style>
			.infobox { background: #ddd; padding: 20px 30px; border-radius: 8px; font-weight: 100; margin: 25px 0;}
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

							 <?php $input->info('Customer Information');?>

							<div class="form_box left">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Title / Role', '', 'form_field',  $title);
									?>
								</div>
							</div>
							<div class="clear"></div>

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
										$input->masterselect('Marital Status', '', 'form_field', array('- Please Select -','Single','Married and lives with spouse','Married but separated','Divorced','Widowed'));
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
										$input->masterselect('Best day to contact', '', 'form_field', array('- Please Select -','Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'));
										$input->masterselect('Best time to contact', '', 'form_field', array('- Please Select -','Anytime','Morning','Afternoon','Evening'));
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Business Name', '', 'form_field');
										$input->masterfield('What is your business entity?', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterselect('Industry', '', 'form_field', $Industry);
										$input->masterselect('State', '', 'form_field', $state);
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Zip', '', 'form_field');
										$input->masterfield('Website', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Phone Number', '', 'form_field');
										$input->masterfield('Number of full-time Employees', '', 'form_field');
									?>
								</div>
							</div>

							<div class="form_box left">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Number of Part-time Employees', '', 'form_field');
									?>
								</div>
							</div>
							<div class="clear"></div>


							 <?php $input->info('Application Information');?>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('First Name__', '', 'form_field','Enter first name here');
										$input->masterfield('Last Name__', '', 'form_field','Enter last name here');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterfield('Zip', '', 'form_field');
										$input->masteradio('Gender_', '', array('Male','Female'));
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->masterdatepicker('Date of Birth', '', 'form_field');
										$input->masteradio('Do you currently have a group life insurance plan for your business?', '', array('Yes','No'));
									?>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib
										$input->mastertextarea('Please describe any requirements you have for a life insurance plan', '', 'form_field','','Requirements_for_a_life_insurance_plan');
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
