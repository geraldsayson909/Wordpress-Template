<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Sell Properties Form';
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

	if( empty($_POST['Complete_Name']) ||
	empty($_POST['Property_Address']) ||
	empty($_POST['Phone_Number']) ||
		empty($_POST['Email'])
		) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email'])))) { $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
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
				elseif($key == 'g-recaptcha-response') continue;
				elseif($key == 'checkboxVal') continue;
				elseif($key == '_Patient_Name__') continue;


				if(!empty($value)){
					$key2 = str_replace('_', ' ', $key);
					if($value == ':') {
					$body .= '<style>.ofdp-header{background:#F0F0F0;}</style><tr><td colspan="2" class="ofdp-header"><b>'.$key2.'</b></td></tr>';
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
		$name = $_POST['Complete_Name'];
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

	}

}
/*************declaration starts here************/
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
$country = array('Please select country.','Afghanistan','Albania','Algeria','Andorra','Angola','Anguilla','Antigua & Barbuda','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bhutan','Bolivia','Bosnia & Herzegovina','Botswana','Brazil','Brunei Darussalam','Bulgaria','Burkina Faso','Myanmar/Burma','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Cayman Islands','Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Democratic Republic of the Congo','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Fiji','Finland','France','French Guiana','Gabon','Gambia','Georgia','Germany','Ghana','Great Britain','Greece','Grenada','Guadeloupe','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Israel and the Occupied Territories','Italy','Ivory Coast (Cote d\'Ivoire)','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kosovo','Kuwait','Kyrgyz Republic (Kyrgyzstan)','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Republic of Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Martinique','Mauritania','Mauritius','Mayotte','Mexico','Moldova, Republic of','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Namibia','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','Korea, Democratic Republic of (North Korea)','Norway','Oman','Pacific Islands','Pakistan','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal','Puerto Rico','Qatar','Reunion','Romania','Russian Federation','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent\'s & Grenadines','Samoa','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovak Republic (Slovakia)','Slovenia','Solomon Islands','Somalia','South Africa','Korea, Republic of (South Korea)','South Sudan','Spain','Sri Lanka','Sudan','Suriname','Swaziland','Sweden','Switzerland','Syria','Tajikistan','Tanzania','Thailand','Timor Leste','Togo','Trinidad & Tobago','Tunisia','Turkey','Turkmenistan','Turks & Caicos Islands','Uganda','Ukraine','United Arab Emirates','United States of America (USA)','Uruguay','Uzbekistan','Venezuela','Vietnam','Virgin Islands (UK)','Virgin Islands (US)','Yemen','Zambia','Zimbabwe');

