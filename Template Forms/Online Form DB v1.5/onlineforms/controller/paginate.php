<?php
require_once 'connect.php';
if( isset($_POST['start']) && isset($_POST['limit']) && isset($_POST['email_type']) && isset($_POST['order']) && isset($_POST['order'])){
	$start = $_POST['start'];
	$limit = $_POST['limit'];
	$emailtype = $_POST['email_type'];
	$sorter = $_POST['sorter'];
	$order = $_POST['order'];
	/*$orderby = ($order == 2)? 'date_deleted':'date_sent';*/
	$start--;
	$emails = $wpdb->get_results('SELECT * FROM formdatabase_emails WHERE email_type = '.$emailtype.'
		ORDER BY '.$sorter.' '.$order.' LIMIT '.$start.','.$limit.'');
}
?>
	<table id="mytable">
		  <?php
			  if( !empty($emails) ){
			  	foreach ($emails as $email) {?>
				  <tr class="<?php echo $email->status;?>">
					  <td class="subject">
   					    <label>&nbsp;</label>
   					    <div class="subject-inline">
   						    <span><?php echo $email->form_from;?></span>
   						    <span>
   							    <?php if( !empty($email->attachments) ){ ?>
   		    				    		<img class="attachment" alt="attachment" src="images/attachment-icon.png" width="14" height="14"/>
   		    						<?php }?>
   								<?php echo (strlen($email->form_subject)>25)?substr($email->form_subject, 0, 25).'...': $email->form_subject;?>
   							</span>
   						</div>
   				    </td>
   				    <td class="table-date">
   					    <span>
   					    	<?php
   					    		$year = date('Y');
   					    		$month_date = date('M d');

   					    		if(date('Y', strtotime($email->date_sent)) == $year){

   					    			if(date('M d', strtotime($email->date_sent)) != $month_date){
   					    				echo date('d M', strtotime($email->date_sent));
   					    			}else{
   									echo date('H:i', strtotime($email->date_sent));
   					    			}

   					    		}else{
   					    			echo date('m/d/Y', strtotime($email->date_sent));
   					    		}
   					    	?>
   					    </span>
   					    <span>
   						    <a href="javascript:;" id="printform" data-id="<?php echo $email->form_id;?>" title="Print">
   						    	<img src="images/print-icon.png" alt="Print" />
   						    </a>
   						    <a href="javascript:;" id="delete-email" data-id="<?php echo $email->form_id;?>" title="Delete">
   						    	<img src="images/delete-icon.png" alt="Delete" />
   						    </a>
   					    </span>
   				    </td>
   				    <td style="display:none"><input type="text" value="<?php echo $email->form_id;?>" ></td>
				  </tr>
			  <?php
			  	}
			  }else{
			  	echo "<tr class='empty'>";
				echo  "<td><span>Empty</span></td>";
			  }
		  ?>

	</table>
