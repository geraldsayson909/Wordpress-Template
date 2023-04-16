<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Workers Compensation Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Business_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||	
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
$business = array('- Please Select -','Single Proprietorsh','Partnership','Corporation','Association','LLC');
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
			Business_Name: "required",
			Address: "required",
			City: "required",
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
			Address: "Required",
			City: "Required",
			Email: "Enter a valid Email",
			Phone: "Required",
			Contact_Name: "Required",
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
						<div class="input textarea" style="color:#000; font-size:12px; font-weight:bold;text-align:left; border-radius:5px;"><input type="hidden" value=":" name="Secure_Workers_compensation_insurance_to_assure_your_employees_of_much_needed_help_in_the_event_of_workplace_injuries_diseases_and_accidents"/><strong>Secure Workers' compensation insurance to assure your employees of much-needed help in the event of workplace injuries, diseases and accidents.<br/><br/>Fill out the online form below as completely as possible. Our agents will get back to you for your quote.</strong>
						</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Business_Name">Business Name<span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Business_Name', 'text','Business_Name','placeholder="Enter business name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="DBA">DBA</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('DBA', 'text','DBA','placeholder="Enter dba here"');
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Contact_Name">Contact Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_Name', 'text','Contact_Name','placeholder="Enter contact name here"'); 
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
						<label for="Phone">Phone<span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','Phone','placeholder="Enter phone here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label for="Website">Website</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Website', 'text','Website','placeholder="Enter website here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Email">Email<span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email here"'); 
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
						<label for="Address">Address<span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Zipcode">Zipcode</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zipcode', 'text','Zipcode','placeholder="Enter zipcode here"');  
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
							<label for="State">State</label>
							<?php 
								// @param field name, class, id and attribute
								$input->select('State', 'select',$state); 
							?>						
					</div>
				</div>
				<hr/>
				<div class="field">
					<div class="input">
						<label for="Current_Insurance_Company">Current Insurance Company</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Insurance_Company', 'text','Current_Insurance_Company','placeholder="Enter current insurance company here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Current_Policy_Expiration_Date">Current Policy Expiration Date</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Policy_Expiration_Date', 'text','DATE','placeholder="Enter current policy expiration here"');  
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
						<label for="if_yes_what_kind">if yes what kind?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('if_yes_what_kind', 'text', 'if_yes_what_kind','placeholder="Enter kind here"'); 
						?>	
					</div>
				</div>
				<hr/>
				<div class="field">
					<div class="input textarea">
						<label for="Type_of_Business">Type of Business</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Type_of_Business', 'Type_of_Business',$business); 
						?>	
					</div>
				</div>
				<div class="field">	
					<div class="input">	
						<label for="Federal_Employee_ID_Number">Federal Employee ID Number</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Federal_Employee_ID_Number', 'text', 'Federal_Employee_ID_Number','placeholder="Enter federal employee id number here"');
						?>
					</div>	
					<div class="input f-right">	
						<label for="Description_of_Business">Description of Business</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Description_of_Business', 'text', 'Description_of_Business','placeholder="Enter description of business operations here"');
						?>
					</div>		
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Number_of_Owners_Executive_to_be_excluded_or_included">Number of Owners, Executive to be excluded / or included</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Number_of_Owners_Executive_to_be_excluded_or_included', '','Number_of_Owners_Executive_to_be_excluded_or_included','placeholder="Enter here" cols="88"'); 
						?>
					</div>		
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Number_of_full_time_employees">Number of full time employees</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_full_time_employees', 'text','Number_of_full_time_employees','placeholder="Enter number of full time employees here"'); 
						?>						
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Duties_of_full_time_employees">Duties of full time employees</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Duties_of_full_time_employees', '','Duties_of_full_time_employees','placeholder="Enter here" cols="88"'); 
						?>
					</div>		
				</div>
				<div class="field">
					<div class="input">
						<label for="Annual_Payroll_of_Full_time_employees">Annual Payroll of Full time employees</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Annual_Payroll_of_Full_time_employees', 'text','Annual_Payroll_of_Full_time_employees','placeholder="Enter annual payroll of full time employees here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Number_of_part_time_employees">Number of part time employees</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_part_time_employees', 'text','Number_of_part_time_employees','placeholder="Enter number of part time employees here"'); 
						?>		
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Duties_of_part_time_employees">Duties of part time employees</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Duties_of_part_time_employees', '','Duties_of_part_time_employees','placeholder="Enter here" cols="88"'); 
						?>
					</div>		
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Annual_Payroll_of_Part_time_employees">Annual Payroll of Part time employees</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Annual_Payroll_of_Part_time_employees', 'text','Annual_Payroll_of_Part_time_employees','placeholder="Enter annual payroll of part time employees here"');  
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