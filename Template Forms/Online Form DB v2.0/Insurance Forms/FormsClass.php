<?php
/**********************************************************/
/*            CODE DESIGN FOR PROWEAVERIANS               */
/*            CODE BY: DEVELOPERS TEAM                    */
/*            Created: NOVEMBER 24, 2009                  */
/*            Version: 1.0.4                              */
/*            Last Updated: May 21. 2018                  */
/**********************************************************/
class FormsClass {
	var $optMonth = array('January','February','March','April','May','June','July','August','September','October','November','December');

	//fields
	// ex. $input->fields('Total','text','Total','readonly="readonly" onkeypress="test" ondblclick="test"');
	function fields($name='',$class='',$id='',$attrib='') {
		$fldname = str_replace(' ', '_', $name);
		$value = '';
		if(isset($_SESSION[$fldname])) $value = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $value = $_POST[$fldname];
		$input = '<div class="field_holder animated_box"><input type="text" name="'.$fldname.'" class="'.$class.'" value="'.$value.'" id="'.$id.'" '.$attrib.'><span class = "animated_class"></span></div>';
		echo $input;
	}

	function label($name='', $required=''){
		$fldname = str_replace(' ', '_', $name);
		$value = '';
		if(isset($_SESSION[$fldname])) $value = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $value = $_POST[$fldname];
		$input = '<div class ="form_label"><label class = "text_uppercase"><strong>'.$name.' <span class = "required_filed">'.$required.'</span></strong></label></div>';
		echo $input;
	}

	 function masterselect($name='',$required='',$class='',$optName='',$id='',$rename='') {
        $n = '';
        $option = '';
        $strfldname = str_replace(' ', '_', $name);
        $fldname = str_replace('?', '', $strfldname);
		
		if($rename == ''){
			$strfldname = str_replace(' ', '_', $name);
			$fldname = str_replace('?', '', $strfldname);
		}else{
			$strfldname = str_replace(' ', '_', $rename);
			$fldname = str_replace('?', '', $strfldname);
		}

		$lblname = str_replace('_', ' ', $name);
        if(isset($_SESSION[$fldname])) $n = $_SESSION[$fldname];
        if(isset($_POST[$fldname])) $n = $_POST[$fldname];

		$inputlabel = '<div class ="form_label"><label class = "text_uppercase"><strong>'.$lblname.' <span class = "required_filed">'.$required.'</span></strong></label></div>';

        foreach($optName as $optVal){
            $cndtn = ($n == $optVal)? 'selected="selected"' : '';
            //$option .= '<option value="'.$optVal.'" '.$cndtn.'>'.$optVal.'</option>';

            if($optVal === $optName[0])
                $option .= '<option value="" '.$cndtn.'>'.$optVal.'</option>';
            else
                $option .= '<option value="'.$optVal.'" '.$cndtn.'>'.$optVal.'</option>';
        }
       $select = '<div class="select-field"><select name="'.$fldname.'" class="'.$class.'" id="'.$id.'" '.$attrib.'>'.$option.'</select></div>';
        echo $inputlabel.''.$select;
    }

	// @param field name, required, value, id, attrib, rows
	function masteradio($name='',$required='',$value='',$rename='',$id='',$attrib='',$rows=''){
		$n = '';
		$style = '';
		$brekz = 0;
		$count = 0;
		$strlblname = str_replace('?', '', $name);
		$fldname = str_replace(' ', '_', $strlblname);
		$lblname = str_replace('_', ' ', $name);
		
		if($rename == ''){
			$strfldname = str_replace(' ', '_', $name);
			$fldname = str_replace('?', '', $strfldname);
		}else{
			$strfldname = str_replace(' ', '_', $rename);
			$fldname = str_replace('?', '', $strfldname);
		}
		
		$radio = '<table class="radio" border="0" cellpadding="0" cellspacing="0"><tr>';
		if(isset($_SESSION[$fldname])) $n = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $n = $_POST[$fldname];

		if($rows == 1){
			$style = " style='width: 100%;'";
		}else if($rows == 2){
			$style = " style='width: 50%;'";
		}else if($rows == 3){
			$style = " style='width: 33%;'";
		}else if($rows == 4){
			$style = " style='width: 25%;'";
		}else if($rows == 5){
			$style = " style='width: 20%;'";
		}

		$inputlabel = '<div class ="form_label"><label class = "text_uppercase"><strong>'.$lblname.' <span class = "required_filed">'.$required.'</span></strong></label></div>';


		foreach($value as $radVal){
			$cndtn = ($n == $radVal)? 'checked="checked"' : '';
			if($brekz == $rows) {
				$radio .= '</tr><tr>';
				$brekz = 0;
			}
			$radio .= '<td'.$style.'><input type="radio" name="'.$fldname.'" value="'.$radVal.'" '.$cndtn.' id="'.$fldname.$count.'" '.$attrib.'>'.'<label for="'.$fldname.$count.'"style="font-weight:normal; color:#000;">'.$radVal.'</label></td>'."\n";
			$brekz++;
			$count++;
		}
		$radio .= "</tr></table>";
		echo $inputlabel.''.$radio;
	}

