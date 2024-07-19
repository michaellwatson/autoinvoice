<?php
	foreach ($ptPrefillsData->json_string_decoded as $key => $value){
		$uniqueNumForColorPick = _generateRandomNumber(8);
		if($key == 0){
?>
			<tr class="tbl_row" data-row="<?php echo $key; ?>">
				<?php
					foreach ($value as $key1 => $value1) {
				?>
						<td>
							<input type="text" class="form-control <?php echo $ptAdvertFieldsRecord['adv_column'].'_field'; ?>" 
								name="<?php echo $ptAdvertFieldsRecord['adv_column'].'_row_'.$key.'_column'.$key1.'[]'; ?>" 
								value="<?php echo $value1; ?>">
						</td>
				<?php
						$td = $key1;
					}
				?>
				<td>
					<i class="fa fa-minus"></i>
				</td>
				<td>
					<button type="button" class="btn btn-primary mb-1 <?php echo $ptAdvertFieldsRecord['adv_column']."_enableDisableColorPick"; ?>" data-enable_disable_colorpick="0" data-unique_num_for_colorpick="<?php echo $uniqueNumForColorPick; ?>">Color Pick On</button>
					<input class="clsColorPicker d-none" id="<?php echo "colorPickerSelector_".$ptAdvertFieldsRecord['adv_column']."_".$uniqueNumForColorPick; ?>" value="">
				</td>
			</tr>
<?php
		}
		else{
?>
			<tr class="tbl_row" data-row="<?php echo $key; ?>">
				<?php
					$tempTd = $td + 1;
					foreach ($value as $key1 => $value1) {
				?>
						<td>
							<input type="text" class="form-control <?php echo $ptAdvertFieldsRecord['adv_column'].'_field'; ?>" 
								name="<?php echo 'row_'.$key.'_column'.$tempTd++.'[]'; ?>" 
								value="<?php echo $value1; ?>">
						</td>
				<?php
					}
				?>
				<td>
					<i class="fa fa-times <?php echo $ptAdvertFieldsRecord['adv_column'].'_remove'; ?>"></i>
				</td>
				<td>
					<button type="button" class="btn btn-primary mb-1 <?php echo $ptAdvertFieldsRecord['adv_column']."_enableDisableColorPick"; ?>" data-enable_disable_colorpick="0" data-unique_num_for_colorpick="<?php echo $uniqueNumForColorPick; ?>">Color Pick On</button>
					<input class="clsColorPicker d-none" id="<?php echo "colorPickerSelector_".$ptAdvertFieldsRecord['adv_column']."_".$uniqueNumForColorPick; ?>" value="">
				</td>
			</tr>
<?php
		}
	}
?>