<div class="container">
    <div class="row" id="id_<?php echo $field['adv_id']?>">
      
        <label class="col-sm-6 col-md-6 control-label">
            <?php echo $field['adv_text']?>: 
        </label>
                            
        <div class="col-md-6 col-sm-6 text-left">

             <div id="field_<?php echo $field['adv_id']?>">

                <input type="text" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" value="<?php echo isset($listing['ad_'.$field['adv_column']]) ? $listing['ad_'.$field['adv_column']] : '';?>" class="form-control"  autocomplete="nope" readonly>
                
            </div> 

        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>