	// @param field name, required, class, replaceholder, rename, id, attrib, value
	function masterfield($name='',$required='' ,$class='',$replaceholder='', $rename='', $id='', $attrib='', $value=''){

		if($rename == ''){
			$strfldname = str_replace(' ', '_', $name);
			$fldname = str_replace('?', '', $strfldname);
		}else{
			$strfldname = str_replace(' ', '_', $rename);
			$fldname = str_replace('?', '', $strfldname);
		}

		$lblname = str_replace('_', ' ', $name);
		$strlblname = str_replace('?', '', $lblname);

		$strlblname = str_replace('?', '', $name);



		if($replaceholder == ''){
			$placeholder = "Enter ".strtolower(preg_replace('/\s+/', ' ',$strlblname)).' here';
		}else{
			$placeholder = $replaceholder;
		}

		$value = '';
		if(isset($_SESSION[$fldname])) $value = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $value = $_POST[$fldname];



		$inputlabel = '<div class ="form_label"><label class = "text_uppercase"><strong>'.$lblname.' <span class = "required_filed">'.$required.'</span></strong></label></div>';


		$inputfield = '<div class="field_holder animated_box"><input type="text" name="'.$fldname.'" class="'.$class.'" value="'.$value.'" id="'.$id.'" placeholder="'.trim($placeholder).'" '.$attrib.'><span class = "animated_class"></span></div>';

		echo $inputlabel.' '.$inputfield;

	}

	//textarea
	// ex. $input->textarea('Total','textarea','Total','readonly="readonly" onkeypress="test" ondblclick="test"','This is a textarea');
	function mastertextarea($name='',$required='' ,$class='',$replaceholder='', $rename='', $id='', $attrib='', $value=''){

		if($rename == ''){
			$fldname = str_replace(' ', '_', $name);
		}else{
			$fldname = str_replace(' ', '_', $rename);
		}

		$lblname = str_replace('_', ' ', $name);
		$strlblname = str_replace('?', '', $lblname);


		if($replaceholder == ''){
			$placeholder = "Enter ".strtolower(preg_replace('/\s+/', ' ',$strlblname)).' here';
		}else{
			$placeholder = $replaceholder;
		}

		if(isset($_SESSION[$fldname])) $value = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $value = $_POST[$fldname];


		$inputlabel = '<div class ="form_label"><label class = "text_uppercase"><strong>'.$lblname.' <span class = "required_filed">'.$required.'</span></strong></label></div>';

		$txtarea = '<div class="field_holder animated_box"><textarea type="text" name="'.$fldname.'" class="'.$class.'" value="'.$value.'" id="'.$id.'" placeholder="'.trim($placeholder).'" '.$attrib.'></textarea><span class = "animated_class"></span></div>';


		echo $inputlabel.''.$txtarea;
	}


	//masterdatepicker
	function masterdatepicker($name='',$required="", $id='', $replaceholder='', $attrib='') {
		$fldname = str_replace(' ', '_', $name);
		$lblname = str_replace('_', ' ', $name);
		$value = '';
		$errorVal = '';

		$lblname = str_replace('_', ' ', $name);
		$strlblname = str_replace('?', '', $lblname);

		if($replaceholder == ''){
			$placeholder = "Enter ".strtolower(preg_replace('/\s+/', ' ',$strlblname)).' here';
		}else{
			$placeholder = $replaceholder;
		}

		if(isset($_SESSION[$fldname])) $value = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $value = $_POST[$fldname];

		$inputlabel = '<div class ="form_label"><label class = "text_uppercase"><strong>'.$lblname.' <span class = "required_filed">'.$required.'</span></strong></label></div>';

		$input = '<div class="field_holder animated_box"><input type="text" name="'.$fldname.'" class=" form_field Date " value="'.$value.'" id="'.$id.'" '.$attrib.' readonly="readonly" placeholder="'.$placeholder.'"><span class = "animated_class"></span></div>';

		echo $inputlabel.''.$input;
	}

