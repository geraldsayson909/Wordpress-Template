<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Order Form';
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

	if(empty($_POST['Your_Name']) ||
		empty($_POST['Product_Name']) ||
		empty($_POST['Quantity']) ||
        empty($_POST['Contact_Number']) ||
        empty($_POST['Email_Address'])
			) {

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
		$name = $_POST['Your_Name'];
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
$country = array('Please select country.','Afghanistan','Albania','Algeria','Andorra','Angola','Anguilla','Antigua & Barbuda','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bhutan','Bolivia','Bosnia & Herzegovina','Botswana','Brazil','Brunei Darussalam','Bulgaria','Burkina Faso','Myanmar/Burma','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Cayman Islands','Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Democratic Republic of the Congo','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Fiji','Finland','France','French Guiana','Gabon','Gambia','Georgia','Germany','Ghana','Great Britain','Greece','Grenada','Guadeloupe','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Israel and the Occupied Territories','Italy','Ivory Coast (Cote d\'Ivoire)','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kosovo','Kuwait','Kyrgyz Republic (Kyrgyzstan)','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Republic of Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Martinique','Mauritania','Mauritius','Mayotte','Mexico','Moldova, Republic of','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Namibia','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','Korea, Democratic Republic of (North Korea)','Norway','Oman','Pacific Islands','Pakistan','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal','Puerto Rico','Qatar','Reunion','Romania','Russian Federation','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent\'s & Grenadines','Samoa','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovak Republic (Slovakia)','Slovenia','Solomon Islands','Somalia','South Africa','Korea, Republic of (South Korea)','South Sudan','Spain','Sri Lanka','Sudan','Suriname','Swaziland','Sweden','Switzerland','Syria','Tajikistan','Tanzania','Thailand','Timor Leste','Togo','Trinidad & Tobago','Tunisia','Turkey','Turkmenistan','Turks & Caicos Islands','Uganda','Ukraine','United Arab Emirates','United States of America (USA)','Uruguay','Uzbekistan','Venezuela','Vietnam','Virgin Islands (UK)','Virgin Islands (US)','Yemen','Zambia','Zimbabwe');

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

		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>
			.information, .information2{background: #fee7e3; color: #444444; font-weight: bold;}
			.information:before{content: url(images/info-reTranspo-icon.png)!important;}
			.information2:before{position: absolute; left: 25px; top: 13px; content: url(images/wage-icon.png)!important;}
			.radio tr td{width:33%; margin-right:0;}
			.radio tr td:last-child {width: 33%; margin-right: 0; }
			@media only screen and (min-width: 110px) and (max-width : 1490px) {
				.radio tr td{width: 33%; margin-right: 0;}
			}
			@media only screen and (max-width : 430px) {
				.radio tr td, .radio tr td:last-child{width:100%; display:block;}
			}
			.hdh_con{text-align: center;padding: 15px 15px; background: #d63d26; font-weight: bold; color: #fff; font-size: 20px; border-radius: 4px;}
		</style>
	</head>
<body>
	<div class="clearfix">
		<div class = "wrapper">
			<div id = "contact_us_form_1" class = "template_form">
				<div class = "form_frame_b">
					<div class = "form_content">
					<form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">
					<?php echo $prompt_message; ?>
					<hr />
					<p class="hdh_con">Order Details</p>
					<input type="hidden" name="Order Details" value=":">
							<div class="form_box">
								<div class="form_box_col2">
					           <div class="group">
					           	<?php
                       $input->label('Your Name','*');
                       $input->fields('Your_Name','form_field','','placeholder="Enter your name here"');
											 ?>
					           </div>
										 <div class="group">
										 <?php
											 $input->label('Product Name ','*');
											 $input->fields('Product_Name','form_field','','placeholder="Enter product name here"');
											?>
										</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col3">
										 <div class="group">
											<?php
											 $input->label('Quantity','*');
											 $input->fields('Quantity','form_field','','placeholder="Enter quantity here"');
											 ?>
										 </div>
										 <div class="group">
										 <?php
											 $input->label('Contact Number','*');
											 $input->fields('Contact_Number','form_field','','placeholder="Enter contact number here"');
											?>
										</div>
										<div class="group">
										<?php
											$input->label('E-mail Address','*');
											$input->fields('Email_Address','form_field','','placeholder="Enter email address here"');
										 ?>
									 </div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
										 <div class="group">
											<?php
											 $input->label('Additional Details','');
											 $input->textarea('Additional_Details','form_field','','placeholder="Enter additional details here"');
											 ?>
										 </div>
								</div>
							</div>
							<p class="hdh_con">DELIVERY ADDRESS</p>
							<input type="hidden" name="DELIVERY ADDRESS" value=":">
							<div class="form_box">
								<div class="form_box_col3">
										 <div class="group">
											<?php
											 $input->label('Street','');
											 $input->fields('Street','form_field','','placeholder="Enter street here"');
											 ?>
										 </div>
										 <div class="group">
											<?php
											 $input->label('City','');
											 $input->fields('City','form_field','','placeholder="Enter city here"');
											 ?>
										 </div>
										 <div class="group">
											<?php
											 $input->label('State/Province/Region','');
											 $input->fields('State_or_Province_or_Region','form_field','','placeholder="Enter state/province/region here"');
											 ?>
										 </div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
										 <div class="group">
											<?php
											 $input->label('Postal/ZIP Code','');
											 $input->fields('Postal_or_ZIP Code','form_field','','placeholder="Enter postal/zip code here"');
											 ?>
										 </div>
										 <div class="group">
											<?php
											 $input->label('Country','');
											 $input->select('Country','form_field', $country);
											 ?>
										 </div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
										 <div class="group">
											<?php
											 $input->label('Additional Details','');
											 $input->textarea('Additional_Details_','form_field','','placeholder="Enter additional details here"');
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
			Your_Name: "required",
			Product_Name: "required",
			Quantity: "required",
			Contact_Number: "required",
			Email_Address: {
				required: true,
				email: true
			}
		},
		messages: {
			Your_Name: "",
			Product_Name: "",
			Quantity: "",
			Contact_Number: "",
Email_Address: ""

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
