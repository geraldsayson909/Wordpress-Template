<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Employee Satisfactory Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php';
if ($_POST){

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "secret={$recaptcha_privite}&response={$_POST['g-recaptcha-response']}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	$result_recaptcha = json_decode($server_output);
	curl_close ($ch);

		if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}elseif(empty($_POST['I_can_see_myself_working_here_in_five_years']) and empty($_POST['I_have_a_clear_understanding_of_my_company\'s_strategic_goals.']) and
	empty($_POST['I_always_know_what_is_expected_of_me_when_it_comes_to_my_goals_and_objectives']) and
	empty($_POST['My_manager_recognizes_my_full_potential_and_capitalizes_on_my_strengths']) and
	empty($_POST['I\'m_proud_to_be_part_of_this_company']) and
	
	empty($_POST['I_always_recommend_my_company_to_others']) and
	empty($_POST['I_believe_in_my_company\'s_mission']) and
	empty($_POST['When_I_do_something_successfully,_it_feels_like_a_personal_accomplishment']) and
	empty($_POST['I_have_all_the_resources_I_need_to_do_my_job_successfully']) and
	empty($_POST['The_management_always_demonstrates_a_commitment_to_quality']) and
	empty($_POST['The_management_always_encourages_others_to_a_commitment_to_quality']) and
	empty($_POST['I_am_involved_in_decision-making_that_affects_my_work']) and
	empty($_POST['I_have_opportunities_to_express_myself']) and
	empty($_POST['I_have_opportunities_to_recommend_new_ideas_and_solutions'])){
			$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please answer at least one question.</span><br/><p class="rclose">x</p></div></div>';
	}
	else{

		$body = '<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px; margin: 0 auto;">
			<div style="border:8px double #c3c3d0; padding:12px;">
			<div align="center" style="color:#990000; font-style:italic; font-size:20px; font-family:Arial; margin:bottom: 15px;">('.$formname.')</div>

			<table width="90%" cellspacing="2" cellpadding="5" align="center" style="font-family:Verdana; font-size:13px">
				';

			foreach($_POST as $key => $value){
				if($key == 'secode') continue;
				elseif($key == 'submit') continue;
				elseif($key == 'g-recaptcha-response') continue;

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
		include 'send_email_curl.php';

		// save data form on database
		include 'savedb.php';

		// save data form on database
		$subject = $formname ;
		$attachments = array();

	 	//name of sender
		$name = "New Message Notification";
		$result = insertDB($name,$subject,$body,$attachments);

		$parameter = array(
			'body' => $body,
			'from' => $from_email,
			'from_name' => $from_name,
			'to' => $to_email,
			'subject' => 'New Message Notification',	
			'attachment' => $attachments	
		);

		$prompt_message = send_email($parameter);
		unset($_POST);

	}

}
/*************declaration starts here************/

?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>

		<link rel="stylesheet" href="style.min.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<script src='https://www.google.com/recaptcha/api.js'></script>
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
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('1. I can see myself working here in five years.');
											// @param field name, class, id and attribute
											$input->radio('I_can_see_myself_working_here_in_five_years',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('2. I have a clear understanding of my company\'s strategic goals.');
											// @param field name, class, id and attribute
											$input->radio('I_have_a_clear_understanding_of_my_company\'s_strategic_goals',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('3. I always know what is expected of me when it comes to my goals and objectives.');
											// @param field name, class, id and attribute
											$input->radio('I_always_know_what_is_expected_of_me_when_it_comes_to_my_goals_and_objectives',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('4. My manager recognizes my full potential and capitalizes on my strengths.');
											// @param field name, class, id and attribute
											$input->radio('My_manager_recognizes_my_full_potential_and_capitalizes_on_my_strengths',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('5. I\'m proud to be part of this company.');
											// @param field name, class, id and attribute
											$input->radio('I\'m_proud_to_be_part_of_this_company',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('6. I always recommend my company to others.');
											// @param field name, class, id and attribute
											$input->radio('I_always_recommend_my_company_to_others',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>
							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('7. I believe in my company\'s mission.');
											// @param field name, class, id and attribute
											$input->radio('I_believe_in_my_company\'s_mission',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>
							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('8. When I do something successfully, it feels like a personal accomplishment.');
											// @param field name, class, id and attribute
											$input->radio('When_I_do_something_successfully,_it_feels_like_a_personal_accomplishment',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>
							
							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('9. I have all the resources I need to do my job successfully.');
											// @param field name, class, id and attribute
											$input->radio('I_have_all_the_resources_I_need_to_do_my_job_successfully',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>
							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('10. The management always demonstrates a commitment to quality.');
											// @param field name, class, id and attribute
											$input->radio('The_management_always_demonstrates_a_commitment_to_quality',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>
							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('11. The management always encourages others to a commitment to quality.');
											// @param field name, class, id and attribute
											$input->radio('The_management_always_encourages_others_to_a_commitment_to_quality',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>
							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('12. I am involved in decision-making that affects my work.');
											// @param field name, class, id and attribute
											$input->radio('I_am_involved_in_decision-making_that_affects_my_work',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>
							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('13. I have opportunities to express myself.');
											// @param field name, class, id and attribute
											$input->radio('I_have_opportunities_to_express_myself',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>
							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('14. I have opportunities to recommend new ideas and solutions.');
											// @param field name, class, id and attribute
											$input->radio('I_have_opportunities_to_recommend_new_ideas_and_solutions',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'));
										?>
									</div>
								</div>
							</div>


							<div class = "form_box5 secode_box">
								<div class = "group">
									<div class="inner_form_box1 recapBtn">
										<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
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
	<script src = "js/jquery-1.9.0.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script src = "js/plugins.min.js"></script>


	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
/* 			First_Name: "required",
			Last_Name: "required",
			Address: "required",
			Phone: "required",
			Question_Comment: "required",
			Email_Address: {
				required: true,
				email: true
			},
			secode: "required"	 */
		},
		messages: {
/* 			First_Name: "",
			Last_Name: "",
			Address: "",
			Phone: "",
			Question_Comment: "",
			Email_Address: "",
			secode: "" */
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


});
</script>
</body>
</html>
