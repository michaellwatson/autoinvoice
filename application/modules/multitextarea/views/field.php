<div class="container">
    <div class="row" id="id_<?php echo $field['adv_id']?>">


    	<div class="col-md-6 text-left">
    		 <?php //echo $field['adv_text']?>
            <?php if($field['adv_required']==1){ ?>
            <span style="color:red;">*</span>
            <?php } ?>:&nbsp;
    	</div>
    	<div class="col-md-6 text-right d-flex">
    		<?php 
			// Input JSON string
			$jsonString = $field['adv_config'];
			// Decode JSON string into an array
			$array = [ucwords($field['adv_text'])];

        	?>
    	</div>
        
        <?php if(($field['adv_post_text']!='')||( isset($adv_config['add']) && (bool)$adv_config['add']) ){?>
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

				echo '<th>' . $value . '</th>';
				
			}
			echo '<th style="width:1%;"></th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody class="'.$field['adv_column'].'_body">';
			$row = 0;
			

            $i = 1;
            $html2 = '';

            $this->db->select('*');
            $this->db->where('field_id', $field['adv_id']);
            $prefill_fields = $this->db->get('prefills')->result_array();

          	if(is_array($prefill_fields) && (sizeof($prefill_fields) > 0)){
            	$html2.= '<h5>Prefill text options</h5>';
          	}
             
            foreach($prefill_fields as $p){
            	$html2.= '<a class="btn btn-primary prefill_button" data-text="'.htmlentities($p['text']).'">'.$p['name'].'</a> ';
                $i++;
            }
            if($html2 !== ''){
                $html2 .= ' <a href="'.base_url('Prefilltext/index/'.$field['adv_id']).'" target="_blank"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }else{
            	$html2 = ' <a href="'.base_url('Prefilltext/index/'.$field['adv_id']).'" target="_blank"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
             
			if((isset($listing['ad_'.$field['adv_column']])) && ($listing['ad_'.$field['adv_column']] !== '')){
				$json = json_decode($listing['ad_'.$field['adv_column']]);
				//print_r($json);
				foreach ($json as $j){

					echo '<tr class="tbl_row" data-row="'.$row.'">';

				  	echo '<td>'.$html2.'<textarea class="form-control ckeditor '.$field['adv_column'].'_field" name="'.$field['adv_column'].''.$row.'" id="'.$field['adv_column'].''.$row.'" rows="10">'.$j.'</textarea></td>';

					echo '<td><i class="fa fa-times '.$field['adv_column'].'_remove multi_remove" aria-hidden="true"></i></td>';
					echo '</tr>';
					$row++;
				}
			}else{
				
				echo '<tr class="tbl_row" data-row="'.$row.'">';

				echo '<td>'.$html2.'<textarea class="form-control ckeditor '.$field['adv_column'].'_field" name="'.$field['adv_column'].''.$row.'" id="'.$field['adv_column'].''.$row.'" rows="10"></textarea></td>';
				
				echo '<td><i class="fa fa-minus" aria-hidden="true"></i></td>';

				$row++;
				echo '</tr>';
			}

			
			echo '</tbody>';
			echo '</table>';
			?>
			<i class="fa fa-plus <?php echo $field['adv_column']?>_plus" aria-hidden="true" style="cursor:pointer;"></i>

			<input type="hidden" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" value='<?php echo isset($listing['ad_'.$field['adv_column']]) ? json_encode($listing['ad_'.$field['adv_column']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT) : '';?>' class="form-control">
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
	
	$(document).ready(function() {

		
		let row_count = <?php echo $row;?>;
		let td_count = <?php echo sizeof($array);?>;

		let <?php echo $field['adv_column'];?>_rowValues = [];
		let <?php echo $field['adv_column'];?>_values = [];

		if($('#<?php echo $field['adv_column'];?>').val() != ''){
			//alert($('#<?php echo $field['adv_column'];?>').val());
			<?php echo $field['adv_column'];?>_rowValues = JSON.parse($('#<?php echo $field['adv_column'];?>').val());
		}

    	$(document).on("click", ".<?php echo $field['adv_column']?>_plus", function(e) {
      		e.preventDefault();
      		//alert('hit');
      		let template = '<?php foreach ($array as $value) { echo '<td>'.$html2.'<textarea class="form-control ckeditor '.$field['adv_column'].'_field" name="'.$field['adv_column'].'{row}" id="'.$field['adv_column'].'{row}" rows="10"></textarea></td><td><i class="fa fa-times '.$field['adv_column'].'_remove multi_remove" aria-hidden="true"></i></td>';  $td++; } ?>';
      		let new_row = template.replaceAll('{row}', row_count);
      		let row = '<tr class="tbl_row"  data-row="'+row_count+'">'+new_row+'</tr>';
      		$('.<?php echo $field['adv_column'];?>_body').append(row);

      		var elem = $('[name="<?php echo $field['adv_column'];?>'+row_count+'"]')[0];
      		console.log(elem);
 			//elem.ckeditor()
 			CKEDITOR.replace(elem);

      		row_count++;
      	});

    	let <?php echo $field['adv_column'];?>_temp = [];

      	    CKEDITOR.on('instanceReady', function (e) {
			    var editor = e.editor;
			    let ename = e.editor.name; 

			    if(String(ename).startsWith('<?php echo $field['adv_column'];?>')){
			    	console.log(ename);

			    	let ix = ename.replace("<?php echo $field['adv_column'];?>", "");
			    	<?php echo $field['adv_column'];?>_temp[ix] = editor.getData();
			    	$('#<?php echo $field['adv_column'];?>').val(JSON.stringify(<?php echo $field['adv_column'];?>_temp, (k, v) => v ?? undefined));

				    editor.on('key', function () {
				        // Your key event handling code here
				        var self = this;

				        let i = ename.replace("<?php echo $field['adv_column'];?>", "");
				        console.log(i);
				        setTimeout(function() {
				        	console.log(self.getData());

				        	let i = ename.replace("<?php echo $field['adv_column'];?>", "");
				        	<?php echo $field['adv_column'];?>_temp[i] = self.getData();
				        	console.log(<?php echo $field['adv_column'];?>_temp);
				        	if($('#<?php echo $field['adv_column'];?>').val() != ''){
				        		$('#<?php echo $field['adv_column'];?>').val(JSON.stringify(<?php echo $field['adv_column'];?>_temp, (k, v) => v ?? undefined));
				        	}
				    	}, 10);
				    });
				}
			});


        $(document).on("click", ".<?php echo $field['adv_column']?>_remove", function(e) {
		    e.preventDefault();
		    var rowNum = $(this).closest('.tbl_row').attr('data-row');
		    $(this).closest('.tbl_row').remove();

		    // Update array index and hidden field value
		    <?php echo $field['adv_column'];?>_rowValues.splice(rowNum, 1);
		    if($('#<?php echo $field['adv_column'];?>').val() != ''){
		    $('#<?php echo $field['adv_column'];?>').val(JSON.stringify(<?php echo $field['adv_column'];?>_rowValues));
			}
		});

    });

</script>

