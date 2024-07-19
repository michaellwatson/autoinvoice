<?php 
$num = 1;
foreach($fields as $f){
	?>			
  <div class="col-md-6 card">
    <div class="card-body">
      <h3 class="card-title">
          <?php echo $f['name'];?>
          <input type="hidden" class="name_<?php echo $f['id'];?>" value="<?php echo $f['name'];?>">
      </h3>

      <div class="card-text data_<?php echo $f['id'];?>">
        <?php
          if($ptAdvertFieldsRecord->adv_field_type == 23){
            echo $f['json_string'];
          }
          else{
            echo $f['text'];
          }
        ?>
      </div>
        <a href="#" class="edit_row card-link" data-id="<?php echo $f['id'];?>">
          <i class="fa fa-pencil" aria-hidden="true" ></i>
        </a>
        <a href="#" class="delete_row card-link" data-id="<?php echo $f['id'];?>">
          <i class="fa fa-times" aria-hidden="true" ></i>
        </a>
    </div>
  </div>
<?php 
$num++;
} ?>