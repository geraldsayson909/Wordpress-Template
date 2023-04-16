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
			<form id="createUserform" action="" method="post">
				<input type="text" id="username" name="username" placeholder="Username" required>
				<input type="password" id="password" name="password" placeholder="Password" required>
				<input type="submit" id="createUserbtn" name="login" value="Create Login">
			</form>
			<p class="forgot_pass">For registered users, <a href="login-page.php">Login.</a></p>
			<div class="msg"></div>
			<div class="se-pre-con"></div>
		</div>
	</div>
</div>
</body>
</html>
