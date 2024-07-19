<?php
$CI =& get_instance();
$CI->load->model('Advertcategorymodel');
?>
<div class="contentpanel">
      
      <div class="row"><!-- col-md-6 -->
        
        <div class="col-md-12">
            <form method="post" id="mapping_form">

              <table class="table table-striped">
                  <?php 
                      $c = 0;
                      foreach($first_line as $f){ ?>
                      <tr>
                          <td>
                              <?php echo $f;?> <?php if($second_line[$c]!=''){ echo '<i>('.$second_line[$c].')</i>'; }?>
                          </td>
                          <td>
                              <select class="form-control" name="maps[]" id="maps_<?php echo $c;?>">
                                <option value="">-- Don't map --</option>
                                <?php 
                                $option_counter = 0;
                                foreach($fields as $f){?>
                                <option value="<?php echo $f['adv_id'];?>" <?php if($option_counter == $c){ echo "selected=\"selected\""; } ?>><?php echo $f['adv_text'];?></option>
                                <?php 
                                $option_counter++;
                                } ?>
                              </select>
                              <input type="text" class="form-control d-none" name="input_<?php echo $c;?>" id="input_<?php echo $c;?>">
                          </td>
                          <td>
                            <input class="form-check-input check" type="checkbox" value="" id="check_<?php echo $c;?>" data-id="<?php echo $c;?>">
                          </td>
                      </tr>
                  <?php 
                      $c++;
                      } ?>
              
              <tr>
                <td>
                  Has header?
                </td>
                <td>
                  <input type="checkbox" value="1" name="has_header" id="has_header" checked="checked">
                </td>
              </tr>

              <?php 
                  $c = 0;
                  foreach($linked_fields as $f){ 
                  ?>
                      <tr>
                          <td>
                              <?php echo $f['adv_text'];?>
                          </td>
                          <td>
                            <?php
                              echo $CI->Advertcategorymodel->getFieldHTML($f, NULL, NULL);
                            ?>
                          </td>
                          <td>
                          </td>
                      </tr>
              <?php 
                  $c++;
                  } 
              ?>
              </table>


              <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right" id="import_button">Import</button> 
              <input type="hidden" name="form_id" id="form_id" value="<?php echo $form_id;?>">
            </form>
		</div>
	</div>
</div>

<script language="javascript">
jQuery(document).ready(function() { 

    jQuery('.check').on('click', function(e){ 
      var id = $(this).attr('data-id');
      if($('#input_'+id).hasClass('d-none')){
        $('#input_'+id).removeClass('d-none'); 
      }else{
        $('#input_'+id).addClass('d-none');
        $('#input_'+id).val('');
      }
    });

});
</script>