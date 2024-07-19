<?php 
$formID = isset($_GET['formID']) ? $_GET['formID'] : '';
if(isset($categories)){
foreach($categories as $c){?>			
            <tr id="cat_<?php echo $c['ad_id'];?>">
                <td><a href="<?php echo base_url('documents/advertAdmin/'.$c['ad_id']);?>?formID=<?php echo $formID;?>"><?php echo $c['ad_name']?></a></td>
               
                <td>
                    <select class="form-control change_role" name="role" data-id="<?php echo $c['ad_id'];?>">
                        <option value="">Select Role</option>
                        <?php foreach($roles as $r){?>
                            <option value="<?php echo $r->id;?>" <?php if($r->id == $c['ad_role']){ echo "selected=\"selected\""; };?>><?php echo $r->name;?></option>
                        <?php } ?>
                    </select>

                </td>
                <td class="table-action">
                
                <?php if($c['ad_parent']>0){?>
                    <a href="<?php echo base_url('documents/advertAdmin/'.$c['ad_id']);?>?formID=<?php echo $formID;?>"><i class="fa fa-pencil"></i></a>
                <?php }else{ ?>


                    <a href="<?php echo base_url('/Post/create_form/');?><?php echo $c['ad_id'];?>?formID=<?php echo $formID;?>">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        
                    </a>
                
                    <a href="<?php echo base_url('/documents/entries/');?><?php echo $c['ad_id'];?>?formID=<?php echo $formID;?>">
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
<td colspan="2">
    <button class="btn btn-primary ladda-button categoryAddButton" type="submit" id="categoryAddButton" data-style="expand-right" data-id="add">Add</button>
</td>
</tr>

<script>
    $(function () {
        
        $(document).on('change', '.change_role',function(e) {
            let val = $(this).val();
            let cat_id = $(this).attr('data-id');

            jQuery.ajax({
                type: "POST",
                url: base_url+'/Documents/update_role',
                data: {'val':val, 'cat_id':cat_id}, // serializes the form's elements.
                dataType:'json',
                success: function(data)
                {
 
                }
            });
        });
    });
</script>