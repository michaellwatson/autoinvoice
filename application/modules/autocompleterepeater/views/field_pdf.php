<?php foreach($items_rows as $i){ ?>
<b><?php echo $i->$display_field_column;?></b>
<br>
<?php echo strip_tags($i->ad_info);?>
<?php } ?>


