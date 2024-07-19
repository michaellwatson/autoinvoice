<style type="text/css">
.btn-danger1 {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}
.btn-danger1:hover {
    color: #fff;
    background-color: #bb2d3b;
    border-color: #b02a37;
}
</style>
<?php 
$this->load->helper('files');

foreach($documents as $d){?>
<li id="document_<?php echo $d['up_id'];?>">
<div class="row" style="width:100%;">
    <label  class="col-sm-1 col-md-1 control-label"><i class="fa fa-arrows" aria-hidden="true"></i></label>
    <div class="col-sm-7 col-md-7 control-label" style="text-align: left;">
        <a href="<?php echo base_url('assets/uploads/'.$d['up_filename']);?>" target="_blank">

        	<i class="fa <?php echo get_file_icon($d['up_filename']);?>" aria-hidden="true"></i>
        	
        	<?php echo $d['up_filename'];?>
        		
        </a>
    </div>
    <div class="col-sm-1 col-md-1 control-label pull-right">
    	<?php echo $d['up_number_of_pages'];?>
    </div>
    <div class="col-sm-3 col-md-3 control-label pull-right">
        <!-- btn_delete_document -->
        <button class="btn btn-danger1  deleteUploadedDocument<?php echo $d['up_id'];?>" data-adv_column="<?php echo $d['adv_column'];?>" data-id="<?php echo $d['up_id'];?>" data-type="document"><i class="fa fa-trash" aria-hidden="true"></i></button>
    </div>
</div>
<div class="dashed-grey"></div>
</li>
<script type="text/javascript">
    $(document).off('click', '.deleteUploadedDocument<?php echo $d['up_id'];?>').on('click', '.deleteUploadedDocument<?php echo $d['up_id'];?>', function(e){
        e.preventDefault();
        if(confirm_delete()){
            <?php
                if(isset($additionalFormType) && $additionalFormType == 'edit'){
            ?>
            let ptBlocksMultipleDatasId = "<?php echo $ptBlocksMultipleDatasId ?>";
            <?php
                }
                else{
            ?>
            let ptBlocksMultipleDatasId = undefined;
            <?php
                }
            ?>
            var dataid = $(this).attr('data-id');
            //alert(dataid);
            $.ajax({
                type: "POST",
                url: '<?php echo base_url('Post/deleteDocuments')?>',
                data: {'id':dataid, ptBlocksMultipleDatasId},
                dataType:'json',
                success: function(data)
                {
                    $('#document_'+dataid).remove();
                }
            });
        }
    });
</script>
<?php } ?>
