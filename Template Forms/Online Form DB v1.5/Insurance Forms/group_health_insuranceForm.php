<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Group Health Insurance Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||	
		empty($_POST['Email']) ||	
		empty($_POST['Phone']) ||		
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
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
$contact_options = array('- Please Select -','Phone','Fax','Email');
$best_time = array('- Please Select -','Anytime','Morning at Home','Morning at Work','Afternoon at Home','Afternoon at Work','Evening at Home','Evening at Work');
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
			First_Name: "required",
			Last_Name: "required",
			Email: {
				required: true,
				email: true
			},
			Phone: "required",
			secode: "required"		
		},
		messages: {
			First_Name: "Required",
			Last_Name: "Required",
			Email: "Enter a valid Email",
			Phone: "Required",
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
	
	var curr_year = new Date().getFullYear();
    $('.Date').datepick({
        yearRange: "1900:"+curr_year+"",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top: 8px; float: right; position: absolute; right: 10px; top: 36px;" />'
    });
});
</script>
<style>
.header{background:#1D2685;color:#FFF;font-size:20px;font-weight:bold;padding:10px;border-radius:7px;}
</style>
<?php
 $yesno = array('- Please Select -','Yes','No');
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
$title = array('- Please Select -','Administration','CEO/President/ Owner','CFO','CIO/CTO','Consultant','Customer Service','Engineer/Programmer','Facilities/Operations','Finance/ Accounting Manager','Finance/ Accounting Staff','General Manager','Human Resources','IS/IT Management','IS/ IT Staff','Marketing Manager','Marketing Staff','Partner/Principal','Purchasing Manager','Sales/ Business Dev. Manager','Sales/ Business Dev.','Vice President/Senior Manager');
$Industry = array('- Please Select -','Advertising/Marketing/PR','Agriculture','Biotech/Pharmaceuticals','Computers - Hardware','Computers - Software','Construction/General Contracting','Consulting','Education','Equipment Sales &amp; Service','Financial Services','Government','Healthcare','Information Services','Insurance','Legal','Manufacturing','Media/Entertainment/Publishing','Non-Profit','Other Services','Real Estate','Restaurant','Retail','Telecom/Utilitie','Transportation/Logistics','Travel/Hospitality','Wholesale');
$entity_type= array('- Please Select -','Association', 'Corporation', 'S Corporation', 'Limited Liability Company', 'Limited Liability Partnership', 'Partnership', 'Sole Proprietorship', 'Limited Partnership', 'Professional Corporation', 'Nonprofit Corporation');
?>
</head>
<body>
	<div id="container" class="rounded-corners">
		<div id="content" class="rounded-corners">
			<form id="submitform" name="contact" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				
				
				<div class="field">
					<div class="input textarea">
						<div class="header"><input type="hidden" value=":" name="Customer_Information" /> Customer Information</div>
					</div>
				</div>	
                <div class="field">
					<div class="input textarea">
						<label>Title / Role</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Title_Role', 'select',$title); 
						?>						
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label>First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','','placeholder="Enter First Name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','','placeholder="Enter Last Name here"'); 
						?>	
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label>Date of Birth</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_of_Birth', 'text Date','Date_of_Birth','placeholder="Enter Date of Birth here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Gender</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Gender', 'select',array('- Please Select -','Female','Male')); 
						?>	
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label>Marital Status</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Marital_Status', 'select',array('- Please Select -','Single','Married and lives with spouse','Married but separated','Divorced','Widowed')); 
						?>						
					</div>
					<div class="input f-right">
						<label>Occupation</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Occupation', 'text','','placeholder="Enter Occupation here"'); 
						?>	
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label>Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','','placeholder="Enter Email here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label>Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','','placeholder="Enter Phone here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Best day to contact</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_day_to_contact', 'select',array('- Please Select -','Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')); 
						?>						
					</div>
					<div class="input f-right">
						<label>Best time to contact</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Best_time_to_contact', 'select',array('- Please Select -','Anytime','Morning','Afternoon','Evening')); 
						?>
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label>Business Name</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Business_Name', 'text','','placeholder="Enter Business Name here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label>What is your business entity?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('What_is_your_business_entity', 'text',$entity_type); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Industry</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Industry', 'text',$Industry);  
						?>					
					</div>
					<div class="input f-right">
						<label>State</label>
						<?php 
							$input->select('State', 'text',$state);  
							
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Zip</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip', 'text','','placeholder="Enter Zip here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label>Website</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Website', 'text','','placeholder="Enter Website here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Phone</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','','placeholder="Enter Phone here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label>Number of full-time employees </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_full_time_employees', 'text','','placeholder="Enter Number of full-time employees  here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label>Number of part-time employees </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_part_time_employees', 'text','','placeholder="Enter Number of part-time employees here"'); 
						?>	
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea"><hr/>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<div class="header"><input type="hidden" value=":" name="Applicant_Information" /> Applicant Information</div>
					</div>
				</div>		
				<div class="field">
					<div class="input textarea">
						<label>Full Name</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Applicant_Full_Name', 'text','','placeholder="Enter Full Name here"'); 
						?>					
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label>Zip</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Applicant_Zip', 'text','','placeholder="Enter Zip here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label>Gender</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Applicant_Gender', array('Male','Female')); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label>Date of Birth</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Applicant_Date_of_Birth', 'text Date','','placeholder="Enter Date of Birth here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label>Do you currently have a group life insurance plan for your business?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Do_you_currently_have_a_group_life_insurance_plan_for_your_business', array('- Please Select -','Yes','No')); 
						?>	
					</div>
				</div>
				
				
				
				
				
				
				
				
				
				<div class="field">	
					<div class="input textarea">	
						<label>Please describe any requirements you have for a life insurance plan: </label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Please_describe_any_requirements_you_have_for_a_life_insurance_plan', '','','placeholder="Please describe any requirements you have for a life insurance plan here" cols="88"'); 
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