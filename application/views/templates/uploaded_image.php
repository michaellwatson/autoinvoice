<?php 
foreach($images as $i){?>
<li id="image_<?php echo $i['up_id'];?>">
<div class="form-group">
    <div class="col-sm-3 col-md-3 control-label" style="text-align: left;">

        <img src="<?php echo base_url();?>/assets/uploaded_images/<?php echo $i['up_filename'];?>?time=<?php echo time();?>" class="img-thumbnail img-responsive">

        <button class="btn btn-danger" data-id="<?php echo $i['up_id'];?>" data-type="document">
	    	<i class="fa fa-trash-o"></i>
	    </button>
    </div>
    <div class="col-sm-7 col-md-7 control-label text-left">

        <input type="text" class="form-control" placeholder="Caption" id="caption_<?php echo $i['up_id'];?>" value="<?php echo $i['up_caption'];?>">

    </div>
    <div class="col-sm-2 col-md-2 control-label">
    	<button class="btn btn-success save_button ladda-button" data-id="<?php echo $i['up_id'];?>" data-type="caption" id="button_caption_<?php echo $i['up_id'];?>" data-style="expand-right">
	    	Save
	    </button>
    </div>
</div>
<div class="dashed-grey"></div>
</li>
<?php } ?>
