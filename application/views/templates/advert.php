<?php 
foreach($fields as $f){
  //check its not related to a module
  $data['field']    = $f;
  $data['listing']  = $listing;
  $data['table']    = $f['adv_table'];
  $data['formID']   = $formID;
  $data['moduleCategory']   = $moduleCategory;
//echo $data['table'];

  if($f['adv_hidden'] == 1){
  ?>
    <div style="display:none;">
  <?php
  }
  $module_name = $f['fi_type'].'/'.ucfirst($f['fi_type']);
  $method ='/field';
  $result = NULL;

    try {
      
      $result = Modules::run($module_name.'/field', $data);
    } catch (Exception $e) {

    }

  //}
  if(!is_null($result)){
    echo $result;
  }else{

    //ad_ecoSignature
    if($f['fi_type']=='signature'){
      $img = '';
      //echo $listing['ad_'.$f['adv_column']];
      if(is_array($listing)){
        if(array_key_exists('ad_'.$f['adv_column'], $listing)){
          $path = $listing['ad_'.$f['adv_column']];
          if($path!=''){
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $img = '<div class="holder '.$f['adv_column'].'_edit"><img src="'.$base64.'"><br><a href="javascript:show(\''.$f['adv_column'].'\');"/>Edit</a></div><br>';
          }
        }
      }
    }
    //var_dump($f);?>			

    <div class="container">

    <div class="row"  id="id_<?php echo $f['adv_id']?>">

              <?php if(/*($f['fi_type']=='signature')||*/($f['fi_type']=='text')){ ?>
                <label class="col-md-12">

                  <?php echo $f['adv_text']?>
                </label>
                <div class="col-md-12 col-sm-12">
                  <?php 
                    if($f['fi_type']!='text'){
                      if($img!=''){
                        echo '<br>'.$img;
                        echo '<div class="'.$f['adv_column'].'_show hide">';
                        echo $f['html'];
                        echo '</div>';
                      }else{
                        echo $f['html'];  
                      }
                      
                    }else{
                      echo $f['adv_post_text'];
                    } 
                  ?>
                </div>
              <?php }else{ ?>

              <?php //if($f['fi_type']!='textarea'){?>
              <label  class="col-sm-<?php echo $width?> col-md-<?php echo $width?> control-label" <?php if($width==4){?>style="text-transform:uppercase;"<?php } ?>><?php echo $f['adv_text']?><?php if($f['adv_required']==1){echo "<span style=\"color:red;\">*</span>";}?>:&nbsp;<?php if($f['adv_info']!=''){?><a href="#" class="forTooltip" tabindex="0" data-trigger="focus" rel="popover" data-content="<?php echo nl2br($f['adv_info'])?>" data-original-title="<?php echo $f['adv_text']?>&nbsp;<a href='#' class='pull-right'>X</a>"><i class="fa fa-info-circle"></i></a><?php } ?></label>
              <?php //} ?>
              <?php if(trim($f['html'])!=''){?>

              <?php if($f['adv_post_text']!=''){?>
              	<div class="col-md-3 col-sm-3 <?php if(in_array($f['adv_column'], $missing_links)){ echo 'bg-warning'; }?>">
              <?php }else{?>
              	<!--<div class="col-md-<?php if($f['fi_type']!='textarea'){?>6<?php }else{ ?>12<?php } ?> col-sm-<?php if($f['fi_type']!='textarea'){?>6<?php }else{ ?>12<?php } ?>">-->
                <div class="col-md-6 col-sm-6 <?php if(in_array($f['adv_column'], $missing_links)){ echo 'bg-warning'; }?>">
              <?php } ?>
                    <?php       
                      $i = 1;
                      $html2 = '';

                      $this->db->select('*');
                      $this->db->where('field_id', $f['adv_id']);
                      $prefill_fields = $this->db->get('prefills')->result_array();
                      //echo $this->db->last_query();
                      //print_r($f);
                      //this doesn't work because I don't know the field type
                      /*
                      $prefills = [];
                      foreach($prefill_fields as $p){
                        $pp = $p['text'];
                        foreach($listing as $key => $value){
                          $k = str_replace('ad_', '', $key);
                          $pp = str_replace('{'.$k.'}', $value, $pp);
                          //echo '{'.$k.'}'.$value.'#';
                          //echo $pp;

                        }
                        $prefills[] = array('text' => $pp, 'name' => $p['name']);
                      }
                      */
                      if(is_array($prefill_fields) && (sizeof($prefill_fields) > 0)){
                        $html2.= '<h5>Prefill text options</h5>';
                      }
                      foreach($prefill_fields as $p){
                        $html2.= '<a class="btn btn-primary prefill_button" data-text="'.htmlentities($p['text']).'">'.$p['name'].'</a> ';
                        $i++;
                      }
                      if($html2 !== ''){
                        $html2 .= ' <a href="'.base_url('Prefilltext/index/'.$f['adv_id']).'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                      }
                      echo $html2;
                    ?>
                    <div id="field_<?php echo $f['adv_id'];?>">
                    <?php echo $f['html']?>
                    </div> 
                </div>
              <?php } ?>
              <?php if($f['adv_post_text']!=''){?>

              	  <div class="col-md-3 col-sm-3  control-label pull-left" style="text-align: left !important;">
                    <?php echo $f['adv_post_text']?>
                  </div>

              <?php } ?>

              <?php if($f['adv_link']!=''){?>

                  <div class="col-md-3 col-sm-3 offset-md-6 control-label pull-left" style="text-align: left !important;">
                    
                    <?php 
                    $value =  $f['adv_link'];
                    foreach($data['listing'] as $k => $v){
                      $key = str_replace('ad_', '', strtolower($k));

                      $value = str_replace('{'.$key.'}', strtolower($v), $value);
                      
                    }
                    $display = strlen($value) > 30 ? substr($value,0,30)."..." : $value;
                    ?>
                    <a href="<?php echo $value;?>" class="btn btn-primary" target="_blank">Visit <?php echo $display; ?></a>
                  </div>

              <?php } ?>

            <?php } ?>
            </div>
            <div class="dashed-grey"></div>
            <?php /*echo $f['adv_info']*/?>
  </div>
  <?php
  }

  if($f['adv_hidden'] == 1){
  ?>
    </div>
  <?php
  }
}