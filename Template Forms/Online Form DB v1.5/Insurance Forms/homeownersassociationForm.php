<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Homeowners Association Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Association_Name']) ||
		empty($_POST['Contact_Person']) ||
		empty($_POST['Email']) ||				
		empty($_POST['Phone']) ||	
		empty($_POST['Property_Address']) ||	
		empty($_POST['City']) ||	
		empty($_POST['Fax']) ||	
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

	 	//name of sender
		$name = $_POST['Contact_Person'];
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
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
$best_contact = array('- Please Select -','Phone','Fax','Email');
$Marital_Status = array('- Please Select -','Married','Unmarried','Divorced');
$bathrooms = array('- Please Select -','None','1','2','3','4','5');
$fireplace = array('- Please Select -','None','1','2','3','4','5');
$units = array('- Please Select -','Condo','Single family residence','Douplex','Triplex','Fourplex','5 or more');
$garagetype = array('- Please Select -','Attached','Detached','Built-in');
$contstructiontype = array('- Please Select -','Frame','Brick/Masonry','Log','Adobe','Other');
$roof_type = array('- Please Select -','Asphalt Shingle','Wood Shingle','Tile','Concrete');
$roof_age = array('- Please Select -','1-10 years','11-20 years','over 20 years');
$exterior_type = array('- Please Select -','Wood Siding','Stucco on Frame','Stucco on Masonry','Paint on Masonry','Solid Brick','Other');
$foundation = array('- Please Select -','Slab','Raised');
$liability = array('- Please Select -','$100,000','$300,000','$500,000','$1,000,000');
$deductible = array('- Please Select -','500','750','1000','1500','200','2500','5000');
$alarmsystem = array('- Please Select -','None','Just at my home','Alert Monitoring Services','Notifies Policies/Fire Dept');
$distance = array('- Please Select -','0-3 miles','4-6 miles','7-10 miles');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $formname; ?></title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<link rel="stylesheet" href="css/jquery.datepick.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {	
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Association_Name: "required",
			Contact_Person: "required",
			City: "required",
			Fax: "required",
			Email: {
				required: true,
				email: true
			},
			Phone: "required",
			Property_Address: "required",
			secode: "required"		
		},
		messages: {
			Association_Name: "Required",
			Contact_Person: "Required",
			City: "Required",
			Fax: "Required",
			Email: "Enter a valid Email",
			Phone: "Required",
			Property_Address: "Required",
			secode: ""
		}
	});
	$('#DATE,#Date,#Date_of_Birth').datepick({
        yearRange: "1970:2014",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top:8px; float:right;" />'
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
<body>
	<div id="container" class="rounded-corners">
		<div id="content" class="rounded-corners">
			<form id="submitform" name="contact" enctype="multipart/form-data" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Homeowners Association Information<input type="hidden" name="Homeowners Association Information" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Association_Name">Association Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Association_Name','text','Association_Name','placeholder="Enter name here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Contact_Person">Contact person <span>*</span></label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Contact_Person','text','Contact_Person','placeholder="Enter name of contact person here"'); 
							?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Phone">Phone <span>*</span></label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Phone','text','Phone','placeholder="Enter phone here"'); 
							?>		
					</div>
					<div class="input f-right">
						<label for="Fax">Fax <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax','text','Fax','placeholder="Enter fax here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email','text','Email','placeholder="Enter email here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Best_way_to_contact">Best way to contact</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Best_way_to_contact', 'select',$best_contact); 
						?>
					</div>
				</div>
				
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">About the property <input type="hidden" name="About the property " value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Property_Address">Property address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_Address','text','Property_Address','placeholder="Enter property address here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="City">City</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City','text','City','placeholder="Enter city here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
							<label for="State">State</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State','select', $state); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Zipcode">Zip code</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->fields('Zipcode','text','Zipcode','placeholder="Enter zip code here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Number_of_house_units">No. of house units</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_house_units','text','Number_of_house_units','placeholder="Enter number of house units here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Number_of_Board_of_Directors">Number of Board of Directors</label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Number_of_Board_of_Directors','text','Number_of_Board_of_Directors','placeholder="Enter number of board director here"'); 
							?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Number_of_full_time_employees">Number of full time employees</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_full_time_employees','text','Number_of_full_time_employees','placeholder="Enter number of full time employees here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Number_of_part-time_employees">Number of part-time employees</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->fields('Number_of_part-time_employees','text','Number_of_part-time_employees','placeholder="Enter number of part time employees here"'); 
						?>
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Coverage<input type="hidden" name="Coverage" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Property">Property </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property','text','Property','placeholder="Enter property here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Liability">Liability</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Liability','text','Liability','placeholder="Enter liability here"');
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">Insurance Information<input type="hidden" name="Insurance Information" value=":"/></div>					
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Prior/current_carrier">Prior/current carrier</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->fields('Prior/current_carrier','text','Prior/current_carrier','placeholder="Enter prior/current carrier here"');
						?>	
					</div>
					<div class="input f-right">
						<label for="Number_of_claims">No. of claims <span style="font-size:11px;color:#000;font-weight:regular;">(in last 3 years)</span></label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->fields('Number_of_claims','text','Number_of_claims','placeholder="Enter number of claims here"');
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="1.Type_of_claim">1. Type of claim</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->fields('1.Type_of_claim','text','1.Type_of_claim','placeholder="Enter type of claim here"');
						?>	
					</div>
					<div class="input f-right">
						<label for="Amount_of_claim">Amount of claim</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->fields('Amount_of_claim','text','Amount_of_claim','placeholder="Enter amount of claim here"');
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="2.Type_of_claim">2. Type of claim</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->fields('2.Type_of_claim','text','2.Type_of_claim','placeholder="Enter type of claim here"');
						?>	
					</div>
					<div class="input f-right">
						<label for="Amount_of_claim_">Amount of claim</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->fields('Amount_of_claim_','text','Amount_of_claim_','placeholder="Enter amount of claim here"');
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="3.Type_of_claim">3. Type of claim</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->fields('3.Type_of_claim','text','3.Type_of_claim','placeholder="Enter type of claim here"');
						?>	
					</div>
					<div class="input f-right">
						<label for="Amount_of_claim__">Amount of claim</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->fields('Amount_of_claim__','text','Amount_of_claim__','placeholder="Enter amount of claim here"');
						?>	
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional_Information">Additional Information <span style="font-size:11px;color:#000;font-weight:regular;">(Please include any losses for the last 5 years)</span></label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Information', '','Additional_Information','placeholder="Enter additional instructions here" cols="88"'); 
						?>
					</div>		
				</div>
				
				<div class="field">	
					<div class="verification">
						<img src="securitycode/SecurityImages.php?characters=5" border="0" id ="securiryimage" alt="Security code" />
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