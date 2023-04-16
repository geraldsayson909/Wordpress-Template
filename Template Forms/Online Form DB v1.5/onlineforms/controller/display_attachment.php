<?php 

require_once 'connect.php';

if(isset($_POST['id'])){

	$id = $_POST['id'];

	$attachment = $wpdb->get_row('SELECT * FROM formdatabase_emails WHERE form_id ='.$id);



	if(!empty($attachment->attachments)){		

		$data = '<div class="attachment-wrapper">

		<p><img class="attachment" alt="attachment" src="images/attachment-icon.png" width="16" height="16"/>Attachments: ';

		$arr = explode('***', $attachment->attachments);

		$lastkey = end(array_keys($arr));

		foreach ($arr as $key => $value) {

			if($key != $lastkey){

				$data .= '<a href="attachments/'.$value.'" download> '.$value.'</a> , ';

			}else{

				$data .= '<a href="attachments/'.$value.'" download>'.$value.'</a>';

			}

		}

		$data.='</p></div>';

		echo json_encode(array('message' => $data));		

	}else{
		echo json_encode(array('message' => ''));
	}	

}

?>

