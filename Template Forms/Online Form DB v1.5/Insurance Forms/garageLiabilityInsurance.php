<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Garage Liability Insurance Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Business_Name']) ||
		empty($_POST['Complete_address_of_the_business']) ||	
		empty($_POST['Email']) ||	
		empty($_POST['Phone']) ||	
		empty($_POST['Contact_Name']) ||	
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
		$name = $_POST['Contact_Name'];
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
$contact_options = array('- Please Select -','Phone','Fax','Email');
$best_time = array('- Please Select -','Anytime','Morning at Home','Morning at Work','Afternoon at Home','Afternoon at Work','Evening at Home','Evening at Work');
$YN = array('- Please Select -','Yes','No');
$business = array('- Please Select -','Individual','Partnership','Corporation','Joint','Venture','others');
$type = array('- Please Select -','Repair shop','Auto dealer','Auto Service Center','Garage Parking','Others');
$insurance = array('- Please Select -','$100,000/200,000','$300,000/600,000','$500,000/1,000,000','1,000,000/2,000,000');
$facility = array('- Please Select -','building','open lot','others');
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
			Business_Name: "required",
			Complete_address_of_the_business: "required",
			Email: {
				required: true,
				email: true
			},
			Phone: "required",
			Contact_Name: "required",
			secode: "required"		
		},
		messages: {
			Business_Name: "Required",
			Complete_address_of_the_business: "Required",
			Email: "Enter a valid Email",
			Phone: "Required",
			Contact_Name: "Required",
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
<body>
	<div id="container" class="rounded-corners">
		<div id="content" class="rounded-corners">
			<form id="submitform" name="contact" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				<div class="field">	
						<div class="input textarea" style="color:#000; font-size:12px; font-weight:bold;text-align:left; border-radius:5px;"><input type="hidden" value=":" name="Garage_liability_insurance_secures_owners_and_operators_of_repair_shops_auto_service_center_and_auto_dealers_against_lawsuits_and_liability_claims_for_physical_injuries_and_property_damage_arising_from_its_products_services_and_operations_It_also_includes_a_cover_for_property_damage_or_loss_caused_on_the_business_own_automobiles"/><strong>Garage liability insurance secures owners and operators of repair shops, auto service center, and auto dealers against lawsuits and liability claims for physical injuries and property damage arising from its products, services and operations. It also includes a cover for property damage or loss caused on the business own automobiles.</strong>
						</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Business_Name">Business Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Business_Name', 'text','Business_Name','placeholder="Enter business name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Fax">Fax</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax', 'text','Fax','placeholder="Enter fax here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Contact_Name">Contact Name  <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_Name', 'text','Contact_Name','placeholder="Enter contact name here"'); 
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
					<div class="input">
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','Phone','placeholder="Enter phone here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label for="Complete_address_of_the_business">Complete address of the business <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Complete_address_of_the_business', 'text','Complete_address_of_the_business','placeholder="Enter complete address of the business here"'); 
						?>	
					</div>
				</div>
				<hr/>
				<div class="field">	
						<div class="input textarea" style="color:#000; font-size:14px; font-weight:bold;text-align:left; border-radius:5px;"><input type="hidden" value=":" name="About_the_Business"/>About the Business 
						</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Form_of_business">Form of business</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Form_of_business', 'Form_of_business',$business); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Type_of_Business">Type of Business</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Type_of_Business', 'Type_of_Business',$type); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">	
						<label for="Description_of_Business">Description of Business</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Description_of_Business', 'text', 'Description_of_Business','placeholder="Enter description of business operations here"');
						?>
					</div>		
					<div class="input f-right">
						<label for="Years_established">Years established</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Years_established', 'text', 'Years_established','placeholder="Enter years established here"');;  
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">	
						<label for="Storage_Facility">Storage Facility</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->select('Storage_Facility', 'Storage_Facility',$facility);
						?>
					</div>		
					<div class="input f-right">
						<label for="Number_of_vehicles_owned_by_the_business">Number of vehicles owned by the business</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_vehicles_owned_by_the_business', 'text', 'Number_of_vehicles_owned_by_the_business','placeholder="Enter number of vehicles owned by the business here"');;  
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Value_of_the_business_vehicles">Value of the business vehicles</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Value_of_the_business_vehicles', 'text', 'Value_of_the_business_vehicles','placeholder="Enter value of the business vehicles here"'); 
						?>	
					</div>
				</div>
				<hr/>
				<div class="field">	
						<div class="input textarea" style="color:#000; font-size:14px; font-weight:bold;text-align:left; border-radius:5px;"><input type="hidden" value=":" name="Covarage_Requested"/>Covarage Requested
						</div>
				</div>
				<div class="field">
					<div class="input">	
						<label for="Property_damage">Property damage</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_damage', 'text', 'Property_damage','placeholder="Enter property damage here"');;  
						?>
					</div>		
					<div class="input f-right">
						<label for="Medical_payments">Medical payments</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Medical_payments', 'text', 'Medical_payments','placeholder="Enter medical payments here"');;  
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Legal_Liability">Legal Liability</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Legal_Liability', 'text','Legal_Liability','placeholder="Enter legal liability here"'); 
						?>						
					</div>
				</div>
				<hr/>
				<div class="field">	
						<div class="input textarea" style="color:#000; font-size:14px; font-weight:bold;text-align:left; border-radius:5px;"><input type="hidden" value=":" name="Insurance_Information "/>Insurance Information 
						</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Current_Insurance_Company">Current Insurance Company</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Insurance_Company', 'text','Current_Insurance_Company','placeholder="Enter current insurance company here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Current_Policy_Expiry">Current Policy Expiry</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Policy_Expiry', 'text','Current_Policy_Expiry','placeholder="Enter current policy expiry here"'); 
						?>		
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Number_of_Years_Insured">Number of Years Insured</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Years_Insured', 'text','Number_of_Years_Insured','placeholder="Enter number of years insured here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Have_you_had_any_claims">Have you had any claims?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Have_you_had_any_claims','Have_you_had_any_claims',$YN); 
						?>		
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="What_kind_of_claims">What kind of claims</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->fields('What_kind_of_claims', 'text','What_kind_of_claims','placeholder="Enter what kind of claims here"'); 
						?>
					</div>		
				</div>
				<hr/>
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional_Information">Additional Information </label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Information', '','Additional_Information','placeholder="Enter additional information here" cols="88"'); 
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