<style type="text/css">
.sticky-wrapper {
  height: 75px; /* Set the height of the wrapper to match the height of the bar */
  width: 100%;
  background-color: #fff;
  border-bottom: 1px solid #ddd;
}

.sticky-bar {
  position: absolute; /* Set position to absolute to dock it in place */
  top: 0; /* Position it at the top of the wrapper */
  width: 100%; /* Make it span the full width of the wrapper */
  background-color: #fff;
  padding: 10px;
  border-bottom: 1px solid #ccc;
}

.sticky-wrapper.sticky {
  position: fixed; /* Change position to fixed to make it follow the user */
  top: 0; /* Position it at the top of the screen */
  z-index: 3; /* Set a higher z-index to make it appear above other content */
}

.sticky-wrapper.sticky table{
  position: fixed; /* Change position to fixed to make it follow the user */
  top: 0; /* Position it at the top of the screen */
  z-index: 3; /* Set a higher z-index to make it appear above other content */
}

.sticky-content {
  position: relative; /* Set position to relative so we can position the inner elements */
  height: 100%; /* Set the height to match the height of the bar */
}

.under-menu{
	margin-top: 71px;
}

.buttonright{
	text-align:right;
}

.right{
	margin-left: auto;
    position: fixed;
    margin-right: 0;
    right: 0px;
    width: 20%;
    text-align: right;
    padding-right:15px;
    padding-top:15px;
}
.left{
	margin-right: auto;
    position: fixed;
    margin-left: 0;
    width: 90%;
    padding-left:15px;
    padding-top:15px;
}

/* Add a margin to the body so the content doesn't get covered up by the sticky bar */
body {
  margin-top: 50px;
}
.checbox_label{
  min-width: 130px;
  text-align: left;
}
.checkbox_margin{
  margin-left: 8px;
}
</style>
<div class="sticky-wrapper">

  	 <div class="sticky-content">
  	 	<div class="left">
        	
      <?php
          if(!empty($listing)){
            $field = $this->Advertcategorymodel->getField(66, $listing)[0];
            //print_r($field);
            if(isset($listing)){
                if(isset($listing['ad_'.$field['adv_column']])){
                    $value =  $listing['ad_'.$field['adv_column']];
                }
            }else{
                $value =  '';
            }

            $field_name = $field['adv_column'];
            $FormsTablesmodel = FormsTablesmodel::findOrFail($field['adv_linkedtableid']);
            $field_id                   = $field['adv_id'];
            $Advertfield                = Advertfieldsmodel::find($field_id);

            $adv_config                 = (array)json_decode($Advertfield->adv_config);

            $search_field       = $adv_config['search'];
            $display_field      = $adv_config['display'];

            $Searchfield                = Advertfieldsmodel::findorFail($search_field);
            $Displayfield               = Advertfieldsmodel::findorFail($display_field);

            $entry = $this->db->where('ad_id', $value)->get($FormsTablesmodel->ft_database_table)->row_array();
            echo trim($entry['ad_'.$Displayfield->adv_column]).', ';
          
          echo trim($listing['ad_DevelopmentName']).', ';

        	$address = '';

    			if (!empty($listing['ad_No'])) {
    			  $address .= trim($listing['ad_No']) . ' ';
    			}

    			if (!empty($listing['ad_Street'])) {
    			  $address .= $listing['ad_Street'] . ', ';
    			}

    			if (!empty($listing['ad_Town'])) {
    			  $address .= $listing['ad_Town'] . ', ';
    			}

    			if (!empty($listing['ad_Postcode'])) {
    			  $address .= $listing['ad_Postcode'];
    			}

    			echo $address;
        }
        //ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
        // Find ad_id values on either side of $i
        if(is_array($listing)){

          $search_value = $listing['ad_id']; // Value of $i to search for
          $key = array_search($search_value, array_column($_SESSION[$this->default_table]['entries'], 'ad_id')); // Find the key for the search value

          $entries_count = count($_SESSION[$this->default_table]['entries']);
          if ($entries_count > 0) {
            $prev_key = ($key - 1 + $entries_count) % $entries_count; // Calculate previous key, taking into account wrapping
            $next_key = ($key + 1) % $entries_count; // Calculate next key, taking into account wrapping
          }else{

          }
          

          // Retrieve ad_id values using calculated keys
          $prev_ad_id = $_SESSION[$this->default_table]['entries'][$prev_key]['ad_id'];
          $next_ad_id = $_SESSION[$this->default_table]['entries'][$next_key]['ad_id'];
          
        }
        //print_r($_SESSION);
        $stayOnPageModel = Stayonpagemodel::where('user_id', $this->userid)->where('form_id', $_GET['formID'])->first();
		?>
    <br>
      <small id="countdown"></small>
      <input id="force_timeout" value="0" type="hidden"/>
    	</div>
    	<div class="right">
        <label class="checbox_label">Stay on page
          <input type="checkbox" class="form-check-input checkbox_margin" id="stay_on_page" value="1" <?php if ($stayOnPageModel) {?>checked="checked"<?php } ?>>
        </label>

          <a href="<?php echo base_url('Post/create_form/1/'.$prev_ad_id.'?formID='.$_GET['formID']);?>" class="btn btn-default"><<</a>
          <a href="<?php echo base_url('Post/create_form/1/'.$next_ad_id.'?formID='.$_GET['formID']);?>" class="btn btn-default">>></a>
          <?php if($show_button){ ?>
       		<button type="submit" class="btn btn-primary submitButton ladda-button " data-style="expand-right" id="submitButton" onclick="saveAndNext();">Save</button>
          <?php } ?>
       	</div>
    </div>

</div>

<script language="javascript">

  
$(document).ready(function() {
    $(document).on("click", "#stay_on_page", function(e) {
        //alert('hit');
        let checked = $(this).is(":checked");
        $.ajax({
            type: "POST",
            url: base_url+'User/stay_on_page_preferences',
            data: {'formID' : <?php echo $_GET['formID'];?>, 'checked': checked}, // serializes the form's elements.
            dataType:'json',
            success: function(data){
                toastr.success(data.msg);
            } 
        });
    });
});

window.addEventListener('load', function() {
	// Get the wrapper element
	var wrapper = document.querySelector('.sticky-wrapper');

	// Get the height of the wrapper element
	var wrapperHeight = wrapper.clientHeight + 30;

	// Listen for the scroll event on the window
	window.addEventListener('scroll', function() {
	  // Check if the user has scrolled past the top of the wrapper
	  if (window.pageYOffset > wrapperHeight/*wrapper.offsetTop*/) {
	    // Add the "sticky" class to the wrapper element
	    wrapper.classList.add('sticky');
	    wrapper.classList.add('under-menu');
	  } else {
	    // Remove the "sticky" class from the wrapper element
	    wrapper.classList.remove('sticky');
	    wrapper.classList.remove('under-menu');
	  }
	});
});
</script>