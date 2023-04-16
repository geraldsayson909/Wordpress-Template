<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Registration Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Phone']) ||				
		empty($_POST['Email']) ||	
		empty($_POST['secode'])) {

	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';	
	$prompt_message = '<div id="error">'.$asterisk . ' Required Fields are empty</div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))))
		{ $prompt_message = '<div id="error">Please enter a valid email address</div>';}
	else if($_SESSION['security_code'] != htmlspecialchars(trim($_POST['secode']), ENT_QUOTES)){
		$prompt_message = '<div id="error">Invalid Security Code</div>';
	}else{
		
		$body = '<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px; margin: 0 auto;">
			<div style="border:8px double #c3c3d0; padding:12px;">
			<div align="center" style="color:#990000; font-style:italic; font-size:20px; font-family:Arial; margin:bottom: 15px;">('.$formname.')</div>
			
			<table width="90%" cellspacing="2" cellpadding="5" align="center" style="font-family:Verdana; font-size:13px">
				';
			
				foreach($_POST as $key => $value){
					if($key == 'secode') continue;
					elseif($key == 'submit') continue;
					
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
		require_once 'config.php';
		require_once 'swiftmailer/mail.php';
		
		// save data form on database
		include 'savedb.php';
		
		// save data form on database
		$subject = $formname ;		
		$attachments = array();		
		
		$name = $_POST['First_Name'].' '.$_POST['Last_Name'];
		$result = insertDB($name,$subject,$body,$attachments);	

		$templateVars = array('{link}' => get_home_url().'/onlineforms/'.$_SESSION['token'], '{company}' => COMP_NAME);

		Mail::Send($template, 'New Message Notification', $templateVars, $to_email, $to_name, $from_email, $from_name, $cc, $bcc);

		if($result){
			$prompt_message = '<div id="success">Your message has been submitted.  We will get in touch with you as soon as possible.<br/>Thank you for your time.</div>';
				unset($_POST);
		}else {
			$prompt_message = '<div id="error">Failed to send email. Please try again.</div>';
		}	
	}
		
}
/*************declaration starts here************/

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $formname; ?></title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
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
			},
			secode: "required"		
		},
		messages: {
			First_Name: "Required",
			Last_Name: "Required",
			Phone: "Required",
			Email: "Enter a valid Email",
			secode: ""
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
});
</script>
</head>
<?php
$prefix = array('- Please Select -','Mr.','Ms.','Mrs.','Dr.');
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');


