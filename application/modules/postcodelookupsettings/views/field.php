<div class="container">
    <div class="row" id="id_<?php echo $field['adv_id']?>">
      
        <label class="col-sm-6 col-md-6 control-label">
            <?php echo $field['adv_text']?>
            <?php if($field['adv_required']==1){ ?>
            <span style="color:red;">*</span>&nbsp;
            <?php } ?>:
        </label>
         <?php 
            if(is_array($listing)){
                $value = $listing['ad_'.$field['adv_column']];
            }else{
                $value = '';
            }
          ?>                   
        <div class="col-md-5 col-sm-5 postcode_single">
            <div id="field_<?php echo $field['adv_id']?>">
                <input type="text" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" class="form-control postcodelookup" value="<?php echo $value; ?>">
            </div> 
        </div>
        <div class="col-md-1 col-sm-1 lookup_button_padding">
            <button type="button" class="btn btn-primary ladda-button pull-right postcodelookup_button postcodelookup_<?php echo $field['adv_id']?>" data-style="zoom-in" data-id="<?php echo $field['adv_id']?>">Lookup</button>
        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>


<div class="container" id="address_results_<?php echo $field['adv_id']?>">
    <div class="row">
      
        <label class="col-sm-6 col-md-6 control-label">
        </label>
                            
        <div class="col-md-6 col-sm-6">

            <select class="form-control postcodelookup_search_results_<?php echo $field['adv_id']?> postcodelookup_search_results">
            </select>

        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>

<?php 

//get the link field names and set them, no need to add the linked fields to this form, keep them on the original
//see if theres additional categories I could make tabs
?>

<script language="javascript">
    var linked_fields = [];
<?php
    $count = 0;
    foreach($linked_fields as $l){
       ?>
       linked_fields[<?php echo $count;?>] = '<?php echo $l[0]['adv_column'];?>';
    <?php
        $count++;
    }
?>

$( document ).ready(function() {
    $(document).on('click', '.postcodelookup_button', function(e){
        var id = $(this).attr('data-id');

        var l = Ladda.create(document.querySelector('.postcodelookup_'+id));
        l.start();

        e.preventDefault();

        $.ajax({
            type: "POST",
            url: base_url+'postcodelookupsettings/lookuppostcode',
            data: {'postcode': $('.postcodelookup').val() }, // serializes the form's elements.
            dataType:'html',
            success: function(data){
                $('.postcodelookup_search_results_'+id).html(data);   
                l.stop();
            } 
        });
    });

    $(document).on('change', '.postcodelookup_search_results', function(e){
        var id = $(this).val();
        $.ajax({
            type: "POST",
            url: base_url+'postcodelookupsettings/lookupid',
            data: {'id': id }, // serializes the form's elements.
            dataType:'json',
            success: function(data){
                
                $('#'+linked_fields[0]).val(data.line1);
                $('#'+linked_fields[1]).val(data.line2);
                $('#'+linked_fields[2]).val(data.line3);
                $('#'+linked_fields[3]).val(data.postcode);
                $('#'+linked_fields[4]).val(data.lat);
                $('#'+linked_fields[5]).val(data.lng);
           
            } 
        });


    });
    
});
</script>