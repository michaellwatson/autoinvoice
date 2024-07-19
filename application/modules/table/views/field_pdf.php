<?php

// Input JSON string
$jsonString = $field['adv_config'];

// Decode JSON string into an array
$array = json_decode(json_decode($jsonString, true), true);
// $col_num = sizeof($array);
// $per = 100/$col_num;
if(is_array($array)){
    $col_num = sizeof($array);
    $per = 100 / $col_num;
}
else{
    $col_num = 0;
    $per = 0;
}
// Generate table column headers
$custom_header_color = false;

if(isset($header_color) && is_array($header_color)){
	$custom_header_color = true;
}
echo '<table class="table table_pdf" style="width:100%;">';
if(!$custom_header_color){
	echo '<thead class="thead-dark">';
}else{
	echo '<thead>';
}

echo '<tr>';
$offset = 0;
if(isset($field['column_widths']) && is_array($field['column_widths'])){
//if(is_array($field['column_widths'])){
	foreach($field['column_widths'] as $f){
		$offset += $f;
	}
	$diff = 100 - $offset;
	$add = $diff/sizeof($field['column_widths']); 
}

$column_counter = 0;
$column_counterx = 0;

foreach ($array as $value) {
	if(isset($field['column_widths']) && is_array($field['column_widths'])){
	// if(is_array($field['column_widths'])){

		if(!$custom_header_color){

			echo '<th style="width:'.$field['column_widths'][$column_counter]+$add.'%;text-align:left;vertical-align: top;font-size:10px">' . $value .'</th>';
		
		}else{

			echo '<th style="width:'.$field['column_widths'][$column_counter]+$add.'%;text-align:left;vertical-align: top;font-size:10px;background-color:'.$header_color[$column_counterx].'">' . $value .'</th>';

		}
		
		//echo '<th style="width:'.$field['column_widths'][$column_counter]+$add.'%;text-align:left;vertical-align: top;font-size:10px">' . $value .'</th>';
	  	$column_counter++;

	}else{

		if(!$custom_header_color){

			echo '<th style="width:'.$per.'%;text-align:left;vertical-align: top;font-size:10px;">' . $value . '</th>';

		}else{

			echo '<th style="width:'.$per.'%;text-align:left;vertical-align: top;font-size:10px;background-color:'.$header_color[$column_counterx].'">' . $value . '</th>';

		}
		
	}
	$column_counterx++;
}


echo '</tr>';
echo '</thead>';
echo '<tbody class="'.$field['adv_column'].'_body">';
$row = 0;



if($listing['ad_'.$field['adv_column']] !== ''){
	$json = json_decode($listing['ad_'.$field['adv_column']]);
	//print_r($json);
	foreach ($json as $key => $j){
		//if(isset($colorsArray) && $colorsArray){
		if(isset($colorsArray) && is_array($colorsArray) && is_array($colorsArray[$key])){
			$rowBgColor = $colorsArray[$key]['color'];
			$trBackgroundColor = "background-color: ".$rowBgColor;
		}
		else{
			$trBackgroundColor = "";
		}
		echo '<tr class="tbl_row" data-row="'.$row.'" style="'.$trBackgroundColor.'">';
		$column_counter = 0;
		$td = 0;
		foreach ($j as $p) {
			if(isset($field['column_widths']) && is_array($field['column_widths'])){
			//if(is_array($field['column_widths'])){
	  			echo '<td style="width:'.$field['column_widths'][$column_counter]+$add.'%;text-align:left;vertical-align: top;font-size:10px">'.str_replace('$',',',$p).'</td>';
	  		}else{
	  			echo '<td style="width:'.$per.'%;text-align:left;vertical-align: top;font-size:10px">'.str_replace('$',',',$p).'</td>';
	  		}
	  		$column_counter++;
	  		$td++;
		}
		while($td < $column_counterx){
			echo '<td style="width:'.$per.'%;text-align:left;vertical-align: top;font-size:10px">-</td>';
	  		$td++;
		}

		echo '</tr>';
		$row++;
		$tdx = 0;
		if(isset($break_after)){
			if($row > $break_after){
				$row = 0;
				echo '</table>';
				echo '<table class="table table_pdf" style="width:100%;page-break-before:always;">';
				echo '<thead class="thead-dark">';
				echo '<tr>';
				foreach ($array as $value) {

					if(isset($field['column_widths']) && is_array($field['column_widths'])){
					//if(is_array($field['column_widths'])){
	  					echo '<td style="width:'.$field['column_widths'][$column_counter]+$add.'%;text-align:left;vertical-align: top;font-size:10px">'.str_replace('$',',',$value).'</td>';
	  				}else{
	  					echo '<td style="width:'.$per.'%;text-align:left;vertical-align: top;font-size:10px">'.str_replace('$',',',$value).'</td>';
	  				}
	  				//$column_counter++;
				  	//echo '<th style="width:'.$per.'%;text-align:left;vertical-align: top;font-size:10px;">' . $value . '</th>';
					$tdx++;
				}
				while($tdx < $column_counterx){
					echo '<td style="width:'.$per.'%;text-align:left;vertical-align: top;font-size:10px">-</td>';
			  		$td++;
				}
				echo '</tr>';
				echo '</thead>';
				echo '<tbody class="'.$field['adv_column'].'_body">';
			}
		}
	}
}

echo '</tbody>';
echo '</table>';