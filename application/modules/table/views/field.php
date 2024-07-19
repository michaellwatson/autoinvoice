<div class="container">
	<?php
		// if($ptPrefillsData){
		if(1){
			echo $htmlPrefillTable;
		}
	?>
    <div class="row" id="id_<?php echo $field['adv_id']?>">
    	<div class="col-md-6 text-left">
    		 <?php echo $field['adv_text']?>
            <?php if($field['adv_required']==1){ ?>
            <span style="color:red;">*</span>
            <?php } ?>:&nbsp;
    	</div>
    	<div class="col-md-6 text-right d-flex">
    		<?php 
			// Input JSON string
			$jsonString = $field['adv_config'];
			// Decode JSON string into an array
			$array = json_decode(json_decode($jsonString, true), true);

        	?>
        	<div class="col-md-3 offset-md-4">
        		<input type="text" class="form-control" id="<?php echo $field['adv_column'];?>_repeatafter" data-id="<?php echo $field['adv_id'];?>" aria-describedby="repeatafter" placeholder="Repeat after" style="margin-bottom:5px;">
        	</div>
        	<div class="col-md-1">
        		<i class="fa fa-pencil big-icon <?php echo $field['adv_column'];?>_column_config" data-columns="<?php echo sizeof($array);?>" data-table="<?php echo $field['adv_column'].'_direct';?>" data-field_id="<?php echo $field['adv_id'];?>"></i>
        	</div>
        	<div class="col-md-1">
        		<i class="fa fa-table <?php echo $field['adv_column'];?>_header_config table_change_headers big-icon"  data-columns="<?php echo sizeof($array);?>" data-table="<?php echo $field['adv_column'].'_direct';?>" data-field_id="<?php echo $field['adv_id'];?>"></i>
        	</div>
        	<div class="col-md-3">
     			<input type="checkbox" name="<?php echo $field['adv_column'];?>_direct" id="<?php echo $field['adv_column'];?>_direct" >
     		</div>
    	</div>
        
        <?php if(($field['adv_post_text']!='')||( isset($adv_config['add']) && (bool)$adv_config['add'] )){?>
        	<div class="col-md-11 col-sm-11">
        <?php }else{?>
        	<div class="col-md-12 col-sm-12">
        <?php } ?>
                  
        	<?php
			//print_r($array);
			// Generate table column headers
			echo '<table class="table '.$field['adv_column'].'_direct table_field" id="'.$field['adv_column'].'_direct_table">';
			echo '<thead class="thead-dark">';
			echo '<tr>';
			$column_counter = 0;
			foreach ($array as $value) {
				if(is_array(@$field['column_widths'])){

					echo '<th style="width:'.$field['column_widths'][$column_counter].'%;"><input type="text" style="max-width:35px;" value="'.$field['column_widths'][$column_counter].'"><br>' . $value . '</th>';
				  	$column_counter++;

				}else{
					echo '<th><input type="text" style="max-width:35px;"><br>' . $value . '</th>';
				}
			}
			echo '<th style="width:1%;"></th>';
			echo '<th style="width:1%;">Row Color</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody class="'.$field['adv_column'].'_body">';
			$row = 0;
			
			
			if((isset($listing['ad_'.$field['adv_column']])) && ($listing['ad_'.$field['adv_column']] !== '')){
				$json = json_decode($listing['ad_'.$field['adv_column']]);
				//print_r($json);
				foreach ($json as $key => $j){
					// $uniqueNumForColorPick = _generateRandomNumber(8);

					echo '<tr class="tbl_row" data-row="'.$row.'">';

					$td = 0;
					foreach ($j as $p) {
						echo '<td><input type="text" class="form-control '.$field['adv_column'].'_field" name="'.$field['adv_column'].'_row_'.$row.'_column'.$td.'[]" value="'.$p.'" /></td>';
				  		$td++;
					}
					if($td < $column_counter){
						echo '<td><input type="text" class="form-control '.$field['adv_column'].'_field" name="'.$field['adv_column'].'_row_'.$row.'_column'.$td.'[]"/></td>';
				  		$td++;
					}
					echo '<td><i class="fa fa-times '.$field['adv_column'].'_remove" aria-hidden="true"></i></td>';

					if(isset($colorsArray) && $colorsArray && is_array($colorsArray) && is_array($colorsArray[$key])){

						//echo '<td><div class="colorPickSelector" data-initialcolor="'.$colorsArray[$key].'"></div><input type="hidden" class="form-control '.$field['adv_column'].'_field" name="'.$field['adv_column'].'_row_'.$row.'_column'.$td.'[]" value="'.$colorsArray[$key].'" /></td>';

						//echo '<td><input id="colorPickerSelector_'.$field['adv_column'].'_'.$key.'" value="" /></td>';

						$uniqueNumForColorPick = $colorsArray[$key]['uniqueNumForColorPick'];
						$colorTemp = $colorsArray[$key]['color'];
						$initializedTemp = $colorsArray[$key]['initialized'];

						if($initializedTemp == 1){ // Initialized
							$enabledTemp = $colorsArray[$key]['enabled'];
							if($enabledTemp == 1){ // Initialized and enabled
								echo '<td>
									<button type="button" class="btn btn-primary mb-1 '.$field['adv_column'].'_enableDisableColorPick" data-enable_disable_colorpick="1" data-unique_num_for_colorpick="'.$uniqueNumForColorPick.'">
										Color Pick Off
									</button>
									<input class="clsColorPicker" id="colorPickerSelector_'.$field['adv_column'].'_'.$uniqueNumForColorPick.'" value="" style="display: none;">
								</td>';
							}
							else{ // Initialized and disabled
								echo '<td>
									<button type="button" class="btn btn-primary mb-1 '.$field['adv_column'].'_enableDisableColorPick" data-enable_disable_colorpick="0" data-unique_num_for_colorpick="'.$uniqueNumForColorPick.'">
										Color Pick On
									</button>
									<input class="clsColorPicker d-none" id="colorPickerSelector_'.$field['adv_column'].'_'.$uniqueNumForColorPick.'" value="" style="display: none;">
								</td>';
							}
						}
						else{ // Not initialized
							echo '<td>
								<button type="button" class="btn btn-primary mb-1 '.$field['adv_column'].'_enableDisableColorPick" data-enable_disable_colorpick="0" data-unique_num_for_colorpick="'.$uniqueNumForColorPick.'">Color Pick On</button>
								<input class="clsColorPicker d-none" id="colorPickerSelector_'.$field['adv_column'].'_'.$uniqueNumForColorPick.'" value="" />
							</td>';
						}
					}
					
					echo '</tr>';
					$row++;
				}
			}else{
				
				echo '<tr class="tbl_row" data-row="'.$row.'">';
				$td = 0;

				$uniqueNumForColorPick = _generateRandomNumber(8);

				foreach ($array as $value) {
			  		echo '<td><input type="text" class="form-control '.$field['adv_column'].'_field" name="'.$field['adv_column'].'_row_'.$row.'_column'.$td.'[]" /></td>';
			  		$td++;
				}
				if($td < $column_counter){
					echo '<td><input type="text" class="form-control '.$field['adv_column'].'_field" name="'.$field['adv_column'].'_row_'.$row.'_column'.$td.'[]"/></td>';
			  		$td++;
				}

				echo '<td><i class="fa fa-minus" aria-hidden="true"></i></td>';

				//echo '<td><div class="colorPickSelector"></div><input type="hidden" class="form-control '.$field['adv_column'].'_field" name="'.$field['adv_column'].'_row_'.$row.'_column'.$td.'[]"></td>';

				echo '<td>
					<button type="button" class="btn btn-primary mb-1 '.$field['adv_column'].'_enableDisableColorPick" data-enable_disable_colorpick="0" data-unique_num_for_colorpick="'.$uniqueNumForColorPick.'">Color Pick On</button>
					<input class="clsColorPicker d-none" id="colorPickerSelector_'.$field['adv_column'].'_'.$uniqueNumForColorPick.'" value="" />
				</td>';

				$row++;
				echo '</tr>';
			}

			
			echo '</tbody>';
			echo '</table>';
			?>
			<i class="fa fa-plus <?php echo $field['adv_column']?>_plus" aria-hidden="true" style="cursor:pointer;"></i>
			<textarea name="<?php echo $field['adv_column'];?>_row" class="<?php echo $field['adv_column'];?>_row" style="width:100%;min-height:300px;display:none;"><?php 
				if((isset($listing['ad_'.$field['adv_column']])) && ($listing['ad_'.$field['adv_column']] !== '')){
					$json = json_decode($listing['ad_'.$field['adv_column']]);
					//print_r($json);
					foreach ($json as $j){

						$td = 0;
						if(!is_null($j)){
							$row_entry_num = sizeof($j);

							foreach ($j as $p) {
						  		echo $p;
						  		$td++;
						  		if($td < ($row_entry_num)){
								echo ', ';
						  		}
							}
						}
						echo "\n";
						//$row++;
					}
				}
			?></textarea>

			<input type="hidden" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" value='<?php echo isset($listing['ad_'.$field['adv_column']]) ? $listing['ad_'.$field['adv_column']] : '';?>' class="form-control">

	      	<input type="hidden" name="<?php echo $field['adv_column'].'_trs';?>" id="<?php echo $field['adv_column'].'_trs';?>" value='<?php echo isset($listing['ad_'.$field['adv_column'].'_trs']) ? $listing['ad_'.$field['adv_column'].'_trs'] : '';?>' class="form-control">
        </div>
        <?php 
            if($field['adv_post_text']!=''){
            ?>
            <div class="col-md-1 col-sm-1  control-label pull-right autocomplete_button">
            <?php echo $field['adv_post_text']?>
            </div>
        <?php } ?>  

    </div>
    <div class="dashed-grey"></div>
