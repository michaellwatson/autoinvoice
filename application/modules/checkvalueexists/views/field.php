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

                <input type="text"  name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" class="form-control" value="<?php echo  $listing['ad_'.$field['adv_column']];?>" autocomplete="off" placeholder="AAA00000">
                <span class="<?php echo $field['adv_column'];?>_status"></span>
            </div> 

        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>



<script language="javascript">
jQuery(document).ready(function() {

    //remove this line for general use
   
    
    $(document).on('keyup', '#<?php echo $field['adv_column'];?>', function(){
        
        $('#<?php echo $field['adv_column'];?>').mask($('#stockaccount_typeahead').val()+'######', {
            translation: {
                '0': null,
                'A': null,
                '#': { pattern: /\d/, recursive: true },
            }
        });
        //console.log($(this).val());     
        if($(this).val()!==''){
            $.ajax({
                type: "POST",
                url: base_url + 'checkvalueexists/Checkvalueexists/search',
                data: {
                    'ci_csrf_token': '',
                    'field': '<?php echo $field['adv_id'];?>',
                    'value': $(this).val()
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == '1') {

                        $('.<?php echo $field['adv_column'];?>_status').html(data.msg);
                        $('.<?php echo $field['adv_column'];?>_status').removeClass('text-danger');
                        $('.<?php echo $field['adv_column'];?>_status').addClass('text-success');
                        $(":submit").attr("disabled", false);


                    } else {
                           
                        $('.<?php echo $field['adv_column'];?>_status').html(data.msg);
                        $('.<?php echo $field['adv_column'];?>_status').addClass('text-danger');
                        $('.<?php echo $field['adv_column'];?>_status').removeClass('text-success');
                        $(":submit").attr("disabled", true);
                        
                    }

                }
            });
        }else{

            $('.<?php echo $field['adv_column'];?>_status').html('');
            $('.<?php echo $field['adv_column'];?>_status').removeClass('text-danger');
            $('.<?php echo $field['adv_column'];?>_status').removeClass('text-success');
            $(":submit").attr("disabled", false);

        }

    });
});
</script>
