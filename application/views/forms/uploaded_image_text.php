<?php 
foreach($images as $i){?>
<li id="image_<?php echo $i['up_id'];?>" <?php echo (int)$_SESSION['two-col']==0 ? 'class="one-col-width"' : '';?>>
    <div style="width:100%; position:relative;">     

        <a href="<?php echo base_url();?>/assets/uploaded_images/<?php echo $i['up_filename'];?>?time=<?php echo time();?>" target="_blank" data-lightbox="files">
            <img src="<?php echo base_url();?>/assets/uploaded_images/<?php echo $i['up_filename'];?>?time=<?php echo time();?>" class="img-thumbnail img-responsive">
        </a>
        <br>

    </div>
</li>
<?php } ?>
