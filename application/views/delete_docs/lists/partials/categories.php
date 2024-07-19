<?php 
$formID = isset($_GET['formID']) ? $_GET['formID'] : '';
if(isset($categories)){
foreach($categories as $c){?>			
            <tr id="cat_<?php echo $c['ad_id'];?>">
                <td><a href="<?php echo base_url('admin/advertAdmin/'.$c['ad_id']);?>?formID=<?php echo $formID;?>"><?php echo $c['ad_name']?></a></td>
               
                <td class="table-action">
                
                <?php if($c['ad_parent']>0){?>
                    <a href="#"><i class="fa fa-pencil"></i></a>
                <?php }else{ ?>


                    <a href="<?php echo base_url('/Post/create_form/');?><?php echo $c['ad_id'];?>?formID=<?php echo $formID;?>">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        
                    </a>
                
                    <a href="<?php echo base_url('/admin/entries/');?><?php echo $c['ad_id'];?>?formID=<?php echo $formID;?>">
                        <i class="fa fa-list" aria-hidden="true"></i>
                    </a>
                <?php } ?>
                <?php //if($c['ad_parent']>$depth){?>
                  <a href="#" class="delete-row" data-id="<?php echo $c['ad_id'];?>"><i class="fa fa-times"></i></a>
                <?php //} ?>
                </td>
              </tr>
<?php 
	}
} ?>
<tr>
<td>
    <input type="text" name="valueAdd" id="valueAdd" value="">
</td>
<td>
    <button class="btn btn-success ladda-button categoryAddButton" type="submit" id="categoryAddButton" data-style="expand-right" data-id="add">Add</button>
</td>
</tr>
