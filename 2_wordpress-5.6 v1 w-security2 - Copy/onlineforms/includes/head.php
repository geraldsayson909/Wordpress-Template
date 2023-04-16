<?php
	if($page == 'Mobile Panel'){
		$class = "page-nav dropdown";
		$id = 'header-mobile';
	}
	else {
		$class = 'container';
		$id = 'header';
		$container = '';
	}

?>

<header id="<?php echo $id?>" class="<?php echo $container?>">
	<div class="<?php echo $class?>">
		<ul>
			<li><a href="javascript:;"><?php echo ucfirst($_SESSION['user_login'])?></a></li>
			<li><a href="javascript:;" class="password-wrapper">Change Password</a></li>
			<li><a href="controller/logout.php" class="logout-wrapper">Logout</a></li>
		</ul>
	</div>
</header>
