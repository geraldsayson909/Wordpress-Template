<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Surety Bonds Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Contact_First_Name']) ||
		empty($_POST['Business_Address_street']) ||
		empty($_POST['City']) ||	
		empty($_POST['Email']) ||	
		empty($_POST['Phone_Number']) ||	
		empty($_POST['Applicants_Full_Name_as_it_appears_on_bond']) ||	
		empty($_POST['Last_Name']) ||	
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
		$name = $_POST['Contact_First_Name'].' '.$_POST['Last_Name'];
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
$best_way = array('- Please Select -','Phone','Fax','Email');
$YN = array('- Please Select -','Yes','No');
$business = array('- Please Select -','Solo Proprietor','Partnership','Corporation','LLC');
$insurance = array('- Please Select -','$100,000/200,000','$300,000/600,000','$500,000/1,000,000','1,000,000/2,000,000');
$limits = array('- Please Select -','1 Million','2 Million','3 Million','4 Million','5 Million');
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
			Contact_First_Name: "required",
			Business_Address_street: "required",
			City: "required",
			Email: {
				required: true,
				email: true
			},
			Phone_Number: "required",
			Applicants_Full_Name_as_it_appears_on_bond: "required",
			Last_Name: "required",
			secode: "required"		
		},
		messages: {
			Contact_First_Name: "Required",
			Business_Address_street: "Required",
			City: "Required",
			Email: "Enter a valid Email",
			Phone_Number: "Required",
			Applicants_Full_Name_as_it_appears_on_bond: "Required",
			Last_Name: "Required",
			secode: ""
		}
	});

	var curr_year = new Date().getFullYear();
	$('#DATE,#Date,#Date_of_Birth').datepick({
        yearRange: "1900:"+curr_year+"",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top: 8px; float: right; position: absolute; right: 10px; top: 36px;" />'
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
						<div class="input textarea" style="color:#000; font-size:12px; font-weight:bold;text-align:left; border-radius:5px;"><input type="hidden" value=":" name="Whether_what_you_need_is_commercial_surety_such_as_probate_and_federal_bonds_or_fidelity_bonds_to_protect_your_business_against_employee_theft_and_dishonesty_we_can_find_the_surety_bond_that_you_are_looking_for"/><strong>Whether what you need is commercial surety such as probate and federal bonds, or fidelity bonds to protect your business against employee theft and dishonesty, we can find the surety bond that you are looking for.<br/><br/>Fill out the online form below as completely as possible. Our agents will get back to you for your quote.</strong>
						</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Contact_First_Name">Contact First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_First_Name', 'text','Contact_First_Name','placeholder="Enter contact first name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="City">City <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"');
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Last_Name">Last Name  <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','Last_Name','placeholder="Enter last name here"'); 
						?>					
					</div>
					<div class="input f-right">
							<label for="State">State</label>
							<?php 
								// @param field name, class, id and attribute
								$input->select('State', 'select',$state); 
							?>						
					</div>
				</div>									
				<div class="field">
					<div class="input">
						<label for="Applicants_Full_Name_as_it_appears_on_bond">Applicant's Full Name as it appears on bond <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Applicants_Full_Name_as_it_appears_on_bond', 'text','Applicants_Full_Name_as_it_appears_on_bond','placeholder="Enter applicants full name here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label for="Zip_Code">Zip Code</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip_Code', 'text','Zip_Code','placeholder="Enter zip code here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Federal_I_D_Number">Federal I.D. Number</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Federal_I_D_Number', 'text','Federal_I_D_Number','placeholder="Enter Federal ID Number here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Phone_Number">Phone Number</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone_Number', 'text','Phone_Number','placeholder="Enter phone number here"');  
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Business_Address_street">Business Address street <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Business_Address_street', 'text','Business_Address_street','placeholder="Enter business address street here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Fax_Number">Fax Number</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax_Number', 'text','Fax_Number','placeholder="Enter fax number here"');  
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Best_way_to_contact_you">Best way to contact you</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_way_to_contact_you', 'Best_way_to_contact_you',$best_way);  
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Date_Business_Establish">Date Business Establish</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_Business_Establish', 'text','DATE','placeholder="Enter date business establish here"');  
						?>	
					</div>
					<div class="input f-right">
						<label for="Type_of_Business">Type of Business</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Type_of_Business', 'Type_of_Business',$business); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Do_You_Have_Business_Insurance">Do You Have Business Insurance</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Do_You_Have_Business_Insurance', 'text','Do_You_Have_Business_Insurance','placeholder="Enter  here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Liability_Limts">Liability Limts</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Liability_Limts', 'text','Liability_Limts','placeholder="Enter liability limts here"');   
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Property_Damage_Limits">Property Damage Limits</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Property_Damage_Limits', 'text', 'Property_Damage_Limits','placeholder="Enter property damage limits here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Have_you_ever_had_a_business_Fail">Have you ever had a business Fail</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Have_you_ever_had_a_business_Fail', 'Have_you_ever_had_a_business_Fail',$YN); 
						?>	
					</div>
				</div>
				<div class="field">	
					<div class="input">	
						<label for="Have_you_ever_had_a_business_filed_bankruptcy">Have you ever had a business filed bankruptcy</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->select('Have_you_ever_had_a_business_filed_bankruptcy', 'Have_you_ever_had_a_business_filed_bankruptcy',$YN); 
						?>	
					</div>	
					<div class="input f-right">	
						<label for="Has_the_owner_of_the_business_ever_filed_for_bankruptcy">Has the owner of the business ever filed for bankruptcy</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->select('Has_the_owner_of_the_business_ever_filed_for_bankruptcy', 'Has_the_owner_of_the_business_ever_filed_for_bankruptcy',$YN); 
						?>	
					</div>		
				</div>
				<hr/>
				<div class="field">	
						<div class="input textarea" style="color:#000; font-size:14px; font-weight:bold;text-align:left; border-radius:5px;"><input type="hidden" value=":" name="Bond_Information"/>Bond Information
						</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Nature_Of_Bond_Required">Nature Of Bond Required</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Nature_Of_Bond_Required', 'text','Nature_Of_Bond_Required','placeholder="Enter nature of bond required here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Obligee_To_whom_bond_is_to_be_given">Obligee (To whom bond is to be given)</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Obligee_To_whom_bond_is_to_be_given', 'text','Obligee_To_whom_bond_is_to_be_given','placeholder="Enter obligee here"'); 
						?>		
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Amount_of_Bond_$">Amount of Bond $</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Amount_of_Bond_$', 'text','Amount_of_Bond_$','placeholder="Enter amount of bond here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Effective_Date">Effective Date</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Effective_Date', 'text','DATE','placeholder="Enter date here"'); 
						?>		
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Term_Of_Bond">Term Of Bond</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Term_Of_Bond', 'text','Term_Of_Bond','placeholder="Enter term of bond here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Has_applicant_been_declined_for_a_bond">Has applicant been declined for a bond</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Has_applicant_been_declined_for_a_bond', 'text','Has_applicant_been_declined_for_a_bond','placeholder="Enter here"'); 
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