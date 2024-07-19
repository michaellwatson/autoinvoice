<div class=""  id="id_<?php echo $id;?>">
  <div class="form-group">
    <?php if($ad_id!=''){ ?>
        <input type="hidden" name="<?php echo $name;?>_id[]" id="<?php echo $name;?>_id[]" class="form-control" value="<?php if(isset($ad_id)){echo $ad_id;}?>">
    <?php } ?>
    <div class="col-md-1 col-sm-1" style="text-align: right;padding-top: 13px;cursor: pointer;">
      <i class="fa fa-minus" aria-hidden="true" onclick="javascript:removeField('<?php echo $id;?>');"></i>
    </div>

    <div  class="col-sm-5 col-md-5">
      <input type="text" name="<?php echo $name;?>_text[]" id="<?php echo $name;?>_text[]" class="form-control" value="<?php if(isset($text)){echo $text;}?>" placeholder="Name">
    </div>

    <div class="col-md-6 col-sm-6">
      <input type="text" name="<?php echo $name;?>[]" id="<?php echo $name;?>[]" class="form-control" value="<?php if(isset($value)){echo $value;}?>" placeholder="Price Ex VAT">
    </div>

    <div class="col-md-3 col-sm-3  control-label pull-left" style="text-align: left !important;">
    </div>
  </div>
  <div class="dashed-grey">
  </div>
</div>