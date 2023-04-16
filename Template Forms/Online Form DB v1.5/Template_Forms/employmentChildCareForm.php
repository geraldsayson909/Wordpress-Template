<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Employment Form';
$prompt_message = '<span style="color:#ff0000;">* = Required Information</span>';

if ($_POST){
	if ($_FILES["attachment"]["error"] > 0) {
		echo "";
	}
	else {
		//echo "Upload: " . $_FILES["attachment"]["name"] . "<br />";
		//echo "Type: " . $_FILES["attachment"]["type"] . "<br />";
		//echo "Size: " . ($_FILES["attachment"]["size"] / 1024) . " Kb<br />";
		//echo "Stored in: " . $_FILES["attachment"]["tmp_name"];
		
		if (file_exists("upload/" . $_FILES["file"]["name"])) {
			echo $_FILES["file"]["name"] . " already exists. ";
		} 
		else {
			move_uploaded_file($_FILES["file"]["tmp_name"],
			"upload/" . $_FILES["file"]["name"]);
		}
	}
	
	if(empty($_POST['Full_Name']) ||
		empty($_POST['Address']) ||
		empty($_POST['City']) ||		
		empty($_POST['Zip']) ||	
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
				elseif($key == 'Verify_Email') continue;
				elseif($key == 'Verify_Password') continue;
				
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
		
		// when form has attachments, uncomment code below
		 if(!empty($_FILES['attachment']['name'])){
		 	$attachmentsdir = ABSPATH.'onlineforms/attachments/';
		 	for($i = 0 ; $i < count($_FILES['attachment']['name']) ; $i++ ){

		 		$tmpFilePath = $attachmentsdir.$_FILES['attachment']['name'][$i];
		 		$targetfolder =  $attachmentsdir.basename($_FILES['attachment']['name'][$i]);

		 		if( move_uploaded_file($_FILES['attachment']['tmp_name'][$i], $targetfolder) ){
		 			$attachments[] = $_FILES['attachment']['name'][$i];
		 		}
			
		 	}
		 }

	 	//name of sender
		$name = $_POST['Full_Name'];
		$result = insertDB($name,$subject,$body,$attachments);		

		
		$templateVars = array('{link}' => get_home_url().'/onlineforms/'.$_SESSION['token'], '{company}' => COMP_NAME);

		Mail::Send($template, 'New Message Notification', $templateVars, $to_email, $to_name, $from_email, $from_name);

		
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
			Full_Name: "required",
			Address: "required",
			City: "required",
			Zip: "required",
			Phone: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			Full_Name: "Required",
			Address: "Required",
			City: "Required",
			Zip: "Required",
			Phone: "Required",
			Attachment: "Required",
			Email: "Enter a valid Email",
			secode: ""
		}
	});
	
		 $("#other_Certificate").find(':input').attr('disabled', 'disabled');
	
		/* checkbox toggle */
	$(".certificate").change(function(){
		if($(this).val() == "Others please specify:"){
			if(this.checked){
				//$(".other_deficits").fadeIn();
				$("#other_Certificate").find(':input').attr('disabled', false);
			}
			else{
			// $(".other_deficits").fadeOut();
			$("#Education_Background_Other").val("");			
			 $("#other_Certificate").find(':input').attr('disabled', 'disabled');
			}
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
			<form id="submitform" name="contact" enctype="multipart/form-data" method="post" action="">				
				<?php echo $prompt_message; ?>
				<hr />
			<div class="field">
					<div class="input textarea">
						<label for="Full_Name">Full Name <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Full_Name', 'text','Full_Name','placeholder="Enter full name here"'); 
						?>						
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Address">Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
						?>						
					</div>
					<div class="input f-right">
						<label for="City">City <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"'); 
						?>						
					</div>
				</div>
			<div class="field">
					<div class="input">
						<label for="State">State</label>
						<?php 
							// @param field name, class, id and attribute
							$input->select('State', 'select',$state); 
						?>						
					</div>
					<div class="input f-right">
						<label for="Zip">Zip <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip', 'text','Zip','placeholder="Enter zip here"'); 
						?>						
					</div>
			</div>
			<div class="field">
					<div class="input">
						<label for="Email">Email Address</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email address here"');
						?>						
					</div>
					<div class="input f-right">
						<label for="Phone">Phone <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone', 'text','Phone','placeholder="Enter phone here"'); 
						?>						
					</div>
			</div>
			<div class="field">
					<div class="input">
						<label class="block" for="Are_you_willing_to_work_full-time_or_part-time?">Are you willing to work full-time or part-time?</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Are_you_willing_to_work_full-time_or_part-time?',array('Yes','No')); 
						?>						
					</div>
					<div class="input f-right">
						<label class="block" for="Are_you_fluent_in_speaking_English?">Are you fluent in speaking English?</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Are_you_fluent_in_speaking_English?',array('Yes','No')); 
						?>						
					</div>
			</div>
			<div class="field">	
					<div class="input textarea">	
						<label for="What_other_languages_do_you_speak/write_besides_English?">What other languages do you speak/write besides English?</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('What_other_languages_do_you_speak/write_besides_English?', '','What_other_languages_do_you_speak/write_besides_English?','placeholder="Enter other languages here" style="height:80px;" cols="88" '); 
						?>
					</div>		
			</div>
			<div class="field">
					<div class="input">
						<label class="block" for="Are_you_CPR_Certified?">Are you CPR Certified?</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Are_you_CPR_Certified?',array('Yes','No')); 
						?>						
					</div>
					<div class="input f-right">
						<label class="block" for="Are_you_trained_with_First_Aid?">Are you trained with First Aid?</label>
						<?php 
							// @param field name, value, id and attribute
							$input->radio('Are_you_trained_with_First_Aid?',array('Yes','No')); 
						?>						
					</div>
			</div>
			<div class="field">	
					<div class="input">	
						<label for="Certifications/Educational_Background">Certifications / Educational Background:</label>	
						<?php 
							// @param field name, class, id and attribute
							$input->chkbox('Certifications/Educational_Background?',array('Early Childhood Education Diploma','Early Childhood Education Degree Diploma','Early Childhood Education Assistant','Vocational/Technical Training (Child Care)','CEGEP','Others please specify:'),'','class="certificate" ',1);
						?>
						<div id="other_Certificate">
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Education_Background_Other', 'text','Education_Background_Other','placeholder="Others please specify here"'); 
						?>
						</div>
					</div>
					<div class="input f-right">	
						<label for="Previous_experience">Have you had previous experience in a Child Care or Day Care facility?<small>(Please describe in detail)</small></label>	
						<?php 
							// @param field name, class, id and attribute
							$input->textarea('Previous_experience', '','Previous_experience','placeholder="Please describe previous experience in detail here"  cols="88"'); 
						?>
					</div>						
			</div>
			<div class="field">
					<div class="input">
						<label for="How_soon_can_you_start?">How soon can you start?</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('How_soon_can_you_start?', 'text','How_soon_can_you_start?','placeholder="Enter how soon can you start here"'); 
						?>						
					</div>
				</div>
				<div class="field">	
					<div class="input">	
						<label for="attachment">Attach Resume</label>	
						<input type="file" class="file" name="attachment[]" multiple>
						<?php 
							// @param field name, class
							//$input->files('attachment', 'file'); 
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