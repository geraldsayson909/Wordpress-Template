<?php
require_once 'connect.php';

include '../detect/user_agents.php';
$detect = new UserAgent();

$emails = $wpdb->get_results('SELECT * FROM formdatabase_emails WHERE email_type = "inbox"  ORDER BY date_sent DESC LIMIT 50');



	$r['message'] = '<div style="background: #55B3E8; color: #fff; font-weight: bold; padding: 8px;">Online Forms List</div>
	<table id="mytable">';		  
			  if( !empty($emails) ){
			  	foreach ($emails as $email) {
				  $r['message'] .= '<tr class="'.$email->status.'">
				    <td class="subject">';
						if($email->status == 'read'){
							$r['message'] .= '<label class="email_status" title="Mark as New" id="unread_msg" data-id="'.$email->form_id.'">
								<a href="javascript:;">
									<img src="images/circle-inactive.png">
								</a>
							</label>';
						}else{
							$r['message'] .= '<label class="email_status" title="Mark as Read" id="read_msg" data-id="<?php echo $email->form_id;?>">
								<a href="javascript:;">
									<img src="images/circle-active.png">
								</a>
							</label>';
						}
					   $r['message'] .= '<div class="subject-inline">
						    <span>'.$email->form_from.'</span>
						    <span>';
							    if( !empty($email->attachments) ){
		    				    		$r['message'] .= '<img class="attachment" alt="attachment" src="images/attachment-icon.png" width="14" height="14"/>';
		    						}
								$r['message'] .= (strlen($email->form_subject)>25)?substr($email->form_subject, 0, 25).'...': $email->form_subject;
							$r['message'] .= '</span>
						</div>
				    </td>
				    <td class="table-date">
					    <span>';
					    		$year = date('Y');
					    		$month_date = date('M d');

					    		if(date('Y', strtotime($email->date_sent)) == $year){

					    			if(date('M d', strtotime($email->date_sent)) != $month_date){
					    				$r['message'] .= date('d M', strtotime($email->date_sent));
					    			}else{
											$r['message'] .= date('H:i', strtotime($email->date_sent));
					    			}

					    		}else{
					    			$r['message'] .= date('m/d/Y', strtotime($email->date_sent));
					    		}
					    $r['message'] .= '</span>
					    <span>';
						    if(!$detect->is_mobile('iOS') && !$detect->is_browser('Safari')){
							    $r['message'] .= '<a href="javascript:;" id="printform" data-id="'.$email->form_id.'" title="Print">
							    	<img src="images/print-icon.png" alt="Print" />
							    </a>';
						    }
						    $r['message'] .= '<a href="javascript:;" id="delete-email" data-id="'.$email->form_id.'" title="Delete">
						    	<img src="images/delete-icon.png" alt="Delete" />
						    </a>
					    </span>
				    </td>
				    <td style="display:none"><input type="text" value="'.$email->form_id.'" ></td>
				  </tr>';
			  	}
			  }else{
			  	$r['message'] .= "<tr class='empty'>";
				 $r['message'] .= "<td><span>Empty</span></td>";
			  }

	$r['message'] .= '</table>';
	echo json_encode($r);
	?>
