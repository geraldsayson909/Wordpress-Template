<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Mobile/Manufactured Home Insurance Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['First_Name']) ||
		empty($_POST['Last_Name']) ||
		empty($_POST['Fax']) ||				
		empty($_POST['Address']) ||				
		empty($_POST['City']) ||				
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
$status = array('- Please Select -','Single','Married and lives with spouse','Married but separated','Divorced','Widowed');
$best_day = array('- Please Select -','Anyday','Weekdays','Weekend','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$best_contact = array('- Please Select -','Phone','Fax','Email');
$construction_type = array('- Please Select -','Mostly wood','Mostly brick','Stucco','Others');
$roof_type = array('- Please Select -','Asphalt Shingle','Wood Shingle','Tile','Concrete');
$primary_heating = array('- Please Select -','Gas','Electric','Hot water/steam','Coal/Oil/Kerosene','Propane','Stove');
$garage_type = array('- Please Select -','Built In','Attached to Home','Not Attached to Home','Carport','No Garage');
$security_system = array('- Please Select -','None','Monitored','Unmonitored');
$fire_alarm = array('- Please Select -','None','Monitored','Unmonitored');
$property_features = array('- Please Select -','Dead Bolts','Fire Extinguisher','Trampoline','Covered Deck/Patio','Swimming Pool');
$Marital_Status = array('- Please Select -','Married','Unmarried','Divorced');
$liability = array('- Please Select -','$100,000','$300,000','$500,000','$1,000,000');
$deductible = array('- Please Select -','500','750','1000','1500','2500','5000');
$alarmsystem = array('- Please Select -','None','Just at my home','Alert Monitoring Services','Notifies Policies/Fire Dept');
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
			Fax: "required",
			Address: "required",
			City: "required",
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
			Fax: "Required",
			Address: "Required",
			City: "Required",
			secode: ""
		}
	});
    
	var curr_year = new Date().getFullYear();
	$('#Current_Policy_Expiration_Date').datepick({
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
					<div class="input">
						<label for="First_Name">First Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('First_Name','text','First_Name','placeholder="Enter first name here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Zip_Code">Zip Code</label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Zip_Code','text','Zip_Code','placeholder="Enter zip code here"'); 
							?>	
						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Last_Name">Last Name <span>*</span></label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Last_Name','text','Last_Name','placeholder="Enter last name here"'); 
							?>	
					</div>
					<div class="input f-right">
						<label for="Phone">Phone <span>*</span></label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Phone','text','Phone','placeholder="Enter phone here"'); 
							?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Address">Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address','text','Address','placeholder="Enter address here"'); 
						?>	
					</div>
					<div class="input f-right">
							<label for="Fax">Fax <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Fax','text','Fax','placeholder="Enter fax here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="City">City <span>*</span></label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('City','text','City','placeholder="Enter city here"'); 
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
						<label for="State">State</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('State', 'select',$state); 
						?>
					</div>
					<div class="input f-right">
						<label for="Best_way_to_contact">Best way to contact</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Best_way_to_contact', 'select',$best_contact); 
						?>
					</div>
				</div>
				<hr/> 
			
				<div class="field">
					<div class="input">
						<label for="Age">Age</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Age','text','Age','placeholder="Enter age here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Marital_Status">Marital Status</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Marital_Status','select', $Marital_Status); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Gender">Gender</label>
						<?php 
							// @param field name, value, id, attribute, rows, class
							$input->radio('Gender',array('Male','Female'));
						?>	
					</div>
				</div>
				<hr/>
				<div class="field">
					<div class="input textarea">
						<label for="Current_Insurance_Company">Current Insurance Company</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Insurance_Company','text','Current_Insurance_Company','placeholder="Enter current insurance company here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Current_Policy_Expiration_Date">Current Policy Expiration Date</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Current_Policy_Expiration_Date','text','Current_Policy_Expiration_Date','placeholder="Enter expiration here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Number_of_Years_Insured">Number of Years Insured</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Number_of_Years_Insured','text','Number_of_Years_Insured','placeholder="Enter number of years here"'); 
						?>	
					</div>
				</div>
				<hr/>
				<div class="field">
					<div class="input">
						<label for="Name_of_Mobile_home_park">Name of Mobile home park</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Name_of_Mobile_home_park','text','Name_of_Mobile_home_park','placeholder="Enter name of mobile home park here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Year_Home_built">Year Home built</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Year_Home_built','text','Year_Home_built','placeholder="Enter year here"'); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Name_of_Manufacturer">Name of Manufacturer</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Name_of_Manufacturer','text','Name_of_Manufacturer','placeholder="Enter name of manufacturer here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Size_of_home">Size of home</label>
						<?php 
							// @param field name, class, optname, id and attribute
								$input->fields('Size_of_home','text','Size_of_home','placeholder="Enter size here"');  
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Purchase_price">Purchase price</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Purchase_price','text','Purchase_price','placeholder="Enter price here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Loan_amount">Loan amount</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Loan_amount','text','Loan_amount','placeholder="Enter amount here"'); 
						?>	
					</div>
				</div>
				<hr/>
				
				<div class="field">
					<div class="input">
						<label for="Year_Purchased">Year Purchased</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->fields('Year_Purchased','text','Year_Purchased','placeholder="Enter year purchased here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Liability_requested ">Liability requested </label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Liability_requested', 'select',$liability); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Deductible">Deductible</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Liability_requested', 'select', $deductible); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Alarm_System">Alarm System </label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('Alarm_System', 'select', $alarmsystem); 
						?>	
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Sq._Footage_of_Residence_or_size">Sq. Footage of Residence or size</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Sq._Footage_of_Residence_or_size','text','Sq._Footage_of_Residence_or_size','placeholder="Enter square footage here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Any_losses_during_the_last_5_years?">Any losses during the last 5 years?</label>
						<?php 
							// @param field name, class, optname, id and attribute
								$input->radio('Any_losses_during_the_last_5_years?',array('Yes','No')); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Breed_of_Dog_if_any">Breed of Dog if any</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Breed_of_Dog_if_any','text','Breed_of_Dog_if_any','placeholder="Enter breed of dog if any here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Earthquake_Quote">Earthquake Quote</label>
						<?php 
							// @param field name, class, id and attribute
								$input->radio('Earthquake_Quote',array('Yes','No')); 
						?>	
					</div>
				</div>
					<div class="field">	
					<div class="input textarea">	
						<label for="Additional_Information">Additional Information <span style="font-size:11px;color:#000;font-weight:regular;">(Please include any losses for the last 5 years)</span></label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Additional_Information', '','Additional_Information','placeholder="Enter additional instruction here" cols="88"'); 
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