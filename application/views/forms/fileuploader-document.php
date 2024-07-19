<div style="padding:15px;">

<!--
<div class="container">

    <div class="row" id="id_3">
                        
        <div class="col-md-6 col-sm-6">
            <div id="field_3">
                <input type="text" name="folder_name" id="folder_name" class="form-control" value="" autocomplete="off" placeholder="Create a new folder">                    
            </div> 
        </div>
        <div class="col-md-6 col-sm-6">
        	<button class="btn btn-primary">Create</button>
        </div>

    </div>

    <div class="dashed-grey"></div>

</div>
-->


<div class="progress active docBar">
	<div class="progress-bar_<?php echo $adv_column."-".$randomNumber;?> pdc_<?php echo $adv_column;?> progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;background-color:#00974A;">
    </div>
</div> 
<div id="file-uploader-documents_<?php echo $adv_column."-".$randomNumber;?>" style="padding-bottom:14px;">       
	<noscript>          
   	<p>Please enable JavaScript to use file uploader.</p>
   	<!-- or put a simple form for upload here -->
    </noscript>         
</div>

<div id="documentUploads_<?php echo $adv_column."-".$randomNumber;?>" class="ul_parent">
	<ul id="sortableDocuments_<?php echo $adv_column."-".$randomNumber;?>" class="ul_child">
	</ul>
</div>
<?php if($adv_column == 'locationofpropertymap'){?>
<table class="display_options_table">
    <tr>
        <!--
        <td>
            <img src="<?php echo base_url("assets/images/1.jpg");?>" class="display_options_table_image <?php if(isset($selected_layout)){ if($selected_layout->layout_id == 1){ echo 'image_selected'; } }?>" data-id="1">
        </td>
        <td>
            <img src="<?php echo base_url("assets/images/1-1.jpg");?>" class="display_options_table_image <?php if(isset($selected_layout)){ if($selected_layout->layout_id == 2){ echo 'image_selected'; } }?>" data-id="2">
        </td>
        -->
        <td>
            <img src="<?php echo base_url("assets/images/1-1s.jpg");?>" class="display_options_table_image <?php if(isset($selected_layout)){ if($selected_layout->layout_id == 3){ echo 'image_selected'; } }?>" data-id="3">
        </td>
        <td>
            <img src="<?php echo base_url("assets/images/1-2.jpg");?>" class="display_options_table_image <?php if(isset($selected_layout)){ if($selected_layout->layout_id == 4){ echo 'image_selected'; } }?>" data-id="4">
        </td>
        <td>
            <img src="<?php echo base_url("assets/images/2-2.jpg");?>" class="display_options_table_image <?php if(isset($selected_layout)){ if($selected_layout->layout_id == 5){ echo 'image_selected'; } }?>" data-id="5">
        </td>
    </tr>
</table>
<?php } ?>
</div>
