<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Mortgage Pre-Qualification Form';
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

	if( empty($_POST['Name']) ||
		empty($_POST['Street']) ||
		empty($_POST['City']) ||
		empty($_POST['Email_Address'])
		) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
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
				elseif($key == 'checkboxVal') continue;
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
			
			// echo $body; exit;

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
			$prompt_message = '<div id="success"><div class="message"><span>THANK YOU</span><br/> <span>for sending us a message!</span><br/><span>We will be in touch with you soon.</span><p class="close">x</p></div></div>';
				unset($_POST);
		}else {
			$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
		}

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
		 <link rel="stylesheet" href="css/intlTelInput.css">
		<link rel="stylesheet" href="style.min.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style media="screen">
			.hdh{text-align: center; text-transform: uppercase; background: #6f6867; padding: 15px 15px; color: #fff; border-radius: 4px; font-size: 20px;}

			.amount{ padding: 10px 6px 10px 74px; }
			#icon { position: absolute; padding: 7px 39px 10px 10px; background: #6f6867;  height: 62px; color: #fff; font-size: 31px; }
			.fa-dollar-sign::before { content: "\f155"; position: relative; left: 13px; top: 5px; }
			.forfont {background: #f1f1f1;padding: 5px;border-radius: 3px;margin: 10px auto;text-transform: uppercase;font-weight: bold;}
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
							
							<p class="hdh">Borrower Information</p>
							
							<input type="hidden" name="Borrower Information" value=":">
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Name', 'form_field','Name','placeholder="Enter name here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Present Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Present_Address', 'form_field','Present_Address','placeholder="Enter address here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">

									<div class="group">
										<?php
											$input->label('Street', '*');
											// @param field name, class, id and attribute
											$input->fields('Street', 'form_field','','placeholder="Enter street here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('City', '*');
											// @param field name, class, id and attribute
											$input->fields('City', 'form_field','','placeholder="Enter city here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Province/Region', '*');
											// @param field name, class, id and attribute
											$input->fields('Province_or_Region', 'form_field','','placeholder="Enter province/region here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Zip Code', '*');
											// @param field name, class, id and attribute
											$input->fields('Zip_Code', 'form_field','','placeholder="Enter zip code here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Social Security Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Social_Secuirty_Number', 'form_field','','placeholder="Enter socail security number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Birth Date', '*');
											// @param field name, class, id and attribute
											$input->fields('Birth_Date', 'form_field','','placeholder="Enter date here"');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Contact Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Contact_Number', 'form_field','','placeholder="Enter contact number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Email Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Email_Address', 'form_field','','placeholder="Enter email address here"');
										?>
									</div>
								</div>
							</div>

							<p class="hdh">Co-Borrower Information</p>
							<input type="hidden" name="Co-Borrower Information" value=":">
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Name', '');
											// @param field name, class, id and attribute
											$input->fields('Name_', 'form_field','Name','placeholder="Enter name here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Present Address', '');
											// @param field name, class, id and attribute
											$input->fields('Present_Address_', 'form_field','Present_Address_','placeholder="Enter address here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">

									<div class="group">
										<?php
											$input->label('Street', '');
											// @param field name, class, id and attribute
											$input->fields('Street_', 'form_field','','placeholder="Enter street here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('City', '');
											// @param field name, class, id and attribute
											$input->fields('City_', 'form_field','','placeholder="Enter city here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Province/Region', '');
											// @param field name, class, id and attribute
											$input->fields('Province_or_Region_', 'form_field','','placeholder="Enter province/region here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Zip Code', '');
											// @param field name, class, id and attribute
											$input->fields('Zip_Code_', 'form_field','','placeholder="Enter zip code here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Social Security Number', '');
											// @param field name, class, id and attribute
											$input->fields('Social_Secuirty_Number_', 'form_field','','placeholder="Enter socail security number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Birth Date', '');
											// @param field name, class, id and attribute
											$input->fields('Birth_Date_', 'form_field','','placeholder="Enter date here"');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Contact Number', '');
											// @param field name, class, id and attribute
											$input->fields('Contact_Number_', 'form_field','','placeholder="Enter contact number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Email Address', '');
											// @param field name, class, id and attribute
											$input->fields('Email_Address_', 'form_field','','placeholder="Enter email address here"');
										?>
									</div>
								</div>
							</div>
							<p class="hdh">Financial Information (Borrower)</p>
							<input type="hidden" name="Financial Information (Borrower)" value=":">
							
							<div class="forfont">Income
							<input type="hidden" name="Income" value=":"></div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Base (per hr/wk/month)', '');
											// @param field name, class, id and attribute
											$input->fields('Base', 'form_field','','placeholder="Enter base here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Bonuses/Overtime', '');
											// @param field name, class, id and attribute
											$input->fields('Bonuses_or_Overtime', 'form_field','','placeholder="Enter bonuses or overtime here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Commissions', '');
											// @param field name, class, id and attribute
											$input->fields('Commissions', 'form_field','','placeholder="Enter commissions here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Dividends/Interest', '');
											// @param field name, class, id and attribute
											$input->fields('Dividends_or_Interest', 'form_field','','placeholder="Enter dividends or Interest here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('Other', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('Total', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>
							
							<div class="forfont">Assets
							<input type="hidden" name="Assets" value=":"></div>
							
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Checking/Savings', '');
											// @param field name, class, id and attribute
											$input->amount('Checking_or_Savings', 'form_field','placeholder="Enter checking or saving here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Stocks/Bonds', '');
											// @param field name, class, id and attribute
											$input->amount('Stocks_or_Bonds', 'form_field','placeholder="Enter stocks or bonds here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Equity in Real Estate', '');
											// @param field name, class, id and attribute
											$input->amount('Equity_in_Real_Estate', 'form_field','placeholder="Enter equity here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('Other', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('Total___', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>
							
							<div class="forfont">Liabilities
							<input type="hidden" name="Liabilities" value=":"></div>
							
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Mortgage/Rent', '');
											// @param field name, class, id and attribute
											$input->fields('Mortgage_or_Rent', 'form_field','','placeholder="Enter morgage/rent here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Auto Loan(s)', '');
											// @param field name, class, id and attribute
											$input->fields('Auto_Loan(s)', 'form_field','','placeholder="Enter auto loan here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Installment Loan(s)', '');
											// @param field name, class, id and attribute
											$input->fields('Installment_Loan(s)', 'form_field','','placeholder="Enter installment Loan here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card_', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card__', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Alimony', '');
											// @param field name, class, id and attribute
											$input->fields('Alimony', 'form_field','','placeholder="Enter alimony here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Childcare/Support', '');
											// @param field name, class, id and attribute
											$input->fields('Childcare_or_Support', 'form_field','','placeholder="Enter childcare/support here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Student Loans', '');
											// @param field name, class, id and attribute
											$input->fields('Student_Loans', 'form_field','','placeholder="Enter student loans here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('_Other___', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('__Total___', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>


							<p class="hdh">Financial Information (Co-borrower)</p>
							<input type="hidden" name="Financial Information (Co-borrower)" value=":">
							
							<div class="forfont">
							Income <input type="hidden" name="Income_" value=":">
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Base (per hr/wk/month)', '');
											// @param field name, class, id and attribute
											$input->fields('Base_', 'form_field','','placeholder="Enter base here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Bonuses/Overtime', '');
											// @param field name, class, id and attribute
											$input->fields('Bonuses_or_Overtime_', 'form_field','','placeholder="Enter bonuses or overtime here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Commissions', '');
											// @param field name, class, id and attribute
											$input->fields('Commissions_', 'form_field','','placeholder="Enter commissions here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Dividends/Interest', '');
											// @param field name, class, id and attribute
											$input->fields('Dividends_or_Interest_', 'form_field','','placeholder="Enter dividends or Interest here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('Other_', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('Total_____', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>
							
							<div class="forfont">Assets <input type="hidden" name="Assets_" value=":"></div>
							
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Checking/Savings', '');
											// @param field name, class, id and attribute
											$input->amount('Checking_or_Savings_', 'form_field','','placeholder="Enter checking or saving here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Stocks/Bonds', '');
											// @param field name, class, id and attribute
											$input->amount('Stocks_or_Bonds_', 'form_field','','placeholder="Enter stocks or bonds here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Equity in Real Estate', '');
											// @param field name, class, id and attribute
											$input->amount('Equity_in_Real_Estate_', 'form_field','','placeholder="Enter equity here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('Other______', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('__Total___', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>
							
							<div class="forfont">Liabilities <input type="hidden" name="Liabilities_" value=":"></div>
							
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Mortgage/Rent', '');
											// @param field name, class, id and attribute
											$input->fields('Mortgage_or_Rent__', 'form_field','','placeholder="Enter morgage/rent here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Auto Loan(s)', '');
											// @param field name, class, id and attribute
											$input->fields('Auto_Loan(s)__', 'form_field','','placeholder="Enter auto loan here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Installment Loan(s)', '');
											// @param field name, class, id and attribute
											$input->fields('Installment_Loan(s)__', 'form_field','','placeholder="Enter installment Loan here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card______', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card________', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('_____Credit_Card__', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Alimony', '');
											// @param field name, class, id and attribute
											$input->fields('Alimony_', 'form_field','','placeholder="Enter alimony here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Childcare/Support', '');
											// @param field name, class, id and attribute
											$input->fields('Childcare_or_Support_', 'form_field','','placeholder="Enter childcare/support here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Student Loans', '');
											// @param field name, class, id and attribute
											$input->fields('Student_Loans___', 'form_field','','placeholder="Enter student loans here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('____Other___', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('_____Total___', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>
							<p style="font-weight:bold; font-size:20px;">AMOUNT AND TERMS OF MORTGAGE REQUEST</p>
							<input type="hidden" name="AMOUNT AND TERMS OF MORTGAGE REQUEST" value=":">
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Pre-Qual Amount Requested', '');
											// @param field name, class, id and attribute
											$input->fields('Pre-Qual_Amount_Requested', 'form_field','','placeholder="Enter amount here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Desired Payment', '');
											// @param field name, class, id and attribute
											$input->fields('Desired_Payment', 'form_field','','placeholder="Enter desired payment here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Mortgage Terms', '');
											// @param field name, class, id and attribute
											$input->chkboxVAL('Mortgage_Terms', array('30 Year Fixed Rate',' 15 Year Fixed Rate',' 7 Year ARM'));
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
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script src = "js/plugins.min.js"></script>

	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Name: "required",
			Present_Address: "required",
			Street: "required",
			City: "required",
			State_or_Province_or_Region: "required",
			Zip_Code: "required",
			Province_or_Region: "required",
			Social_Secuirty_Number: "required",
			Birth_Date: "required",
			Contact_Number: "required",
			Email_Address:{
				required: true,
				email: true
			}
			
		},
		messages: {
			Name: "",
			Present_Address: "",
			Street: "",
			City: "",
			Province_or_Region: "",
			Zip_Code: "",
			Social_Secuirty_Number: "",
			Birth_Date: "",
			Contact_Number: "",
			Email_Address: ""
		}
	});


	checkboxValues('Mortgage_Terms');
		function checkboxValues(inputAttrName) {
				var inputAttrName = inputAttrName;
				var inputHidden = $('input[name="'+inputAttrName+'"]').attr('value');
				var checkedValues = '';
				var checkboxClass = $('input.'+inputAttrName+'');

				$.each(checkboxClass, function(index) {
						$(this).on('change', function() {
								var x = $(this).attr('value') + ', ';
								if($(this).is(':checked')) {
										inputHidden += x;
										checkedValues = inputHidden.replace(/,\s*$/, "");
										$('input[name="'+inputAttrName+'"]').attr('value', checkedValues);
								} else {
										inputHidden = inputHidden.replace(x, '');
										checkedValues = inputHidden.replace(/,\s*$/, "");
										$('input[name="'+inputAttrName+'"]').attr('value', checkedValues);
								}
						});
				});
		}


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
			$('.g-recaptcha').addClass('errors').attr('id','recaptcha');
		  }
	});

});

</script>
</body>
</html>
