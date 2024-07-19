<div class="container">
    <div class="row" id="id_<?php echo $field['adv_id']?>">
      
        <label class="col-sm-6 col-md-6 control-label">
            <?php echo $field['adv_text']?>
            <?php if($field['adv_required']==1){ ?>
            <span style="color:red;">*</span>:&nbsp;
            <?php } ?>
        </label>
                            
        <div class="col-md-6 col-sm-6 text-left">

             <div id="field_<?php echo $field['adv_id']?>">

                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" value="1" <?php if($listing['ad_'.$field['adv_column']]==1){?>checked<?php } ?>>
                </div>

            </div> 

        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>

