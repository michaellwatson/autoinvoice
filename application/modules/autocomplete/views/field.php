<!--
<link rel="stylesheet" id="typeaheadjs-css"  href="<?php echo base_url('assets/css/autocomplete/typeaheadjs.css');?>" type="text/css" media="all" />
<script src="<?php echo base_url('assets/js/autocomplete/bloodhound.min.js');?>"></script>
<script src="<?php echo base_url('assets/js/autocomplete/typeahead.bundle.min.js');?>"></script>
-->
<?php 
    if(isset($listing)){
        if(isset($listing['ad_'.$field['adv_column']])){
            $value =  $listing['ad_'.$field['adv_column']];
        }
    }else{
        $value =  '';
    }
    

    //$value =  $listing['ad_'.$field['adv_column']];
    

    $field_name = $field['adv_column'];
    $FormsTablesmodel = FormsTablesmodel::findOrFail($field['adv_linkedtableid']);


    if(strtolower($FormsTablesmodel->ft_database_table) == strtolower($field['adv_table'])){
        //are we doing a lookup on the same table?
        if(isset($listing['ad_id'])){
            $value = $listing['ad_id'];
        }
    }

    $field_id                   = $field['adv_id'];
    $Advertfield                = Advertfieldsmodel::find($field_id);

    $adv_config                 = (array)json_decode($Advertfield->adv_config);


    $search_field       = $adv_config['search'];
    $display_field      = $adv_config['display'];

    $Searchfield                = Advertfieldsmodel::findorFail($search_field);
    $Displayfield               = Advertfieldsmodel::findorFail($display_field);


    $entry = $this->db->where('ad_id', $value)->get($FormsTablesmodel->ft_database_table)->row_array();
    //echo $this->db->last_query();


    //$field = $this->db->where('adv_table', $FormsTablesmodel->ft_database_table)->limit(1)->order_by('adv_order', 'ASC')->get('pt_advert_fields')->row();
    //$ds = $this->db->where('ad_id', $listing['ad_'.$field['adv_column']])->get($FormsTablesmodel->ft_database_table)->row_array();
    //$title = $this->Advertcategorymodel->getDataValue($ds['ad_title']);
    //if(isset($title['ds_value'])){
    //    $title = $title['ds_value'].' ';    
    //}

    //$v = $ds['ad_membershipnumber']. ' ('.$title.$ds['ad_firstname'].' '.$ds['ad_lastname'].')';
?>

<div class="container">
    <div class="row" id="id_<?php echo $field['adv_id']?>">
      
        <label class="col-sm-6 col-md-6 control-label">
            <?php echo $field['adv_text']?>
            <?php if($field['adv_required']==1){ ?>
            <span style="color:red;">*</span>
            <?php } ?>:&nbsp;
        </label>
        
        <?php if(($field['adv_post_text']!='') || (isset($adv_config['add']) && (bool)$adv_config['add'])){?>
          <div class="col-md-5 col-sm-5 autcomplete_single">
        <?php }else{?>
          <div class="col-md-6 col-sm-6">
        <?php } ?>
                  
<!--                    
        <div class="col-md-6 col-sm-6 text-left">
-->
            <div id="bloodhound" id="field_<?php echo $field['adv_id']?>">
                <?php
                 if(strtolower($FormsTablesmodel->ft_database_table) == strtolower($field['adv_table'])){
                ?>

                    <input type="text" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>_typeahead" value="<?php echo isset($listing['ad_'.$field['adv_column']]) ? $listing['ad_'.$field['adv_column']] : '';?>" class="typeahead form-control"  autocomplete="nope">
                
                <?php }else{ ?>
                
                    <input class="typeahead form-control" type="text" value="<?php if(isset($Displayfield->adv_column) && isset($entry) && isset($entry['ad_'.$Displayfield->adv_column])){ echo $entry['ad_'.$Displayfield->adv_column]; }?>" id="<?php echo $field['adv_column'];?>_typeahead" autocomplete="nope">

                    <input type="hidden" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" value="<?php echo $value;?>">
                
                <?php } ?>
            </div>

        </div>
        <?php 
            if(($field['adv_post_text']!='')||((bool)$adv_config['add'])){

                if((bool)$adv_config['add']){
                    $field['adv_post_text'] = '<a class="btn btn-primary"  data-toggle="modal" data-target="#'.$field['adv_column'].'_modal"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>';
                }

            ?>
            <div class="col-md-1 col-sm-1  control-label pull-right autocomplete_button">
            <?php echo $field['adv_post_text']?>
            </div>
        <?php } ?>  

    
    </div>
    <div class="dashed-grey"></div>
</div>

<script language="javascript">
jQuery(document).ready(function() {
    var users = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: base_url +'/autocomplete/search?query=%QUERY&linkedtable=<?php echo $field['adv_linkedtableid'];?>&field=<?php echo $field['adv_id'];?>'
    });
             
    users.initialize();
     
    jQuery('#<?php echo $field['adv_column'];?>_typeahead').typeahead(null, {
        name: 'users',
        displayKey: 'value',
        source: users.ttAdapter()
    }).on('typeahead:selected', function($e, datum){
        console.log(datum);
        //action
        $('#<?php echo $field_name;?>').val(datum.key);
        $('#<?php echo $field['adv_column'];?>_typeahead').val(datum.value);
    });
});


$(document).on('show.bs.modal','#<?php echo $field['adv_column'];?>_modal', function () {
    $('#<?php echo $field['adv_column'];?>_iframe').attr('src', base_url+'Post/create_form/1?formID=<?php echo $FormsTablesmodel->ft_id;?>&no_header=true');
})
</script>

<!-- Modal -->
<div class="modal fade" id="<?php echo $field['adv_column'];?>_modal" tabindex="-1" role="dialog" aria-labelledby="<?php echo $field['adv_column'];?>_modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
         <h4 class="modal-title" id="myModalLabel">Add <?php echo ucfirst($field['adv_column']);?></h4>
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       
      </div>
      <div class="modal-body">
          <iframe src="" width="100%" height="500" frameborder="0" allowtransparency="true" id="<?php echo $field['adv_column'];?>_iframe"></iframe>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->