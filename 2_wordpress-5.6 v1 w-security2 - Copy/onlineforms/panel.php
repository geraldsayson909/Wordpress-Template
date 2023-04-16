<?php
	@session_start();
	if( empty($_SESSION['user_login']) && $_SESSION['login_status'] != 1){
		header("Location: index.php");
		die();
	}

	set_include_path ('includes');
	include 'details.php';
	include 'header.php';
	include 'modals.php';
	$a = 'current';
	include 'head.php';
	include 'banner.php';
	include 'modals.php';

?>

<div id="main" class="clearfix">
	<div class="container">
	 	<div class="email-table f-left">

	 	</div>

		<div class="form_pane f-right">
			<div class="show-subject">

			</div>
			<hr class="border-line"></hr>

			<div class="show-attachment">

			</div>
			<div class="showtable">
				<p id="no-email">To view an email, click on it.</p>
			</div>
		</div>
	 	<div class="tableinfo clearfix">
	 	</div>
 	</div>
</div>
</body>
</html>
