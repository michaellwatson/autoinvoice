<?php 
$this->load->helper('files');

foreach($documents as $d){?>
<li id="document_<?php echo $d['up_id'];?>">
<div class="row" style="width:100%;">
    <label  class="col-sm-1 col-md-1 control-label"><i class="fa fa-arrows" aria-hidden="true"></i></label>
    <div class="col-sm-8 col-md-8 control-label" style="text-align: left;">
        <a href="<?php echo base_url('assets/uploads/'.$d['up_filename']);?>" target="_blank">

        	<i class="fa <?php echo get_file_icon($d['up_filename']);?>" aria-hidden="true"></i>
        	
        	<?php echo $d['up_filename'];?>
        		
        </a>
    </div>
    <div class="col-sm-3 col-md-3 control-label pull-right">
    	<button class="btn btn-danger btn_delete_document" data-id="<?php echo $d['up_id'];?>" data-type="document"><i class="fa fa-trash" aria-hidden="true"></i></button>
    </div>
</div>
<div class="dashed-grey"></div>
</li>
<?php } ?>
