<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Job Seekers Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Job_Applying_For']) ||		
		empty($_POST['First_Name']) ||	
		empty($_POST['MI']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['SSN']) ||
		empty($_POST['Address']) ||
		empty($_POST['Zip']) ||
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
	
		$body = '<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px; margin: 	0 auto;">
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
		
		
		$name = $_POST['First_Name'].' '.$_POST['MI'].' '.$_POST['Last_Name'];
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

$Period = array('Select Period','Per Hour','Per Year');
$form_state = 'New Jersey';	
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
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
			Job_Applying_For: "required",
			First_Name: "required",
			MI: "required",
			Last_Name: "required",
			SSN: "required",
			Address: "required",
			Zip: "required",
			Telephone: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			Job_Applying_For: "Required",
			First_Name: "Required",
			MI: "Required",
			Last_Name: "Required",
			SSN: "Required",
			Address: "Required",
			Zip: "Required",
			Telephone: "Required",
			Email: "Enter a valid Email",
			secode: ""
		}
	});
		$('#DATE,#Date,#Date_of_Birth').datepick({
        yearRange: "1970:2014",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top:-20px; float:right;margin-right:8px;" />'
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
					<div class="input textarea">	
						<label for="Job_Applying_For">Job Applying for? <span>*</span></label>	
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Job_Applying_For', 'text','Job_Applying_For','placeholder="Job Applying for?"'); 
						?>
					</div>		
				</div>
				<div class="field">
					<div class="input">
						<label for="First_Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name', 'text','First_Name','placeholder="Enter first name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="MI">MI. <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('MI', 'text','MI','placeholder="Enter middle initial here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Last_Name">Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','Last_Name','placeholder="Enter last name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="SSN">Social Security Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('SSN', 'text','SSN','placeholder="Enter social security number here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Date_of_Birth">Date of Birth </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date_of_Birth','text','Date_of_Birth','placeholder="Enter date of birth here" style="width:90%;"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Address">Street Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter street address here"'); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="City">City </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City','text','City','placeholder="Enter city here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="State">State </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State', 'select',$state); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Zip">Zip <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip','text','Zip','placeholder="Enter zip code here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Telephone">Telephone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Telephone','text','Telephone','placeholder="Enter telephone here"'); 
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
										
					</div>						
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Education">Education </label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Education',array('High School','Vo-Tech','College','Post Grad.')); 
						?>		
					</div>		
				</div>
				<div class="field">
					<div class="input">
						<label for="Desired_Wage">Desired Wage </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Desired_Wage','text','Desired_Wage','placeholder="Enter desired wage here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Select_Period">Select Period </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Select_Period', 'select',$Period); 
						?>						
					</div>						
				</div>
				
				<div class="field">	
					<div class="input textarea">	
						<label for="Skills">Skills (Separate with comma )</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Skills', '','Skills','placeholder="Enter your skills here" cols="88"'); 
						?>
					</div>		
				</div>
				<div class="field">
					<div class="input">
						<label for="Full_Time_Week_Days">Full Time Week Days </label>
						
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Full_Time_Week_Days',array('1st','2nd','3rd')); 
						?>	
					</div>	
					<div class="input f-right">
						<label for="Weekends">Weekends</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Weekends',array('1st','2nd','3rd')); 
						?>						
					</div>						
				</div>
				<div class="field">
					<div class="input">
						<label for="Part_Time">Part-Time </label>
						
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Part_Time',array('Days','Evenings')); 
						?>	
					</div>	
					<div class="input f-right">
						<label for="Marital_Status">Marital Status</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Marital_Status',array('Single','Married','Exemptions')); 
						?>
						If Exemptions	
                        <?php 
							// @param field name, class, id and attribute
							$input->fields('Exemptions','text','Exemptions','placeholder="Exemptions"'); 
						?>						
					</div>						
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="Authorized">Employment Eligibility Verification</label><br/>   Authorized
					    <?php 
							// @param field name, value, id and attribute
							$input->radio('Authorized',array('Yes','No')); 
						?>	
					</div>		
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="I_attest_under_penalty_that_I_am">I attest under penalty that I am</label>	
						  <?php 
							// @param field name, value, id and attribute
							$input->radio('I_attest_under_penalty_that_I_am',array('A.U.S. Citizen','Lawful Perm Resident','An Alien Authorized to work')); 
						?>	
					</div>	
				</div>		
		        <div class="field">
					<div class="input">
						<label for="Alien_Number">Alien Number </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Alien_Number', 'text','Alien_Number','placeholder="Enter alien number here"'); 
						?>						
					</div>	
					<div class="input f-right">
						
						<label for="Authorized_to_work_until">Authorized to work until </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Authorized_to_work_until','text','DATE','placeholder="Enter authorized to work here until" '); 
						?>	
					</div>						
				</div>
				<div class="field">
					<div class="input textarea">
							<div class="input12 center">
							<label for="Company">Company</label>
                                      <?php
                                              // @param field name, class, id and attribute
                                              $input->fields('Company_1', 'text','','placeholder="Enter company here"');
                                              $input->fields('Company_2', 'text','','placeholder="Enter company here"');
                                              $input->fields('Company_3', 'text','','placeholder="Enter company here"');
											$input->fields('Company_4', 'text','','placeholder="Enter company here"');
                                      ?>       
							</div>
							<div class="input12 center">
							<label for="Job_Title">Job Title</label>
                                      <?php
                                              // @param field name, class, id and attribute
                                              $input->fields('Job_Title_1', 'text','','placeholder="Enter job title here"');
                                              $input->fields('Job_Title_2', 'text','','placeholder="Enter job title here"');
                                              $input->fields('Job_Title_3', 'text','','placeholder="Enter job title here"');
											$input->fields('Job_Title_4', 'text','','placeholder="Enter job title here"');
                                      ?>     
							</div>
							<div class="input12 center">
							<label for="Start_Date" style="display:block;">Start Date</label>
                                      <?php
                                              // @param field name, class, id and attribute
                                             $input->fields('Start_Date_1','text','DATE','placeholder="Enter date"'); 
                                             $input->fields('Start_Date_2','text','DATE','placeholder="Enter date"'); 
                                             $input->fields('Start_Date_3','text','DATE','placeholder="Enter date"');
										   $input->fields('Start_Date_4','text','DATE','placeholder="Enter date"'); 
                                      ?>
							</div>
							<div class="input12 center">
							 <label for="End_Date" style="display:block;">End Date</label>
                                       <?php
                                              // @param field name, class, id and attribute
                                             $input->fields('End_Date_1','text','DATE','placeholder="Enter date"'); 
                                             $input->fields('End_Date_2','text','DATE','placeholder="Enter date"'); 
                                             $input->fields('End_Date_3','text','DATE','placeholder="Enter date"');
											 $input->fields('End_Date_4','text','DATE','placeholder="Enter date"'); 
                                      ?>
							</div>
							<div class="input12 center">
                                      <label for="Wage">Wage</label>
                                      <?php
                                              // @param field name, class, id and attribute
                                              $input->fields('Wage_1', 'text','','placeholder="Enter wage here"');
                                              $input->fields('Wage_2', 'text','','placeholder="Enter wage here"');
                                              $input->fields('Wage_3', 'text','','placeholder="Enter wage here"');
											  $input->fields('Wage_4', 'text','','placeholder="Enter wage here"');
                                      ?>
                             </div>
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