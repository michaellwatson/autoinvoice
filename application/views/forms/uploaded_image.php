<?php 
//print_r($field);
foreach($images as $i){?>
<li id="image_<?php echo $i['up_id'];?>" <?php echo (int)$_SESSION['two-col']==0 ? 'class="one-col-width"' : '';?>>
    <div style="width:100%; position:relative;">     

        <a class="btn btn-danger1 deleteUploadedImage<?php echo $i['up_id'];?>" data-id="<?php echo $i['up_id'];?>" data-type="document" style="position: absolute; top: 0; right: 0; z-index: 1;">
            <i class="fa fa-times"></i>
        </a>
        <?php
                    $notfound = false;
                    //echo $_SERVER["DOCUMENT_ROOT"]."/assets/uploaded_images/".$i['up_filename'];
                    $size = @getimagesize($_SERVER["DOCUMENT_ROOT"]."/assets/uploaded_images/".$i['up_filename']);
                    
                    if ($size == false) {
                        $path = 'assets/images/notfound1.png';
                        //echo '##';
                        $notfound = true;
                        ?>
                        <a href="#">
                            <img src="<?php echo base_url($path);?>" class="img-thumbnail img-responsive">
                        </a>
                    <?php
                    }else{
                        $path = "/assets/uploaded_images/".$i['up_filename'];
                        ?>
                            <a href="<?php echo base_url();?>/assets/uploaded_images/<?php echo $i['up_filename'];?>?time=<?php echo time();?>" target="_blank" data-lightbox="files">
                                <img src="<?php echo base_url();?>/assets/uploaded_images/<?php echo $i['up_filename'];?>?time=<?php echo time();?>" class="img-thumbnail img-responsive">
                            </a>
                        <?php
                    }

        if(!$notfound){
        ?>
        <br>
        <div class="row">
            <div class="col-md-1 col-sm-1">
                <?php
                    list($width, $height, $type, $attr) = getimagesize("/assets/uploaded_images/".$i['up_filename']);
                ?>
                <i class="fa fa-crop crop_button" aria-hidden="true" data-image="<?php echo base_url();?>/assets/uploaded_images/<?php echo $i['up_filename'];?>?time=<?php echo time();?>" data-imgId="<?php echo $i['up_id'];?>" data-img="<?php echo $i['up_filename'];?>" data-w="<?php echo $width;?>" data-h="<?php echo $height;?>" data-field-id="<?php echo $field_id;?>" data-column="<?php echo $field[0]['adv_column'];?>"></i>
            </div>
            <div class="col-md-6 col-sm-6 caption-padding">
                <input type="text" class="form-control" placeholder="Caption" id="caption_<?php echo $i['up_id'];?>" value="<?php echo $i['up_caption'];?>">
            </div>

            <div class="col-md-6 col-sm-6">
                <button class="btn btn-primary save_button ladda-button caption_button" data-id="<?php echo $i['up_id'];?>" data-type="caption" id="button_caption_<?php echo $i['up_id'];?>" data-style="expand-right" style="width:100%;">
                    Save
                </button>
            </div>
        </div>
        <?php } ?>
    </div>
</li>
<script type="text/javascript">
    $(document).off('click', '.deleteUploadedImage<?php echo $i['up_id'];?>').on('click', '.deleteUploadedImage<?php echo $i['up_id'];?>', function(e){
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
                url: '<?php echo base_url('Post/deleteImages')?>',
                data: {'id':dataid, ptBlocksMultipleDatasId},
                dataType:'json',
                success: function(data)
                {
                    //alert('hit');
                    $('#image_'+dataid).remove();
                }
            });
        }
    });
    /*var jqueryObject = $('.deleteUploadedImage<?php echo $i['up_id'];?>');
    var rawDOMElement = jqueryObject.get(0);
    var eventObject = $._data(rawDOMElement, 'events');
    if(eventObject != undefined && eventObject.click != undefined){
        // alert("Click event already exists");
    }
    else{
        // alert("Click event does not exists");
    }
    if(!$(document).data('events') 
        || !$(document).data('events').click 
        || !$(document).data('events').click.some(event => event.selector === '.deleteUploadedImage<?php echo $i['up_id'];?>')
    ){
        console.log(".deleteUploadedImage<?php echo $i['up_id'];?>");
    }*/
</script>
<?php } ?>