	//textarea
	// ex. $input->textarea('Total','textarea','Total','readonly="readonly" onkeypress="test" ondblclick="test"','This is a textarea');
	function textarea($name='',$class='',$id='',$attrib='',$value = '') {
		$fldname = str_replace(' ', '_', $name);
		if(isset($_SESSION[$fldname])) $value = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $value = $_POST[$fldname];
		$txtarea = '<div class="field_holder animated_box"><textarea name="'.$fldname.'" class="'.$class.'" id="'.$id.'" '.$attrib.'>'.$value.'</textarea><span class = "animated_class"></span></div>';
		echo $txtarea;
	}

	//select with script
    // ex. $input->select('Small_Box','select',$box,'Small_Box','onchange="getTotal();" onkeypress="test" ondblclick="test"');
    function select($name='',$class='',$optName='',$id='',$attrib='') {
        $n = '';
        $option = '';
        $fldname = str_replace(' ', '_', $name);
        if(isset($_SESSION[$fldname])) $n = $_SESSION[$fldname];
        if(isset($_POST[$fldname])) $n = $_POST[$fldname];
        foreach($optName as $optVal){
            $cndtn = ($n == $optVal)? 'selected="selected"' : '';
            //$option .= '<option value="'.$optVal.'" '.$cndtn.'>'.$optVal.'</option>';

            if($optVal === $optName[0])
                $option .= '<option value="" '.$cndtn.'>'.$optVal.'</option>';
            else
                $option .= '<option value="'.$optVal.'" '.$cndtn.'>'.$optVal.'</option>';
        }
         $select = '<div class="select-field"><select name="'.$fldname.'" class="'.$class.'" id="'.$id.'" '.$attrib.'>'.$option.'</select></div>';
        echo $select;
    }


	//radio
	// ex. $input->radio($input->radio('Example',array('Yes','No'),'Example','readonly="readonly" onkeypress="test" ondblclick="test"');
	function radio($name='',$value='',$id='',$attrib='',$rows=''){
		$n = '';
		$style = '';
		$brekz = 0;
		$count = 0;
		$fldname = str_replace(' ', '_', $name);


		$radio = '<table class="radio" border="0" cellpadding="0" cellspacing="0"><tr>';
		if(isset($_SESSION[$fldname])) $n = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $n = $_POST[$fldname];
		if($rows == 1){
			$style = "style='width: 100%;'";
		}else if($rows == 2){
			$style = " style='width: 50%;'";
		}else if($rows == 3){
			$style = " style='width: 33%;'";
		}else if($rows == 4){
			$style = " style='width: 25%;'";
		}

		foreach($value as $radVal){
			$cndtn = ($n == $radVal)? 'checked="checked"' : '';
			if($brekz == $rows) {
				$radio .= '</tr><tr>';
				$brekz = 0;
			}
			$radio .= '<td'.$style.'><input type="radio" name="'.$fldname.'" value="'.$radVal.'" '.$cndtn.' id="'.$fldname.$count.'" '.$attrib.'>'.'<label for="'.$fldname.$count.'"style="font-weight:normal; color:#000;">'.$radVal.'</label></td>'."\n";
			$brekz++;
			$count++;
		}
		$radio .= "</tr></table>";
		echo $radio;
	}

	function radioline($name='',$value='',$id='',$attrib='',$rows=''){
		$n = '';
		$style = '';
		$brekz = 0;
		$count = 0;
		$fldname = str_replace(' ', '_', $name);

		$radio = '<div class="radioLine form_box_col'.$rows.'">';
		if(isset($_SESSION[$fldname])) $n = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $n = $_POST[$fldname];
		if(empty($rows)){
			$rows = 4;
		}

		foreach($value as $radVal){
			$cndtn = ($n == $radVal)? 'checked="checked"' : '';
			if($brekz == $rows) {
				$radio .= '';
				$brekz = 0;
			}
			$radio .= '<div'.$style.'><input type="radio" name="'.$fldname.'" value="'.$radVal.'" '.$cndtn.' id="'.$fldname.$count.'" '.$attrib.'>'.'<label for="'.$fldname.$count.'"style="font-weight:normal; color:#000;">'.$radVal.'</label></div>'."\n";
			$brekz++;
			$count++;
		}
		$radio .= "</div>";
		echo $radio;
	}


