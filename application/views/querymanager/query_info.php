<?php
$CI =& get_instance();
$CI->load->model('Advertcategorymodel');

?>
<div class="contentpanel">
  <div class="row"><!-- col-md-6 -->
      <div class="col-md-12">
          
          <form method="post" id="widget_form">

              <h3>QUERY CONFIG</h3>

              <div class="widget_config">

                <table class="table table-striped">
                <thead>
                  <th>Field</th>
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
                      <input class="form-check-input check" type="checkbox" value="<?php echo $f['adv_id'];?>" id="where[]" name="where[]">
                    </td>
                    <td>
                      <select id="operator_<?php echo $f['adv_id'];?>" name="operator_<?php echo $f['adv_id'];?>" class="form-control">
                          <option value="" selected="selected">Please Select</option>

                          <option value="=">=</option>
                          <option value=">">></option>
                          <option value="<"><</option>
                          <option value="!=">!=</option>
                                  
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
              <?php 
                /*
                foreach($spare_date_fields as $s){?>
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
              <?php }
              */ ?>

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

  

  jQuery(document).on("submit","#widget_form", function(e) {

  	  	e.preventDefault();
      	alert('hit');
      	var widget_data = $(this).serialize();
      	console.log(widget_data);

        jQuery.ajax({
            type: "POST",
            url: base_url+'Querymanager/save_query',
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