<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Landlords/Rental Form';
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
						<div class="header"><input type="hidden" value=":" name="Customer_nformation" /> Customer Information</div>
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
					<div class="input textarea">
						<div class="header"><input type="hidden" value=":" name="Property_Home_Details" /> Property/Home Details</div>
					</div>
				</div>	
				<div class="field">
					<div class="input">
						<label>Approximate Year Built</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Approximate_Year_Built', 'text','','placeholder="Enter Approximate Year Built here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label>Approximate square footage</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Approximate_square_footage', 'text','','placeholder="Enter Approximate square footage here"'); 
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
						<label>Property Type</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Property_Type', 'text',$property); 
						?>					
					</div>
					<div class="input f-right">
						<label>Exterior Walls</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Exterior_Walls', 'text',array('- Please Select -','double brick','brick veneer','timber','weather board','steel','concrete','fibro','stone/sandstone','polystyrene',' Asbestos cement','Mud brick','other construction')); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Roof</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Roof', 'text',array('- Please Select -','concrete tiles','fibro','metal/iron','slate','tile','copper','tin','shingle','thatched','other')); 
						?>					
					</div>
					<div class="input f-right">
						<label>Number of Stories</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Stories', 'text','','placeholder="Enter Number of Stories here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Number of Bedrooms</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Bedrooms', 'text','','placeholder="Enter Number of Bedrooms here"'); 
						?>					
					</div>
					<div class="input f-right">
						<label>Number of Bathrooms</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Bathrooms', 'text','','placeholder="Enter Number of Bathrooms here"'); 
						?>	
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label>Security system</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Security_system', 'text',array('- Please Select -','none','monitored','unmonitored')); 
						?>					
					</div>
					<div class="input f-right">
						<label>Fire alarm</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Fire_alarm', 'text',array('- Please Select -','none','monitored','unmonitored')); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Is your building/property managed by a Licensed Property Management agent?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Is_your_building_property_managed_by_a_Licensed_Property_Management_agent', array('Yes','No')); 
						?>					
					</div>
					<div class="input f-right">
						<label>Is your building/property part of a Strata title place?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('Is_your_building_property_part_of_a_Strata_title_place', array('Yes','No')); 
						?>		
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>What quantity of contents do you have?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('What_quantity_of_contents_do_you_have', array('Fixtures and fittings only ','Furniture & major appliances in addition to fixtures and fittings','Fully furnished including bedding and kitchenware as well as fixtures and fittings'),'','',1); 
						?>					
					</div>
					<div class="input f-right">
						<label>What standard of contents do you have?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->radio('What_standard_of_contents_do_you_have', array('Average - no name brands, basic equipment, self-assembled furniture, etc.','quality - well-known brands, superior equipment, standard furnitures, etc. ','prestige - designer brands, handcrafted furniture, etc. '),'','',1); 
						?>					
					</div>					
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label>List any additional features in your property (ex. Grage, swimming pool, etc.)</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('List_any_additional_features_in_your_property', '','','placeholder="List any additional features in your property here" cols="88"'); 
						?>
					</div>		
				</div>
				<div class="field">
					<div class="input textarea">
						<div class="header"><input type="hidden" value=":" name="Coverage_Requested_Desired" /> Coverage Requested/Desired</div>
					</div>
				</div>	
				
				<div class="field">
					<div class="input">
						<label>Liability Protection</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Liability_Protection', 'text',array('- Please Select -','$100,000','$200,000','$300,000','$400,000','$500,000','$600,000','$700,000','$800,000','$900,000','$1,000,000')); 
						?>					
					</div>
					<div class="input f-right">
						<label>Deductible</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Deductible', 'text',array('- Please Select -','$1,000-$5,000','$5,000-$10,000','$10,000-$15,000','15,000-$20,000','20,000-$25,000','25,000-$30,000','30,000-$35,000','35,000-$40,000','40,000-$45,000','45,000-$50,000')); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label>Personal Property</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Personal_Property', 'text',array('- Please Select -','$1,000-$5,000','$5,000-$10,000','$10,000-$15,000','15,000-$20,000','20,000-$25,000','25,000-$30,000','30,000-$35,000','35,000-$40,000','40,000-$45,000','45,000-$50,000')); 
						?>					
					</div>
					<div class="input f-right">
						<label>Loss of Use</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Loss_of_Use', 'text',array('- Please Select -','$10,000','$20,000','$30,000','$40,000','$50,000','$60,000','$70,000','$80,000','$90,000','$100,000')); 
						?>	
					</div>
				</div>
				<div class="field">	
					<div class="input textarea">	
						<label>Additional Coverages/Comments</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Comments', '','','placeholder="Additional Coverages/Comments here" cols="88"'); 
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