	//checkbox
	// ex. $input->chkbox($are_you_licensed_in_the_state_of_state?',array('Yes','No'),'Example','readonly="readonly" onkeypress="test" ondblclick="test"');
	function chkbox($name='',$Val='',$id='',$attrib='',$rows=''){
		$fldname = str_replace(' ', '_', $name);
		$ctr = 1;
		$brekz = 0;
		$chckbox = '<table class="checkbox-table" border="0" cellpadding="0" cellspacing="0" style="margin-top:6px; width: 100%;"><tr>';
		if(empty($rows)){
			$rows = 4;
		}
		foreach($Val as $chckVal){
			$cndtn = '';
			if(isset($_SESSION[$fldname.'_'.$ctr]))
				$cndtn = ($_SESSION[$fldname.'_'.$ctr] == $chckVal)? 'checked="checked"' : '';
			if(isset($_POST[$fldname.'_'.$ctr]))
				$cndtn = ($_POST[$fldname.'_'.$ctr] == $chckVal)? 'checked="checked"' : '';

			if($brekz == $rows) {
				$chckbox .= '</tr><tr>';
				$brekz = 0;
			}
			$chckbox .= '<td><input type="checkbox" class="wskCheckbox" name="'.$fldname.'_'.$ctr.'" value="'.$chckVal.'" '.$cndtn.' id="'.$fldname.'_'.$ctr.'" '.$attrib.'/><label class="wskLabel" for="'.$fldname.'_'.$ctr.'">'.$chckVal.'</label></td>'."\n";
			$brekz++;
			$ctr++;
		}
		$chckbox .= "</tr></table>";
		echo $chckbox;
	}



	//buttons
	//ex. $input->buttons('submit','submit','Submit','','Submit','onchange="test" onkeypress="test" ondblclick="test"');
	function buttons($type='',$name='',$value='',$class='',$id='',$attrib='') {
		$button = '<input type="'.$type.'" name="'.$name.'" class="'.$class.'" value="'.$value.'" id="'.$id.'" '.$attrib.'>';
		echo $button;
	}

	//select option for months $input->selectMonth('Months','width: 80px; font-size:11px;');
	function selectMonth($name='',$class='') {
		$cndtn = '';
		$fldname = str_replace(' ', '_', $name);
		$curMon = date('F',time());
		$option = '<option value="'.$curMon.'">'.$curMon.'</option>';

		foreach($this->optMonth as $optkey => $optVal){
			if(isset($_SESSION[$fldname]))
				$cndtn = ($_SESSION[$fldname] == $optVal)? 'selected="selected"' : '';
			if(isset($_POST[$fldname]))
				$cndtn = ($_POST[$fldname] == $optVal)? 'selected="selected"' : '';
			$option .= '<option value="'.$optVal.'" '.$cndtn.'>'.$optVal.'</option>';
		}
		$selectMonth = '<select name="'.$fldname.'" class="'.$class.'">'.$option.'</select>';
		echo $selectMonth;
	}

	//select option for days $input->selectDays('Day_Birth','',array(30));
	function selectDays($name='',$class='') {
		$cndtn = '';
		$fldname = str_replace(' ', '_', $name);
		$numdays = array(date('t'));
		$curMon = date('F',time());
		$curDay = date('j',time());
		$optDays = '<option value="'.$curDay.'">'.$curDay.'</option>';
		foreach($numdays as $optkey => $optVal){
			if($optkey == $curMon){
				for($days=1;$days<=$optVal;$days++){
					if(isset($_SESSION[$fldname]))
						$cndtn = ($_SESSION[$fldname] == $days)? 'selected="selected"' : '';
					if(isset($_POST[$fldname]))
						$cndtn = ($_POST[$fldname] == $days)? 'selected="selected"' : '';
					if($days<=9)
						$days = 0 . $days;
						$optDays .= '<option value="'.$days.'" '.$cndtn.'>'.$days.'</option>';
				}
			}
		}
		$selectDays = '<select name="'.$fldname.'" class="'.$class.'">'.$optDays.'</select>';
		echo $selectDays;
	}

