<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Job Application Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php';
if ($_POST){

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "secret={$recaptcha_privite}&response={$_POST['g-recaptcha-response']}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	$result = json_decode($server_output);
	curl_close ($ch);
	
	if( empty($_POST['Last_Name']) ||
		empty($_POST['First_Name']) ||
		empty($_POST['Street_Address']) ||
		empty($_POST['City']) ||
		empty($_POST['_Phone']) ||
		empty($_POST['Email'])
	) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	/* else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';} */
	else if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	} 
	else{

		$body = '<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px; margin: 0 auto;">
			<div style="border:8px double #c3c3d0; padding:12px;">
			<div align="center" style="color:#990000; font-style:italic; font-size:20px; font-family:Arial; margin:bottom: 15px;">('.$formname.')</div>

			<table width="90%" cellspacing="2" cellpadding="5" align="center" style="font-family:Verdana; font-size:13px">
				';

			foreach($_POST as $key => $value){
				if($key == 'submit') continue;
				elseif($key == 'Verify_Email') continue;
				elseif($key == 'Verify_Password') continue;
				elseif($key == 'g-recaptcha-response') continue;

				if(!empty($value)){
					$key2 = str_replace('_', ' ', $key);
					if($value == ':') {
						$body .= '<tr><td colspan="2" style="background:#F0F0F0; line-height:30px"><b>'.$key2.'</b></td></tr>';
					}else if($key == "Docu01"){
						$body .= '<tr><td colspan="2" line-height:30px">Commonwealth Supportive Services (CSS) is an equal opportunity employer. CSS does not discriminate in employment on accuont of race, color, religion, national origin, citizenship status, ancestry, age, gender, sexual orientation, marital status or any other protected class. <br></br> <input type="checkbox" name="check[]" value="" checked disabled /> I understand that neither the completion of this application nor any other part of my consideration for employment establishes nay obligation for CSS to hire me. If I am hired, I understand that either CSS or I can terminate my employment at any time and for any reason, with or without prior notice. I understand that no representative of CSS has the authority to make any assurance to the contrary.</td></tr>';
					}else if($key == "Docu02"){
						$body .= '<tr><td colspan="2" line-height:30px"><input type="checkbox" name="check[]" value="" checked disabled /> been concealed. I authorize CSS to contact references provided for employment reference check. I also authorize CSS to conduct background checks upon offer of employment. If any information I have provided is untrue, I understand that this will constitute cause for denial of employment or immediate dismissal.</td></tr>';
					}else {
						$body .= '<tr><td><b>'.$key2.'</b>:</td> <td>'.htmlspecialchars(trim($value), ENT_QUOTES).'</td></tr>';
					}
				}
			}
			$body .= '
			</table>

			</div>
			</div>';

			echo $body; exit;

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
			$validextensions = array('pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'); // include file type here
			for($i = 0 ; $i < count($_FILES['attachment']['name']) ; $i++ ){

				$checkfile =  $attachmentsdir.$_FILES['attachment']['name'][$i];
				//$tobeuploadfile = $_FILES['attachment']['tmp_name'][$i];
				$tempfile = pathinfo($_FILES['attachment']['name'][$i]);
				if(in_array(strtolower($tempfile['extension']), $validextensions)){
					if(file_exists($checkfile)){
						$storedfile = $tempfile['filename'].'-'.time().'.'.$tempfile['extension'];
					}else{
						$storedfile = $_FILES['attachment']['name'][$i];
					}

					if( move_uploaded_file($_FILES['attachment']['tmp_name'][$i], $attachmentsdir.$storedfile) ){
						$attachments[] = $storedfile;
					}
				}
			}
		}

	 	//name of sender
		$name = $_POST['First_Name'].' '.$_POST['Last_Name'];
		$result = insertDB($name,$subject,$body,$attachments);

		$templateVars = array('{link}' => get_home_url().'/onlineforms/'.$_SESSION['token'], '{company}' => COMP_NAME);

		Mail::Send($template, 'New Message Notification', $templateVars, $to_email, $to_name, $from_email, $from_name, $cc, $bcc);

		if($result){
			$prompt_message = '<div id="success"><div class="message"><span>THANK YOU</span><br/> <span>for sending us a message!</span><br/><span>We will be in touch with you soon.</span><p class="close">x</p></div></div>';
				unset($_POST);
		}else {
			$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
		}

	}

}
/*************declaration starts here************/
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
$best_time_to_call = array('- Please select -','Anytime','Morning at Home','Morning at Word','Afternoon at Home','Afternoon at Work','Evening at Home','Evening at Work');
$status = array('- Please select -','Active','Inactive','Restricted','Conditional','Pending');
$position = array('- Please select -','Home Health Aide','Certified Nursing Assistant');
$work = array('- Please select -','A.M.','P.M.','Overnight','Weekend','Open');
?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>

		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
		<link rel="stylesheet" href="style.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/datepicker.css">
		<link rel="stylesheet" href="css/jquery.datepick.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

		<link rel="stylesheet" href="css/proweaverPhone.css" type="text/css"/>
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>


		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>
		@import url('https://fonts.googleapis.com/css?family=Lato:400,400i|Open+Sans:400,400i');


		body { font-family: Arial,Helvetica,sans-serif; }
			::placeholder { font-family: Arial,Helvetica,sans-serif;}
			::-moz-placeholder { font-family: Arial,Helvetica,sans-serif; }
			::-webkit-input-placeholder { font-family: Arial,Helvetica,sans-serif; }
			:-ms-input-placeholder { font-family: Arial,Helvetica,sans-serif;}
			select.form_field { font-family: Arial,Helvetica,sans-serif;}

			/* #author p, li, ul { font-size: 17px; } */

			.fieldbox { margin: 10px 0; }
			.fieldheader p { margin: 0; background-color: #f27024; padding: 8px; color: #fff; text-align: center; font-weight: 700; text-transform:uppercase;}
			.fieldcontent { padding: 10px; border: 1px solid #f27024; border-top: 0; }
		
		.forbold .form_label label {      text-align: center;     margin: 10px auto;     display: block;     background: #f1f1f1;     border: 1px solid #d8d8d8;     padding: 4px;  }
		.forfont p {font-weight: bold;margin: 10px auto;}
		
			table, thead,tr,th{font-weight: bold;}
				#daysOfWeek { width: 100%; border: 1px solid; padding: 5px; border-collapse: collapse; border-spacing: 0; margin: 10px 0; }
				#daysOfWeek thead { padding: 0; margin: 0; text-align: center; border: 1px solid; }
				#daysOfWeek td { overflow: hidden; position: relative; }
				#daysOfWeek tbody td { border: 1px solid; }
				#daysOfWeek input { width: 100%; border: 0 !important; padding: 10px 5px; }
				#daysOfWeek td input + .datepick-trigger { position: absolute; z-index: 1; right: 5px; top: 90%; }
				.datepick-trigger { margin-top: -20px !important; }
				p.error { color: #f00; font-weight: 700; }
				.tile { text-align: center; font-weight: 700; }
				.days th { padding: 6px; border: 1px solid;}

				.firstth th {padding: 10px 0; }

				.pad{position: relative;cursor: url("../images/pen.cur"), crosshair;cursor: url("../images/pen.cur") 16 16, crosshair;-ms-touch-action: none;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;border: 1px solid #CECECE;height: 65px;}
				.clearButton {top: 0;position: absolute;left: 0;font-size: 0.75em;line-height: 1.375;}
				.sig{}
				.sig small {color: #f00;font-size: 20px;font-weight:bold;}

				input[type=checkbox] {transform: scale(1.5);-webkit-appearance: checkbox;-moz-appearance:checkbox;appearance:checkbox;}
				
				#daysOfWeek tbody tr td .forfont {padding: 0 10px; }
				
				
				@media only screen and (max-width: 850px) {
				#daysOfWeek, #daysOfWeek thead, tr, th, tbody, td { border: 0; }
				#daysOfWeek thead { display: none; }
				#daysOfWeek td { display: block; }
				#daysOfWeek td:first-child { font-size: 16px; text-align:center;font-weight: 700; border-bottom: 1px solid; }
				#daysOfWeek td:not(:first-child):before { content: attr(data-label); border-bottom: 2px solid; }
				#daysOfWeek tr { margin-bottom: 15px; display: inline-block; width: 100%; border: 1px solid; }
				@media only screen and (max-width: 885px) {
				.check table tbody tr td {display: block;}
			}
				}
		</style>
	</head>
<body>
	<div class="clearfix">
		<div class = "wrapper">
			<div id = "contact_us_form_1" class = "template_form">
				<div class = "form_frame_b">
					<div class = "form_content">
						<?php if($testform):?><div class="test-mode"><i class="fas fa-info-circle"></i><span>You are in test mode!</span></div><?php endif;?>

						<form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">
								<?php echo $prompt_message; ?>

								<div class="fieldbox">
									<div class="fieldheader">
										<p>Applicant Information</p>
										<input type="hidden" name="Applicant Information" value=":">
									</div>

										<div class="fieldcontent">
										
										<div class="form_box right">
											<div class="form_box_col1">
												<div class="group">
													<?php
														$input->label('Date', '');
														// @param field name, class, id and attribute
														$input->fields('Date', 'form_field Date','Date','placeholder="Enter date here"');
													?>
												</div>
											</div>
										</div>
										
										<div class="clearfix"></div>
										
										<?php $input->label('full Name', ''); ?>
																				
										<div class="form_box">
											<div class="form_box_col3">

												<div class="group">
													<?php
														$input->label('Last', '*');
														// @param field name, class, id and attribute
														$input->fields('Last_Name', 'form_field','Last_Name','placeholder="Enter last here"');
													?>
												</div>
												<div class="group">
													<?php
														$input->label('First', '*');
														// @param field name, class, id and attribute
														$input->fields('First_Name', 'form_field','First_Name','placeholder="Enter first here"');
													?>
												</div>
												<div class="group">
													<?php
														$input->label('Middle Initial', '');
														// @param field name, class, id and attribute
														$input->fields('Middle_Initial', 'form_field','Middle_Initial','placeholder="Enter middle initial here"');
													?>
												</div>
											</div>
										</div>
										
										<?php $input->label('Address', ''); ?>
										
										<div class="form_box">
											<div class="form_box_col2">
												<div class="group">
													<?php
														$input->label('Street Address', '*');
														// @param field name, class, id and attribute
														$input->fields('Street_Address', 'form_field','Street_Address','placeholder="Enter street address here"');
													?>
												</div>
												<div class="group">
													<?php
														$input->label('Apartment/Unit Number', '');
														// @param field name, class, id and attribute
														$input->fields('Apartment_or_Unit_Number', 'form_field','Apartment_or_Unit_Number','placeholder="Enter apartment/unit number here"');
													?>
												</div>
											</div>
										</div>
										
										<div class="form_box">
											<div class="form_box_col3">
												<div class="group">
													<?php
														$input->label('City', '*');
														// @param field name, class, id and attribute
														$input->fields('City', 'form_field','City','placeholder="Enter city here"');
													?>
												</div>
												<div class="group">
													<?php
														$input->label('State', '');
														// @param field name, class, id and attribute
														$input->select('State', 'form_field', $state);
													?>
												</div>
												<div class="group">
													<?php
														$input->label('Zip Code', '');
														// @param field name, class, id and attribute
														$input->fields('Zip_Code', 'form_field','Zip_Code','placeholder="Enter zip code here"');
													?>
												</div>
											</div>
										</div>
										
										<div class="form_box">
											<div class="form_box_col2">
												<div class="group">
													<?php
														$input->label('Phone', '*');
														// @param field name, class, id and attribute
														$input->phoneInput('_Phone', 'form_field','_Phone','placeholder="Enter phone here"');
													?>
												</div>
												<div class="group">
													<?php
														$input->label('Email', '*');
														// @param field name, class, id and attribute
														$input->fields('Email', 'form_field','Email','placeholder="Enter email here"');
													?>
												</div>
											</div>
										</div>
										
										<div class="form_box">
											<div class="form_box_col2">
												<div class="group">
													<?php
														$input->label('Date Available', '');
														// @param field name, class, id and attribute
														$input->fields('Date_Available', 'form_field Date','Date_Available','placeholder="Enter date here"');
													?>
												</div>
												<div class="group">
													<?php
														$input->label('Last 4 digits of Social Security Number', '');
														// @param field name, class, id and attribute
														$input->fields('Social_Security_Number', 'form_field','Social_Security_Number','placeholder="Enter social security number here"');
													?>
												</div>
											</div>
										</div>
										
										<div class="form_box">
											<div class="form_box_col2">
												<div class="group">
													<?php
														$input->label('Position Applied for', '');
														// @param field name, class, id and attribute
														$input->fields('Position_Applied_for', 'form_field','Position_Applied_for','placeholder="Enter position here"');
													?>
												</div>
												<div class="group">
													<?php
														$input->label('Desired Salary', '');
														// @param field name, class, id and attribute
														$input->fields('Desired_Salary', 'form_field','Desired_Salary','placeholder="Enter salary here"');
													?>
												</div>
											</div>
										</div>
										
										<div class="form_box">
											<div class="form_box_col2">
												<div class="group">
													<?php
														$input->label('Are you authorized to work in the U.S.?', '');
														// @param field name, class, id and attribute
														$input->radio('Authorized_to_work_in_the_US', array('Yes','No'));
													?>
												</div>	
												<div class="group">
													<?php
														$input->label('Are you at least 18 years or older?', '');
														// @param field name, class, id and attribute
														$input->radio('At_least_18_years_or_older', array('Yes','No'));
													?>
												</div>
											</div>
										</div>
										
										<div class="form_box">
											<div class="form_box_col2">
												<div class="group">
													<?php
														$input->label('Do you know anyone who works at CSS?', '');
														// @param field name, class, id and attribute
														$input->radio('Have_knowledge_who_works_at_css', array('Yes','No'));
													?>
												</div>	
												<div class="group" id="Explanation01">
													<?php
														$input->label('Who?', '');
														// @param field name, class, id and attribute
														$input->fields('Who', 'form_field','Who','placeholder="Enter here"');
													?>
												</div>
											</div>
										</div>
										
										<div class="form_box">
											<div class="form_box_col1">
												<div class="group">
													<?php
														$input->label('Have you been convicted of a crime that would prevent you from working with persons with disabilities?', '');
														// @param field name, class, id and attribute
														$input->radio('Have_been_convicted_of_a_crime_that_would_prevent_you_from_working_with_persons_with_disabilities', array('Yes','No'));
													?>
												</div>
											</div>
										</div>
										
										<div class="form_box">
											<div class="form_box_col1">
												<div class="group">
													<?php
														$input->label('Have you ever been terminated from employment or asked to resign?', '');
														// @param field name, class, id and attribute
														$input->radio('Ever_been_terminated_from_employment_or_asked_to_resign', array('Yes','No'));
													?>
												</div>
											</div>
										</div>
										
										<div class="form_box">
											<div class="form_box_col1">
												<div class="group" id="Explanation02">
													<?php
														$input->label('Explain', '');
														// @param field name, class, id and attribute
														$input->textarea('Explain', 'form_field','Explain','placeholder="Enter explanation here"');
													?>
												</div>
											</div>
										</div>
										
										</div>
										</div>
										
										
										<div class="fieldbox">
											<div class="fieldheader">
												<p>Education</p>
												<input type="hidden" name="Education" value=":">
											</div>

											<div class="fieldcontent">
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('High School', '');
															// @param field name, class, id and attribute
															$input->fields('High_School', 'form_field','High_School','placeholder="Enter high school here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Address', '');
															// @param field name, class, id and attribute
															$input->fields('Address', 'form_field','Address','placeholder="Enter address here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('From', '');
															// @param field name, class, id and attribute
															$input->fields('From', 'form_field Date','From','placeholder="Enter date here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('To', '');
															// @param field name, class, id and attribute
															$input->fields('To', 'form_field Date','To','placeholder="Enter date here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Did you graduate?', '');
															// @param field name, class, id and attribute
															$input->radio('Graduated_in_high_school', array('Yes','No'));
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Diploma', '');
															// @param field name, class, id and attribute
															$input->fields('Diploma', 'form_field','Diploma','placeholder="Enter diploma here"');
														?>
													</div>
												</div>
											</div>
											
											<hr>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('College', '');
															// @param field name, class, id and attribute
															$input->fields('College', 'form_field','College','placeholder="Enter college here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Address', '');
															// @param field name, class, id and attribute
															$input->fields('Address_', 'form_field','Address_','placeholder="Enter address here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('From', '');
															// @param field name, class, id and attribute
															$input->fields('From_', 'form_field Date','From_','placeholder="Enter date here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('To', '');
															// @param field name, class, id and attribute
															$input->fields('To_', 'form_field Date','To_','placeholder="Enter date here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Did you graduate?', '');
															// @param field name, class, id and attribute
															$input->radio('Graduated_in_high_school', array('Yes','No'));
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Diploma', '');
															// @param field name, class, id and attribute
															$input->fields('Diploma_', 'form_field','Diploma_','placeholder="Enter diploma here"');
														?>
													</div>
												</div>
											</div>
											
											<hr>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('College', '');
															// @param field name, class, id and attribute
															$input->fields('College', 'form_field','College','placeholder="Enter college here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Address', '');
															// @param field name, class, id and attribute
															$input->fields('Address__', 'form_field','Address__','placeholder="Enter address here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('From', '');
															// @param field name, class, id and attribute
															$input->fields('From__', 'form_field Date','From__','placeholder="Enter date here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('To', '');
															// @param field name, class, id and attribute
															$input->fields('To__', 'form_field Date','To__','placeholder="Enter date here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Did you graduate?', '');
															// @param field name, class, id and attribute
															$input->radio('Graduated_in_other_school', array('Yes','No'));
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Diploma', '');
															// @param field name, class, id and attribute
															$input->fields('Diploma__', 'form_field','Diploma__','placeholder="Enter diploma here"');
														?>
													</div>
												</div>
											</div>

											</div>
										</div>
										
										<div class="fieldbox">
											<div class="fieldheader">
												<p>Days and Hours Available</p>
												<input type="hidden" name="Days and Hours Available" value=":">
											</div>

											<div class="fieldcontent">
											
												<table id="daysOfWeek">
													<thead>
														<tr class="days">
															<th>Day</th>
															<th>Sunday</th>
															<th>Monday</th>
															<th>Tuesday</th>
															<th>Wednesday</th>
															<th>Thursday</th>
															<th>Friday</th>
															<th>Saturday</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td data-label="Day">
																<div class="forfont"><?php $input->label('From', ''); ?> </div>
															</td>
															<td data-label="Sunday">
																<input type="text" name="Sunday"  class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Monday">
																<input type="text" name="Monday" class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Tuesday">

																<input class="form_field" type="text" name="Tuesday" placeholder="Enter here">
															</td>
															<td data-label="Wednesday">
																<input type="text" name="Wednesday"  class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Thursday">
																<input type="text" name="Thursday"  class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Friday">
																<input type="text" name="Friday"  class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Saturday">
																<input type="text" name="Saturday"  class="form_field" placeholder="Enter here">
															</td>
														</tr>
														<tr>
															<td data-label="Day">
																<div class="forfont"><?php $input->label('to', ''); ?> </div>
															</td>
															<td data-label="Sunday">
																<input type="text" name="Sunday_"  class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Monday">
																<input type="text" name="Monday_" class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Tuesday">

																<input class="form_field" type="text" name="Tuesday_" placeholder="Enter here">
															</td>
															<td data-label="Wednesday">
																<input type="text" name="Wednesday_"  class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Thursday">
																<input type="text" name="Thursday_"  class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Friday">
																<input type="text" name="Friday_"  class="form_field" placeholder="Enter here">
															</td>
															<td data-label="Saturday">
																<input type="text" name="Saturday_"  class="form_field" placeholder="Enter here">
															</td>
														</tr>
													</tbody>
												</table>
											
											
											</div>
											
										</div>
										
										
										<div class="fieldbox">
											<div class="fieldheader">
												<p>Employment History</p>
												<input type="hidden" name="Employment History" value=":">
											</div>

											<div class="fieldcontent">
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Company', '');
															// @param field name, class, id and attribute
															$input->fields('Company', 'form_field','Company','placeholder="Enter company here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Phone', '');
															// @param field name, class, id and attribute
															$input->phoneInput('Phone', 'form_field','Phone','placeholder="Enter phone here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Address', '');
															// @param field name, class, id and attribute
															$input->fields('Address___', 'form_field','Address___','placeholder="Enter address here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Supervisor', '');
															// @param field name, class, id and attribute
															$input->fields('Supervisor', 'form_field','Supervisor','placeholder="Enter supervisor here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Starting Salary', '');
															// @param field name, class, id and attribute
															$input->fields('Starting_Salary', 'form_field','Starting_Salary','placeholder="Enter starting salary here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Ending Salary', '');
															// @param field name, class, id and attribute
															$input->fields('Ending_Salary', 'form_field','Ending_Salary','placeholder="Enter ending salary here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Job Title', '');
															// @param field name, class, id and attribute
															$input->fields('Job_Title', 'form_field','Job_Title','placeholder="Enter job title here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Responsibilities', '');
															// @param field name, class, id and attribute
															$input->fields('Responsibilities', 'form_field','Responsibilities','placeholder="Enter responsibilities here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('From', '');
															// @param field name, class, id and attribute
															$input->fields('From___', 'form_field Date','From___','placeholder="Enter date here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('To', '');
															// @param field name, class, id and attribute
															$input->fields('To___', 'form_field Date','To___','placeholder="Enter date here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col1">
													<div class="group">
														<?php
															$input->label('Reason for leaving', '');
															// @param field name, class, id and attribute
															$input->textarea('Reason_for_leaving', 'form_field','Reason_for_leaving','placeholder="Enter reason here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col1">
													<div class="group">
														<?php
															$input->label('May we contact your previous supervisor for a reference?', '');
															// @param field name, class, id and attribute
															$input->radio('We_contact_your_previous_supervisor_for_a_reference', array('Yes','No'));
														?>
													</div>
												</div>
											</div>
											
											
											
											<hr>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Company', '');
															// @param field name, class, id and attribute
															$input->fields('Company_', 'form_field','Company_','placeholder="Enter company here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Phone', '');
															// @param field name, class, id and attribute
															$input->phoneInput('Phone_', 'form_field','Phone_','placeholder="Enter phone here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Address', '');
															// @param field name, class, id and attribute
															$input->fields('Address____', 'form_field','Address____','placeholder="Enter address here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Supervisor', '');
															// @param field name, class, id and attribute
															$input->fields('Supervisor_', 'form_field','Supervisor_','placeholder="Enter supervisor here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Starting Salary', '');
															// @param field name, class, id and attribute
															$input->fields('Starting_Salary_', 'form_field','Starting_Salary_','placeholder="Enter starting salary here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Ending Salary', '');
															// @param field name, class, id and attribute
															$input->fields('Ending_Salary_', 'form_field','Ending_Salary_','placeholder="Enter ending salary here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Job Title', '');
															// @param field name, class, id and attribute
															$input->fields('Job_Title_', 'form_field','Job_Title_','placeholder="Enter job title here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Responsibilities', '');
															// @param field name, class, id and attribute
															$input->fields('Responsibilities_', 'form_field','Responsibilities_','placeholder="Enter responsibilities here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('From', '');
															// @param field name, class, id and attribute
															$input->fields('From____', 'form_field Date','From____','placeholder="Enter date here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('To', '');
															// @param field name, class, id and attribute
															$input->fields('To____', 'form_field Date','To____','placeholder="Enter date here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col1">
													<div class="group">
														<?php
															$input->label('Reason for leaving', '');
															// @param field name, class, id and attribute
															$input->textarea('Reason_for_leaving_', 'form_field','Reason_for_leaving_','placeholder="Enter reason here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col1">
													<div class="group">
														<?php
															$input->label('May we contact your previous supervisor for a reference?', '');
															// @param field name, class, id and attribute
															$input->radio('We_contact_your_previous_supervisor_for_a_reference_', array('Yes','No'));
														?>
													</div>
												</div>
											</div>
											
											<hr>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Company', '');
															// @param field name, class, id and attribute
															$input->fields('Company__', 'form_field','Company__','placeholder="Enter company here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Phone', '');
															// @param field name, class, id and attribute
															$input->phoneInput('Phone__', 'form_field','Phone__','placeholder="Enter phone here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Address', '');
															// @param field name, class, id and attribute
															$input->fields('Address_____', 'form_field','Address_____','placeholder="Enter address here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Supervisor', '');
															// @param field name, class, id and attribute
															$input->fields('Supervisor__', 'form_field','Supervisor__','placeholder="Enter supervisor here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Starting Salary', '');
															// @param field name, class, id and attribute
															$input->fields('Starting_Salary__', 'form_field','Starting_Salary__','placeholder="Enter starting salary here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Ending Salary', '');
															// @param field name, class, id and attribute
															$input->fields('Ending_Salary__', 'form_field','Ending_Salary__','placeholder="Enter ending salary here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Job Title', '');
															// @param field name, class, id and attribute
															$input->fields('Job_Title__', 'form_field','Job_Title__','placeholder="Enter job title here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Responsibilities', '');
															// @param field name, class, id and attribute
															$input->fields('Responsibilities__', 'form_field','Responsibilities__','placeholder="Enter responsibilities here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('From', '');
															// @param field name, class, id and attribute
															$input->fields('From_____', 'form_field Date','From_____','placeholder="Enter date here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('To', '');
															// @param field name, class, id and attribute
															$input->fields('To_____', 'form_field Date','To_____','placeholder="Enter date here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col1">
													<div class="group">
														<?php
															$input->label('Reason for leaving', '');
															// @param field name, class, id and attribute
															$input->textarea('Reason_for_leaving__', 'form_field','Reason_for_leaving__','placeholder="Enter reason here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col1">
													<div class="group">
														<?php
															$input->label('May we contact your previous supervisor for a reference?', '');
															// @param field name, class, id and attribute
															$input->radio('We_contact_your_previous_supervisor_for_a_reference__', array('Yes','No'));
														?>
													</div>
												</div>
											</div>
											
											<hr>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Company', '');
															// @param field name, class, id and attribute
															$input->fields('Company___', 'form_field','Company___','placeholder="Enter company here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Phone', '');
															// @param field name, class, id and attribute
															$input->phoneInput('Phone___', 'form_field','Phone___','placeholder="Enter phone here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Address', '');
															// @param field name, class, id and attribute
															$input->fields('Address______', 'form_field','Address______','placeholder="Enter address here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Supervisor', '');
															// @param field name, class, id and attribute
															$input->fields('Supervisor___', 'form_field','Supervisor___','placeholder="Enter supervisor here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Starting Salary', '');
															// @param field name, class, id and attribute
															$input->fields('Starting_Salary___', 'form_field','Starting_Salary___','placeholder="Enter starting salary here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Ending Salary', '');
															// @param field name, class, id and attribute
															$input->fields('Ending_Salary___', 'form_field','Ending_Salary___','placeholder="Enter ending salary here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Job Title', '');
															// @param field name, class, id and attribute
															$input->fields('Job_Title___', 'form_field','Job_Title___','placeholder="Enter job title here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Responsibilities', '');
															// @param field name, class, id and attribute
															$input->fields('Responsibilities___', 'form_field','Responsibilities___','placeholder="Enter responsibilities here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('From', '');
															// @param field name, class, id and attribute
															$input->fields('From______', 'form_field Date','From______','placeholder="Enter date here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('To', '');
															// @param field name, class, id and attribute
															$input->fields('To______', 'form_field Date','To______','placeholder="Enter date here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col1">
													<div class="group">
														<?php
															$input->label('Reason for leaving', '');
															// @param field name, class, id and attribute
															$input->textarea('Reason_for_leaving___', 'form_field','Reason_for_leaving___','placeholder="Enter reason here"');
														?>
													</div>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col1">
													<div class="group">
														<?php
															$input->label('May we contact your previous supervisor for a reference?', '');
															// @param field name, class, id and attribute
															$input->radio('We_contact_your_previous_supervisor_for_a_reference___', array('Yes','No'));
														?>
													</div>
												</div>
											</div>
											
											</div>
										</div>

										<div class="fieldbox">
											<div class="fieldheader">
												<p>References</p>
												<input type="hidden" name="References" value=":">
											</div>

											<div class="fieldcontent">
											
												<div class="forfont"><?php $input->label('Please list three professional references (not related to you)', ''); ?></div>
												
												<div class="form_box">
													<div class="form_box_col2">
														<div class="group">
															<?php
																$input->label('Full Name', '');
																// @param field name, class, id and attribute
																$input->fields('Full_Name_', 'form_field','Full_Name_','placeholder="Enter full name here"');
															?>
														</div>
														<div class="group">
															<?php
																$input->label('Relationship', '');
																// @param field name, class, id and attribute
																$input->fields('Relationship', 'form_field','Relationship','placeholder="Enter relationship here"');
															?>
														</div>
													</div>
												</div>
												
												<div class="form_box">
													<div class="form_box_col2">
														<div class="group">
															<?php
																$input->label('Company', '');
																// @param field name, class, id and attribute
																$input->fields('Company____', 'form_field','Company____','placeholder="Enter company here"');
															?>
														</div>
														<div class="group">
															<?php
																$input->label('Phone', '');
																// @param field name, class, id and attribute
																$input->phoneInput('Phone____', 'form_field','Phone____','placeholder="Enter phone here"');
															?>
														</div>
													</div>
												</div>
												
												<div class="form_box">
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Address', '');
																// @param field name, class, id and attribute
																$input->fields('Address_______', 'form_field','Address_______','placeholder="Enter address here"');
															?>
														</div>
													</div>
												</div>
												
												<hr>
												
												<div class="form_box">
													<div class="form_box_col2">
														<div class="group">
															<?php
																$input->label('Full Name', '');
																// @param field name, class, id and attribute
																$input->fields('Full_Name__', 'form_field','Full_Name__','placeholder="Enter full name here"');
															?>
														</div>
														<div class="group">
															<?php
																$input->label('Relationship', '');
																// @param field name, class, id and attribute
																$input->fields('Relationship_', 'form_field','Relationship_','placeholder="Enter relationship here"');
															?>
														</div>
													</div>
												</div>
												
												<div class="form_box">
													<div class="form_box_col2">
														<div class="group">
															<?php
																$input->label('Company', '');
																// @param field name, class, id and attribute
																$input->fields('Company_____', 'form_field','Company_____','placeholder="Enter company here"');
															?>
														</div>
														<div class="group">
															<?php
																$input->label('Phone', '');
																// @param field name, class, id and attribute
																$input->phoneInput('Phone_____', 'form_field','Phone_____','placeholder="Enter phone here"');
															?>
														</div>
													</div>
												</div>
												
												<div class="form_box">
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Address', '');
																// @param field name, class, id and attribute
																$input->fields('Address________', 'form_field','Address________','placeholder="Enter address here"');
															?>
														</div>
													</div>
												</div>
												
												<hr>
												
												<div class="form_box">
													<div class="form_box_col2">
														<div class="group">
															<?php
																$input->label('Full Name', '');
																// @param field name, class, id and attribute
																$input->fields('Full_Name___', 'form_field','Full_Name___','placeholder="Enter full name here"');
															?>
														</div>
														<div class="group">
															<?php
																$input->label('Relationship', '');
																// @param field name, class, id and attribute
																$input->fields('Relationship__', 'form_field','Relationship__','placeholder="Enter relationship here"');
															?>
														</div>
													</div>
												</div>
												
												<div class="form_box">
													<div class="form_box_col2">
														<div class="group">
															<?php
																$input->label('Company', '');
																// @param field name, class, id and attribute
																$input->fields('Company______', 'form_field','Company______','placeholder="Enter company here"');
															?>
														</div>
														<div class="group">
															<?php
																$input->label('Phone', '');
																// @param field name, class, id and attribute
																$input->phoneInput('Phone______', 'form_field','Phone______','placeholder="Enter phone here"');
															?>
														</div>
													</div>
												</div>
												
												<div class="form_box">
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Address', '');
																// @param field name, class, id and attribute
																$input->fields('Address________', 'form_field','Address________','placeholder="Enter address here"');
															?>
														</div>
													</div>
												</div>
												
												
											</div>
										</div>
										
										
										<div class="fieldbox">
											<div class="fieldheader">
												<p>Military Service</p>
												<input type="hidden" name="Military Service" value=":">
											</div>

											<div class="fieldcontent">
											
												<div class="form_box">
													<div class="form_box_col1">
														<div class="group">
															<?php
																$input->label('Branch', '');
																// @param field name, class, id and attribute
																$input->fields('Branch', 'form_field','Branch','placeholder="Enter branch here"');
															?>
														</div>
													</div>
												</div>
												
												<div class="form_box">
													<div class="form_box_col2">
														<div class="group">
															<?php
																$input->label('From', '');
																// @param field name, class, id and attribute
																$input->fields('From_______', 'form_field Date','From_______','placeholder="Enter date here"');
															?>
														</div>
														<div class="group">
															<?php
																$input->label('To', '');
																// @param field name, class, id and attribute
																$input->fields('To_______', 'form_field Date','To_______','placeholder="Enter date here"');
															?>
														</div>
													</div>
												</div>
												
												<div class="form_box">
													<div class="form_box_col2">
														<div class="group">
															<?php
																$input->label('Rank at Discharge', '');
																// @param field name, class, id and attribute
																$input->fields('Rank_at_Discharge', 'form_field','Rank_at_Discharge','placeholder="Enter rank at discharge here"');
															?>
														</div>
														<div class="group">
															<?php
																$input->label('Type of Discharge', '');
																// @param field name, class, id and attribute
																$input->fields('Type_of_Discharge', 'form_field','Type_of_Discharge','placeholder="Enter type of discharge here"');
															?>
														</div>
													</div>
												</div>
											
											</div>
										</div>
										
										<div class="fieldbox">
											<div class="fieldheader">
												<p>Disclaimer and Signature</p>
												<input type="hidden" name="Disclaimer and Signature" value=":">
											</div>

											<div class="fieldcontent">
												<div class="forfont">
												
												<p>Commonwealth Supportive Services (CSS) is an equal opportunity employer. CSS does not discriminate in employment on accuont of race, color, religion, national origin, citizenship status, ancestry, age, gender, sexual orientation, marital status or any other protected class.</p>

												<p><input type="checkbox" name="Docu01" style="-webkit-appearance:checkbox"/> I understand that neither the completion of this application nor any other part of my consideration for employment establishes nay obligation for CSS to hire me. If I am hired, I understand that either CSS or I can terminate my employment at any time and for any reason, with or without prior notice. I understand that no representative of CSS has the authority to make any assurance to the contrary.</p>

												<p><input type="checkbox" name="Docu02" style="-webkit-appearance:checkbox"/> I attest with my signature below that I have given to Commonwealth Supportive Services, true and complete information on this application. No requested information has been concealed. I authorize CSS to contact references provided for employment reference check. I also authorize CSS to conduct background checks upon offer of employment. If any information I have provided is untrue, I understand that this will constitute cause for denial of employment or immediate dismissal. </p>
												</div>
											</div>
											
											<div class="form_box">
												<div class="form_box_col2">
													<div class="group">
														<?php
															$input->label('Print Name', '');
															// @param field name, class, id and attribute
															$input->fields('Print_Name', 'form_field','Print_Name','placeholder="Enter name here"');
														?>
													</div>
													<div class="group">
														<?php
															$input->label('Date', '');
															// @param field name, class, id and attribute
															$input->fields('Date_', 'form_field Date','Date_','placeholder="Enter date here"');
														?>
													</div>
												</div>
											</div>
											
										</div>
										
							<div class = "form_box5 secode_box">
								<div class = "group">
									<div class="inner_form_box1 recapBtn">
										<div class="g-recaptcha" data-sitekey="6LeV8XEUAAAAAH5pQoATt6wQSAxIwWZeCa19tepp"></div>
										<div class="btn-submit"><input type = "submit" class = "form_button" value = "SUBMIT" /></div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $input->phone(true); ?>
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.js"></script>
	<script src = "js/proweaverPhone.js"></script>
	<script src = "js/jquery.mask.min.js"></script>
	

	<script type="text/javascript">

$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		ignore: ":hidden",
		rules: {
			Last_Name: "required",
			First_Name: "required",
			Street_Address: "required",
			City: "required",
			_Phone: "required",
			Email: {
				required: true,
				email: true
			},
		},
		messages: {
			Last_Name: "",
			First_Name: "",
			Street_Address: "",
			City: "",
			_Phone: "",
			Email: ""

		}
	});

	$('input.timepicker').timepicker({});
 
	$("#Explanation02, #Explanation03, #Explanation04, #Explanation05, #Explanation06").hide();
	
	$("#Explanation01").find(':input').attr('disabled', 'disabled');
	/* radio toggle */
	$("input[name='Have_knowledge_who_works_at_css']").change(function(){
		if($(this).val() == "Yes"){
			$("#Explanation01").find(':input').attr('disabled', false);
		}else{
			$("#Explanation01").find(':input').attr('disabled', 'disabled');
		}
	});

	/* radio toggle */
	$("input[name='Ever_been_terminated_from_employment_or_asked_to_resign']").change(function(){
		if($(this).val() == "Yes"){
			$("#Explanation02").fadeIn();
			$("#Explanation02").find(':input').attr('disabled', false);
		}else{
			$("#Explanation02").fadeOut();
			$("#Explanation02").find(':input').attr('disabled', 'disabled');
		}
	});
	
	/* radio toggle */
	$("input[name='Convicted_of_a_crime_in_the_past_5_years']").change(function(){
		if($(this).val() == "Yes"){
			$("#Explanation04").fadeIn();
			$("#Explanation04").find(':input').attr('disabled', false);
		}else{
			$("#Explanation04").fadeOut();
			$("#Explanation04").find(':input').attr('disabled', 'disabled');
		}
	});
	
	/* radio toggle */
	$("input[name='Capable_of_performing_the_job_set_forth_in_the_job_description']").change(function(){
		if($(this).val() == "No"){
			$("#Explanation05").fadeIn();
			$("#Explanation05").find(':input').attr('disabled', false);
		}else{
			$("#Explanation05").fadeOut();
			$("#Explanation05").find(':input').attr('disabled', 'disabled');
		}
	});
	
	/* radio toggle */
	$("input[name='Position_applying_for']").change(function(){
		if($(this).val() == "Therapist"){
			$("#Explanation06").fadeIn();
			$("#Explanation06").find(':input').attr('disabled', false);
		}else{
			$("#Explanation06").fadeOut();
			$("#Explanation06").find(':input').attr('disabled', 'disabled');
		}
	});

	$("#submitform").submit(function(){
		if($(this).valid()){
			$('.load_holder').css('display','block');
			self.parent.$('html, body').animate(
				{ scrollTop: self.parent.$('#myframe').offset().top },
				500
			);
		}
		if(grecaptcha.getResponse() == "") {
			var $recaptcha = document.querySelector('#g-recaptcha-response');
				$recaptcha.setAttribute("required", "required");
				$('.g-recaptcha').addClass('errors').attr('id','recaptcha');
		  }
	});

	$( "input" ).keypress(function( event ) {
		if(grecaptcha.getResponse() == "") {
			var $recaptcha = document.querySelector('#g-recaptcha-response');
			$recaptcha.setAttribute("required", "required");
		  }
	});

	$('.Date').datepicker();
	$('.Date').attr('autocomplete', 'off');


});
$(function() {
  $('.Date, .date').datepicker({
	autoHide: true,
	zIndex: 2048,
  });
});
</script>
</body>
</html>
