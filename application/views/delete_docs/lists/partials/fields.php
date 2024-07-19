<?php 
$num = 1;
foreach($fields as $f){
	//var_dump($f);
	?>			
            <tr id="sort_<?php echo $f['adv_id']?>">
                <td><?php echo $f['fi_name']?></td>
                <td><?php echo $f['adv_text']?></td>
                <td><?php echo $f['html']?></td>
                <td><div class="cropText"><?php echo $f['adv_post_text']?></div></td>
                <td><?php echo $f['adv_info']?></td>
                <td></td>
                <td class="table-action">
                  <a href="<?php echo base_url('Prefilltext/index/'.$f['adv_id'].'');?>">
                    <span class="fa-stack fa-3x">
                      <i class="fa fa-circle-o fa-stack-2x"></i>
                      <strong class="fa-stack-1x">1</strong>
                    </span>
                  </a>
 
                  <a href="javascript:showAddField('<?php echo $f['adv_id']?>');"><i class="fa fa-pencil"></i></a>
                 <!-- <a href="#" class="delete-row"><i class="fa fa-trash-o"></i></a>-->
                 <?php if($f['adv_field_type']==4){?>
                  <a href="javascript:showAddField('<?php echo $f['adv_associated_fieldId']?>');"><i class="fa fa-pencil"></i></a>
                <?php } ?>
                <?php if($f['adv_datasourceId']!=0){?>
                  <a href="javascript:editDS('<?php echo $f['adv_datasourceId'];?>');"><i class="fa fa-gbp" aria-hidden="true"></i></a>
                <?php } ?>
                <?php //get datasource from field ?>
                <?php 
                 $CI =& get_instance();
                 $CI->db->select('*');
                 $CI->db->where('adv_id',$f['adv_associated_fieldId']);
                 $associated_field = $CI->db->get('advert_fields')->row_array();
                ?>
                <?php if($associated_field['adv_datasourceId']!=0){?>
                  <a href="javascript:editDS('<?php echo $associated_field['adv_datasourceId'];?>');"><i class="fa fa-gbp" aria-hidden="true"></i></a>
                <?php } ?>

                <i class="fa fa-times deleteField" aria-hidden="true" data-id="<?php echo $f['adv_id'];?>"></i>
                </td>
              </tr>
<?php 
$num++;
} ?>