<?php 
    
    $value =  $listing['ad_'.$field['adv_column']];
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
(<?php echo $value;?>) <?php echo $entry['ad_'.$Displayfield->adv_column];?>

