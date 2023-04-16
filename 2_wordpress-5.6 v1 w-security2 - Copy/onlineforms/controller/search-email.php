<?php
require_once 'connect.php';
include '../detect/user_agents.php';
$detect = new UserAgent();

if( !empty($_POST['searchval']) && !empty($_POST['cond']) ){
	$searchval = $_POST['searchval'];
	$cond = $_POST['cond'];
	$emails = $wpdb->get_results("SELECT * FROM formdatabase_emails WHERE (email_type = ".$cond.") AND (form_from LIKE '%$searchval%' OR form_content LIKE '%$searchval%' OR form_subject LIKE '%$searchval%') LIMIT 50");
}
else{
	echo "No selected mail(s) to delete.";
}
?>
	<table id="mytable">
	  	<?php
		  if( !empty($emails) ){
		  	foreach ($emails as $email) {?>
			  <tr class="<?php echo $email->status;?>">
				<td class="subject">
						<?php if($email->status == 'read'){?>
							 <label class="email_status" title="Mark as Unread" id="unread_msg" data-id="<?php echo $email->form_id;?>">
								 <a href="javascript:;">
									 <img src="images/circle-inactive.png">
								 </a>
							 </label>
						<?php }else{?>
							<label class="email_status" title="Mark as Read" id="read_msg" data-id="<?php echo $email->form_id;?>">
							    <a href="javascript:;">
								    <img src="images/circle-active.png">
							    </a>
						    	</label>
						<?php }?>
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
					    <?php if(!$detect->is_mobile('iOS') && !$detect->is_browser('Safari')){?>
						  <a href="javascript:;" id="printform" data-id="<?php echo $email->form_id;?>" title="Print">
						    	<img src="images/print-icon.png" alt="Print" />
						  </a>
					    <?php }?>
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
