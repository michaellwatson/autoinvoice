
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

                <select class="form-select form-control" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>">
                  <option value="">Please Select</option>
                  <option  value="1" <?php echo (isset($listing['ad_'.$field['adv_column']]) && $listing['ad_'.$field['adv_column']] == 1) ? "selected=\"selected\"" : '';?>>Phil Diamond</option>
                  <option  value="2" <?php echo (isset($listing['ad_'.$field['adv_column']]) && $listing['ad_'.$field['adv_column']] == 2) ? "selected=\"selected\"" : '';?>>Stephen Brooker</option>
                  <option  value="3" <?php echo (isset($listing['ad_'.$field['adv_column']]) && $listing['ad_'.$field['adv_column']] == 3) ? "selected=\"selected\"" : '';?>>Both</option>
                </select>

            </div> 

        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>