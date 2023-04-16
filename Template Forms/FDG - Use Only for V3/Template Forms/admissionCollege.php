<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Admission College Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php';
if ($_POST){
	if(empty($_POST['Full_Name']) ||
		
		empty($_POST['Email_Address']) ) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}else{

		$body = '<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px; margin: 0 auto;">

			<div style="border:8px double #c3c3d0; padding:12px;">
			<div align="center" style="font-size:22px; font-family:Times New Roman, Times, serif; color:#051d38;">'.COMP_NAME.'</div>
			<div align="center" style="color:#990000 !important; font-style: italic !important; font-size:13px !important; font-family:Arial !important;">('.$formname.')</div>
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
			
		echo $body; exit;


    	// for email notification
		require_once 'config.php';
		require_once 'swiftmailer/mail.php';

		// save data form on database
		include 'savedb.php';


		// save data form on database
		$subject = $formname ;
		$attachments = array();


		$name = $_POST['Full_Name'];
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
$country = array('Please select country.','Afghanistan','Albania','Algeria','Andorra','Angola','Anguilla','Antigua & Barbuda','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bhutan','Bolivia','Bosnia & Herzegovina','Botswana','Brazil','Brunei Darussalam','Bulgaria','Burkina Faso','Myanmar/Burma','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Cayman Islands','Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Democratic Republic of the Congo','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Fiji','Finland','France','French Guiana','Gabon','Gambia','Georgia','Germany','Ghana','Great Britain','Greece','Grenada','Guadeloupe','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Israel and the Occupied Territories','Italy','Ivory Coast (Cote d\'Ivoire)','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kosovo','Kuwait','Kyrgyz Republic (Kyrgyzstan)','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Republic of Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Martinique','Mauritania','Mauritius','Mayotte','Mexico','Moldova, Republic of','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Namibia','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','Korea, Democratic Republic of (North Korea)','Norway','Oman','Pacific Islands','Pakistan','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal','Puerto Rico','Qatar','Reunion','Romania','Russian Federation','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent\'s & Grenadines','Samoa','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovak Republic (Slovakia)','Slovenia','Solomon Islands','Somalia','South Africa','Korea, Republic of (South Korea)','South Sudan','Spain','Sri Lanka','Sudan','Suriname','Swaziland','Sweden','Switzerland','Syria','Tajikistan','Tanzania','Thailand','Timor Leste','Togo','Trinidad & Tobago','Tunisia','Turkey','Turkmenistan','Turks & Caicos Islands','Uganda','Ukraine','United Arab Emirates','United States of America (USA)','Uruguay','Uzbekistan','Venezuela','Vietnam','Virgin Islands (UK)','Virgin Islands (US)','Yemen','Zambia','Zimbabwe');
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
$marital= array('- Please Select -','Single','Married','Divorced','Widowed');
$preferToContact = array('- Please Select -','Phone','Fax','Email');
$marital_status = array('- Please Select -','Single','Married','Divorced','Widowed', 'Separated', 'Annulled');

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
		<link rel="stylesheet" href="css/proweaverPhone.css?ver=<?php echo time(); ?>">
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>
			@media only screen and (max-width : 780px) {
				.strong_head{margin-top: 20px !important;}
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
								<p class="strong_head">Applicant's Information</p><input type="hidden" name="Applicant's_Information" value=":" />
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Full Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Full_Name', 'form_field','Full_Name','placeholder="Enter full name here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Address', 'form_field','Address','placeholder="Enter address here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Country', '*');
											// @param field name, class, id and attribute
											$input->select('Country', 'form_field',$country);
										?>
									</div>
									
								</div>
							</div>
							
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group" id="forstate">
										<?php
											// @param label-name, if required
											$input->label('State', '');
											// @param field name, class, id and attribute
											$input->select('State', 'form_field', $state);
										?>
									</div>
									<div class="group">
										<?php
											$input->label('City', '*');
											// @param field name, class, id and attribute
											$input->fields('City', 'form_field','City','placeholder="Enter city here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Zip Code', '*');
											// @param field name, class, id and attribute
											$input->fields('Zip_Code', 'form_field','Zip_Code','placeholder="Enter zip code here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Email Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Email_Address', 'form_field','Email_Address','placeholder="example@domain.com"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Phone Number', '*');
											// @param field name, class, id and attribute
											$input->phoneInput('Phone_Number', 'form_field','Phone_Number','placeholder="Enter phone number here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Date of Birth','*');
											// @param field name, class, id and attribute
											$input->fields('Date_of_Birth', 'form_field Date','Date_of_Birth','placeholder="Enter date of birth here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Age', '*');
											// @param field name, class, id and attribute
										?>
										<input type="text" class="form_field" name="Age" maxlength="3" onkeypress="return isNumberKey(event)" placeholder='Enter age here'>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Marital Status', '*');
											// @param field name, class, id and attribute
											$input->select('Marital_Status', 'form_field', $marital_status);
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<p class="strong_head" >Educational Background</p><input type="hidden" name="Educational_Background" value=":" />
							</div>

							<div class="information">
								Please prepare official documents, transcripts, and/or GED test results which may be used during the evaluation
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('School Last Attended', '*');
											// @param field name, class, id and attribute
											$input->fields('School_Last_Attended', 'form_field','School_Last_Attended','placeholder="Enter school last attended here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Graduation Date','*');
											// @param field name, class, id and attribute
											$input->fields('Graduation_Date', 'form_field Date','Graduation_Date','placeholder="Enter graduation date here"');
										?>
									</div>
								</div>
							</div>
							
							<div class="form_box">
								<div class="form_box_col2" >
									<div class="group">
										<?php
											$input->label('Country', '*');
											// @param field name, class, id and attribute
											$input->select('Country_', 'form_field', $country);
										?>
									</div>
									<div class="group" id="forstate_">
										<?php
											// @param label-name, if required
											$input->label('State', '');
											// @param field name, class, id and attribute
											$input->select('State_', 'form_field', $state);
										?>
									</div>
									
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
								
									<div class="group">
										<?php
											$input->label('City', '*');
											// @param field name, class, id and attribute
											$input->fields('City_', 'form_field','City_','placeholder="Enter city here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Zip Code', '*');
											// @param field name, class, id and attribute
											$input->fields('Zip_Code_', 'form_field', 'Zip_Code_', 'placeholder="Enter zip code here"');
										?>
									</div>
									
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Highest Education Level Attained', '*');
											// @param field name, class, id and attribute
											$input->fields('Highest_Education_Level_Attained', 'form_field','Highest_Education_Level_Attained','placeholder="Enter highest education level attained here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('GED Scores', '*');
											// @param field name, class, id and attribute
											$input->fields('GED Scores', 'form_field','GED Scores','placeholder="Enter GED scores here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('GED School', '*');
											// @param field name, class, id and attribute
											$input->fields('GED School', 'form_field','GED School','placeholder="Enter GED school here"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('GED Date','*');
											// @param field name, class, id and attribute
											$input->fields('GED_Date', 'form_field Date','GED_Date','placeholder="Enter GED date here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<p class="strong_head" >Person to Contact in Case of Emergency</p><input type="hidden" name="Person_to_Contact_in_Case_of_Emergency" value=":" />
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Name', '*');
											// @param field name, class, id and attribute
											$input->fields('_Name_', 'form_field','_Name_','placeholder="Enter name here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Address', '*');
											// @param field name, class, id and attribute
											$input->fields('_Address_', 'form_field','_Address_','placeholder="Enter address here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Phone Number', '*');
											// @param field name, class, id and attribute
											$input->phoneInput('_Phone_Number', 'form_field','_Phone_Number','placeholder="Enter phone number here"');
										?>
									</div>
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
											<!---<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>-->
										<div class="btn-submit"><input type = "submit" class = "form_button" value = "SUBMIT" /></div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div><?php $input->phone(true); ?>
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.min.js"></script>
		<script src = "js/jquery.mask.min.js"></script>
	<script src = "js/proweaverPhone.js"></script> 


	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Full_Name: "required",
			Address: "required",
			City: "required",
			City_: "required",
			Country: "required",
			Zip_Code: "required",
			Country_: "required",
			Zip_Code_: "required",
			Phone_Number: "required",
			Date_of_Birth: "required",
			Age: "required",
			Marital_Status: "required",
			School_Last_Attended: "required",
			City_of_School: "required",
			School_State: "required",
			Graduation_Date: "required",
			Highest_Education_Level_Attained: "required",
			GED_Scores: "required",
			GED_School: "required",
			GED_Date: "required",
			_Name_: "required",
			_Address_: "required",
			_Phone_Number: "required",
			Phone_Number_: "required",
			Email_Address: {
				required: true,
				email: true
			},
		},
		messages: {
			Full_Name: "",
			Address: "",
			City: "",
			City_: "",
			Country: "",
			Zip_Code: "",
			Country_: "",
			Zip_Code_: "",
			Phone_Number: "",
			Date_of_Birth: "",
			Age: "",
			Marital_Status: "",
			School_Last_Attended: "",
			City_of_School: "",
			School_State: "",
			Graduation_Date: "",
			Highest_Education_Level_Attained: "",
			GED_Scores: "",
			GED_School: "",
			GED_Date: "",
			_Name_: "",
			_Address_: "",
			_Phone_Number: "",
			Email_Address: "",
			Phone_Number_: ""
		}
	});
		/* select disable */
		$("select[name='Country']").change(function(){
				if($(this).val() != "United States of America (USA)"){
					//$("#State").fadeIn();
					
					$("#forstate").find(':input').attr('disabled', 'disabled');
				}else{
					//$("#State").fadeOut();
					$("#forstate").find(':input').attr('disabled', false);
				}
			});
			
				/* select disable*/
		$("select[name='Country_']").change(function(){
				if($(this).val() != "United States of America (USA)"){
					//$("#State").fadeIn();
					
					$("#forstate_").find(':input').attr('disabled', 'disabled');
				}else{
					//$("#State").fadeOut();
					$("#forstate_").find(':input').attr('disabled', false);
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
		
		function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
      }

	</script>
</body>
</html>
