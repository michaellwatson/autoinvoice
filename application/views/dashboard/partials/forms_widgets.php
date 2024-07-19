<?php 
if(isset($tablesList)){
foreach($tablesList as $t){?>		
            <?php 
              if($t['ft_categories']!==''){
              $obj =& get_instance();
              $obj->db->select('ad_id');
              $obj->db->where('ad_parent',0);
              $cat_id = $obj->db->get($t['ft_categories'])->row_array();
            ?>	
            <tr id="tab_<?php echo $t['ft_id'];?>">
                <!--
                <td><a href="<?php echo base_url();?>Post/create_form/<?php echo $cat_id['ad_id'];?>?formID=<?php echo $t['ft_id'];?>"><?php echo $t['ft_name']?></a></td>
                -->
                <td><?php echo $t['ft_name']?></td>
                <td class="table-action pull-right">


                	<a href="<?php echo base_url();?>Dashboard/create_widget/<?php echo $t['ft_id'];?>" data-toggle="tooltip" data-placement="bottom" title="Add a Widget">
                  <i class="fa fa-plus"></i>
                  </a>

              
                </td>
            </tr>
<?php 
            }
	}
} ?>