</div>

<script type="text/javascript">
	<?php
		if(isset($ptBlocksMultipleDatasId) || isset($additionalFormType)){
		}
		else{
	?>
	$(document).ready(function() {
	<?php
		}
	?>
		function _generateRandomNumberJS(length) {
		    let generator = "1209348756";
		    let result = "";
		    for (let i = 1; i <= length; i++) {
		        result += generator.charAt(Math.floor(Math.random() * generator.length));
		    }
		    return result;
		}

		$(document).on("click", ".<?php echo $field['adv_column']?>_column_config", function(e) {

			//alert('hit');
			let columns = $(this).data("columns");
			let field_id = $(this).data("field_id");
			let entry_id = $("#entry_id").val();
			let field_value = $('.<?php echo $field['adv_column']?>_row').val();
			let isEmpty = 0;
			if(field_value == ''){
				isEmpty = 1;
			}

			//alert(columns);
			//alert(field_id);
			//alert(entry_id);

			let table = $('#<?php echo $field['adv_column'].'_direct_table';?>');

			var headers = [];
			table.find('thead').each(function() {
			  $(this).find('th').each(function(index) {
			    headers.push($(this).text());
			  });
			});

			var json = JSON.stringify(headers);
			// console.log(json);

			$('#table_columns_table').val($(this).data("table"));
			$('#table_columns_entry_id').val(entry_id);
			$('#table_columns_id_field').val(field_id);
			$('#table_columns_headers').val(json);
			$('#table_columns_isEmpty').val(isEmpty);
			$('#table_columns_count').val(columns);

			$('#table_columns').modal('show');

		});

		$(document).on("click", ".<?php echo $field['adv_column']?>_header_config", function(e) {
			
			let columns = $(this).data("columns");
			//alert(columns);
			let field_id = $(this).data("field_id");

			let entry_id = $("#entry_id").val();

			$('#table_header_table').val($(this).data("table"));
			$('#table_header_entry_id').val(entry_id);

			$('#table_header_id_field').val(field_id);
			$('#table_settings').modal('show');

			var numOfInputs = parseInt(columns); // Get the input value and parse it to an integer
			//alert(numOfInputs);

			var thHeaders = [];
		    let datatable = $(this).attr('data-table');
		    //alert(datatable);
		    
		    $('.'+datatable).find('th').each(function( index ) {
		    	thHeaders.push($(this).text());
		    });
		    //console.log(thHeaders);

	        var $inputContainer = $('#table_settings_inputContainer'); // Get the input container element
	        $inputContainer.empty(); // Empty the container before appending new input fields
	        if (numOfInputs > 0) {
	          // Set a maximum limit of 10
	          if (numOfInputs > 10) {
	            numOfInputs = 10;
	            $(this).val(10); // Update the input value to 10
	          }
	          for (var i = 0; i < numOfInputs; i++) {
	            // Append new text input fields with class "form-control"
	            $inputContainer.append('<br><input type="text" class="form-control <?php echo $d['adv_column'];?>_field" placeholder="Column Header" value="'+thHeaders[i]+'"><br>');
	          }
	        }
		});
		
		let row_count = <?php echo $row;?>;
		let td_count = <?php echo sizeof($array);?>;

		let <?php echo $field['adv_column'];?>_rowValues = [];
		let <?php echo $field['adv_column'];?>_rowValuesTrs = [];

		function <?php echo $field['adv_column'].'_';?>resetRowValues(){
			<?php echo $field['adv_column'];?>_rowValues = [];
			<?php echo $field['adv_column'];?>_rowValuesTrs = [];
		}

		if($('#<?php echo $field['adv_column'];?>').val() != ''){
			//alert($('#<?php echo $field['adv_column'];?>').val());
			<?php echo $field['adv_column'];?>_rowValues = JSON.parse($('#<?php echo $field['adv_column'];?>').val());
			<?php echo $field['adv_column'];?>_rowValuesTrs = JSON.parse($('#<?php echo $field['adv_column'];?>').val());
		}

    	$(document).on("click", ".<?php echo $field['adv_column']?>_plus", function(e) {
            e.preventDefault();
            //alert('hit');
            let uniqueNumForColorPick = _generateRandomNumberJS(8);

            let table = $('.<?php echo $field['adv_column']?>_direct');
            row_count = table.find('tbody tr').length;

            let template = "<?php foreach ($array as $value) { echo '<td><input type=\"text\" class=\"form-control '.$field['adv_column'].'_field\" name=\"row_{row}_column'.$td.'[]\" /></td>';  $td++; } ?>";

                template += "<?php echo '<td><i class=\"fa fa-times '.$field['adv_column'].'_remove\"></i></td>';  $td++; ?>";

                template += "<?php echo '<td><button type=\"button\" class=\"btn btn-primary mb-1 '.$field['adv_column'].'_enableDisableColorPick\" data-enable_disable_colorpick=\"0\" data-unique_num_for_colorpick=\"{uniqueNumForColorPick}\">Color Pick On</button><input class=\"clsColorPicker d-none\" id=\"colorPickerSelector_'.$field['adv_column'].'_{uniqueNumForColorPick}\" value=\"\" /></td>';  $td++; ?>";

            	template = template.replaceAll('{row}', row_count);
            	
            let new_row = template.replaceAll('{uniqueNumForColorPick}', uniqueNumForColorPick);

            let row = '<tr class="tbl_row"  data-row="'+row_count+'">'+new_row+'</tr>';
            $('.<?php echo $field['adv_column'];?>_body').append(row);
            
            // initializeColorpickSelector(row_count);

            //<?php //echo $field['adv_column']."_initColorPicker"; ?>(row_count);

            row_count++;
        });

    	/* This function is used to get all the table datas */
        function <?php echo $field['adv_column']."_getTableData"; ?>(){
        	let status = false; let message = ''; let resultData = [];

		    let table = $('.<?php echo $field['adv_column']?>_direct');
		    let tbody = table.find('tbody');
		    
		    let tempValues = [];
            let tempValues1 = [];
		    
		    tbody.find('tr').each(function(rowIndex, rowElement) {
		        let row = [];
		        let row1 = [];
		        
		        $(rowElement).find('td').each(function(cellIndex, cellElement) {

		        	let cellValue = $(cellElement).find('input').val(); // Get the value of the input field in the cell
		        	let cellInput = $(cellElement).find('input'); // Get the value of the input field in the cell

		        	if(cellInput.length > 0){
		        		/*let idColorpickInput = cellInput.prop('id'); // colorPickerSelector_balconies_fraew_0
		        		let uniqueNumForColorPick;
		        		if(idColorpickInput){
		        			let lastIndex = idColorpickInput.lastIndexOf('_'); // Find the last index of '_'
		        			if(lastIndex == -1){
		        			}
		        			else{
		        				uniqueNumForColorPick = idColorpickInput.substring(lastIndex + 1); // Get the substring starting from the index after '_'
		        			}
		        		}*/
		        		let uniqueNumForColorPick = _getUniqueNumForColorPick( cellInput.prop('id') );

			            if(!!cellValue){
		            		if ( cellInput.get(0).id.indexOf('colorPickerSelector_') !== 0 ) {
		            			row.push(cellValue);
		            		}

		            		if ( cellInput.get(0).id.indexOf('colorPickerSelector_') !== 0 ) {
								row1.push(cellValue);
		            		}
		            		else{
		            			let isDisabled = cellInput.prop('disabled');
		            			if(isDisabled){ // If color picker initialized and disabled
		            				// row1.push("#fff"); // Old Code

		            				let tempObj = { 'uniqueNumForColorPick': uniqueNumForColorPick, 'color': '#fff', 'initialized': true, 'enabled': false };
		            				row1.push(tempObj);
								}
								else{ // If color picker initialized and enabled
			            			let elementId = cellInput.get(0).id;
			            			// row1.push( $(`#${elementId}`).spectrum("get").toHexString() ); // Old Code

			            			let tempObj = { 'uniqueNumForColorPick': uniqueNumForColorPick, 'color': $(`#${elementId}`).spectrum("get").toHexString(), 'initialized': true, 'enabled': true };
		            				row1.push(tempObj);
								}
		            		}
		            	}
		            	else{ // If color picker not initialized
		            		if ( cellInput.get(0).id.indexOf('colorPickerSelector_') !== 0 ) {
		            		}
		            		else{
		            			// row1.push("#fff"); // Old Code

		            			let tempObj = { 'uniqueNumForColorPick': uniqueNumForColorPick, 'color': '#fff', 'initialized': false };
	            				row1.push(tempObj);
		            		}
		            	}
		        	}
		        });
		        
		        tempValues.push(row);
		        tempValues1.push(row1);
		    });
		    // console.log("Table data: ");
		    // console.log(tempValues);
		    // console.log(tempValues1);

		    status = true;
		    resultData = { tempValues, tempValues1 };
		    let responseData = { status: status, message: message, data: resultData };
		    return responseData;
		}
		
    	//get values
    	$(document).on("keyup", ".<?php echo $field['adv_column']?>_field", function(e) {
			let getTableDataRes = <?php echo $field['adv_column']."_getTableData"; ?>();
			let tableDataResData = getTableDataRes.data;
				let tempValues = tableDataResData.tempValues;
				let tempValues1 = tableDataResData.tempValues1;

			// console.log(tempValues);
			// console.log(tempValues1);

			<?php echo $field['adv_column'];?>_rowValues = tempValues;
            <?php echo $field['adv_column'];?>_rowValuesTrs = tempValues1;

            $('#<?php echo $field['adv_column'];?>').val(JSON.stringify(<?php echo $field['adv_column'];?>_rowValues, (k, v) => v ?? undefined));
            $('#<?php echo $field['adv_column']."_trs";?>').val(JSON.stringify(<?php echo $field['adv_column'];?>_rowValuesTrs, (k, v) => v ?? undefined));
    	});

        $(document).on("click", ".<?php echo $field['adv_column']?>_remove", function(e) {
			e.preventDefault();
		    var rowNum = $(this).closest('.tbl_row').attr('data-row');
		    $(this).closest('.tbl_row').remove();

			let getTableDataRes = <?php echo $field['adv_column']."_getTableData"; ?>();
			let tableDataResData = getTableDataRes.data;
				let tempValues = tableDataResData.tempValues;
				let tempValues1 = tableDataResData.tempValues1;

			// console.log(tempValues);
			// console.log(tempValues1);
			
		    $('#<?php echo $field['adv_column'];?>').val(JSON.stringify(tempValues));
		    $('#<?php echo $field['adv_column']."_trs";?>').val(JSON.stringify(tempValues1));
    	});

        setTimeout(function() {
			 $("#<?php echo $field['adv_column'];?>_direct").bootstrapSwitch({
		    	"offText" : "Row", 
		    	"onText" : "Direct",
		    	"onSwitchChange" : function(event, state){
		    		
		            if(state){
		            	//$('#saveToField').attr('disabled', false);
		            	$('.<?php echo $field['adv_column'];?>_row').show();
		            	
		            	$('.<?php echo $field['adv_column'];?>_plus').hide();
		            	$('.<?php echo $field['adv_column'];?>_direct').hide();

		            }else{
		            	//$('#saveToField').attr('disabled','disabled');
		            	$('.<?php echo $field['adv_column'];?>_direct').show();
		            	$('.<?php echo $field['adv_column'];?>_row').hide();
		            	$('.<?php echo $field['adv_column'];?>_plus').show();

		            }
		            
		        },
			});
        }, 900);

		$(document).on("keyup", ".<?php echo $field['adv_column']?>_row", function(e) {
			let csvData = $(this).val();
			let lines = csvData.split('\n');
			// console.log(lines);
			//let headers = lines[0].split(',');
			let result = [];
			
			
			for (let i = 0; i < lines.length; i++) {
				let tempValues = [];
			  	let currentLine = lines[i].trim().replace(/,$/, '').split(',');
			  	for (let y = 0; y< currentLine.length; y++) {
			  		// console.log(currentLine[y]);
			 		tempValues.push(currentLine[y]);
			 	}
			 	// console.log(tempValues);
			 	result.push(tempValues);
			 	// console.log(result);
			}
			

			$('#<?php echo $field['adv_column'];?>').val(JSON.stringify(result, (k, v) => v ?? undefined));
		});

		<?php
			// https://seballot.github.io/spectrum/
			/* This function is used to handle initialized and enable/disable color picker */
		?>
		$(document).on("click", ".<?php echo $field['adv_column'].'_enableDisableColorPick'; ?>", function(e) {
			let advColumn = '<?php echo $advColumn; ?>';
			let selectorKeyup = `.${advColumn}_field`;

			let enableDisableButton = $(e.currentTarget);
				let buttonText = enableDisableButton.text();
				let enableDisableColorpick = enableDisableButton.data('enable_disable_colorpick');
				
				let colorpickInput = enableDisableButton.closest('td').find('.clsColorPicker');
					let isDisabled = colorpickInput.prop('disabled');
					let idColorpickInput = colorpickInput.prop('id'); // colorPickerSelector_balconies_fraew_0
						let lastIndex = idColorpickInput.lastIndexOf('_'); // Find the last index of '_'
						let index = idColorpickInput.substring(lastIndex + 1); // Get the substring starting from the index after '_'

						let selector = `#${idColorpickInput}`;

			if(enableDisableColorpick == 0){ // The current status is OFF. Set it to ON.
				colorpickInput.removeClass('d-none');

				enableDisableButton.data('enable_disable_colorpick', '1');
				enableDisableButton.text('Color Pick Off');

				if(isDisabled){
					$(selector).spectrum("enable");
					$(selectorKeyup).trigger("keyup");
				}
				else{ // Runs only first time for initialization of a color picker
					<?php echo $field['adv_column']."_initColorPicker"; ?>(`${index}`);
					$(selectorKeyup).trigger("keyup");
				}
			}
			else if(enableDisableColorpick == 1){ // The current status is ON. Set it to OFF.
				colorpickInput.addClass('d-none');

				enableDisableButton.data('enable_disable_colorpick', '0');
				enableDisableButton.text('Color Pick On');

				$(selector).spectrum("disable");
				$(selectorKeyup).trigger("keyup");
			}
		});

		<?php
			/* Following If condition used to handle click event of any table prefill button */
			if($ptPrefillsData){
				foreach ($ptPrefillsData as $key => $value) {
					if( isset($additionalFormType) ){ // add / edit popup data
		        		$clsPrefixSelector = '.clsPrefillButtonTable_'.$advId.'_'.$value->id;
		        		$additionalFormTypeJS = $additionalFormType;
		        	}
		        	else{
		        		$clsPrefixSelector = '.clsPrefillButtonTable_'.$advId_Original.'_'.$value->id;
		        		$additionalFormTypeJS = '';
		        	}
		?>
					$(document).on('click', '<?php echo $clsPrefixSelector; ?>', function(e){
						let advId = $(e.currentTarget).data("advid");
						let ptPrefillsId = $(e.currentTarget).data("ptprefillsid");
						let additionalFormType = '<?php echo $additionalFormTypeJS ?>';
						let formType = '<?php echo $formType ?>';
						let newId = '<?php echo $newId ?>';
						let data = { advId, ptPrefillsId, additionalFormType, formType, newId };
						let type = 'post';
						let url = "<?php echo base_url('table/table/prefillTable') . '/' ?>";
						let dataType = 'json';
						var l = Ladda.create($('<?php echo $clsPrefixSelector; ?>').get(0));
						l.start();
						$.ajax({
							url, type, data, dataType,
							// contentType: false, cache: false, processData:false,
							success: function (response) {
								if(response.status){
									<?php echo $field['adv_column'].'_';?>resetRowValues();

									$(response.data.tbodySelector).html(response.data.html);

									if(response.data.colorPickerSelectorInitArray.length > 0){
										response.data.colorPickerSelectorInitArray.forEach(function(currentValue, index, arr) {
											if(typeof window[currentValue] === 'function'){ // Check if the function exists and is callable
											    window[currentValue](index); // Call the function
											}
											else{
											}
										});
									}

									$(response.data.keyupSelector).trigger("keyup");
								}
								else{
									toastr.error(response.message);
								}
								l.stop();
							},
							error: function(jqXHR, textStatus, errorThrown){
								console.log(`jqXHR: ${jqXHR}`);
								console.log(`textStatus: ${textStatus}`);
								console.log(`errorThrown: ${errorThrown}`);
								l.stop();
							}
						});
					});
		<?php
				}
			}
		?>

	<?php
	if(isset($ptBlocksMultipleDatasId) || isset($additionalFormType)){
	}
	else{
	?>
			
    });

	<?php
	}
	?>
	//resize script
    // Query the table
    const <?php echo $field['adv_column'].'_direct';?> = document.getElementById('<?php echo $field['adv_column'].'_direct_table';?>');
    // console.log(<?php echo $field['adv_column'].'_direct';?>);
    // Query all headers
    const <?php echo $field['adv_column'].'_cols';?> = <?php echo $field['adv_column'].'_direct';?>.querySelectorAll('th');
    // console.log(<?php echo $field['adv_column'].'_cols';?>);

    const <?php echo $field['adv_column'].'_';?>updateInputWidths = function() {
      <?php echo $field['adv_column'].'_cols';?>.forEach((col) => {
        let currentWidth = parseFloat($(col).width()) || 0;

        const tableWidth = <?php echo $field['adv_column'].'_direct';?>.offsetWidth;
        currentWidth = currentWidth / tableWidth * 100;

        const input = col.querySelector('input');
        if (input) {
          input.value = Math.round(currentWidth, 2);
        }
      });
    };

    const <?php echo $field['adv_column'].'_';?>createResizableColumn = function (col, resizer) {
        // Track the current position of mouse
        let x = 0;
        let w = 0;

        const <?php echo $field['adv_column'].'_';?>mouseDownHandler = function (e) {
            // Get the current mouse position
            x = e.clientX;

            // Calculate the current width of column
            const styles = window.getComputedStyle(col);
            w = parseInt(styles.width, 10);

            // Attach listeners for document's events
            document.addEventListener('mousemove', <?php echo $field['adv_column'].'_';?>mouseMoveHandler);
            document.addEventListener('mouseup',<?php echo $field['adv_column'].'_';?>mouseUpHandler);
        };
        /*
        const mouseMoveHandler = function (e) {
            // Determine how far the mouse has been moved
            const dx = e.clientX - x;
            console.log(dx);
            // Update the width of column
            col.style.width = `${w + dx}px`;
        };
        */
       const <?php echo $field['adv_column'].'_';?>mouseMoveHandler = function (e) {
        // Determine the percentage of the mouse movement
        
        const dx = e.clientX - x;
        const tableWidth = <?php echo $field['adv_column'].'_direct';?>.offsetWidth;
        const columnWidth = (w + dx) / tableWidth * 100;

        // Update the width of the column as a percentage
        col.style.width = `${columnWidth}%`;
        //$(col).closest('input').val(columnWidth);
        //let $input = $(col).find('input');
        //$input.val(Math.round(columnWidth));

       <?php echo $field['adv_column'].'_';?>updateInputWidths();
      };

        // When user releases the mouse, remove the existing event listeners
        const <?php echo $field['adv_column'].'_';?>mouseUpHandler = function () {
        	let percentages = [];

        	<?php echo $field['adv_column'].'_cols';?>.forEach((col) => {

		        const input = col.querySelector('input');
		        if (input) {
		          //console.log(input.value);
		          percentages.push(input.value);
		        }
		        // console.log(percentages);
		    });

        	<?php if(isset($listing['ad_id'])){?>
		    $.ajax({
            	type: "POST",
	            url: base_url+'table/Table/update_config',
	            data: {percentages: JSON.stringify(percentages), 'field': <?php echo $field['adv_id'];?>, 'entry_id': <?php echo $listing['ad_id'];?>}, // serializes the form's elements.
	            dataType:'json',
	            success: function(data){
	                //l.stop();
	                // console.log(data);
	                toastr.success(data.msg);
	                $("#table_settings").modal('hide');
	            } 
	        });
			<?php } ?>

            document.removeEventListener('mousemove', <?php echo $field['adv_column'].'_';?>mouseMoveHandler);
            document.removeEventListener('mouseup', <?php echo $field['adv_column'].'_';?>mouseUpHandler);
        };

        resizer.addEventListener('mousedown', <?php echo $field['adv_column'].'_';?>mouseDownHandler);
    };

    // Loop over them
    [].forEach.call(<?php echo $field['adv_column'].'_cols';?>, function (col) {
        // Create a resizer element
        const resizer = document.createElement('div');
        resizer.classList.add('resizer');

        // Set the height
        resizer.style.height = `${<?php echo $field['adv_column'].'_direct';?>.offsetHeight}px`;

        // Add a resizer element to the column
        col.appendChild(resizer);

        // Will be implemented in the next section
        <?php echo $field['adv_column'].'_';?>createResizableColumn(col, resizer);
    });

    const <?php echo $field['adv_column'].'_';?>mouseDownHandler = function(e) {
        resizer.classList.add('resizing');
    };

    const <?php echo $field['adv_column'].'_';?>mouseUpHandler = function() {
        resizer.classList.remove('resizing');
    };

    <?php if(isset($listing['ad_id'])){?>
    $('#<?php echo $field['adv_column'];?>_repeatafter').keyup(function() {
    	//alert('hit');
    	let repeateafter = $(this).val();
		$.ajax({
        	type: "POST",
            url: base_url+'table/Table/update_config',
            data: {repeatafter: repeateafter, 'field': <?php echo $field['adv_id'];?>, 'entry_id': <?php echo $listing['ad_id'];?>}, // serializes the form's elements.
            dataType:'json',
            success: function(data){
                //l.stop();
                // console.log(data);
                toastr.success(data.msg);
                $("#table_settings").modal('hide');
            } 
        });
    });
	<?php } ?>
    /*
    $('.<?php echo $field['adv_column'].'_direct';?> input').keyup(function() {
    	var value = $(this).val();
    	let currentCol = $(this);

    	let oldWidth = $(this).parent().width();
    	$(this).parent().css({'border': '1px solid red'});
    	console.log('oldWidth'+oldWidth);
    	
    	//alert($(this).parent().parent().width());

    	$(this).parent().css({'width' : value+'%'});

    	let tableWidth =  $(this).parent().parent().width();


    	let dif = 0;
    	if(oldWidth > value){
    		dif = value - oldWidth;
    	}else{
    		dif = oldWidth - value;
    	}
    	console.log(dif);
    	//alert(value);
    	// Update column widths
	    var table = $(this).closest('table'); // Get the table element
	    console.log(table);
	    
	    // Update column widths for all cells in the same column
	    table.find('th').each(function() {
	    	if(currentCol !== $(this)){

	    	}else{
	    		$(this).css({'width': value+'%'});
	    	}
	    });

	    setTimeout(function(){
    		var f = (oldWidth / tableWidth) * 100;
    		alert(f);
	    }, 1000)

    });
    */
