<?php $page = 'Register'; 
$is_home = false;
?>

<?php
set_include_path ('includes');
include "head.php";
include "header.php";
include "nav.php";
include "nh_banner.php";
?>

<!-- Main -->
<div id="main_area">
	<div class="wrapper">
	<div class="main_holder">
	<div class="main_con">

		<main>
			<p>Register to our subscription list by filling out the form below with your information.</p>

			<iframe id="myframe" class="" src="forms/registrationForm.php"></iframe>
		</main>

	</div>
	<div class="clearfix"></div>
	</div>
</div>
</div>
  <!-- End Main -->




<?php
include "footer.php";
?>