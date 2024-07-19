<?php 
  $ci =&get_instance();
  $ci->load->model('Advertcategorymodel');

foreach($fields as $f){

            $data_text = '';
              if($f['fi_type']!='checkboxes'){
                if($f['adv_datasourceId']!=0){ 
                  
              
                      $value = $ci->Advertcategorymodel->getDataValue($listing['ad_'.$f['adv_column']]);
                      if($value['ds_value']!=''){
                        $data_text = '<label class="control-label"><b>'.$value['ds_value'].'</b></label>';   
                      }
                      
                      if($f['adv_associated_fieldId']!='0'){
                        $column = $ci->Advertcategorymodel->getColumnById($f['adv_associated_fieldId']);   
                        $value = $ci->Advertcategorymodel->getDataValue($listing['ad_'.$column]);
                        //var_dump($value);
                        if($value['ds_value']!=''){
                          $data_text = '&nbsp;<label class="control-label"><b>'.$value['ds_value'].'</b></label>';
                        }
                      }
                
                }else if($f['adv_linkedtableid']!=0){

                  $table = $ci->db->where('ft_id', $f['adv_linkedtableid'])->get('pt_forms_tables')->row_array();
                  //echo $this->db->last_query();
                  if($table['ft_is_users']==1){

                    $result = $ci->db->where('us_id', $listing['ad_'.$f['adv_column']])->get('pt_user')->row_array();
                    $data_text = '<label class="control-label"><b>'.$result['us_firstName'].' '.$result['us_surname'].'</b></label>'; 
                    
                  }else{

                    $first_fields = $ci->db->select('adv_column')->where('adv_table', $table['ft_database_table'])->get('advert_fields')->row_array()['adv_column'];
                    $result = $ci->db->where('ad_id', $listing['ad_'.$f['adv_column']])->get($table['ft_database_table'])->row_array();
                    if(!empty($result)){
                      $data_text = '<div class="control-label"><b>'.$result['ad_'.$first_fields].'</b></div>'; 
                    }else{
                      $data_text = '<div class="control-label"><b>-</b></div>'; 
                    }
                  }

                }else{

                  if($f['fi_type']=='radioBool'){
                    switch($listing['ad_'.$f['adv_column']]){
                      case 0:
                        $data_text = '<div class="control-label"><b>No</b></div>';
                      break;
                      case 1: 
                        $data_text = '<div class="control-label"><b>Yes</b></div>';
                      break;
                    }
                  }else if($f['fi_type']=='image'){

                    $data_text = 'image';

                    $images = $this->Advertcategorymodel->getImages($listing['ad_id'], $f['adv_id']);
                    //print_r($images);
                    $field_images['images'] = $images;
                    $data_text = '<ul style="list-style-type: none;">'.$this->load->view('forms/uploaded_image_text', $field_images, true).'</ul>';

                  }else if($f['fi_type']=='table'){

                    $field_data1['column']   =  $f['adv_column'];
                    $field_data1['field']    =  $f;
                    $field_data1['listing']  = $listing;
                    $data_text = Modules::run('table/table/field_pdf', $field_data1); 


                  }else{
                    if($listing['ad_'.$f['adv_column']]!=''){

                      $data_text = '<div class="control-label"><b>'.$listing['ad_'.$f['adv_column']].'</b></div>';
                    
                    }
                  }
                }
              }else{
                $ds = explode(',',$listing['ad_'.$f['adv_column']]);
                //var_dump($ds);
                $size = sizeof($ds);
                $count = 0;
                foreach($ds as $d){
                  $value = $ci->Advertcategorymodel->getDataValue($d);  
                  if($value['ds_value']!=''){
                    $data_text.='<div class="control-label"><b>'.$value['ds_value'].'</b></div>';
                    $count++;
                    if($count<($size-1)){
                      $data_text.=', ';
                    }
                  }
                }
              }
              ?>	

            <div class="row" style="margin-left:0px;<?php if(trim($data_text)==''){?>display:none;<?php } ?>">

            <label  class="col-sm-<?php echo $width?> col-md-<?php echo $width?> control-label" <?php if($width==4){?>style="text-transform:uppercase;"<?php } ?>><?php echo $f['adv_text']?><?php if($f['adv_required']==1){echo "<span style=\"color:red;\">*</span>";}?>:&nbsp;<?php if($f['adv_info']!=''){?><a href="#" class="forTooltip" tabindex="0" data-trigger="focus" rel="popover" data-content="<?php echo nl2br($f['adv_info'])?>" data-original-title="<?php echo $f['adv_text']?>&nbsp;<a href='#' class='pull-right'>X</a>"><i class="fa fa-info-circle"></i></a><?php } ?></label>

            <?php if($f['adv_post_text']!=''){?>
            	<div class="col-md-3 col-sm-3">
            <?php }else{?>
              <div class="col-md-6 col-sm-6">
            <?php } ?>
                  
            
              <?php echo $data_text;?>

                </div>
            <?php if($f['adv_post_text']!=''){?>
            	<div class="col-md-3 col-sm-3  control-label pull-left" style="text-align: left !important;">
                <?php //echo $f['adv_post_text']?>
                </div>
            <?php } ?>
          </div>

<?php } ?>