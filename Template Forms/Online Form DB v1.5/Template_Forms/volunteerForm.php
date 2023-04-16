<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Volunteer Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if(empty($_POST['Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['Telephone']) ||		
		empty($_POST['How_would_you_like_to_receive_information_from_us']) ||	
		empty($_POST['If_you_have_previous_volunteer_experience_please_describe_it_here']) ||
		empty($_POST['Please_list_any_of_your_special_skills_and_other_languages_spoken']) ||
		empty($_POST['How_did_you_first_hear_about_our_program']) ||		
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
		$name = $_POST['Name'];
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
$areasNeed = array('- Please Select -','Fundraising','PR/Marketing','Office Assistance','Volunteer Recruitment','Special Events');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $formname; ?></title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {	
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Name: "required",
			Address: "required",
			Telephone: "required",
			How_would_you_like_to_receive_information_from_us: "required",
			If_you_have_previous_volunteer_experience_please_describe_it_here: "required",
			Please_list_any_of_your_special_skills_and_other_languages_spoken: "required",
			How_did_you_first_hear_about_our_program: "required",
			Areas_of_need: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			Name: "Required",
			Address: "Required",
			Telephone: "Required",
			Email: "Enter a valid Email",
			secode: "Required",
			How_would_you_like_to_receive_information_from_us: "Required",
			If_you_have_previous_volunteer_experience_please_describe_it_here: "Required",
			Please_list_any_of_your_special_skills_and_other_languages_spoken: "Required",
			Areas_of_need: "Required",
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
						<label for="How_did_you_first_hear_about_our_program">How did you first hear about our program? <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('How_did_you_first_hear_about_our_program', 'text','How_did_you_first_hear_about_our_program','placeholder="Enter detail here"'); 
						?>						
					</div>		
				</div>	
				<div class="field">				
					<div class="input textarea">
						<label for="Please_list_any_of_your_special_skills_and_other_languages_spoken">Please list any of your special skills and other languages spoken <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Please_list_any_of_your_special_skills_and_other_languages_spoken', '','Please_list_any_of_your_special_skills_and_other_languages_spoken','placeholder="Enter special skills and other languages spoken here" style="height:85px;" cols="88"'); 
						?>	
					</div>	
				</div>
				<div class="field">				
					<div class="input textarea">
						<label for="If_you_have_previous_volunteer_experience_please_describe_it_here">If you have previous volunteer experience please describe it here <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('If_you_have_previous_volunteer_experience_please_describe_it_here', '','If_you_have_previous_volunteer_experience_please_describe_it_here','placeholder="Enter previous volunteer experience and description here" style="height:85px;" cols="88"'); 
						?>	
					</div>	
				</div>
				<div class="field">
					<div class="input textarea">
						<label for="How_would_you_like_to_receive_information_from_us">How would you like to receive information from us? <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('How_would_you_like_to_receive_information_from_us', 'text','How_would_you_like_to_receive_information_from_us','placeholder="Enter detail here"'); 
						?>						
					</div>		
				</div>
				<div class="field">
					<div class="input textarea">
						<div class="field" style="text-align:center;">
							<span style="color:#000; font-weight:bold;">Contact Information</span><input type="hidden" name="Contact Information" value=":" />	
						</div>
						<div class="field">
							<div class="input">
								<label for="Name">Name <span>*</span></label>
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Name', 'text','Name','placeholder="Enter name here"'); 
								?>						
							</div>	
							<div class="input f-right">
								<label for="Address">Address <span>*</span></label>
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
								?>						
							</div>	
						</div>
						<div class="field">
							<div class="input">
								<label for="Telephone">Telephone <span>*</span></label>
								<?php 
									// @param field name, class, id and attribute
									$input->fields('Telephone', 'text','Telephone','placeholder="Enter telephone here"'); 
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
							<div class="input textarea">
								<label style="float:left;" for="Areas_of_need">If you are interested in helping in any of the following areas of need, please select one <span>*</span></label>	
								<?php 
									// @param field name, class, optname, id and attribute
									$input->select('Areas_of_need', 'select',$areasNeed); 
								?>
							</div>
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