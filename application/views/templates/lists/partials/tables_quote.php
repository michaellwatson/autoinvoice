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
                <td><a href="<?php echo base_url();?>Post/create_form/<?php echo $cat_id['ad_id'];?>?formID=<?php echo $t['ft_id'];?>"><?php echo $t['ft_name']?></a></td>
               
                <td class="table-action pull-right">

                  <a href="#" data-toggle="tooltip" data-placement="bottom" title="Allow Imports">
                    <i class="fa fa-upload <?php echo ($t['ft_import']==1)? 'selected_search' :'unselected_search';?> import" aria-hidden="true" data-id="<?php echo $t['ft_id']?>" data-include="<?php echo ($t['ft_import']==1)? 0 :1;?>"></i>                 
                  </a>

                  <a href="#" data-toggle="tooltip" data-placement="bottom" title="Export as PDF" class="btn-pdf" data-id="<?php echo $t['ft_id']?>" data-include="<?php echo ($t['ft_pdf']==1)? 0 :1;?>">
                    <i class="fa fa-file-pdf-o <?php echo ($t['ft_pdf']==1)? 'selected_search' :'unselected_search';?>" aria-hidden="true"></i>
                  </a>

                  <a href="#" data-toggle="tooltip" data-placement="bottom" title="Export as Pages" class="btn-word" data-id="<?php echo $t['ft_id']?>" data-include="<?php echo ($t['ft_word']==1)? 0 :1;?>">
                    <i class="fa fa-file-text-o <?php echo ($t['ft_word']==1)? 'selected_search' :'unselected_search';?>" aria-hidden="true"></i>
                  </a>

                  <a href="#" data-toggle="tooltip" data-placement="bottom" title="Accessable via API" class="btn-api" data-id="<?php echo $t['ft_id']?>" data-include="<?php echo ($t['ft_api']==1)? 0 :1;?>">
                    <i class="fa fa-play <?php echo ($t['ft_api']==1)? 'selected_search' :'unselected_search';?>" aria-hidden="true"></i>
                  </a>

                	<a href="<?php echo base_url();?>Post/create_form/<?php echo $cat_id['ad_id'];?>?formID=<?php echo $t['ft_id'];?>" data-toggle="tooltip" data-placement="bottom" title="Add an Entry">
                  <i class="fa fa-plus"></i>
                  </a>

                  <a href="<?php echo base_url();?>entries/show/<?php echo $cat_id['ad_id'];?>?formID=<?php echo $t['ft_id'];?>" data-toggle="tooltip" data-placement="bottom" title="View entries">
                  <i class="fa fa-list" aria-hidden="true"></i>
                  </a>

                  <a href="<?php echo base_url();?>documents/advertAdmin/<?php echo $cat_id['ad_id'];?>?formID=<?php echo $t['ft_id'];?>" data-toggle="tooltip" data-placement="bottom" title="Edit form fields">
                  <i class="fa fa-edit" aria-hidden="true"></i>
                  </a>
              
                </td>
            </tr>
<?php 
            }
	}
} ?>