	//select option for years
	function selectYears($name='',$class='',$start,$upto) {
		$cndtn = '';
		$fldname = str_replace(' ', '_', $name);
		$curYr = date('Y',time());
		$optYears = '<option value="'.$curYr.'">'.$curYr.'</option>';
		for($year=$start;$year<=$upto;$year++){
			if(isset($_SESSION[$fldname]))
				$cndtn = ($_SESSION[$fldname] == $year)? 'selected="selected"' : '';
			if(isset($_POST[$fldname]))
				$cndtn = ($_POST[$fldname] == $year)? 'selected="selected"' : '';
			$optYears .= '<option value="'.$year.'" '.$cndtn.'>'.$year.'</option>';
		}
		$selectYears = '<select name="'.$fldname.'" class="'.$class.'">'.$optYears.'</select>';
		echo $selectYears;
	}

	//file
	function files($name='', $class='') {
		$fldname = str_replace(' ', '_', $name);
		$file = '<input name="'.$fldname.'" type="file" class="'.$class.'"/>';
		echo $file;
	}


	//datepicker
	function datepicker($name='',$id='',$attrib='') {
		$fldname = str_replace(' ', '_', $name);
		$value = '';
		$errorVal = '';
		if(isset($_SESSION[$fldname])) $value = $_SESSION[$fldname];
		if(isset($_POST[$fldname])) $value = $_POST[$fldname];

		$input = '<div class="field_holder animated_box"><input type="text" name="'.$fldname.'" class=" form_field Date " value="'.$value.'" id="'.$id.'" '.$attrib.' readonly="readonly"><span class = "animated_class"></span></div>';

		echo $input;
	}

	// input date today only
	function datetoday($name='',$id='',$attrib='') {
		$fldname = str_replace(' ', '_', $name);
		$value = date("F j, o");
		$errorVal = '';
		$input = '<div class="field_holder animated_box"><input type="text" name="'.$fldname.'" class="form_field" value="'.$value.'" id="'.$id.'" '.$attrib.' readonly="readonly"><span class = "animated_class" ></span></div>';
		echo $input;
	}

	// input date today only
	function amount($name='',$id='',$attrib='') {
		$fldname = str_replace(' ', '_', $name);
		$errorVal = '';

		$input = '<div class="field_holder animated_box"> <i id="icon" class="fas fa-dollar-sign "></i> <input type="text" name="'.$fldname.'" class="form_field amount" id="'.$id.'" '.$attrib.'><span class = "animated_class" ></span></div>';

		echo $input;
	}


	function checktxt($name='',$value='',$text='') {
		$fldname = str_replace(' ', '_', $name);
		$errorVal = '';
		$theID = str_replace('_', ' ', $name);

		$input =  '<input class="wskCheckbox" name="'.$name.'" value="'.$value.'" id="'.$theID.'" type="checkbox"><label class="wskLabel" for="'.$theID.'"><span class="wskCircle"></span>'.$text.'</label>';

		echo $input;
	}

	function radiotxt($name='',$value='',$text='') {
		$fldname = str_replace(' ', '_', $name);
		$errorVal = '';
		$theID = str_replace('_', ' ', $name);

		$input =  '<input type="radio" name="'.$name.'" value="'.$value.'" id="'.$theID.'">'.'<label for="'.$theID.'"style="font-weight:normal; color:#000;">'.$text.'</label>';

		echo $input;
	}

	// @param field name, required, class, replaceholder, rename, id, attrib, value
	function masterfieldicon($icontxt='', $name='',$required='' ,$class='',$replaceholder='', $rename='', $id='', $attrib='', $value=''){

			if($rename == ''){
					$fldname = str_replace(' ', '_', $name);
			}else{
					$fldname = str_replace(' ', '_', $rename);
			}

			$lblname = str_replace('_', ' ', $name);
			$strlblname = str_replace('?', '', $lblname);

			if($replaceholder == ''){
					$placeholder = "Enter ".strtolower(preg_replace('/\s+/', ' ',$strlblname)).' here';
			}else{
					$placeholder = $replaceholder;
			}

			$value = '';
			if(isset($_SESSION[$fldname])) $value = $_SESSION[$fldname];
			if(isset($_POST[$fldname])) $value = $_POST[$fldname];

			$inputfield = '<div class="field_holder animated_box"> <i id="icon" class="fas">'.$icontxt.'</i> <input type="text" name="'.$fldname.'" class="'.$class.' fldicon" value="'.$value.'" id="'.$id.'" placeholder="'.trim($placeholder).'" '.$attrib.'><span class = "animated_class"></span></div>';

			 echo $inputfield;
	}

	function info($name=''){
		$input = '<div class="info_heading"><i class="fas fa-info"></i> '. $name .'<input type="hidden" value=":" name="'.$name.'"></div>';
		echo $input;
	}

}
?>
