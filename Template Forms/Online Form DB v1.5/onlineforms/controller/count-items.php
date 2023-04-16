<?php 
require_once 'connect.php';
if( isset($_POST['emailtype']) ){

	$emailtype = $_POST['emailtype'];

	$totalemail = $wpdb->get_var('SELECT COUNT(*) FROM formdatabase_emails WHERE email_type='.$emailtype.'');
	// $totalemail = mysqli_num_rows($qrytotalemail);

	$perpage = 50;
	$startpage= 1;
	$counter = $wpdb->get_var('SELECT COUNT(*) FROM formdatabase_emails WHERE email_type='.$emailtype.' LIMIT '.$perpage.' ');
	// $counter = mysqli_num_rows($result);
	if($totalemail > $perpage){
		$currItems = $perpage;
	}elseif($counter == $perpage){
		$currItems = $counter;
	}elseif($totalemail == 0){
		$startpage = $currItems= 0;
	}else{
		$currItems = $counter;
	}

	if($totalemail <= $perpage){
		$r['message'] = '<p>
		 <input id="firstpage" type="button" disabled>
		 <input id="previouspage" type="button" disabled>
		 <span>'.$startpage.' - '.$currItems.' of '.$totalemail.'</span>
		 <input id="nextpage" type="button" disabled>
		 <input id="lastpage" type="button" disabled>
		 </p>';
	}else{
	$r['message'] = '<p>
		 <input id="firstpage" type="button" disabled>
		 <input id="previouspage" type="button" disabled>
		 <span>'.$startpage.' - '.$currItems.' of '.$totalemail.'</span>
		 <input id="nextpage" type="button">
		 <input id="lastpage" type="button">
		 </p>';
	 }
	 echo json_encode($r);
}
?>
