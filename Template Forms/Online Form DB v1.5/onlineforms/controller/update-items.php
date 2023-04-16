<?php 
require_once 'connect.php';
if( isset($_POST['emailtype']) && isset($_POST['start']) ){

	$emailtype = $_POST['emailtype'];

	$totalemail = $wpdb->get_var('SELECT COUNT(*) FROM formdatabase_emails WHERE email_type='.$emailtype.'');
	// $totalemail = mysqli_num_rows($qrytotalemail);

	$startpage = $_POST['start'];
	$counter = $wpdb->get_var('SELECT COUNT(*) FROM formdatabase_emails WHERE email_type='.$emailtype.' LIMIT 50 ');
	// $counter = mysqli_num_rows($result);

	$currItems = ($startpage - 1) + $counter;
	if($startpage == 1){
		echo '<p>
		 <input id="firstpage" type="button" disabled>
		 <input id="previouspage" type="button" disabled>
		 <span>'.$startpage.' - '.$currItems.' of '.$totalemail.'</span>
		 <input id="nextpage" type="button">
		 <input id="lastpage" type="button">
		 </p>';
		 exit;
	}
	
	if($totalemail <= $currItems) {
		$currItems = $totalemail;
		echo '<p>
		 <input id="firstpage" type="button">
		 <input id="previouspage" type="button">
		 <span>'.$startpage.' - '.$currItems.' of '.$totalemail.'</span>
		 <input id="nextpage" type="button" disabled>
		 <input id="lastpage" type="button" disabled>
		 </p>';
		 exit;
	}
	
	echo '<p>
		 <input id="firstpage" type="button">
		 <input id="previouspage" type="button">
		 <span>'.$startpage.' - '.$currItems.' of '.$totalemail.'</span>
		 <input id="nextpage" type="button">
		 <input id="lastpage" type="button">
		 </p>';
}
?>
