<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

<div class="container">
    <div class="row" id="id_<?php echo $field['adv_id']?>">
      
        <label class="col-sm-6 col-md-6 control-label">
            <?php echo $field['adv_text']?>
            <?php if($field['adv_required']==1){ ?>
            <span style="color:red;">*</span>:&nbsp;
            <?php } ?>
        </label>
                            
        <div class="col-md-6 col-sm-6 text-left">

             <div id="field_<?php echo $field['adv_id']?>">

                <div class="dropdown">
                  <button class="btn btn-secondary dropdown-toggle <?php echo $field['adv_column'];?>" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  	<?php 
                  	if(isset($listing['ad_'.$field['adv_column']])){
                  		switch($listing['ad_'.$field['adv_column']]){
                  			case "1":
                  				echo '<img src="'.base_url('assets/images/icons/flag_red.png').'" class="small-icon">';
                  			break;
                  			case "2":
                  				echo '<img src="'.base_url('assets/images/icons/flag_yellow.png').'" class="small-icon">';
                  			break;
                  			case "3":
                  				echo '<img src="'.base_url('assets/images/icons/flag_green.png').'" class="small-icon">';
                  			break;
                  			case "4":
                  				echo '<img src="'.base_url('assets/images/icons/flag_blue.png').'" class="small-icon">';
                  			break;
                  			case "5":
                  				echo '<img src="'.base_url('assets/images/icons/flag_orange.png').'" class="small-icon">';
                  			break;
                  			case "6":
                  				echo '<img src="'.base_url('assets/images/icons/flag_pink.png').'" class="small-icon">';
                  			break;
                  			case "7":
                  				echo '<img src="'.base_url('assets/images/icons/flag_purple.png').'" class="small-icon">';
                  			break;
                  			case "8":
                  				echo '<img src="'.base_url('assets/images/icons/flag_white.png').'" class="small-icon">';
                  			break;
                  			default:
                  				echo 'None';
                  			break;
                  		}
                  	}
                    ?>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#" data-id="1">
                      <img src="<?php echo base_url('assets/images/icons/flag_red.png');?>" class="small-icon">
                    </a>
                    <a class="dropdown-item" href="#" data-id="2">
                      <img src="<?php echo base_url('assets/images/icons/flag_yellow.png');?>" class="small-icon">
                    </a>
                    <a class="dropdown-item" href="#" data-id="3">
                      <img src="<?php echo base_url('assets/images/icons/flag_green.png');?>" class="small-icon">
                    </a>
                    <a class="dropdown-item" href="#" data-id="4">
                      <img src="<?php echo base_url('assets/images/icons/flag_blue.png');?>" class="small-icon">
                    </a>
                    <a class="dropdown-item" href="#" data-id="5">
                      <img src="<?php echo base_url('assets/images/icons/flag_orange.png');?>" class="small-icon">
                    </a>
                    <a class="dropdown-item" href="#" data-id="6">
                      <img src="<?php echo base_url('assets/images/icons/flag_pink.png');?>" class="small-icon">
                    </a>
                    <a class="dropdown-item" href="#" data-id="7">
                      <img src="<?php echo base_url('assets/images/icons/flag_purple.png');?>" class="small-icon">
                    </a>
                    <a class="dropdown-item" href="#" data-id="8">
                      <img src="<?php echo base_url('assets/images/icons/flag_white.png');?>" class="small-icon">
                    </a>
                  </div>
                </div>

                <input type="hidden" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" value="<?php echo isset($listing['ad_'.$field['adv_column']]) ? $listing['ad_'.$field['adv_column']] : '';?>">

            </div> 

        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>

<script>
  jQuery(document).ready(function() {
  
      $(".dropdown-menu a").click(function(){
      	let data_id = $(this).attr('data-id');
      	$('#<?php echo $field['adv_column'];?>').val(data_id);
        $(".<?php echo $field['adv_column'];?>").html($(this).html());
      });

  });
</script>