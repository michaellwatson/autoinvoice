<?php 
foreach($documents as $d){?>
<li id="document_<?php echo $d['do_id'];?>">
<div class="form-group">
    <label  class="col-sm-1 col-md-1 control-label"><img src="<?php echo base_url('assets/images/add-advert-photo-change-order.gif');?>"></label>
    <div class="col-sm-5 col-md-5 control-label" style="text-align: left;">
        <?php echo $d['do_name'];?>
    </div>
    <div class="col-sm-3 col-md-3 control-label pull-right">
    <button class="btn btn-danger btn_delete_document" data-id="<?php echo $d['do_id'];?>" data-type="document"><i class="fa fa-trash" aria-hidden="true"></i></button>
    </div>
</div>
<div class="dashed-grey"></div>
</li>
<?php } ?>
