<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Reservation Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Passengers_Name']) ||
		empty($_POST['Total_Number_of_Passengers']) ||
		empty($_POST['Contact_Number']) ||				
		empty($_POST['Email']) ||	
		empty($_POST['Date']) ||	
		empty($_POST['Time_to_Pick_Up']) ||
		empty($_POST['Name_of_Location']) ||	
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
		
		
		$name = $_POST['Passengers_Name'];
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
$transpo = array('Point to Point','Hourly Transportation','Pickup from Airport','Drop Off at Airport');
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
			Passengers_Name: "required",
			Total_Number_of_Passengers: "required",
			Contact_Number: "required",
			Email: {
				required: true,
				email: true
			},
			Departure_Date: "required",
			Arrival_Date: "required",
			Date: "required",
			Time_to_Pick_Up: "required",
			secode: "required"		
		},
		messages: {
			Passengers_Name: "Required",
			Total_Number_of_Passengers: "Required",
			Contact_Number: "Required",
			Email: "Required",
			Departure_Date: "required",
			Arrival_Date: "required",
			Date: "Required",
			Time_to_Pick_Up: "Required",
			secode: ""
		}
	});
	 var curr_year = new Date().getFullYear();
	$('#DATE,#Date,#Date_of_Birth').datepick({
         yearRange: "1900:"+curr_year+"",
        showTrigger: '<img src="images/calender.png" alt="Select date" style="margin-top: 8px; float: right; position: absolute; right: 12px; top: 36px;" />'
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
						<label for="Passengers_Name">Please Provide Main Passenger's Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Passengers_Name', 'text','Passengers_Name','placeholder="Enter passenger\'s name here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Total_Number_of_Passengers">Total Number of Passengers <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Total_Number_of_Passengers', 'text','Total_Number_of_Passengers','placeholder="Enter total number of passengers here"'); 
						?>						
					</div>
				</div>
				
				<div class="field">
					<div class="input">
						<label for="Contact_Number">Contact Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Contact_Number', 'text','Contact_Number','placeholder="Enter contact number here"'); 
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
					<div class="input">
						<label for="Departure_Date">Departure Date <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Departure_Date','text','Date','placeholder="Enter departure date here"'); 
						?>						
					</div>	
					<div class="input f-right">
						<label for="Arrival_Date">Arrival Date <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Arrival_Date','text','Date','placeholder="Enter arrival date here"'); 
						?>						
					</div>
				</div>
				
				
				<div class="field">
					<div class="input">
						<label for="Type_of_Transportation">Type of Transportation Service</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Type_of_Transportation', 'select',$transpo); 
						?>						
					</div>
				</div>
				
				<div class="field">
					<div class="input textarea">
						<div style="background:#0074CA; color:#ffffff; font-size:14px; font-weight:bold; padding:3px 10px;">When will you need the transportation services?<input type="hidden" name="When will you need the transportation services?" value=":"/></div>					
					</div>
				</div>
				
				<div class="field">				
					<div class="input">
						<label for="Date">Date <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Date','text','Date','placeholder="Enter date here"'); 
						?>	
					</div>
					<div class="input f-right">
						<label for="Time_to_Pick_Up">Time to Pick-Up <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Time_to_Pick_Up', 'text','Time_to_Pick_Up','placeholder="Enter time to pick-up here"'); 
						?>
					</div>
				</div>
				
				<div class="field">	
					<div class="input textarea">	
						<label for="Name_of_Location">Enter full Pick-Up Address or Name of Location</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Name_of_Location', '','Name_of_Location','placeholder="Enter name of location here" cols="88"'); 
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