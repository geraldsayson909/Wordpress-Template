<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Online Booking Form';
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

	if( empty($_POST['Name']) ||
		empty($_POST['Phone']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||
		empty($_POST['State_or_Region']) ||
		empty($_POST['Street_Address']) ||
		empty($_POST['City_']) ||
		empty($_POST['State_or_Region_']) ||
		empty($_POST['Email'])) {


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
				if($key == 'submit') continue;
				elseif($key == 'Verify_Email') continue;
				elseif($key == 'Verify_Password') continue;
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

			//echo $body; exit;

		 // for email notification
		require_once 'config.php';
		require_once 'swiftmailer/mail.php';

		// save data form on database
		include 'savedb.php';


		// save data form on database
		$subject = $formname ;
		$attachments = array();
		// when form has attachments, uncomment code below
		if(!empty($_FILES['attachment']['name'])){
			$attachmentsdir = ABSPATH.'onlineforms/attachments/';
			$validextensions = array('pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'); // include file type here
			for($i = 0 ; $i < count($_FILES['attachment']['name']) ; $i++ ){

				$checkfile =  $attachmentsdir.$_FILES['attachment']['name'][$i];
				//$tobeuploadfile = $_FILES['attachment']['tmp_name'][$i];
				$tempfile = pathinfo($_FILES['attachment']['name'][$i]);
				if(in_array(strtolower($tempfile['extension']), $validextensions)){
					if(file_exists($checkfile)){
						$storedfile = $tempfile['filename'].'-'.time().'.'.$tempfile['extension'];
					}else{
						$storedfile = $_FILES['attachment']['name'][$i];
					}

					if( move_uploaded_file($_FILES['attachment']['tmp_name'][$i], $attachmentsdir.$storedfile) ){
						$attachments[] = $storedfile;
					}
				}
			}
		}

	 	//name of sender
		$name = $_POST['Name'];
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
$incoterm = array('- Please select -','CIF (Cost, Insurance and Freight)', 'CIP (Carriage and Insurance Paid to)', 'CFR (Cost and Freight)', 'CPT (Carriage paid to)', 'DAT (Delivered at Terminal)', 'DAP (Delivered at Place)', 'DDP (Delivery Duty Paid)', 'EXW (Ex Works)', 'FAS (Free Alongside Ship)', 'FCA (Free Carrier)', 'FOB (Free on Board)');
$method = array('- Please select -','Air Transportation','Road Transportation','Rail Transportation','Marine Transportation');
$country = array('- Please select -','United States', 'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Anguilla', 'Antigua & Barbuda', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia ', 'Herzegovina', 'Botswana', 'Brazil', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republ', 'Chad', 'Chile', 'China', 'China', 'Colombia', 'Comoros', 'Congo', 'Congo', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Fiji', 'Finland', 'France', 'French Guiana', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Great Britain', 'Greece', 'Grenada', 'Guadeloupe', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Israel', 'Italy', 'Ivory Coast', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'North Korea', 'South Korea', 'Kosovo', 'Kuwait', 'Kyrgyz Republic', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macedonia', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar/Burma', 'Namibia', 'Nepal', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Norway', 'Oman', 'Pacific Islands', 'Pakistan', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Reunion', 'Romania', 'Russian Federation', 'Rwanda', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovak Republic', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Swaziland', 'Sweden', 'Switzerland', 'Syria', 'Tajikistan', 'Tanzania', 'Thailand', 'Netherlands', 'Timor Leste', 'Togo', 'Trinidad & Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks & Caicos Islands', 'Uganda', 'Ukraine', 'United Arab Emirates', 'Uruguay', 'Uzbekistan', 'Venezuela', 'Vietnam', 'Virgin Islands', 'Virgin Islands', 'Yemen', 'Zambia', 'Zimbabwe' );
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
			.main.fieldbox { margin-bottom: 30px; }
			.fieldheader p { margin: 0; background-color: #f1f1f1; padding: 15px; text-align: center; font-weight: 700;font-size:20px;text-transform: uppercase; color: #4f4a4a; }
			.wskCheckbox { border: 2px solid #5a5a5a; color: #1d1d1d; cursor: pointer; display: inline-block; float: left; height: 14px; margin: 0 20px 10px 20px; outline-color: #eaeaea; padding: 0; position:relative; width: 14px; -webkit-transition: all 0.3s ease-in-out; -moz-transition: all 0.3s ease-in-out; transition: all 0.3s ease-in-out; z-index:1; }
			.titled{width: 40px; max-width: 90%; /*! margin: 10px 0; */ height: auto; background: none; padding:  0 !important;border: none;border-bottom: 2px solid black;font: inherit;}
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




						<div class="main fieldbox">



							<div class="fieldheader">
								<p>Personal Details</p>
								<input type="hidden" name="Personal Details" value=":">
							</div>

						<div class="form_box ">
							<div class="form_box_col1">
								<div class="group">
									<?php
										$input->label('Name', '*');
										$input->fields('Name', 'form_field','Name','placeholder="Enter name here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Company Name', '');
										$input->fields('Company_Name', 'form_field','Company_Name','placeholder="Enter company name here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Email', '*');
										$input->fields('Email', 'form_field','Email','placeholder="Enter email here"');
									?>
								</div>
							</div>
						</div>



						<div class="form_box ">
							<div class="form_box_col2">
							<div class="group">
									<?php
										$input->label('Phone', '*');
										$input->fields('Phone', 'form_field','Phone','placeholder="Enter phone here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Telephone', '');
										$input->fields('Telephone', 'form_field','Telephone','placeholder="Enter telephone here"');
									?>
								</div>
							</div>
						</div>






						<div class="fieldbox">
							<div class="fieldheader">
								<p>Pick-up Location</p>
								<input type="hidden" name="Pick-up Location" value=":">
							</div>
						<div class="fieldcontent">




						<div class="form_box ">
							<div class="form_box_col1">
								<div class="group">
									<?php
										$input->label('Address', '*');
										$input->fields('Address', 'form_field','Address','placeholder="Enter address here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('City', '*');
										$input->fields('City', 'form_field','City','placeholder="Enter city here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('State/Region','*');
										$input->fields('State_or_Region', 'form_field','State_or_Region','placeholder="Enter state or region here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Postal/Zip Code', '');
										$input->fields('Postal_or_Zip_Code', 'form_field','Postal_or_Zip_Code','placeholder="Enter postal or zip code here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Country','');
										$input->select('Country', 'form_field', $country);
									?>
								</div>
							</div>
						</div>
					</div>
					</div>


				<div class="fieldbox">
							<div class="fieldheader">
								<p>Shipment Destination</p>
								<input type="hidden" name="Shipment Destination" value=":">
							</div>
						<div class="fieldcontent">



						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Consignee Name', '');
										$input->fields('Consignee_Name', 'form_field','Consignee_Name','placeholder="Enter consignee name here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Street Address', '*');
										$input->fields('Street_Address', 'form_field','Street_Address','placeholder="Enter street address here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('City', '*');
										$input->fields('City_', 'form_field','City_','placeholder="Enter city here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('State/Region','*');
										$input->fields('State_or_Region_', 'form_field','State_or_Region_','placeholder="Enter state or region here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Postal/Zip Code', '');
										$input->fields('Postal_or_Zip_Code_', 'form_field','Postal_or_Zip_Code_','placeholder="Enter postal or zip code here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Country','');
										$input->select('Country_', 'form_field', $country);
									?>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="fieldbox">
							<div class="fieldheader">
								<p>Shipment Details</p>
								<input type="hidden" name="Shipment Details" value=":">
							</div>
						<div class="fieldcontent">

						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Method of Transportation','');
										$input->select('Method_of_Transportation', 'form_field', $method);
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Commodity', '');
										$input->fields('Commodity', 'form_field','Commodity','placeholder="Enter commodity here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box">
							<div class="form_box_col1">
								<div class="group">
									<?php
										$input->label('Is the cargo hazardous?','');
										$input->radio('Hazardous_Cargo', array('Yes','No'),'Hazardous_Cargo','',2);
									?>
								</div>
							</div>
						</div>

						<div class="form_box" id="Explanation1">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('UN Number', '');
										$input->fields('UN_Number', 'form_field','UN_Number','placeholder="Enter un number here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Class Number', '');
										$input->fields('Class_Number', 'form_field','Class_Number','placeholder="Enter class number here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Delivery Date','');
										$input->fields('Delivery_Date', 'form_field Date','Delivery_Date','placeholder="Enter delivery date here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Delivery Time', '');
										$input->fields('Delivery_Time', 'form_field','Delivery_Time','placeholder="Enter delivery time here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Pick-up Date','');
										$input->fields('Pick-up_Date', 'form_field Date','Pick-up_Date','placeholder="Enter pick-up date here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Pick-up Time', '');
										$input->fields('Pick-up_Time', 'form_field','Pick-up_Time','placeholder="Enter pick-up time here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col3">
								<div class="group">
									<?php
										$input->label('Total Number of Package','');
										$input->fields('Total_Number_of_Package', 'form_field','Total_Number_of_Package','placeholder="Enter total number of package here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Package Type','');
										$input->fields('Package_Type', 'form_field','Package_Type','placeholder="Enter package type here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Weight per package', '');
										$input->fields('Weight_per_package', 'form_field','Weight_per_package','placeholder="Enter weight per package here"');
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col2">
								<div class="group">
									<?php
										$input->label('Total Shipment Weight','');
										$input->fields('Total_Shipment_Weight', 'form_field','Total_Shipment_Weight','placeholder="Enter total shipment weight here"');
									?>
								</div>
								<div class="group">
									<?php
										$input->label('Select an Incoterm','');
										$input->select('Select_an_Incoterm', 'form_field', $incoterm);
									?>
								</div>
							</div>
						</div>

						<div class="form_box ">
							<div class="form_box_col1">
								<div class="group">
									<?php
										$input->label('Content Description and Notes','');
										$input->textarea('Content_Description_and_Notes', 'form_field text','Content_Description_and_Notes','placeholder="Enter content description and notes here"');
									?>
								</div>
							</div>
						</div>

					</div>
				</div>


							<div class = "form_box5 secode_box">
								<div class="inner_form_box1 recapBtn">
									<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
									<div class="btn-submit"><input type = "submit" class = "form_button" value = "SUBMIT" /></div>
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
	<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.min.js"></script>
	<script src = "js/jquery.mask.min.js"></script>


	<script type="text/javascript">

$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		ignore: ":hidden",
		rules: {
			Name: "required",
			Phone: "required",
			Email: "required",
			Address: "required",
			City: "required",
			State_or_Region: "required",
			Street_Address: "required",
			City_: "required",
			State_or_Region_: "required"
		},
		messages: {
			Name: "",
			Phone: "",
			Email: "",
			Address: "",
			City: "",
			State_or_Region: "",
			Street_Address: "",
			City_: "",
			State_or_Region_: ""
		}
	});

	$('input.timepicker').timepicker({});


	$("#Explanation1").hide();


		$("input[name='Hazardous_Cargo']").change(function(){
		if($(this).val() == "Yes"){
			$("#Explanation1").fadeIn();
			$("#Explanation1").find(':input').attr('disabled', false);
		}else{
			$("#Explanation1").fadeOut();
			$("#Explanation1").find(':input').attr('disabled', 'disabled');
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