$country = array('- Please Select -','Afghanistan','Aland Islands','Albania','Algeria','American Samoa','Andorra','Angola','Anguilla','Antarctica','Antigua and Barbuda','Argentina','Armenia','Aruba','Ascension Island','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bermuda','Bhutan','Bolivia','Bosnia-Herzegovina','Botswana','Bouvet Island','Brazil','British Indian O. Terr.','Brunei Darussalam','Bulgaria','Burkina Faso','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Cayman Islands','Central African Rep.','Chad','Chile','China','Christmas Island','Cocos (Keeling) Isl.','Colombia','Comoros','Congo','Congo, Democratic Republic of','Cook Islands','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Diego Garcia','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Falkland Isl. (Malvinas)','Faroe Islands','Fiji','Finland','France','France (European Ter.)','French Guiana','French Polynesia','French Southern Terr.','Gabon','Gambia','Georgia, Republic Of','Germany','Ghana','Gibraltar','Great Britain','Greece','Greenland','Grenada','Guadeloupe','Guam','Guatemala','Guinea','Guinea Bissau','Guyana','Haiti','Heard &amp; McDonald Isl.','Honduras','Hong Kong','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Ivory Coast','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati','Korea (North)','Korea(South)','Kosovo','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Macau','Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Martinique','Mauritania','Mauritius','Mayotte','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','Netherlands Antilles','New Caledonia','New Zealand','Nicaragua','Niger','Nigeria','Niue','Norfolk Island','Northern Mariana Islands','Norway','Oman','Pakistan','Palau','Palestinian Territory','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Pitcairn Island','Poland','Portugal','Puerto Rico','Qatar','Reunion','Romania','Russian Federation','Rwanda','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovak Republic','Slovenia','Solomon Islands','Somalia','South Africa','South Georgia  and  South Sandwich Islands','Spain','Sri Lanka','St. Barth&#233;lemy','St. Helena','St. Kitts &amp; Nevis','St. Lucia','St. Martin','St. Pierre &amp; Miquelon','St. Vincent &amp; Grenadines','Sudan','Suriname','Svalbard &amp; Jan Mayen Is','Swaziland','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste','Togo','Tokelau','Tonga','Trinidad &amp; Tobago','Tunisia','Turkey','Turkmenistan','Turks &amp; Caicos Islands','Tuvalu','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','US Minor outlying Isl.','Uzbekistan','Vanuatu','Vatican City State','Venezuela','Vietnam','Virgin Islands (British)','Virgin Islands (US)','Wallis &amp; Futuna Islands','Western Sahara','Yemen','Yugoslavia','Zambia','Zimbabwe');
?>
<body>
	<div id="container" class="rounded-corners">
		<div id="content" class="rounded-corners">
			<form id="submitform" name="contact" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				
				<div class="input">
						<label for="Prefix">Prefix:</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Prefix', 'select',$prefix); 
						?>
				</div>
				<div class="field">
					<div class="input">
						<label for="First Name">First Name: <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','First_Name','placeholder="Enter first name here"'); 
						?>						
					</div>						
					<div class="input f-right">
						<label for="Last Name">Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','Last_Name','placeholder="Enter last name here"'); 
						?>	
					</div>	 
				</div>
				<div class="field">		
					<div class="input">
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','Phone','placeholder="Enter phone here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Cell Number:">Cell Number:</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Cell_Number', 'text','Phone','placeholder="Enter cell number here"'); 
						?>
					</div>	
				</div>
				
				<div class="field">		
					<div class="input">
						<label for="Fax:">Fax:</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax', 'text','Fax','placeholder="Enter fax here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email here"'); 
						?>	
					</div>					
				</div>
				
				<div class="field">		
					<div class="input textarea">
						<label for="Street Address:">Street Address:</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Street_Address', 'text','Street_Address','placeholder="Enter street address here"'); 
						?>
					</div>		
				</div>
				<div class="field">		
					<div class="input">
						<label for="City">City:</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"'); 
						?>
					</div>		
					<div class="input f-right">
							<label for="State">State</label>
							<?php 
								// @param field name, class, optname, id and attribute
								$input->select('State', 'select',$state); 
							?>
					</div>
				</div>
				<div class="field">		
					<div class="input">
						<label for="Zip Code:">Zip Code:</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip_Code', 'text','Zip_Code','placeholder="Enter zip code here"'); 
						?>
					</div>		
					<div class="input f-right">
							<label for="Country">Country:</label>
							<?php 
								// @param field name, class, optname, id and attribute
								$input->select('Country', 'select',$country); 
							?>
					</div>
				</div>
				<div class="field">		
					<div class="input">
						<label for="Where_Did_You_Learn_About_Us">Where Did You Learn About Us? </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Where_Did_You_Learn_About_Us', 'text','Where_Did_You_Learn_About_Us','placeholder="Enter where did you learn about us here"'); 
						?>
					</div>		
				</div>
				 
				
				<div class="field">	
					<div class="verification">
						<img src="../forms/securitycode/SecurityImages.php?characters=5" border="0" id ="securiryimage" alt="Security code" />
						<?php 
							// @param field name, class, id and attribute
							$input->fields('secode', 'text','secode','placeholder="Enter security code here" title="This confirms you are a human user and not a spam-bot." maxlength="5"'); 
						?>	
						<button type='submit' class="button">Submit</button>						
					</div>	
				</div>
			</form>	
			<div class="clearfix"></div>			
		</div>
	</div>
</body>	
</html>