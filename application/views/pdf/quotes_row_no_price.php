<?php 

  $ci =&get_instance();
  $ci->load->model('Advertcategorymodel');

  $c = 0;

foreach($fields as $f){
	//alt_row/row
	//var_dump($f);?>		
            <?php
            $data_text = '';
            //$pricing = '';
              if($f['fi_type']!='checkboxes'){
                if($f['adv_datasourceId']!=0){ 
                  
                  //echo '###';
                  $value = $ci->Advertcategorymodel->getDataValue($listing['ad_'.$f['adv_column']]);
                  $pricing = $ci->Advertcategorymodel->getDataValuePricing($listing['ad_'.$f['adv_column']]);
                  //print_r($pricing);
                  
                  if($value['ds_value']!=''){
                  	//echo '##';
                  	
                    $data_text = $value['ds_value'];   
                  }
                  if($f['adv_associated_fieldId']!='0'){
                    $column = $ci->Advertcategorymodel->getColumnById($f['adv_associated_fieldId']);   
                    $value = $ci->Advertcategorymodel->getDataValue($listing['ad_'.$column]);
                    //$pricing = $ci->Advertcategorymodel->getDataValuePricing($listing['ad_'.$column]);
                    //var_dump($value);
                    if($value['ds_value']!=''){
                      $data_text = $value['ds_value'];
                    }
                  }
                  //echo $data_text;
                }else{
                  if($f['fi_type']=='radioBool'){
                    switch($listing['ad_'.$f['adv_column']]){
                      case 0:
                        $data_text = 'No';
                      break;
                      case 1: 
                        $data_text = 'Yes';
                      break;
                    }
                  }else{
                    if($listing['ad_'.$f['adv_column']]!=''){
                      $data_text = $listing['ad_'.$f['adv_column']];
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
                    $data_text.=$value['ds_value'];
                    $count++;
                    if($count<($size-1)){
                      $data_text.=', ';
                    }
                  }
                }
              }
              ?>	
            	<tr class="<?php echo ($c++%2==1)?'row':'alt_row'?>" style="<?php if(trim($data_text)==''){?>display:none;<?php } ?>">
      					<td width="33%" class="dashed" style="padding-left:20%;"><?php echo $f['adv_text']?></td>
      					<td width="33%" class="dashed"><?php /*echo $data_text;*/?><?php if(trim($data_text)==''){?>Not Applicable<?php }else{ echo $data_text;?></td>
      					<td class="dashed right">

      					</td>
      				</tr>
            
<?php 

} ?>




<tr>
<td colspan="3">
	<div style="padding-top:3px;"></div>
</td>
</tr>
<!--light header end-->