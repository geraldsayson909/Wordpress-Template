<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Hotel Reservation Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Check_In']) ||
		empty($_POST['Check_Out']) ||
		empty($_POST['Rooms']) ||				
		empty($_POST['Guests_per_room']) ||	
		empty($_POST['First_Name']) ||	
		empty($_POST['Last_Name']) ||	
		empty($_POST['Email']) ||	
		empty($_POST['Telephone_Number']) ||	
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
			Check_In: "required",
			Check_Out: "required",
			Rooms: "required",
			Guests_per_room: "required",
			First_Name: "required",
			Last_Name: "required",
			Email: {
				required: true,
				email: true
			},
			Telephone_Number: "required",
			secode: "required"		
		},
		messages: {
			Check_In: "Required",
			Check_Out: "Required",
			Rooms: "Required",
			Guests_per_room: "Required",
			First_Name: "Required",
			Last_Name: "Required",
			Email: "Enter a valid Email",
			Telephone_Number: "Required",
			secode: ""
		}
	});
	
	var curr_year = new Date().getFullYear();
    $('#DATE,#Date,#Check_In,#Check_Out').datepick({
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
	$('input[name="Special_Rates"]').click(function(){
		if ($(this).val() == "Corporate/Promotional Code"){
			$("#codesPop").show();
			$("#promoCode").show();
			$("#groupCode").hide();
			$("#promoCode").find(':input').attr('disabled', false);
			$("#groupCode").find(':input').attr('disabled', true);
		}
		else if ($(this).val() == "Group Code"){
			$("#codesPop").show();
			$("#groupCode").show();
			$("#promoCode").hide();
			$("#groupCode").find(':input').attr('disabled', false);
			$("#promoCode").find(':input').attr('disabled', true);
		}
		else {
			$("#codesPop").hide();
			$("#promoCode").hide();
			$("#groupCode").hide();
			$("#promoCode").find(':input').attr('disabled', true);
			$("#groupCode").find(':input').attr('disabled', true);
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
						<div style="font-weight:bold;">Select Your Dates<input type="hidden" name="Dates Selected" value=":"/></div>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Check_In">Check In <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Check_In', 'text','Check_In','placeholder="Enter date here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Check_Out">Check Out <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Check_Out', 'text','Check_Out','placeholder="Enter date here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<div style="font-weight:bold;">Guest Information<input type="hidden" name="Guest Information" value=":"/></div>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Rooms">Rooms <span>*</span></label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Rooms', 'select',array('- Please Select -','1','2','3','4','5')); 
						?>					
					</div>
					<div class="input f-right">
						<label for="Guests_per_room">Guests per room <span>*</span></label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('Guests_per_room', 'select',array('- Please Select -','1','2','3','4','5','6')); 
						?>					
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Special_Rates"><div style="font-weight:bold;">Special Rates</div> </label>
						<?php 
							// @param field name, value, id, attribute and rows
							$input->radio('Special_Rates',array('None','Senior/Discount','Military','Corporate/Promotional Code','Group Code'),'Special_Rates','',1);
						?>					
					</div>
					<div class="input f-right" id="codesPop" style="display:none;">
						<div id="promoCode" style="display:none;">
							<label for="Corporate_or_Promotional_Code">Corporate/Promotional Code</label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Corporate_or_Promotional_Code', 'text','Corporate_or_Promotional_Code','placeholder="Enter code here"'); 
							?>	
						</div>
						<div id="groupCode" style="display:none;">
							<label for="Group_Code">Group Code</label>
							<?php 
								// @param field name, class, id and attribute
								$input->fields('Group_Code', 'text','Group_Code','placeholder="Enter code here"'); 
							?>	
						</div>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<div style="font-weight:bold;">Personal Details<input type="hidden" name="Personal Details" value=":"/></div>
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
						<label for="Last_Name">Last Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Last_Name', 'text','Last_Name','placeholder="Enter last name here"'); 
						?>					
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<div style="font-weight:bold;">Contact Details<input type="hidden" name="Contact Details" value=":"/></div>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<div>Let us know where we can send your booking confirmation. We will not use your details for marketing promotions.</div>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Email">Email <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Telephone_Number">Telephone Number <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Telephone_Number', 'text','Telephone_Number','placeholder="Enter telephone number here"'); 
						?>					
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="Mobile">Mobile </label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Mobile', 'text','Mobile','placeholder="Enter mobile here"'); 
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
						<button type='submit' class="button medButton">Book Now!</button>						
					</div>	
				</div>
			</form>	
			<div class="clearfix"></div>			
		</div>
	</div>
</body>	
</html>