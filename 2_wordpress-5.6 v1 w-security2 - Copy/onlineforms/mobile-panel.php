<?php
	@session_start();
	if( empty($_SESSION['user_login']) && $_SESSION['login_status'] != 1 ){
		header("Location: index.php");
		die();
	}
	set_include_path ('includes');
	include 'details.php';
	include 'header.php';
	include 'modals.php';
	$a = 'current';
	include 'banner.php';
	include 'head.php';
	include 'modals.php';

?>

<div id="main" class="clearfix">
	<div class="mobile-container">

	 	<div class="email-table">

	 	</div>

		<div class="back-btn">
			<a href="javascript:;" id="back"><img src="images/prev-btn.png" alt="Back"/>Back</a>
		</div>

		<div class="form_pane">
			<div class="show-subject">

			</div>
			<hr class="border-line"></hr>

			<div class="show-attachment">

			</div>
			<div class="showtable">
				<p id="no-email">To view an email, click on it.</p>
			</div>
		</div>
	 	<div class="tableinfo clearfix mobile-tableinfo">
	 	</div>
 	</div>
</div>
</body>
</html>
