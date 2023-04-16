<?php
set_include_path ('includes');
include 'details.php';
include 'header.php';

?>

<div id="main" class="clearfix">
	<div class="box clearfix">
		<div id="loginpage">
			<div class="head-title">
			<a href="index.php"><img src="images/logo.png"></a>
			<div class="title">
					<h1>FORM</h1>
					<h2>Database</h2>
				</div>
			</div>
			<h3 class="forgotpasstext">Forgot Password</h3>
			<form id="forgotform" action="" method="post">
				<input type="text" id="username" name="username" placeholder="Username"required>
				<input type="submit" id="forgotpass" name="forgotpass" value="Reset Password">
			</form>
			<p class="linktologin"><a href="login-page.php">« Back to Login</a></p>
			<div class="msg"></div>
			<div class="se-pre-con"></div>
		</div>
	</div>
</div>
</body>
</html>
