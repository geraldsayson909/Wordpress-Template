<?php
	@session_start();

	set_include_path ('includes');
	include 'details.php';
	include 'header.php';
	include 'modals.php';
	$a = 'current';
	include 'modals.php';
?>

<div id="main" class="clearfix">
	<div class="">
		<div class="back-btn" id="back-panel">
			<a href="javascript:;" id="go-panel"><img src="images/prev-btn.png" alt="Back"/>Go to Panel</a>
		</div>
		<div class="form_pane">
			<div class="print" style="text-align:right">
				<a href="javascript:;" id="printform" data-id="<?php echo substr($_GET['token'], -2)?>" title="Print">
					<img src="images/print-icon.png" alt="Print" style="vertical-align:top"> Print
				</a>
			</div>
			<div class="show-subject">
			</div>
			<hr class="border-line"></hr>
			<div class="show-attachment">
			</div>
			<div class="showtable">
				<div class="loader"></div>
			</div>
		</div>
 	</div>
</div>
</body>
</html>
