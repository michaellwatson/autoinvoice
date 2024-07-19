<?php
$CI =& get_instance();
$CI->load->model('Advertcategorymodel');

ob_start(); //Start remembering everything that would normally be outputted, but don't quite do anything with it yet
$c = 0;
?>
<table class="table table-striped">
  <thead>
    <th>Field</th>
    <th class="text-center">Date Field</th>
    <th class="text-center">Where</th>
    <th>Operator</th>
    <th>Value</th>
  </thead>
<?php
foreach($fields as $f){ ?>
  <tr>
      <td data-id="<?php echo $f['adv_id'];?>">
          <?php echo $f['adv_text'];?>
      </td>

      <td class="text-center">
        <input class="form-check-input check" type="checkbox" value="<?php echo $f['adv_column'];?>" id="date[]" name="date[]" <?php if($f['fi_id']!==9){?>disabled<?php } ?>>
      </td>
      <td class="text-center">
        <input class="form-check-input check" type="checkbox" value="<?php echo $f['adv_id'];?>" id="where[]" name="where[]">
      </td>
      <td>
        <select id="operator_<?php echo $f['adv_id'];?>" name="operator_<?php echo $f['adv_id'];?>" class="form-control">
            <option value="" selected="selected">Please Select</option>

            <option value="=">=</option>
            <option value=">">></option>
            <option value="<"><</option>
                    
        </select> 
      </td>
      <td>
        <input type="text" class="form-control" name="value_<?php echo $f['adv_id'];?>" id="value_<?php echo $f['adv_id'];?>">
      </td>
  </tr>
<?php 
  $c++;
} 
?>
<?php foreach($spare_date_fields as $s){?>
  <tr>
      <td>
          <?php echo ucfirst(str_replace('_', ' ', preg_replace('/^ad_/', '', $s)));?>
      </td>

      <td class="text-center">
        <input class="form-check-input check" type="checkbox" value="<?php echo $s;?>" name="date[]">
      </td>
      <td colspan="3">
      </td>

  </tr>
<?php } ?>

</table>
<?php
$output = ob_get_contents(); //Gives whatever has been "saved"
ob_end_clean(); //Stops saving things and discards whatever was saved
ob_flush();
?>
<div class="contentpanel">
  <div class="row"><!-- col-md-6 -->
      <div class="col-md-12">
          
          <form method="post" id="widget_form">

              <h3>WIDGET CONFIG</h3>

              <div class="widget_config">

                <table class="table table-striped">
                  <tr>
                    <td>
                      Widget Type
                    </td>
                    <td>
                      <select id="widget_type" name="widget_type" class="form-control widget_type">
                        <option value="" selected="selected">Please Select</option>
                        <option value="count_entries" >Count Entries</option>
                        <option value="sum_values" >Sum the value of a field</option>
                      </select> 
                    </td>

                    <td>
                      Color
                    </td>
                    <td>
                      <select id="widget_color" name="widget_color" class="form-control widget_color">
                        <option value="" selected="selected">Please Select</option>
                        <option value="bg-info" >Blue</option>
                        <option value="bg-primary" >Purple</option>
                        <option value="bg-success" >Green</option>
                        <option value="bg-warning" >Yellow</option>
                      </select> 
                    </td>

                    <td>
                      Name
                    </td>
                    <td>
                      <input type="text" class="form-control" name="widget_name" id="widget_name">
                    </td>
                  </tr>
                </table>

              </div> 
              <button type="submit" class="btn btn-primary ladda-button save_widget" data-style="expand-right" id="save_widget">Save</button> 
              
              <input type="hidden" name="form_id" id="form_id" value="<?php echo $form_id;?>">
          		
          </form>

		</div>
	</div>
</div>

<script language="javascript">
jQuery(document).ready(function() { 

  
  jQuery(document).on("change",".widget_type",function() {

      $('.widget_config').append(<?php echo json_encode($output);?>);

  });

  jQuery(document).on("submit","#widget_form", function(e) {

  	  	e.preventDefault();
      	alert('hit');
      	var widget_data = $(this).serialize();
      	console.log(widget_data);

        jQuery.ajax({
            type: "POST",
            url: base_url+'Dashboard/save_widget',
            data: widget_data, // serializes the form's elements.
            dataType:'json',
            success: function(data)
            {

            }
        });

      
      
  });
  /*
    jQuery('.check').on('click', function(e){ 
      var id = $(this).attr('data-id');
      if($('#input_'+id).hasClass('d-none')){
        $('#input_'+id).removeClass('d-none'); 
      }else{
        $('#input_'+id).addClass('d-none');
        $('#input_'+id).val('');
      }
    });
  */

});
</script>