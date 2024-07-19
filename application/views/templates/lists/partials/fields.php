<?php 
$num = 1;
foreach($fields as $f){
	//var_dump($f);
	?>			
            <tr id="sort_<?php echo $f['adv_id']?>">
                <td>
                  <i class="fa fa-arrows" aria-hidden="true"></i> <?php echo $f['fi_name']?>
                  <?php
                      $path = FCPATH.'application/';

                      $module_loaded = false;

                      if(is_dir($path.'modules/'.$f['fi_type'])){
                        //method exists would be more elegant
                        try{
                          $field_name = Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/get', 'field_name');

                          if(strtolower($field_name) == strtolower($f['fi_type'])){

                            echo Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/config', $f);
                            
                            $module_loaded = true;
                          }
                        }catch(Exception $e){
                          $module_loaded = false;
                        }
                      }
                  ?>

                </td>
                <td><?php echo $f['adv_text']?></td>
                <td><?php echo $f['html']?></td>
                <td><div class="cropText"><?php echo $f['adv_post_text']?></div></td>
                <td><?php echo $f['adv_info']?></td>
                <td class="text-center">
                  <h2>
                    <i class="fa fa-check <?php echo ($f['adv_search']==1)? 'selected_search' :'unselected_search';?> in_search" aria-hidden="true" data-id="<?php echo $f['adv_id']?>" data-include="<?php echo ($f['adv_search']==1)? 0 :1;?>"></i>
                  </h2>
                </td>
                <td class="text-center">
                  <h2>
                    <i class="fa fa-check <?php echo ($f['adv_show_field']==1)? 'selected_search' :'unselected_search';?> in_display" aria-hidden="true" data-id="<?php echo $f['adv_id']?>" data-include="<?php echo ($f['adv_show_field']==1)? 0 :1;?>"></i>
                  </h2>
                </td>
                <td class="table-action">


                  <a class="<?php echo ($f['adv_api']==1)? 'selected_search' :'unselected_search';?> api" data-id="<?php echo $f['adv_id']?>">
                    <i class="fa fa-code"></i>
                  </a>
                  <a href="<?php echo base_url('Prefilltext/index/'.$f['adv_id'].'');?>">
                    <i class="fa fa-check fa-pencil-square-o"></i>
                  </a>
 
                  <a href="javascript:showAddField('<?php echo $f['adv_id']?>');"><i class="fa fa-pencil"></i></a>
                 <!-- <a href="#" class="delete-row"><i class="fa fa-trash-o"></i></a>-->
                 <?php if($f['adv_field_type']==4){?>
                  <a href="javascript:showAddField('<?php echo $f['adv_associated_fieldId']?>');"><i class="fa fa-pencil"></i></a>
                <?php } ?>
                <?php if($f['adv_datasourceId']!=0){?>
                  <a href="javascript:editDS('<?php echo $f['adv_datasourceId'];?>', '<?php echo $f['adv_id'];?>');"><i class="fa fa-database" aria-hidden="true"></i></a>
                <?php } ?>
                <?php //get datasource from field ?>
                <?php 
                 $CI =& get_instance();
                 $CI->db->select('*');
                 $CI->db->where('adv_id',$f['adv_associated_fieldId']);
                 $associated_field = $CI->db->get('advert_fields')->row_array();
                ?>
                <?php if($associated_field['adv_datasourceId']!=0){?>
                  <a href="javascript:editDS('<?php echo $associated_field['adv_datasourceId'];?>', '<?php echo $f['adv_id'];?>');"><i class="fa fa-database" aria-hidden="true"></i></a>
                <?php } ?>
                <a href="#" class="deleteField">
                  <i class="fa fa-times deleteField" aria-hidden="true" data-id="<?php echo $f['adv_id'];?>"></i>
                </a>
                </td>
              </tr>
<?php 
$num++;
} ?>