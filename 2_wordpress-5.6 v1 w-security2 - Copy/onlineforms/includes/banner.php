<?php
	require_once 'controller/connect.php';

	if($page == 'Mobile Panel'){
		$container = 'mobile-container';
		$r_content = 'mobile-right-content';
		$l_content = 'mobile-left-content';
		$href = 'mobile-panel.php';
	}else{
		$container = 'container';
		$r_content = 'f-right';
		$l_content = 'f-left';
		$href = 'panel.php';
	}

?>

<!--Banner-->
<div id="banner">
	<div class="<?php echo $container ?>">
		<div class="<?php echo $l_content ?>">
			<div class="comp-logo">
				<a href="<?php echo $href ?>">
					<img class="logo" src="images/logo.png" alt="logo"/>
					<div class="title">
						<h1>FORM</h1>
						<h2>Database</h2>
					</div>
				</a>
			</div>
		</div>

		<div class="<?php echo $r_content ?>">
			<ul class="right-list">
				<li>
					<form id="searchform" method="post" action="">
						<input id="searchinput" type="text" name="form_search" placeholder="Enter keyword...">
						<button id="search" href="javascript:;"><img src="images/search-icon-white.png" alt="Search" /></button>
					</form>
				</li>
				<li id="trash-list"><a id="trash" href="javascript:;"><img src="images/trash-icon.png" alt="Trash" />Trash</a></li>
				<li id="inbox-list"><a id="inbox" href="javascript:;">Inbox</a></li>
				<?php if($page == 'Mobile Panel'){?>
					<li id="nav-toggle">
						<a class="nav-toggle-button">
							<i class="fa fa-navicon fa-2x" id="nav-menu" >
								&nbsp;&nbsp;&nbsp;&nbsp;
							</i>
						</a>
					</li>
				<?php }?>
			</ul>
		</div>
	</div>
</div>
<!--end banner-->
