<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Condo Insurance Form';
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
		empty($_POST['Phone']) ||
		empty($_POST['Email'])
		) {

	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))))
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
$Marital_Status = array('Single','Married and lives with spouse','Married but separated','Divorced','Widowed');
$contact = array('Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$time = array('Anytime','Morning','Afternoon','Evening');
$SecuritySystem =  array('None','Monitored','Unmonitored');
$FireAlarm =  array('None','Monitored','Unmonitored');
$Liability_Protection = array('$100,000','$200,000','$300,000','$400,000','$500,000','$600,000','$700,000','$800,000','$900,000','$1,000,000');
$Deductible= array('$1,000-$5,000','$5,000-$10,000','$10,000-$15,000','$15,000-$20,000','$20,000-$25,000','$25,000-$30,000','$30,000-$35,000','$35,000-$40,000','$40,000-$45,000','$45,000-$50,000');
$Personal_Property = array('$1,000-$5,000','$5,000-$10,000','$10,000-$15,000','$15,000-$20,000','$20,000-$25,000','$25,000-$30,000','$30,000-$35,000','$35,000-$40,000','$40,000-$45,000','$45,000-$50,000');
$Loss_of_Use = array('$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000');

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
								$input->masterfield('Email', '*', 'form_field');
								$input->masterfield('Phone', '*', 'form_field');
							?>

						</div>
					</div>


					 <div class="form_box">
						<div class="form_box_col2">
							<?php
								// @param field name, required, class, replaceholder, rename, id, attrib
								$input->masterselect('Best_day_to_contact', '', 'form_field',  array('Any day','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thurdsay','Friday','Saturday','Sunday'),'','','2');
							?>
							<?php
								// @param field name, required, class, replaceholder, rename, id, attrib
								$input->masterselect('Best_time_to_contact', '', 'form_field',  array('Any time','Morning','Afternoon','Evening'));
							?>
						</div>
					</div>
					<hr/>


              <?php $input->info('Dwelling Information');?>

              <div class="form_box">
                <div class="form_box_col2">
                  <?php
                    // @param field name, required, class, replaceholder, rename, id, attrib
                    $input->masterfield('Approximate Year Built', '', 'form_field');
                    $input->masterfield('Approximate Square Footage', '', 'form_field');
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
                    $input->masterfield('Zip', '', 'form_field');
                  ?>
                </div>
              </div>

              <div class="form_box">
                <div class="form_box_col2">
                  <?php
                    // @param field name, required, class, replaceholder, rename, id, attrib
                    $input->masterfield('Number of Units', '', 'form_field');
                    $input->masterselect('Number of Stories', '', 'form_field', array('Own','Rent'));
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
                    $input->masterselect('Security System', '', 'form_field', array('None','Monitored','Unmonitored'));
                    $input->masterselect('Fire Alarm', '', 'form_field', array('None','Monitored','Unmonitored'));
                  ?>
                </div>
              </div>
			  <hr/>

			  <p class="strong_head">Coverage Desired / Requested</p>
			  <div class="form_box">
                <div class="form_box_col2">
                  <?php
                    // @param field name, required, class, replaceholder, rename, id, attrib
                    $input->masterselect('Liability Protection', '', 'form_field',$Liability_Protection);
                    $input->masterselect('Deductible', '', 'form_field', $Deductible);
                  ?>
                </div>
              </div>
			  <div class="form_box">
                <div class="form_box_col2">
                  <?php
                    // @param field name, required, class, replaceholder, rename, id, attrib
                    $input->masterselect('Personal Property', '', 'form_field',$Personal_Property);
                    $input->masterselect('Loss of Use', '', 'form_field', $Loss_of_Use);
                  ?>
                </div>
              </div>

			<div class="form_box">
				<div class="form_box_col1">
					<?php
						// @param field name, required, class, replaceholder, rename, id, attrib
						$input->mastertextarea('Additional Comments', '', 'form_field','Enter description here');
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
			Phone: "required",
			Email: {
				required: true,
				email: true
			}
		},
		messages: {
			First_Name: "",
			Last_Name: "",
			Phone: "",
			Email: ""
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
