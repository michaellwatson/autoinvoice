<?php 
$count = 0;
foreach($fields as $f){

  //check its not related to a module
  $data['field']    = $f;
  $data['listing']  = $listing;
  $width = 3;

  if($f['adv_hidden'] == 1){
  ?>
    <div style="display:none;">
  <?php
  }
  $data['count'] = $count;
  //echo $f['fi_type'];
  $result = Modules::run($f['fi_type'].'/'.ucfirst($f['fi_type']).'/field', $data);
  if(!is_null($result)){

    echo $result;

    $count++;
    if($count == 2){
      $count = 0;
    }
    //change to 2 column
  	if($f['fi_type'] == 'postcodelookupsettings'){
  		$count = 0;
  	}
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
    //var_dump($f);
    //echo $f['fi_type'].'#';

    switch($f['fi_type']){

    	case 'textarea':
    		$label_width = 2;
    	    $count = 0;
    		$width = 10;
    		$label = true;
    	break;
    	case 'image':
    		//force a newlin
    		$label_width = 2;
    		$count = 0;
    		$width = 10;
    		$label = true;
    	break;

    	default:
    		$label_width = 2;
    		$width = 4;
    		$label = true;
    	break;

    }


    ?>			
	
	<?php if($count == 0){?>
    <div class="container-fluid">
      
      <div class="row"  id="id_<?php echo $f['adv_id']?>">
    <?php } ?>

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

          <label  class="col-sm-<?php echo $label_width?> col-md-<?php echo $label_width?> control-label <?php if(!$label) {?>d-none<?php } ?>"><?php echo $f['adv_text']?>
            <?php if($f['adv_required']==1){echo "<span style=\"color:red;\">*</span>";}?>:&nbsp;
            <?php if($f['adv_info']!=''){?><a href="#" class="forTooltip" tabindex="0" data-trigger="focus" rel="popover" data-content="<?php echo nl2br($f['adv_info'])?>" data-original-title="<?php echo $f['adv_text'] ?>&nbsp;<a href='#' class='pull-right'>X</a>">
            <i class="fa fa-info-circle"></i></a>
            <?php } ?>
          </label>


          <?php if(trim($f['html'])!=''){?>

            <?php if($f['adv_post_text']!=''){?>
            	<div class="col-md-<?php echo $width;?> col-sm-<?php echo $width;?> <?php echo (!$label) ? 'col-md-offset-2 col-sm-offset-2' : ''; ?>">
            <?php }else{?>
            	<!--<div class="col-md-<?php if($f['fi_type']!='textarea'){?>6<?php }else{ ?>12<?php } ?> col-sm-<?php if($f['fi_type']!='textarea'){?>6<?php }else{ ?>12<?php } ?>">-->
              <div class="col-md-<?php echo $width;?> col-sm-<?php echo $width;?> <?php echo (!$label) ? 'col-md-offset-2 col-sm-offset-2' : ''; ?>">
            <?php } ?>

            <?php 

              if(!$label){
              	echo $f['adv_text']; 
              	echo ($f['adv_required']==1) ? "<span style=\"color:red;\">*</span>" : ''.'<br>';
              }          
              $i = 1;
              $html2 = '';

              $this->db->select('*');
              $this->db->where('field_id', $f['adv_id']);
              $prefill_fields = $this->db->get('prefills')->result_array();
              //echo $this->db->last_query();
              //print_r($f);

              foreach($prefill_fields as $p){
                $html2.= '<a class="btn btn-primary prefill_button" data-text="'.htmlentities($p['text']).'">'.$p['name'].'</a>';
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

          	  <div class="col-md-<?php echo $width;?> col-sm-<?php echo $width;?>  control-label pull-left" style="text-align: left !important;">
                <?php echo $f['adv_post_text']?>
              </div>

          <?php } ?>

          <?php if($f['adv_link']!=''){ ?>

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

        <?php } 

        switch($f['fi_type']){

	    	case 'textarea':
	    	case 'image':
	    		//force a newline
	    		$count = 0;
	    		?>
	    			</div>
			        <div class="dashed-grey"></div>
			    	</div>
	    		<?php
	    	break;
	    	default:
	    	    if($count % 2){?>
			        </div>
			        <div class="dashed-grey"></div>
			    	</div>
        		<?php 
        		} 
	    		$count++;
	    	break;

    	}

    
    if($count == 2){
      $count = 0;
    }
  }

  if($f['adv_hidden'] == 1){
  ?>
    </div>
  <?php
  }
}