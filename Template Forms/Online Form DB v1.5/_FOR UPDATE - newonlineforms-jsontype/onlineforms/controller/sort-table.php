<?php
require_once 'connect.php';
if( !empty($_POST['sort']) ){
	$order = $_POST['order'];
	$sorter = $_POST['sort'];
	$cond = $_POST['cond'];
	$emails = $wpdb->get_results('SELECT * FROM formdatabase_emails WHERE email_type = '.$cond.' ORDER BY '.$sorter.' '.$order.' LIMIT 50');
	//print_r($result);
	
}
?>
<table id="mytable">
		  <?php 
			  if( !empty($emails) ){
			  	foreach ($emails as $email) {?>
				  <tr class="<?php echo $email->status;?>">
				  	<td class="tdcheckbox"><input class="checkbox" type="checkbox" name="checklist[]" value="<?php echo $email->form_id;?>"></td>
				    <td><?php echo $email->form_from;?></td>
				    <td class="tdcheckbox">
				    	<?php if( !empty($email->attachments) ){ ?>
				    		<img class="attachment" alt="attachment" src="images/attachment-icon.png" width="14" height="14"/>
						<?php }?>
				    </td>
				    <td><?php echo $email->form_subject;?></td>
				    <td><?php  
				  	  		echo date('M. d, Y h:i:s', strtotime($email->date_sent));?></td>
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