<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'New Customer Application Form';
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

	if(empty($_POST['Full_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||
		empty($_POST['Zip']) ||
		empty($_POST['Phone_Number']) ||
		empty($_POST['Email_Address'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(!$result_recaptcha->success){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}else{

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
		<style>
			.drugradio:first-child .radio tr td {     display: block;     float: none;     width: 100%;     margin-bottom: 20px; padding: 15px;}
			.drugradio2 .radio tr td { width: 25%; border: none; padding: 0; height: 40px; }

			.amount, .fldicon{
			  padding: 10px 65px;
			}
			#icon {
				position: absolute;
				padding: 10px 15px 10px 10px;
				background: #616161;
				height: 62px;
				color: #fff;
				font-size: 17px;
				line-height:40px;
				width:60px;
				text-align: center;
				font-weight:bold;
			}
			.fa-dollar-sign::before {
				content: "\f155";
				position: relative;
				left: 13px;
				top: 5px;
			}

			.form_head {border-radius: 10px; }
			.form_head p.title_head:nth-child(1) { background: #616161;  margin: 0;  padding: 10px;  color: #fff;  font-weight: bold;  border-top-right-radius: 8px;  border-top-left-radius: 8px;}
			.form_head .form_box .form_box_col1 p { margin-bottom: 4px; }
			.form_head .form_box { margin: 0; padding: 25px 28px; border: 2px solid #ddd; border-top: none;  border-bottom-right-radius: 8px;  border-bottom-left-radius: 8px;}

			.drugradio2 [type="radio"]:checked + label::before, .drugradio2 [type="radio"]:not(:checked) + label::before{left:0!important}
			.drugradio2 [type="radio"]:checked + label::after, .drugradio2 [type="radio"]:not(:checked) + label::after{left:3px !important}
			.drugradio2 tr td label{padding-left: 40px !important;}
			.drugradio2 div.form_label:nth-child(3){margin:0;}

			@media only screen and (max-width : 1000px) {
				.form_box.left, .form_box.right{float:none; width: 100%;}
				.form_box_col1.drugradio{column-count: 1!important; width:100%;}
				.drugradio:first-child .radio tr td{display: inline-block; margin-bottom: 0; padding: 10px;}
			}
			@media only screen and (max-width : 780px) {
				.amount, .fldicon{margin-bottom: 10px; padding: 10px 0 10px 65px;}
				#icon + input + span.animated_class{height: 64px;}
			}
			@media only screen and (max-width : 600px) {
				.drugradio2{padding-bottom: 20px;}
				.drugradio2 .radio tr td{width:100%!important; padding: 10px; height:auto; border: 1px dashed #e5e5e5;}
				.drugradio2 div.form_label:nth-child(3){display:none;}
			}

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
							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Full Name', '*', 'form_field','Enter full name here','Full_Name');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Address', '*', 'form_field','Enter address here','Address');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('City', '*', 'form_field','Enter city here','City');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterselect('State', '','form_field',$state);
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Zip', '*', 'form_field','Enter zip code here','Zip');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Email Address', '*', 'form_field','Enter email address here','Email_Address');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Phone Number', '*', 'form_field','Enter phone number here','Phone_Number');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masteradio('EZ Open Caps?', '',array('Yes','No'),'EZ_Open_Caps');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masteradio('Refill maintenance medications each month?', '',array('Yes','No'),'Refill_maintenance_medications_each_month');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1 ">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masteradio('Drug Allergy?', '',array('Yes','No'));
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1 drugradio2">
										<?php
											// @param field name, value, id and attribute
											$input->label('');
											$input->radio('Other_',array('Aspirin','Penicillin','Sulfa ','Codeine','Quinolones','Cephalosporin','Macrolides','Other'),'','',4);
										?>
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->label('');
										$input->fields('Other__', 'form_field','Other_Drug','placeholder="Enter other drug allergy here" style="height: 77px;"');
										?>
								</div>
							</div>
							<div class="clearfix"></div>

						<div class="form_box">
						<p class="strong_head" style="margin-top: 20px;">Current Medications <span style="font-size:15px; color:#ff0000">(including over-the-counter and herbal)</span></p><input type="hidden" name="Current_Medications" value=":" />
					</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('01','', '', 'form_field','Enter current medications here','Current_Medications_1');
										?>
									</div>
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('02','', '', 'form_field','Enter current medications here','Current_Medications_2');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('03','', '', 'form_field','Enter current medications here','Current_Medications_3');
										?>
									</div>
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('04','', '', 'form_field','Enter current medications here','Current_Medications_4');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->mastertextarea('List Medical Conditions', '', 'form_field','Enter list of medical conditions here','List_Medical_Conditions');
									?>
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
			Zip: "required",
			Phone_Number: "required",
			Email_Address: {
				required: true,
				email: true
			}
		},
		messages: {
			Full_Name: "",
			Address: "",
			City: "",
			Zip: "",
			Phone_Number: "",
			Email_Address: "",
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


	$(".drugradio2, #Other_Drug").hide();

	/* radio toggle */
	$("input[name='Drug_Allergy']").change(function(){
		if($(this).val() == "Yes"){
			if(this.checked){
				$(".drugradio2").slideToggle();
				$(".drugradio2").find(':input').attr('disabled', false);
			}
		}else{
			$(".drugradio2").slideUp();
			$(".drugradio2").find(':input').attr('disabled', 'disabled');
		}
	});
		$("input[name='Other_']").change(function(){
		if($(this).val() == "Other"){
			if(this.checked){
				$("#Other_Drug").slideToggle();
				$('input[name="Other__"]').attr('disabled',false);
			}
		}else{
			$("#Other_Drug").slideUp();
			$('input[name="Other__"]').attr('disabled',true);
		}
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
