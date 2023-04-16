<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'For Employer / Job Order / Staffing Request Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Company_Name']) ||		
		empty($_POST['Your_Full_Name']) ||		
		empty($_POST['Your_Position_in_the_Company']) ||		
		empty($_POST['Company_Address']) ||		
		empty($_POST['City']) ||		
		empty($_POST['State']) ||		
		empty($_POST['Zip_Code']) ||		
		empty($_POST['Phone_Number']) ||		

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
		$name = $_POST['Your_Full_Name'];
		$result = insertDB($name,$subject,$body,$attachments);		

		$templateVars = array('{link}' => get_home_url().'/onlineforms/'.$_SESSION['token'], '{company}' => COMP_NAME);

		Mail::Send($template, 'New Message Notification', $templateVars, $to_email, $to_name, $from_email, $from_name, $cc, $bcc);

		if($result){
			$prompt_message = '<div id="success">Your message has been submitted. We will get in touch with you as soon as possible.<br/>Thank you for your time.</div>';
				unset($_POST);
		}else {
			$prompt_message = '<div id="error">Failed to send email. Please try again.</div>';
		}

	}

		

}

/*************declaration starts here************/

$form_state = 'Florida';	
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
			Company_Name: "required",
			Your_Full_Name: "required",
			Your_Position_in_the_Company: "required",
			Company_Address: "required",
			City: "required",
			State: "required",
			Zip_Code: "required",
			Target_Start_Date: "required",
			Phone_Number: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"

		
		},
		messages: {
			Company_Name: "Required",
			Your_Full_Name: "Required",
			Your_Position_in_the_Company: "Required",
			Company_Address: "Required",
			City: "Required",
			State: "Required",
			Zip_Code: "Required",
			Phone_Number: "Required",
			Target_Start_Date: "Required",
			Email: "Enter valid email",
			secode: ""
		}
	});
	     var curr_year = new Date().getFullYear();
		 
		$('#DATE,#Date,#Date_of_Birth').datepick({
        yearRange: "1900:"+curr_year+"",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top: -30px;float: right; margin-right: 10px;" />'
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

						<label for="Company_Name">Company Name <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Company_Name', 'text','Company_Name','placeholder="Enter company name here"'); 

						?>						

					</div>						

				</div>

				<div class="field">

					<div class="input textarea">

						<label for="Your_Full_Name">Your Full Name  <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Your_Full_Name', 'text','Your_Full_Name','placeholder="Enter your full name here"'); 

						?>						

					</div>						

				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Your_Full_Name">Your Position in the Company <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Your_Position_in_the_Company', 'text','Your_Position_in_the_Company','placeholder="Enter your position in the company here"'); 

						?>						

					</div>						

				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Company_Address">Company Address <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Company_Address', 'text','Company_Address','placeholder="Enter your company address here"'); 

						?>						

					</div>						

				</div>
			<div class="field">

					<div class="input textarea">

						<label for="City">City <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('City', 'text','City','placeholder="Enter your city here"'); 

						?>						

					</div>						

				</div>
				<div class="field">

					<div class="input textarea">
						<label for="State">State <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
								$input->select('State', 'select',$state); 
						?>	
					</div>	
				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Zip_Code">Zip Code <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Zip_Code', 'text','Zip_Code','placeholder="Enter zip code here"'); 

						?>						

					</div>						

				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Zip_Code">Phone Number<span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Phone_Number', 'text','Phone_Number','placeholder="Enter your phone number here"'); 

						?>						

					</div>						

				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Email">Email Address<span>*</span></label>

						<?php 

							// @param field name, class, id and attribute

							$input->fields('Email', 'text','Email','placeholder="Enter your email address here"'); 

						?>						

					</div>						

				</div>
				<div class="field">	
					<div class="input textarea">	
						<label for="What_position(s)_in your_company_would_you_like_to_fill', '','What_position(s)_in your_company_would_you_like_to_fill">What position(s) in your company would you like to fill? Please also provide the qualifications and/or the number of staff you need </label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('What_position(s)_in your_company_would_you_like_to_fill', '','What_position(s)_in your_company_would_you_like_to_fill','placeholder="Enter position(s) in  your company you like to fill here" cols="88"'); 
						?>
					</div>		
				</div>
				<div class="field">	
					<div class="input textarea">
					<label for="Time_Frame_of_Employment">Time Frame of Employment</label>	
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Time_Frame_of_Employment',array('Per Diem','Temporary','Permanent')); 
						?>	
					</div>
				</div>
				<div class="field">

					<div class="input textarea">

						<label for="Target_Start_Date">Target Start Date <span>*</span></label>

						<?php 

							// @param field name, class, id and attribute
							$input->fields('Target_Start_Date', 'text','DATE','placeholder="Enter your target start date here"'); 

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