?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>
		 <link rel="stylesheet" href="css/intlTelInput.css">
		<link rel="stylesheet" href="style.min.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />
		<link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">

		<link rel="stylesheet" href="css/proweaverPhone.css" type="text/css"/>
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<link href="assets/jquery.signaturepad.css" rel="stylesheet">
		<style>
		.group.singleradio tr td label {     padding-right: 50px; }

			.amount { padding-left: 73px!important;  }
			#icon { position: absolute; padding: 7px 39px 10px 10px; background: #f1f1f1; height: 62px; color: #000; font-size: 31px; }
			.fa-dollar-sign::before { content: "\f155"; position: relative; left: 13px; top: 5px; }
			body { font-family: 'Muli', sans-serif;}
					::placeholder { font-family: 'Muli', sans-serif; }
					::-moz-placeholder { font-family: 'Muli', sans-serif; }
					::-webkit-input-placeholder { font-family: 'Muli', sans-serif; }
					:-ms-input-placeholder { font-family: 'Muli', sans-serif;}
					select.form_field { font-family: 'Muli', sans-serif;}
			.main.fieldbox { margin-bottom: 30px; }
			.fieldbox { margin: 10px 0; }
			.fieldheader p { margin: 0; background: #669c26; padding: 13px; color: #fff; text-align: center; font-weight: 700; border-top-left-radius: 5px; border-top-right-radius: 10px; font-size:25px;}
			.fieldcontent { padding: 20px; border: 3px solid #669c26; border-top: 0; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; }
			hr{color:#ccc;margin-top:20px;}
			.formhead {      background: #f1f1f1;  color:      #000;     text-align: center;     font-size: 17px;     padding: 10px;     font-weight: bold;     text-transform: uppercase;     margin-top: 20px;     margin-bottom: 0 !important;  }
			.formhead span{text-transform: none;font-size: 15px;font-weight: normal;display:block}
			.comp{font-weight:bold;color:#001e57;background:0;}
			.forright { float: right; width: 49% !important; }

			@media only screen and (max-width: 900px) {
			strong, b {     font-weight: bold;     font-size: 14px; }
			}
			@media only screen and (max-width: 780px) {
			strong, b {     font-weight: bold;     font-size: 16px; }
			.forright { float: right; width: 100% !important; }
			}


			table, thead,tr,th{font-weight: bold;}
					#weeks { width: 100%; border: 1px solid; padding: 5px; border-collapse: collapse; border-spacing: 0; margin: 10px 0; }
					#weeks thead { padding: 0; margin: 0; text-align: center; border: 1px solid; }
					#weeks td { overflow: hidden; position: relative; }
					#weeks tbody td { border: 1px solid;  }

					.form_field.tablecust {     background:     #f1f1f1; height:27px;}
					.foresp {     padding: 0 3px;text-align:center; width:130px}
					#weeks input { width: 100%; border: 0 !important; padding: 10px 5px; }
					#weeks td input + .datepick-trigger { position: absolute; z-index: 1; right: 5px; top: 90%; }
					.datepick-trigger { margin-top: -20px !important; }
					p.error { color: #f00; font-weight: 700; }
					.tile { text-align: center; font-weight: 700; }
					.days th { padding: 6px; border: 1px solid;}

					.firstth th {padding: 10px 0; }


		  .datepick-trigger { margin-top: -20px !important; }
		  p.error { color: #f00; font-weight: 700; }
		  .tile { text-align: center; font-weight: 700; }
			.days th { padding: 6px; }

		  .firstth th {padding: 10px 0; }
		  ul{list-style:disc; margin-left:20px;}

		  .group.groupv input {     padding-left: 75px; }
		  .group.singleradio td {      display: block;     width: 100% !important;  }

		  .pad{position: relative;cursor: url("../images/pen.cur"), crosshair;cursor: url("../images/pen.cur") 16 16, crosshair;-ms-touch-action: none;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;border: 1px solid #CECECE;height: 65px;}
			.clearButton {top: 0;position: absolute;left: 0;font-size: 0.75em;line-height: 1.375;}
			.sig{}
			.sig small {color: #f00;font-size: 20px;font-weight:bold;}

			input[type=checkbox] {transform: scale(1.5);-webkit-appearance: checkbox;-moz-appearance:    checkbox;appearance:         checkbox;}

			.form_field {     height: 63px;     padding: 15px;     font-size: 15px;     font-family: Muli; }

			.group.custcheck table tbody tr td label {     margin-top: 20px; }

			.file {     width: 100%;     padding: 17px; }
			.mt15{margin-top:15px;}
			.upcase{text-transform: uppercase;}
			.boldy {font-weight:bold;}
			.required{color:red;}

			.subhead {margin-top:15px; text-transform: uppercase; font-weight: bold;}

			input[type="checkbox"] { transform: scale(1.2);}
			.sentenceinput{width: 200px; padding: 4px; border: 0; border-bottom: 1px solid; text-align: center;}
			ol{margin-left:25px;}

		  @media only screen and (max-width: 1000px) {
			 .foresp {width:100%;}
		 	#weeks, #weeks thead, tr, th, tbody, td {border: 0px solid #000; }
			#weeks thead { display: none; }
			#weeks td { display: block; }
			#weeks td:first-child { font-size: 16px; text-align:center;font-weight: 700; border-bottom: 1px solid; }
			#weeks td:not(:first-child):before { content: attr(data-label); border-bottom: 2px solid; }
			#weeks tr { margin-bottom: 15px; display: inline-block; width: 100%; border: 1px solid; }
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
						<div class="form_box_col2">
							<div class="group">
								<?php
									$input->label('Complete Name', '*');
									// @param field name, class, id and attribute
									$input->fields('Complete_Name', 'form_field','','placeholder="Enter name here"');
								?>
							</div>
							<div class="group">
								<?php
									$input->label('Property Address', '*');
									// @param field name, class, id and attribute
									$input->fields('Property_Address', 'form_field','','placeholder="Enter address here"');
								?>
							</div>
						</div>
					</div>

					<div class="form_box">
						<div class="form_box_col1">
							<div class="group">
								<?php
									$input->label('Type of Property', '');
									// @param field name, class, id and attribute
									$input->chkboxVal('Type_of_Property', array('Residential','Commercial','Agricultural','Others'),'','','1');
								?>
							</div>
						</div>
					</div>

					<div class="form_box" id="Explanation">
						<div class="form_box_col2">
							<div class="group">
								<?php
									// $input->label('Email Address', '*');
									// @param field name, class, id and attribute
									$input->fields('Other_Type_of_Property', 'form_field','','placeholder="Enter here"');
								?>
							</div>
						</div>
					</div>

					<div class="form_box">
						<div class="form_box_col2">
							<div class="group">
								<?php
									$input->label('Email', '*');
									// @param field name, class, id and attribute
									$input->fields('Email', 'form_field','','placeholder="Enter email here"');
								?>
							</div>
							<div class="group">
								<?php
									$input->label('Phone Number', '*');
									// @param field name, class, id and attribute
									$input->fields('Phone_Number', 'form_field','','placeholder="Enter number here"');
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
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>
	<script src = "js/proweaverPhone.js"></script>
	<script src = "js/jquery.mask.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.min.js"></script>

	<script src="assets/json2.min.js"></script>
	<script src="jquery.signaturepad.js"></script>

	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Complete_Name: "required",
			Property_Address: "required",
			Email: "required",
			Phone_Number: "required",
			secode: "required"
		},
		messages: {
			Complete_Name: "",
			Property_Address: "",
			Email: "",
			Phone_Number: "",
			secode: ""
		}
	});

	$("#Explanation").hide();


	$("#Type_of_Property_4").change(function(){
		if($(this).is(':checked')){
			$("#Explanation").fadeIn();
			$("#Explanation").find(':input').attr('disabled', false);
		}else{
			$("#Explanation").fadeOut();
			$("#Explanation").find(':input').attr('disabled', 'disabled');
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
function removeHTML(id){
	$('#mainCloneCount_'+id).remove();
}

	checkboxValues('Type_of_Property');



 function checkboxValues(inputAttrName) {
                var inputAttrName = inputAttrName;
                var inputHidden = $('input[name="'+inputAttrName+'"]').attr('value');
                var checkedValues = '';
                var checkboxClass = $('input.'+inputAttrName+'');

                $.each(checkboxClass, function(index) {
                        $(this).on('change', function() {
                                var x = $(this).attr('value') + ', ';
                                if($(this).is(':checked')) {
                                        inputHidden += x;
                                        checkedValues = inputHidden.replace(/,\s*$/, "");
                                        $('input[name="'+inputAttrName+'"]').attr('value', checkedValues);
                                } else {
                                        inputHidden = inputHidden.replace(x, '');
                                        checkedValues = inputHidden.replace(/,\s*$/, "");
                                        $('input[name="'+inputAttrName+'"]').attr('value', checkedValues);
                                }
                        });
                });
        }
</script>
</body>
</html>
