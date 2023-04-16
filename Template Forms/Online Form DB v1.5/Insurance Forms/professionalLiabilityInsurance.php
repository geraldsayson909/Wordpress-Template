<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Professional Liability Insurance Form';
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
$YN = array('Yes','No');
$rent_own = array('- Please Select -','Rent','Own');
$insurance = array('- Please Select -','$100,000/200,000','$300,000/600,000','$500,000/1,000,000','1,000,000/2,000,000');
$business = array('- Please Select -','Single Proprietorsh','Partnership','Corporation','Association','LLC');
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
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
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
					<div class="field">	
						<div class="input textarea" style="color:#000; font-size:12px; font-weight:bold;text-align:left; border-radius:5px;"><input type="hidden" value=":" name="Fill_out_the_online_form_below_as_completely_as_possible_Our_agents_will_get_back_to_you_for_your_quote"/><strong>With Professional Liability Insurance, professionals such as physicians, lawyers, accountants and real estate brokers are protected in the event of liability claims for damage or losses that may be suffered by their clients in the course of their professional services.<br/><br/>Fill out the online form below as completely as possible. Our agents will get back to you for your quote.</strong>
						</div>
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
						<label for="FEI_Number">FEI Number</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('FEI_Number', 'text','FEI_Number','placeholder="Enter fei number here"');  
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Address">Address  <span>*</span></label>
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
					<div class="input">
						<label for="State">State</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State', 'select',$state); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','Phone','placeholder="Enter Phone here"'); 
						?>	
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label for="City">City <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter contact name here"'); 
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
						<label for="Business_Name">Business Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Business_Name', 'text','Business_Name','placeholder="Enter business name here"'); 
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
						<label for="DBA">DBA</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('DBA', 'text','DBA','placeholder="Enter dba here"'); 
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
						<label for="Current_Policy_Expiry">Current Policy Expiry</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Policy_Expiry', 'text','Current_Policy_Expiry','placeholder="Enter building square footage here"');  
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Number_of_Years_Insured">Number of Years Insured</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Years_Insured', 'text','Number_of_Years_Insured','placeholder="Enter current insurance company here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Have_you_had_any_claims_in_the_last_5_years">Have you had any claims in the last 5 years</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Have_you_had_any_claims_in_the_last_5_years', 'text','Have_you_had_any_claims_in_the_last_5_years','placeholder="Enter claims in the last 5 years here"');  
						?>	
					</div>
				</div>
				
				<div class="field">	
					<div class="input textarea">	
						<label for="Give_us_a_brief_description_of_you_day_to_day_operation">Give us a brief description of you day to day operation</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Give_us_a_brief_description_of_you_day_to_day_operation', '','Give_us_a_brief_description_of_you_day_to_day_operation','placeholder="Enter description here" cols="88"'); 
						?>
					</div>		
				</div>
				<hr/>
				<div class="field">
					<div class="input">
						<label for="Type_of_Business">Type of Business</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Type_of_Business', 'Type_of_Business',$business); 
						?>	
					</div>
			
					<div class="input f-right">
						<label for="Category_of_Business">Category of Business</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Category_of_Business', 'text','Category_of_Business','placeholder="Enter category of business here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Year_Established">Year Established</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year_Established','text','Year_Established','placeholder="Enter year established here"');  
						?>	
					</div>
					<div class="input f-right">
						<label for="Number_of_Office_Locations">Number of Office Locations</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Office_Locations', 'text','Number_of_Office_Locations','placeholder="Enter number of office locations here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Rent_or_Own_Office">Rent or Own Office</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Rent_or_Own_Office', 'Rent_or_Own_Office',$rent_own); 
						?>						
					</div>
				</div>
				<hr/>
				<div class="field">
					<div class="input">
						<label for="Annual_Gross_Revenue">Annual Gross Revenue</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Annual_Gross_Revenue', 'text','Annual_Gross_Revenue','placeholder="Enter annual gross revenue here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Number_of_Employees">Number of Employees</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Employees', 'text','Number_of_Employees','placeholder="Enter number of employees here"'); 
						?>		
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Liability_limit_requested">Liability limit requested</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Liability_limit_requested', 'text','Liability_limit_requested','placeholder="Enter liability limit requested here"'); 
						?>		
					</div>
					<div class="input f-right">
						<label for="Employee_payroll">Employee payroll</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Employee_payroll', 'text','Employee_payroll','placeholder="Enter employee payroll here"'); 
						?>		
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional_Information">Additional Information</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Information', '','Additional_Information','placeholder="Enter additional information here" cols="88"'); 
						?>
					</div>		
				</div>
				<hr/>
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