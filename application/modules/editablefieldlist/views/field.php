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

                <input type="text"  name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" class="form-control" value="<?php echo  $listing['ad_'.$field['adv_column']];?>" autocomplete="off">

            </div> 

        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>