/* This function is used to initialize color picker */
function <?php echo $field['adv_column']."_initColorPicker"; ?>(rowCount=''){
	let selector = '';
	let advColumn = '<?php echo $advColumn; ?>';
	selector = `#colorPickerSelector_${advColumn}_${rowCount}`;
	$(selector).spectrum({
		type: "color",
		// color: "#D9E2F3",
		showPaletteOnly: true,
		togglePaletteOnly: true,
		hideAfterPaletteSelect: true,
		showInput: true,
		showInitial: true,
		showPalette: true,
		palette: [
	        ["#FFFFFF", "#D9E2F3", "#A8D08D", "#F7CAAC", "#FF0000"],
	    ]
	});
	$(selector).on('change.spectrum', function(e, tinycolor){
		let color = tinycolor.toHexString();
		let selector = `.${advColumn}_field`;
		$(selector).trigger("keyup");
	});
}
<?php
	/* Following If condition used to set selected data in the edit form for color picker */
	if( isset($listing['ad_id']) ){
		if((isset($listing['ad_'.$field['adv_column']])) && ($listing['ad_'.$field['adv_column']] !== '')){
			$json = json_decode($listing['ad_'.$field['adv_column']]);
			foreach ($json as $key => $j){
?>
				//<?php //echo $field['adv_column']."_initColorPicker"; ?>(<?php echo $key; ?>);

<?php
				if(isset($colorsArray) && $colorsArray && is_array($colorsArray) && is_array($colorsArray[$key])){
					$uniqueNumForColorPick = $colorsArray[$key]['uniqueNumForColorPick'];
					$colorTemp = $colorsArray[$key]['color'];
					$initializedTemp = $colorsArray[$key]['initialized'];

					if($initializedTemp == 1){ // Initialized
						$enabledTemp = $colorsArray[$key]['enabled'];
?>
						<?php echo $field['adv_column']."_initColorPicker"; ?>("<?php echo $uniqueNumForColorPick; ?>");
						
						setTimeout(function(){
							let advColumn = '<?php echo $advColumn; ?>';
							let key = '<?php echo $uniqueNumForColorPick; ?>';
							let selector = `#colorPickerSelector_${advColumn}_${key}`;
							let color = '<?php echo $colorTemp ?>';
							$(selector).spectrum("set", color);

							let selectorField = `.${advColumn}_field`;
<?php
							if($enabledTemp == 1){ // Initialized and enabled
							}
							else{ // Initialized and disabled
?>
								$(selector).spectrum("disable");
<?php
							}
?>
							$(selectorField).trigger("keyup");
						}, 300);
<?php
					}
					else{ // Not initialized
						//
					}
				}
			}
		}
		else{
?>
			//<?php //echo $field['adv_column']."_initColorPicker"; ?>(0); // Old Code
<?php
		}
	}
	else{
?>
		//<?php //echo $field['adv_column']."_initColorPicker"; ?>(0); // Old Code
<?php
	}
?>
</script>