<?php 

  $ci =&get_instance();
  $ci->load->model('Advertcategorymodel');

  $c = 0;
  $column_count = 0;
foreach($fields as $f){
	//alt_row/row
	//var_dump($f);
            //echo $f['fi_type'].'_'.$listing['ad_'.$f['adv_column']];
            //echo '@'.$f['fi_type'].'@';
            $data_text = '';
            //$pricing = '';

              if($f['fi_type']!='checkboxes'){
                if($f['adv_datasourceId']!=0){ 
                  
                  //echo '###';
                  $value = $ci->Advertcategorymodel->getDataValue($listing['ad_'.$f['adv_column']]);
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
                  }else if($f['fi_type']=='signature'){
                    $img = '';
                    //echo $listing['ad_'.$f['adv_column']];
                    $path = $listing['ad_'.$f['adv_column']];
                    if($path!=''){
                          $type = pathinfo($path, PATHINFO_EXTENSION);
                          $data = file_get_contents($path);
                          $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                          $img = '<img src="'.$base64.'" style="width:250px;">';
                    }
                    $data_text.=$img;
                  }else if($f['fi_type']=='text'){
                    
                    $data_text.=$f['adv_post_text'].'@';

                  }else if($f['fi_type']=='image'){
                      
                    $images = $ci->Advertcategorymodel->getImages($listing['ad_id'], $f['adv_id']);
                    $i = 0;
                    $c = 0;
                    
                    if(sizeof($images) > 0){
                      // Start the table
                      $data_text = '<table style="width:100%;">';
                      $c = 0; // Initialize counter
                      $cx = 0;

                      // Iterate through the images
                      foreach ($images as $i) {
                          list($width, $height, $type, $attr) = getimagesize('assets/uploaded_images/' . $i['up_filename']);
                          $path1 = 'assets/uploaded_images/' . $i['up_filename'];
                          $type = pathinfo($path1, PATHINFO_EXTENSION);
                          $data_i = file_get_contents($path1);
                          $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_i);

                          // Check if the counter is 0, indicating a new row in the table
                          if ($c == 0) {
                              $data_text .= '<tr>';
                          }

                          // Add an image and caption in a table cell
                          $data_text .= '<td style="width:50%; vertical-align:top;text-align:center;"><img src="' . $base64 . '" style="max-height:200px;"><br>' . $i['up_caption'] . '</td>';
                          //$data_text .= '<td style="width:50%; vertical-align:top;text-align:center;"><br>' . $i['up_caption'] . '</td>';

                          $c++; // Increment counter
                          $cx++;

                          // Check if the counter is 2, indicating the end of a row in the table
                          if ($c == 2) {
                              $c = 0; // Reset counter
                              $data_text .= '</tr>';
                          }


                          // Check if the counter is 8, indicating the end of the table
                          if ($cx == 8) {

                              if (($c < 2) && ($c != 0)) {
                                $c = 0; // Reset counter
                                $data_text .= '</tr>';
                              }
                              $cx = 0; // Reset counter
                              $c = 0;
                              $data_text .= '</table>'; // Close the table

                              // Add a <div> to force a page break
                              $data_text .= '<div style="page-break-before: always;"></div>';

                              // Start a new table
                              $data_text .= '<table style="width:100%;">';
                          }
                        }
                        /*
                        if($c < 2){
                          $data_text .= '</tr>';
                        }
                        */
                        // Close the table
                        $data_text .= '</table>';
                      }

                  }else{
                    //echo '#';
                    //echo $f['fi_type'].'#';
                    //echo 'ad_'.$f['adv_column'];
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
              //echo '#'.$data_text.'<br>';
              if($f['fi_type']=='image'){ ?>
              <tr class="row<?php /*echo ($c++%2==1)?'row':'alt_row'*/?>" style="<?php if(trim($data_text)=='')/*||($data_text==0)*/{?>display:none;<?php } ?>">
                <td align="center" style="width:100%;" colspan="2">
                  <h4><?php echo $f['adv_text']?></h4>
                  <?php echo $data_text;?>
                </td>
              </tr>
              <?php }else{

                if($f['fi_type']=='text'){
                ?>
                <tr class="row">
                      <td colspan="2">
                      <?php echo $f['adv_text']?>
                      <br>
                      <?php echo $data_text;?>
                      </td>
                </tr>
                <?php
                }else if($f['adv_column']=='IncludeSignature'){
                  if($data_text=='Yes'){
                    ?>
                    <tr class="row">
                      <td colspan="2">
                        <?php 
                          if($format=='doc'){
                        ?>
                          <img src="<?php echo base_url('assets/img/phil_signature.jpg');?>">
                        <?php
                        }else{
                        ?>
                          <img src="assets/img/phil_signature.jpg">
                        <?php } ?>
                      </td>
                    </tr>
                    <?php
                  }
                }else{

                  $path = dirname(dirname(__FILE__)).'/../';
                  //echo $path;
                  $dirs = scandir($path.'/modules/');
                  $modules = array_diff($dirs, array('.', '..'));
                  //print_r($modules);

                  if(in_array(strtolower($f['fi_type']), $modules)){
              
                    $data['field']    = $f;
                    $data['listing']  = $listing;
                    $result = Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/field_pdf', $data);
                    ?>
                    <tr class="row">
                      <td width="33%" class="bordered"><?php echo $f['adv_text']?></td>
                      <td width="66%" class="bordered"><?php echo $result;?></td>
                    </tr>
                    <?php
                        
                  }else{

                      //if we have some tables in it, then give it its own row
                      if (preg_match('/<table/', $data_text) == false) { 
                      //echo '<div style="page-break-before: always;"></div>';
                      ?>
                      <tr class="row<?php /*echo ($c++%2==1)?'row':'alt_row'*/?>" style="<?php if(trim($data_text)=='')/*||($data_text==0)*/{?>display:none;<?php } ?>">
                        <td width="33%" class="bordered"><?php echo $f['adv_text']?></td>
                        <td width="66%" class="bordered"><?php 
                          if($f['fi_type'] == 'date'){
                            echo date('d/m/Y', (int)$data_text);
                          }else{
                            echo $data_text;
                          }
                          ?></td>
                      </tr>
                      <?php
                      } else { 
                        ?>
                        <tr class="row<?php /*echo ($c++%2==1)?'row':'alt_row'*/?>" style="<?php if(trim($data_text)=='')/*||($data_text==0)*/{?>display:none;<?php } ?>">
                        <td width="100%" class="bordered" colspan="2"><?php echo $data_text;?></td>
                        </tr>
                        <?php
                      }  
                  }
                }
              }
              $c++;

} ?>




<tr>
<td colspan="2">
	<div style="padding-top:3px;"></div>
</td>
</tr>
</table>