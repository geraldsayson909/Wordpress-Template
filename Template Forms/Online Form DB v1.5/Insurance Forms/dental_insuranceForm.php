<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Dental Form';
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
 $property = array('','house','villa/townhouse','unit','caravan','hotel/motel/hostel','mobile home','retirement village unit/villa','nursing home unit/villa','guest house/boarding house','granny flat','other');
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
						<label>Address</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','','placeholder="Enter Address here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label>City</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','','placeholder="Enter City here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>State</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State', 'text',$state); 
						?>					
					</div>
					<div class="input f-right">
						<label>Zip</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip', 'text','','placeholder="Enter Zip here"'); 
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
						<label>Weight</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Weight', 'text','','placeholder="Enter Weight here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Height</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Height', 'text','','placeholder="Enter Height here"'); 
						?>	
					</div>
				</div>					
				<div class="field">
					<div class="input textarea">
						<label>Tobacco/Nicotine Use</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Tobacco_Nicotine_Use', 'text',array('- Please Select -','Never', 'Current User', 'Within past year', 'Over 1 year ago','Over 2 years ago','Over 3years ago','Over 4 years ago','Over 5 years ago')); 
						?>					
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label>Please list any medications currently prescribed and any health history </label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Please_list_any_medications_currently_prescribed_and_any_health_history', '','','placeholder="Please list any medications currently prescribed and any health history  here" cols="88"'); 
						?>
					</div>		
				</div>
				<div class="field">
					<div class="input textarea">
						<div class="header"><input type="hidden" value=":" name="Spouse_Information" /> Spouse Information</div>
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label>First Name</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Spouse_First_Name', 'text','','placeholder="Enter First Name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Middle Initial</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Spouse_Middle_Initial', 'text','','placeholder="Enter Middle Initial here"'); 
						?>	
					</div>
				</div>

                 <div class="field">
					<div class="input">
						<label>Last Name</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Spouse_Last_Name', 'text','','placeholder="Enter Last Name here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label>Date of Birth</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Spouse_Date_of_Birth', 'text Date','','placeholder="Enter Date of Birth here"'); 
						?>
					</div>
				</div>				
				<div class="field">
					<div class="input textarea">
						<div class="header"><input type="hidden" value=":" name="Dependent_Information" /> Dependent  Information</div>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Number of children to be covered</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_children_to_be_covered', 'text','','placeholder="Enter Number of children to be covered here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label>Ages separated by comma</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Ages_separated_by_comma', 'text','','placeholder="Enter Ages separated by comma here"'); 
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