<?php
if (!function_exists('getBalconyAdditionalPtImageIds')) {
    function getBalconyAdditionalPtImageIds($blockId) {
        $ptImagesIdsArray = array();

        $CI =& get_instance();
        $CI->load->model('PtBlocksMultipleDatas');

        $whereArray = array('pt_block_id' => $blockId);
        $ptBlocksMultipleDatas = $CI->PtBlocksMultipleDatas->getData($whereArray, '', 'asc');
        // _pre($ptBlocksMultipleDatas);
        
        foreach ($ptBlocksMultipleDatas as $key => $value) {
            $single_row_data = $value->single_row_data;
            $single_row_data_array = json_decode($single_row_data, true);
            // _pre($single_row_data_array);
            if(!empty($single_row_data_array['pt_images_ids'])){
                foreach ($single_row_data_array['pt_images_ids'] as $key1 => $value1) {
                    $ptImagesIdsArray[] = $value1;
                }
            }
        }
        // _pre($ptImagesIdsArray);
        return $ptImagesIdsArray;
    }
}
if (!function_exists('getBalconyAdditionalPtDocumentIds')) {
    function getBalconyAdditionalPtDocumentIds($blockId) {
        $ptDocumentsIdsArray = array();

        $CI =& get_instance();
        $CI->load->model('PtBlocksMultipleDatas');

        $whereArray = array('pt_block_id' => $blockId);
        $ptBlocksMultipleDatas = $CI->PtBlocksMultipleDatas->getData($whereArray, '', 'asc');
        // _pre($ptBlocksMultipleDatas);
        
        foreach ($ptBlocksMultipleDatas as $key => $value) {
            $single_row_data = $value->single_row_data;
            $single_row_data_array = json_decode($single_row_data, true);
            // _pre($single_row_data_array);
            if(!empty($single_row_data_array['pt_documents_ids'])){
                foreach ($single_row_data_array['pt_documents_ids'] as $key1 => $value1) {
                    $ptDocumentsIdsArray[] = $value1;
                }
            }
        }
        // _pre($ptDocumentsIdsArray);
        return $ptDocumentsIdsArray;
    }
}
?>