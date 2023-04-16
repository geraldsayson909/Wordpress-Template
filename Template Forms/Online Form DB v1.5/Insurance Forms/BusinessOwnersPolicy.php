<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Business Owners Policy Form';
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
$entity_type= array('- Please Select -','Association', 'Corporation', 'S Corporation', 'Limited Liability Company', 'Limited Liability Partnership', 'Partnership', 'Sole Proprietorship', 'Limited Partnership', 'Professional Corporation', 'Nonprofit Corporation');
$industry = array('- Please Select -','Advertising/Marketing/PR','Agriculture','Biotech/Pharmaceuticals','Computers - Hardware','Computers - Software','Construction/General Contracting','Consulting','Education','Equipment Sales &amp; Service','Financial Services','Government','Healthcare','Information Services','Insurance','Legal','Manufacturing','Media/Entertainment/Publishing','Non-Profit','Other Services','Real Estate','Restaurant','Retail','Telecom/Utilitie','Transportation/Logistics','Travel/Hospitality','Wholesale');
$contruction_type = array('- Please Select -','wood frame', 'joisted masonry', 'masonry', 'non-customable', 'fire resistive');
$app_revenue = array('- Please Select -','Under $100,000', '$100,000 - 499,999', '$500,000-$999,999', '$1,000,000-$9,999,999', '$10,000,000+');
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
	
	var curr_year = new Date().getFullYear();
    $('#Effective_Date,#Expiration_Date').datepick({
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
			<form id="submitform" name="contact" enctype="multipart/form-data" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">CONTACT INFORMATION<input type="hidden" name="Contact Information" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="First_Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name','text','First_Name','placeholder="Enter first name here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Last_Name">Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name','text','Last_Name','placeholder="Enter last name here"'); 
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
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email','text','Email','placeholder="Enter email here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Fax">Fax</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax','text','Fax','placeholder="Enter fax here"'); 
						?>	
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">BUSINESS INFORMATION<input type="hidden" name="Business Information" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="What_is_your_business_entity">What is your business entity?</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('What_is_your_business_entity', 'select',$entity_type); 
						?>
					</div>
					<div class="input f-right">
						<label for="Industry">Industry</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Industry', 'select',$industry); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Business_Name">Business Name</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Business_Name','text','Business_Name','placeholder="Enter business name here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Web_Address">Web Address</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Web_Address','text','Web_Address','placeholder="Enter web address here"'); 
						?>	
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0044AF; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">MAILING ADDRESS<input type="hidden" name="Mailing Address" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Street_Address_1">Street Address 1</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Street_Address_1','text','Street_Address_1','placeholder="Enter street address 1 here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Street_Address_2">Street Address 2</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Street_Address_2','text','Street_Address_2','placeholder="Enter street address 2 here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="City">City</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City','text','City','placeholder="Enter city here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Address">State</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('State', 'select',$state); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Zip">Zip</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip','text','Zip','placeholder="Enter zip here"'); 
						?>	
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Describe_your_operations">Describe your operations</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Describe_your_operations', '','Describe_your_operations','placeholder="Enter operations description here" cols="88" style="height:70px;"'); 
						?>
					</div>		
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="color:#000; font-size:14px; font-weight:bold;">What is the breakdown of these individuals?<input type="hidden" name="What is the breakdown of these individuals?" value=";"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Full_or_part-time_Employees">Full or part-time Employees</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Full_or_part-time_Employees','text','Full_or_part-time_Employees','placeholder="Enter full/part-time employees here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Sub-contractors_or_Consultants">Sub-contractors/Consultants</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Sub-contractors_or_Consultants','text','Sub-contractors_or_Consultants','placeholder="Enter sub-contractors/consultants here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Business_area_occupied">Business area occupied (square feet)</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Business_area_occupied','text','Business_area_occupied','placeholder="Enter business area occupied here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Number_of_stories_in_this_building">Number of stories in this building</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_stories_in_this_building','text','Number_of_stories_in_this_building','placeholder="Enter number of stories here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Sprinklered">Sprinklered?</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Sprinklered',array('Yes','No'));
						?>	
					</div>
					<div class="input f-right">
						<label for="Construction_Type">Construction Type</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Construction_Type', 'select',$contruction_type); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Policy_effective_date_desired">Policy effective date desired</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Policy_effective_date_desired','text','Effective_Date','placeholder="Enter effective date here" '); 
						?>	
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="color:#000; font-size:14px; font-weight:bold;">If you currently have business insurance, please indicate the following: [Optional]<input type="hidden" name="If you currently have business insurance" value=";"/></div>					
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Current_provider">Current provider</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_provider','text','Current_provider','placeholder="Enter current provider here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Expiration_Date">Expiration Date</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Expiration_Date','text','Expiration_Date','placeholder="Enter expiration date here"'); 
						?>	
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Additional_Requirements">Please describe any additional requirements or specifics about your insurance needs. The more information you can provide here, the more accurately our vendors can be in providing quotes</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Information', '','Additional_Information','placeholder="Enter additional information here" cols="88"'); 
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