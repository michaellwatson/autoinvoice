<h3><?php 

if(strlen($start_number)<6){
	echo number_format(($start_number/100),2);
}else{
	echo substr($start_number, 0, 2) . '.' . substr($start_number, 2, 2) . '.' . substr($start_number, 4, 2);
}

?> <?php echo $field['adv_text'];?></h3>
<?php
if(isset($jsonString)){
	$array = json_decode(json_decode($jsonString, true), true);
}
else{
	$array = array();
}
echo '<table>';
echo '<tbody>';

$start_number++;
//print_r($array);

if($listing['ad_'.$field['adv_column']] !== ''){
	$json = json_decode($listing['ad_'.$field['adv_column']]);

	$row = 0;
	foreach ($json as $j){
		echo '<tr class="tbl_row" data-row="'.$row.'">';
		$column_counter = 0;
		$td = 0;
		
			echo '<td>';
			if(sizeof($json) > 1){
				//echo '<b>'.number_format(($start_number/100),2).'</b> '.$j;
				if (strpos($j, '{page_break}') !== false) {
					$parts = explode('{page_break}', $j);
		        	foreach ($parts as $part) {


		            	echo '<b>';
						if(strlen($start_number)<6){
		            		echo number_format(($start_number / 100), 2); 
		            	}else{
		            		echo substr($start_number, 0, 2) . '.' . substr($start_number, 2, 2) . '.' . substr($start_number, 4, 2);
		            	}
		            	echo '</b> ' . trim($part) . '<br>';
		            	$row = 0;
		            	//echo '</td></tr>';
		            	//echo '</table>';
						//echo '<table style="width:100%;">';
						//echo '<tbody>';
						echo '<div style="page-break-before:always;"></div>';
		        	}
		        }else{
		        	if(strlen($start_number)<6){
		        		echo '<b>'.number_format(($start_number/100),2).'</b> '.$j;
		        	}else{
		            	echo '<b>'.substr($start_number, 0, 2) . '.' . substr($start_number, 2, 2) . '.' . substr($start_number, 4, 2).'</b> '.$j;
		            }
		        }
			}else{
				echo $j;
			}

			echo '</td>';
		
		
		echo '</tr>';
		$row++;
		$tdx = 0;
		if(isset($break_after)){
			if($row > $break_after){
				$row = 0;
				echo '</table>';
				//echo '<table class="table_pdf" style="width:100%;page-break-before:always;">';
				//echo '<tbody class="'.$field['adv_column'].'_body">';
				echo '<table style="width:100%;page-break-before:always;">';
				echo '<tbody>';
			}
		}
		$start_number++;
	}
}

echo '</tbody>';
echo '</table>';