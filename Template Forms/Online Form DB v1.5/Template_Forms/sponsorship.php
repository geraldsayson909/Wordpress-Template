<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();
ob_start();

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
			First_Name: "required",
			Last_Name: "required",
			Address: "required",
			City: "required",
			Zip: "required",
			Phone_Day: "required",
			Email: {
				required: true,
				email: true
			},
			secode: "required"		
		},
		messages: {
			First_Name: "Required",
			Last_Name: "Required",
			Address: "Required",
			City: "Required",
			Zip: "Required",
			Phone_Day: "Required",
			Email: "Enter a valid Email",
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
			<form id="submitform" name="contact" method="post" action="https://www.paypal.com/cgi-bin/webscr" target="_parent">				
				<div class="field">
					<div class="input">
						<label for="amount_1">Amount</label>
					</div>
					<div class="input f-right">
						<span style="float:left; color:#000; padding-top:5px;">$ &nbsp;</span>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('amount_1', 'text','amount_1','placeholder="Enter amount here"'); 
						?>
						
						<!-- PAYPAL ACCOUNT DETAILS HERE -->
						<input name="quantity_1" type="hidden" id="quantity_1" value="1" />
						<input name="item_name_1" type="hidden" id="item_name_1" value="African Christian Fellowship - Minnesota Chapter" />
						<input name="notify_url" type="hidden" id="notify_url" value="http://www.proweaver.net/acfminnesota/" />
						<input name="rm" type="hidden" id="rm" value="2" />
						
						<input type="hidden" name="cmd" value="_cart">
						<input type="hidden" name="upload" value="1">
						<input type="hidden" name="business" value="info@domainname.com" />
						<input type="hidden" name="currency_code" value="USD" />
						<input type="hidden" name="return" value="http://www.proweaver.net/acfminnesota/success.php" />
						<input type="hidden" name="cancel_return" value="http://www.proweaver.net/acfminnesota/" />
						<input name="item_name" type="hidden" id="item_name" value="African Christian Fellowship Donation" />
						<!-- end of PAYPAL ACCOUNT DETAILS HERE -->
						
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<div style="background:#eeeeee;">&nbsp;</div>
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
					<div class="input">
						<label for="Address">Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Address', 'text','Address','placeholder="Enter address here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="address2">Address 2</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('address2', 'text','address2','placeholder="Enter address 2 here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="City">City <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('City', 'text','City','placeholder="Enter city here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="State">State</label>
						<?php 
							// @param field name, class, optname, id and attribute
							$input->select('State', 'select',$state); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Country">Country</label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Country', 'text','Country','placeholder="Enter country here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Zip">Zip Code <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Zip', 'text','Zip','placeholder="Enter zip here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input">
						<label for="Phone_Day">Phone Day <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Phone_Day', 'text','Phone_Day','placeholder="Enter phone day here"'); 
						?>
					</div>
					<div class="input f-right">
						<label for="Email">Email Address <span>*</span></label>
						<?php 
							// @param field name, class, id and attribute
							$input->fields('Email', 'text','Email','placeholder="Enter email here"'); 
						?>
					</div>
				</div>
				<div class="field">
					<div class="input textarea">
						<div style="background:#eeeeee;">&nbsp;</div>
					</div>
				</div>
				<div class="field">	
					<div class="verification">
						<input name="CheckOut" type="submit"  class="body buttonPaypal" id="CheckOut" value="Submit &raquo;" style="cursor: pointer;" />
					</div>	
				</div>
			</form>	
			<div class="clearfix"></div>			
		</div>
	</div>
</body>	
</html>