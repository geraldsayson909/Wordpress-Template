<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Refill Prescription Form';
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

	if(empty($_POST['Last_Name']) ||
		empty($_POST['First_Name']) ||
		empty($_POST['Phone_Number']) ||
		empty($_POST['Rx_1'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
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
$choices = array('- Please Select -','No, thanks','Yes, via phone');
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

		<script src='https://www.google.com/recaptcha/api.js'></script>
			<style>
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
			.mrg0 { margin: 0; }
			.form_head .form_box { margin: 0; padding: 25px 28px; border: 2px solid #ddd; border-top: none;  border-bottom-right-radius: 8px;  border-bottom-left-radius: 8px;}
			.grouping .form_box_col1 { margin: 0 0 20px 0; }
			@media only screen and (max-width : 780px) {
				.form_box.left{width: 100%;}
				.amount, .fldicon{margin-bottom: 10px; padding: 10px 0 10px 65px;}
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
						<p class="strong_head" >Who is this prescription for?</p><input type="hidden" name="Who_is_this_prescription_for" value=":" />
					</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Last Name', '*', 'form_field','Enter last name here','Last_Name');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('First Name', '*', 'form_field','Enter first name here','First_Name');
									?>
								</div>
							</div>
							<div class="form_box left">
								<div class="form_box_col1">
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Phone Number ', '*', 'form_field','Enter phone number here','Phone_Number');
									?>
								</div>
							</div>
							<div class="clearfix"></div>

					<div class="form_box" style="margin: 20px 0 0 0;">
						<p class="strong_head">RX REFILL NUMBERS <span class="required_filed">*</span></p><input type="hidden" name="RX_Refill_Numbers" value=":" />
					</div>

							<div class="form_box mrg0">
								<div class="form_box_col2">
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('01','', '*', 'form_field','Enter RX refill number here','Rx_1');
										?>
									</div>
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('02','', '*', 'form_field','Enter RX refill number here','Rx_2');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('03','', '*', 'form_field','Enter RX refill number here','Rx_3');
										?>
									</div>
									<div class="group">
											<?php
											// @param field name, required, class, replaceholder, rename, id, attrib, value
												$input->masterfieldicon('04','', '*', 'form_field','Enter RX refill number here','Rx_4');
											?>
									</div>
								</div>
							</div>
					<div class="form_box">
						<p class="strong_head">ADD MORE PRESCRIPTIONS <span style="color:#000; font-size:15px; font-weight:normal;">(OVER THE COUNTER ITEM)</span></p><input type="hidden" name="More Prescriptions" value=":" />
					</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
									<div class="form_head grouping">
										<p class="title_head">Name</p>
										<div class="form_box">
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('1', '*', 'form_field','Enter name here','Prescription_Name_1');
												?>
											</div>
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('2', '', 'form_field','Enter name here','Prescription_Name_2');
												?>
											</div>
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('3', '', 'form_field','Enter name here','Prescription_Name_3');
												?>
											</div>
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('4', '', 'form_field','Enter name here','Prescription_Name_4');
												?>
											</div>
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('5', '', 'form_field','Enter name here','Prescription_Name_5');
												?>
											</div>
										</div>
									</div>
									</div>

									<div class="group">
									<div class="form_head grouping">
										<p class="title_head">Qty</p>
										<div class="form_box">
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('1', '*', 'form_field','Enter quantity here','Prescription_Qty_1');
												?>
											</div>
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('2', '', 'form_field','Enter quantity here','Prescription_Qty_2');
												?>
											</div>
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('3', '', 'form_field','Enter quantity here','Prescription_Qty_3');
												?>
											</div>
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('4', '', 'form_field','Enter quantity here','Prescription_Qty_4');
												?>
											</div>
											<div class="form_box_col1">
												<?php
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->masterfield('5', '', 'form_field','Enter quantity here','Prescription_Name_5');
												?>
											</div>
										</div>
									</div>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masteradio('Pick up or Delivery', '*',array('Pickup','Delivery'));
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterselect('Would you like us to notify you when your prescription(s) are ready?','','form_field',$choices);
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
			Last_Name: "required",
			First_Name: "required",
			Rx_1: "required",
			Prescription_Name_1: "required",
			Prescription_Qty_1: "required",
			Pick_up_or_Delivery: "required",
			Phone_Number: "required"
		},
		messages: {
			Last_Name: "",
			First_Name: "",
			Rx_1: "",
			Prescription_Name_1: "",
			Prescription_Qty_1: "",
			Pick_up_or_Delivery: "",
			Phone_Number: ""
		}
	});
	$("#submitform").submit(function(){
		if($(this).valid()){
			self.parent.$('html, body').animate(
				{ scrollTop: self.parent.$('#myframe').offset().top },
				500
			);
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

		$(function() {
		  $('.Date, .date').datepicker({
			autoHide: true,
			zIndex: 2048,
		  });
		});

	</script>
</body>
</html>
