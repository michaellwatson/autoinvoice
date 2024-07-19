<?php 
if(isset($tablesList)){
foreach($tablesList as $t){?>			
            <tr id="tab_<?php echo $t['ft_id'];?>">
                <td><a href="<?php echo base_url('admin/advertAdmin?formID='.$t['ft_id']);?>"><?php echo $t['ft_name']?></a></td>
               
                <td class="table-action">
                  


                <?php //if($c['ad_parent']>0){?>
                	<a href="#"><i class="fa fa-pencil"></i></a>
                <?php //} ?>
                <?php //if($c['ad_parent']>$depth){?>
                  <!--<a href="#" class="delete-row"><i class="fa fa-trash-o"></i></a>-->
                  <a href="#" class="delete-row" data-id="<?php echo $t['ft_id'];?>"><i class="fa fa-times"></i></a>
                <?php //} ?>
                </td>
              </tr>
<?php 
	}
} ?>