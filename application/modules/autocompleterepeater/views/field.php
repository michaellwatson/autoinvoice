<link rel="stylesheet" id="typeaheadjs-css"  href="<?php echo base_url('assets/css/autocomplete/typeaheadjs.css');?>" type="text/css" media="all" />
<script src="<?php echo base_url('assets/js/autocomplete/bloodhound.min.js');?>"></script>
<script src="<?php echo base_url('assets/js/autocomplete/typeahead.bundle.min.js');?>"></script>


<div class="container">
    <div class="row" id="id_<?php echo $field['adv_id']?>">
      
        <label class="col-sm-6 col-md-6 control-label">
            <?php echo $field['adv_text']?>
            <?php if($field['adv_required']==1){ ?>
            <span style="color:red;">*</span>:&nbsp;
            <?php } ?>
        </label>
                            
        <div class="col-md-6 col-sm-6 text-left">

            <?php 
                
                if(isset($listing['ad_'.$field['adv_column']])){
                    $value =  $listing['ad_'.$field['adv_column']];
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


                //$field = $this->db->where('adv_table', $FormsTablesmodel->ft_database_table)->limit(1)->order_by('adv_order', 'ASC')->get('pt_advert_fields')->row();
                //$ds = $this->db->where('ad_id', $listing['ad_'.$field['adv_column']])->get($FormsTablesmodel->ft_database_table)->row_array();
                //$title = $this->Advertcategorymodel->getDataValue($ds['ad_title']);
                //if(isset($title['ds_value'])){
                //    $title = $title['ds_value'].' ';    
                //}

                //$v = $ds['ad_membershipnumber']. ' ('.$title.$ds['ad_firstname'].' '.$ds['ad_lastname'].')';
            ?>

            <div id="bloodhound" id="field_<?php echo $field['adv_id']?>">
                <input class="typeahead form-control" type="text" value="<?php if(!(empty($entry))){ echo $entry['ad_'.$Displayfield->adv_column]; }?>" id="<?php echo $field['adv_column'];?>_typeahead">

                <input type="hidden" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" value="<?php echo $value;?>">
            </div>

        </div>
    
    </div>
    <div class="dashed-grey"></div>

    <div class="row" id="list_<?php echo $field['adv_id']?>">
        <div class="col-sm-6 col-md-6 control-label">

        </div>
        <div class="col-md-6 col-sm-6 text-left">
            <ul class="list-group">
                <?php foreach($items_rows as $i){ ?>
                    <li class="list-group-item d-flex" data-id="<?php echo $i->id;?>">
                        <?php //print_r($i);?>
                        <div class="col-md-11"><?php echo $i->$display_field_column;?>g</div>
                        <div class="col-md-1">
                            <i class="fa fa-times-circle-o <?php echo $field['adv_id']?>_autocomplete_repeater_remove_item" aria-hidden="true"></i>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="dashed-grey"></div>
</div>

<script language="javascript">
jQuery(document).ready(function() {
    var users = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: base_url +'/autocompleterepeater/search?query=%QUERY&linkedtable=<?php echo $field['adv_linkedtableid'];?>&field=<?php echo $field['adv_id'];?>'
    });
             
    users.initialize();
     
    jQuery('#<?php echo $field['adv_column'];?>_typeahead').typeahead(null, {
        name: 'users',
        displayKey: 'value',
        source: users.ttAdapter()
    }).on('typeahead:selected', function($e, datum){
        console.log(datum);
        //action
        console.log($('#list_<?php echo $field['adv_id']?> .list-group .'+datum.key));
        if($('#list_<?php echo $field['adv_id']?> .list-group .'+datum.key).length==0){

            $('#list_<?php echo $field['adv_id']?>').find('.list-group').append('<li class="list-group-item d-flex"><div class="col-md-11">'+datum.value+'</div><div class="col-md-1"><i class="fa fa-times-circle-o <?php echo $field['adv_id']?>_autocomplete_repeater_remove_item" aria-hidden="true"></i></div></li>');
            $("."+datum.key).inputSpinner();
            $("#<?php echo $field['adv_column'];?>_typeahead").val('');
            //$('#<?php echo $field_name;?>').val(datum.key);
            //$('#<?php echo $field['adv_column'];?>_typeahead').val(datum.value);
        }else{
            toastr.error('Field already exists, please update quantity instead');
        }
    });

    $(document).on('click', '.<?php echo $field['adv_id']?>_autocomplete_repeater_remove_item', function(){

        //alert('hit');
        let repeater_id = $(this).parent().parent().attr('data-id');
        let item_id = $(this).parent().parent().find('input[name="<?php echo $field['adv_id']?>_item[]"]').val();
        
        //alert(repeater_id);
        //alert(item_id);

        if (typeof repeater_id !== "undefined") {
            $.ajax({
                type: "POST",
                url: base_url+'autocompleterepeater/remove',
                data: {'repeater_id': repeater_id, 'item_id': item_id}, // serializes the form's elements.
                dataType:'json',
                success: function(data){
                

                } 
            });
        }
        $('.'+item_id).parent().parent().remove();

    });
});
</script>