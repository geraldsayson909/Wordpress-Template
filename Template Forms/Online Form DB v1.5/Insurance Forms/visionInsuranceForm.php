<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Vision Insurance Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Your_Name']) ||
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

	 	//name of sender
		$name = $_POST['Your_Name'];
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
$status = array('- Please Select -','Single','Married and lives with spouse','Married but separated','Divorced','Widowed');
$best_day = array('- Please Select -','Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$best_time = array('- Please Select -','Anytime','Morning','Afternoon','Evening');


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
			Your_Name: "required",
			
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			Your_Name: "Required",
			
			Email: "Enter a valid Email",
			secode: ""
		}
	});
	
	var curr_year = new Date().getFullYear();
	$('#DATE,#Date,#Date_of_Birth,#Current_Policy_Expiration_Date,#Birth_Date').datepick({
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
						<div style="background:none; color:#000; font-size:12px;text-align:center; font-weight:bold; padding:3px 10px;">Your Personal Information<input type="hidden" name="Personal Information" value=":"/></div>					
					</div>
				</div>

				<div class="field">
					<div class="input">
						
						<label for="Your_Name">Your Name: <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Your_Name','text','Your_Name','placeholder="Enter your name here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Street_Address">Street Address: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Street_Address','text','Street_Address','placeholder="Enter street address here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="City">City: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City','text','City','placeholder="Enter city here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="State">State: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Licensed_State', 'select',$state); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Zip">Zip Code: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip','text','Zip','placeholder="Enter zip code here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Email">Email: <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email','text','Email','placeholder="Enter email here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Phone">Phone: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone','text','Phone','placeholder="Enter phone here"'); 
						?>	

					</div>
					<div class="input f-right">
					
						<label for="Fax">Fax: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax','text','Fax','placeholder="Enter fax here"'); 
						?>	

					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label>Marital Status:</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Marital_Status',array('Single','Married'));
						?>	

					</div>
					<div class="input f-right">
						<label>Do You Own Your Own Business?</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Do_You_Own_Your_Own_Business?',array('Yes','No'));
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label>Vision Ins. Currently? (If yes, list carrier, and # of years continuous. If none, type N/C)</label>
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('list_carrier_and_no_of_years_continuous', '','list_carrier_and_no_of_years_continuous','placeholder="Enter a list carrier, and # of years continuous here" cols="88"'); 
						?>

					</div>
					
				</div>
				<hr/>
				<div class="field">
					<div class="input textarea">
						<div style="background:none; color:#000; font-size:12px;text-align:center; font-weight:bold; padding:3px 10px;">Underwriting Information<input type="hidden" name="Underwriting Information" value=":"/></div>					
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Insured_Name">Insured Name: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Insured_Name','text','Insured_Name','placeholder="Enter insured name here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Birthdate">Birthdate:</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Birthdate', 'text','Birth_Date','placeholder="Enter birthdate here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Insured_Height">Insured Height: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Insured_Height','text','Insured_Height','placeholder="Enter insured height here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="Insured_Weight">Insured Weight: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Insured_Weight','text','Insured_Weight','placeholder="Enter insured weight here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Insured_Occupation">Insured Occupation: </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Insured_Occupation','text','Insured_Occupation','placeholder="Enter insured occupation here"'); 
						?>	
						
					</div>
					<div class="input f-right">
						<label for="If_yes_describe">Hazardous Activities? (if yes, describe): </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('If_yes_describe','text','If_yes_describe','placeholder="Enter more information here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Sex">Sex(F/M):  </label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Sex',array('Female','Male'));
						?>	
					</div>
					<div class="input f-right">	
						<label for="List_childrens_ages_to_be_covered">List children's ages to be covered: 	</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('List_childrens_ages_to_be_covered','text','List_childrens_ages_to_be_covered','placeholder="Enter list childrens ages to be covered here"'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Any_Pre_existing_Vision_Conditions?">Any Pre-existing Vision Conditions?  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Any_Pre_existing_Vision_Conditions?','text','Any_Pre_existing_Vision_Conditions?','placeholder=""'); 
						?>	
						
					</div>
				</div>	
				<div class="field">
					<div class="input textarea">
						<label for="Any_Covered_Person_Have_Specific_Vision_Insurance_Needs?">Any Covered Person Have Specific Vision Insurance Needs?  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Any_Covered_Person_Have_Specific_Vision_Insurance_Needs?','text','Any_Covered_Person_Have_Specific_Vision_Insurance_Needs?','placeholder=""'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<div style="background:none; color:#000; font-size:12px;text-align:center; font-weight:bold; padding:3px 10px;">Coverage Information<input type="hidden" name="Coverage Information" value=":"/></div>					
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="How_Long_Do_You_Want_Policy_For?">How Long Do You Want Policy For? (i.e., monthly, quarterly, 6 month, etc.):  </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('How_Long_Do_You_Want_Policy_For?','text','How_Long_Do_You_Want_Policy_For?','placeholder=""'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="What_Deductible_or_Coverage_Do_You_Want?">What Deductible or Coverage Do You Want? ($250 ded., 80% Coverage, etc.): </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('What_Deductible_or_Coverage_Do_You_Want?','text','What_Deductible_or_Coverage_Do_You_Want?','placeholder=""'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Any_special_coverages_needed?">Any special coverages needed? (Contact Lens Cov. Lasik Cov., etc.): </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Any_special_coverages_needed?','text','Any_special_coverages_needed?','placeholder=""'); 
						?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Tell_Us_What_You_Want_MOST_in_your_Vision_Plan_or_list_any_other_Remarks_here">Tell Us What You Want MOST in your Vision Plan, or list any other Remarks here: 	 </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Tell_Us_What_You_Want_MOST_in_your_Vision_Plan_or_list_any_other_Remarks_here','text','Tell_Us_What_You_Want_MOST_in_your_Vision_Plan_or_list_any_other_Remarks_here','placeholder=""'); 
						?>	
						
					</div>
				</div>
				<hr/>
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