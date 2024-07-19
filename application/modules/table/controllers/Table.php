<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Table extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;

	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->form_name = 'Table';
		$this->field_name = 'table';
		$this->load->library('form_validation');
		$this->load->model('Advertcategorymodel');
		$this->load->model('Usermodel');
		$this->load->model('PtBlocksTableDatasModel');
		$this->load->model('PtPrefillsModel');
	}

	function test(){
		echo $this->form_name.' Installed';
	}

	function get($data){
		return $this->$data;
	}

	function list($data){
		$this->load->view('list', $data);
	}

	function config($d){
		//return var_dump($d);
		$linked_table = FormsTablesmodel::find($d['adv_linkedtableid']);

		$linked_fields = Advertfieldsmodel::where('adv_table', $linked_table->ft_database_table)->get();
		//print_r($linkedfields);
		$data['d'] = $d;
		$data['linked_table'] 	= $linked_table;
		$data['linked_fields'] 	= $linked_fields;
		$this->load->view('config', $data);
	}

	function save_config(){

		//todo add validation
		$field_id 					= $this->input->post('field');
		$Advertfield 				= Advertfieldsmodel::find($field_id);
		$config 					= $this->input->post('columns');
		//$adv_config 				= (array)json_decode($Advertfield->adv_config);
		//$adv_config[$config] 		= $this->input->post('data');
		$Advertfield->adv_config 	= json_encode($config);
		$Advertfield->update();
		echo json_encode(array('status' => 1, 'msg'=> $this->lang->line('updated_successfully')));
	}

	function update_config(){

		//todo add validation
		$field_id 					= $this->input->post('field');
		$entry_id 					= $this->input->post('entry_id');
		$Advertfield 				= Advertfieldsmodel::find($field_id);

		$config 					= $this->input->post('columns');
		$percentages				= $this->input->post('percentages');
		$repeatafter				= $this->input->post('repeatafter');
		//$adv_config 				= (array)json_decode($Advertfield->adv_config);
		//$adv_config[$config] 		= $this->input->post('data');
		$Alternateheadersmodel = Alternateheadersmodel::where(['field_id' => $field_id, 'entry_id' => $entry_id])->first();

		if(!empty($Alternateheadersmodel)){

			if($this->input->post('columns')){
				if($config!==''){
					$Alternateheadersmodel->alternate_header = json_encode($config);
				}
			}
			if($this->input->post('percentages')){
				if($percentages!==''){
					$Alternateheadersmodel->column_widths = json_encode($percentages);
				}
			}
			if($this->input->post('repeatafter')){
				if($repeatafter!==''){
					$Alternateheadersmodel->repeat_after = $repeatafter;
				}
			}
			$Alternateheadersmodel->update();

		}else{

			$Alternateheadersmodel = new Alternateheadersmodel();
			$Alternateheadersmodel->field_id			= $field_id;
			$Alternateheadersmodel->entry_id			= $entry_id; 
			if($this->input->post('columns')){
				if($config!==''){
					$Alternateheadersmodel->alternate_header	= json_encode($config);
				}
			}
			if($this->input->post('percentages')){
				if($percentages!==''){
					$Alternateheadersmodel->column_widths = json_encode($percentages);
				}
			}
			if($this->input->post('repeatafter')){
				if($repeatafter!==''){
					$Alternateheadersmodel->repeat_after = $repeatafter;
				}
			}
			$Alternateheadersmodel->save();
		}

		echo json_encode(array('status' => 1, 'msg'=> $this->lang->line('updated_successfully')));
	}

	function field_id($c){
		return "{name:'".$c[0]."', type:'bool'}";
	}

	function install(){
		//check file/db exists

		$form_name 	= $this->form_name;
		$table 		= $this->table;
		$categories = $this->categories;

		//create custom field types
		//do we need this?

		$insert = [];
		$insert['fi_type'] = strtolower(str_replace(' ', '', $form_name));
		$insert['fi_name'] = $form_name;
		$insert['fi_hasDatasource'] = 0;
		$this->db->insert('field_type', $insert);
		$field_id = $this->db->insert_id();

	}

	private function _getTrsDataForPage($listing, $advColumn, $adId){
		$status = false; $message = ''; $resultData = array();

		$dataArray = json_decode($listing['ad_'.$advColumn]);
		// _pre($dataArray, 0);
		$trsData = array();
		if($dataArray){
			$whereArray = array('block_id' => $adId);
			$ptBlocksTableDatas = $this->PtBlocksTableDatasModel->getData($whereArray, 1);
			if(!empty($ptBlocksTableDatas)){
				// _pre($ptBlocksTableDatas);

				$tableData = $ptBlocksTableDatas->table_data;
				$tableDataArr = json_decode($tableData, true);

				// _pre($tableDataArr);

				if(!empty($tableDataArr)){
					foreach ($tableDataArr as $key => $value) {
						if( isset( $value['ad_'.$advColumn.'_trs'] ) ){
							$trsData = $value['ad_'.$advColumn.'_trs'];
							break;
						}
					}
				}
			}
		}
		// _pre($trsData);
		if($trsData){
			$trsDataArray = $trsData['data'];
			$colorsArray = $trsData['colorsArray'];
		}
		else{
			$trsDataArray = array();
			$colorsArray = array();
		}

		$status = true;
		$resultData = array( 'trsDataArray' => $trsDataArray, 'colorsArray' => $colorsArray );
		$responseData = array( 'status' => $status, 'message' => $message, 'data' => $resultData );
		return $responseData;
	}

	private function _getTrsDataForPopup($listing, $advColumn, $adId, $data){
		$status = false; $message = ''; $resultData = array();

		$ptBlocksMultipleDatas = $data['ptBlocksMultipleDatas'];
		$id = $ptBlocksMultipleDatas->id;
		$pt_block_id = $ptBlocksMultipleDatas->pt_block_id;
		$pt_blocks_category_id = $ptBlocksMultipleDatas->pt_blocks_category_id;

		$single_row_data = $ptBlocksMultipleDatas->single_row_data;
		$single_row_data_array = json_decode($single_row_data, true);

		$userid = $ptBlocksMultipleDatas->userid;

		$dataArray = json_decode($listing['ad_'.$advColumn]);
		$trsData = array();
		if($dataArray){
			if( isset($single_row_data_array['ad_'.$advColumn.'_trs']) && !empty($single_row_data_array['ad_'.$advColumn.'_trs']) ){
				$adAdvColumnTrs = $single_row_data_array['ad_'.$advColumn.'_trs'];

				if( isset($adAdvColumnTrs[0]) && !empty($adAdvColumnTrs[0]) ){
					$trsData = $adAdvColumnTrs[0];
				}
			}
		}
		// _pre($trsData);
		if($trsData){
			$trsDataArray = $trsData['data'];
			$colorsArray = $trsData['colorsArray'];
		}
		else{
			$trsDataArray = array();
			$colorsArray = array();
		}

		$status = true;
		$resultData = array( 'trsDataArray' => $trsDataArray, 'colorsArray' => $colorsArray );
		$responseData = array( 'status' => $status, 'message' => $message, 'data' => $resultData );
		return $responseData;
	}

	private function _getPrefillTableData($advId, $advIdWithNewId, $data){
		$status = false; $message = ''; $resultData = array();

		$whereArray = array('field_id' => $advId);
		$ptPrefillsData = $this->PtPrefillsModel->getData($whereArray);
		// _pre($ptPrefillsData);

		$html = '';
		if($ptPrefillsData){
			$html .= '<h5>Prefill text options</h5>';
		}

		foreach ($ptPrefillsData as $key => $value) {
			if( isset($data['additionalFormType']) ){ // add / edit popup data
				$classSelector = 'clsPrefillButtonTable_'.$advIdWithNewId.'_'.$value->id;
			}
			else{
				$classSelector = 'clsPrefillButtonTable_'.$advId.'_'.$value->id;
			}
			$html .= ' <a class="btn btn-primary ladda-button '.$classSelector.'" data-advid="'.$advId.'" data-ptprefillsid="'.$value->id.'" data-style="expand-right">';
			$html .= $value->name;
			$html .= '</a>';
		}

		$html .= ' <a href="'.base_url('Prefilltext/index/'.$advId).'" target="_blank">';
		$html .= '<i class="fa fa-pencil" aria-hidden="true"></i>';
		$html .= '</a>';

		$status = true;
		$resultData = array(
			'html' => $html,
			'ptPrefillsData' => $ptPrefillsData,
		);
		$responseData = array( 'status' => $status, 'message' => $message, 'data' => $resultData );
		return $responseData;
	}

	//if you're creating a new field type, handle its appearance with this function
	function field($data){
		// _pre($data);

		$advColumn = $data['field']['adv_column'];
		$advId = $data['field']['adv_id'];

		$advColumnWithNewId = $advColumn."_".$data['newId'];
		$advIdWithNewId = $advId."_".$data['newId'];

		$listing = $data['listing'];

		// $adId = $listing['ad_id'];
		// $adId = !empty($listing['ad_id']) ? $listing['ad_id'] : null;
		// $adId = (isset($listing) && isset($listing['ad_id'])) ? $listing['ad_id'] : null;
		$adId = $listing['ad_id'] ?? null;

		$entryId = $adId;



		if(isset($data['listing']['ad_id'])){ // edit data
			$Alternateheadersmodel = Alternateheadersmodel::where(['field_id' => $data['field']['adv_id'], 'entry_id' => $data['listing']['ad_id']])->first();
			if(!empty($Alternateheadersmodel)){
				//print_r($Alternateheadersmodel);
				if(!is_null($Alternateheadersmodel->alternate_header) || ($Alternateheadersmodel->alternate_header !== 'null')){
					if(is_array(json_decode(json_decode($Alternateheadersmodel->alternate_header)))){
						$data['field']['adv_config'] = $Alternateheadersmodel->alternate_header;
					}
				}
				if(!is_null($Alternateheadersmodel->column_widths)){
					//print_r(json_decode(json_decode($Alternateheadersmodel->column_widths)));
					if(is_array(json_decode(json_decode($Alternateheadersmodel->column_widths)))){
						//echo $Alternateheadersmodel->column_widths.'##';
						$data['field']['column_widths'] = json_decode(json_decode($Alternateheadersmodel->column_widths));
					}
				}
			}

			if( isset($data['additionalFormType']) ){ // edit popup data
				$trsDataRes = $this->_getTrsDataForPopup($listing, $advColumn, $adId, $data);
				// _pre($trsDataRes);
				$data['trsDataArray'] = $trsDataRes['data']['trsDataArray'];
				$data['colorsArray'] = $trsDataRes['data']['colorsArray'];

				$tempAdAdvColumn = $data['listing']['ad_'.$advColumn];
				$data['listing']['ad_'.$advColumnWithNewId] = $tempAdAdvColumn;

				$tempAdAdvColumnTrs = $data['trsDataArray'];
				$data['listing']['ad_'.$advColumnWithNewId.'_trs'] = json_encode($tempAdAdvColumnTrs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
			}
			else{ // edit page data
				$trsDataRes = $this->_getTrsDataForPage($listing, $advColumn, $adId);
				// _pre($trsDataRes);
				$data['trsDataArray'] = $trsDataRes['data']['trsDataArray'];
				$data['colorsArray'] = $trsDataRes['data']['colorsArray'];
			}
		}

		$data['advColumn_Original'] = $advColumn;
		$data['advId_Original'] = $advId;

		$data['advColumn'] = $advColumn;
		$data['advId'] = $advId;

		if( isset($data['additionalFormType']) ){ // add / edit popup data
			$data['field']['adv_column'] = $advColumnWithNewId;
			$data['field']['adv_id'] = $advIdWithNewId;
			// _pre($data);

			$data['advColumn'] = $advColumnWithNewId;
			$data['advId'] = $advIdWithNewId;

			// _pre($data);
		}
		else{ // add / edit page data
			// _pre($data);
		}

		$prefillTableData = $this->_getPrefillTableData($advId, $advIdWithNewId, $data);
		$htmlPrefillTable = $prefillTableData['data']['html'];
		$ptPrefillsData = $prefillTableData['data']['ptPrefillsData'];
			$data['htmlPrefillTable'] = $htmlPrefillTable;
			$data['ptPrefillsData'] = $ptPrefillsData;

		if(isset($data['listing']['ad_id'])){
			$data['formType'] = 'edit';
		}
		else{
			$data['formType'] = 'add';
		}

		$this->load->view('field', $data);
	}

	//if you're creating a new field type, handle its appearance with this function
	function field_pdf($data){

		$advColumn = $data['field']['adv_column'];

		$listing = $data['listing'];
		$adId = $listing['ad_id'];

		$Alternateheadersmodel = Alternateheadersmodel::where(['field_id' => $data['field']['adv_id'], 'entry_id' => $data['listing']['ad_id']])->first();
		if(!empty($Alternateheadersmodel)){
			//print_r($Alternateheadersmodel);
			if(!is_null($Alternateheadersmodel->alternate_header) || ($Alternateheadersmodel->alternate_header !== 'null')){
				if(is_array(json_decode(json_decode($Alternateheadersmodel->alternate_header)))){
					$data['field']['adv_config'] = $Alternateheadersmodel->alternate_header;
				}
			}
			if(!is_null($Alternateheadersmodel->column_widths)){
				//print_r(json_decode(json_decode($Alternateheadersmodel->column_widths)));
				if(is_array(json_decode(json_decode($Alternateheadersmodel->column_widths)))){
					//echo $Alternateheadersmodel->column_widths.'##';
					$data['field']['column_widths'] = json_decode(json_decode($Alternateheadersmodel->column_widths));
				}
			}
		}

		if(isset($data['listing']['ad_id'])){
			if(isset($data['ptBlocksMultipleDatas'])){
				$trsDataRes = $this->_getTrsDataForPopup($listing, $advColumn, $adId, $data);
				// _pre($trsDataRes);
				$data['trsDataArray'] = $trsDataRes['data']['trsDataArray'];
				$data['colorsArray'] = $trsDataRes['data']['colorsArray'];
			}
			else{
				$trsDataRes = $this->_getTrsDataForPage($listing, $advColumn, $adId);
				// _pre($trsDataRes);
				$data['trsDataArray'] = $trsDataRes['data']['trsDataArray'];
				$data['colorsArray'] = $trsDataRes['data']['colorsArray'];
			}
		}

		// _pre($data);

		$this->load->view('field_pdf', $data);

		// echo "hi there";
		// exit;

	}

	function create($f, $entry_id){
		//not sure why we need this?
		$table 		= $_SESSION['ft_database_table'];

		$exists = $this->db->where('ad_'.$f['adv_column'], $this->input->post($f['adv_column']))->get($table)->row();
		echo $this->db->last_query();
		print_r($exists);
		if(!empty($exists)){
		//echo json_encode(array('status' => 0, 'msg' => 'This value already exists for '.$f['adv_text']));
		//exit();
		}

	}

	function update($f, $entry_id){

		$table 		= $_SESSION['ft_database_table'];

		$exists = $this->db->where('ad_id !=',  $this->uri->segment(4))->where('ad_'.$f['adv_column'], $this->input->post($f['adv_column']))->get($table)->row();

		if(!empty($exists)){
		//echo json_encode(array('status' => 0, 'msg' => 'This value already exists for '.$f['adv_text']));
		//exit();
		}

	}

	private function getInfoByIdEloquent($id){
		$data = Advertfieldsmodel::where('adv_id', $id)->first();
		return $data;
	}
	public function prefillTable(){
		$status = false; $message = ''; $resultData = array();

		// _pre($this->input->post());

		// Page Add
		/*Array
		(
		    [advId] => 211
		    [ptPrefillsId] => 13
		    [additionalFormType] => 
		    [formType] => add
		    [newId] => 
		)*/

		// Page Edit
		/*Array
		(
		    [advId] => 211
		    [ptPrefillsId] => 13
		    [additionalFormType] => 
		    [formType] => edit
		    [newId] => 
		)*/

		// Popup Add
		/*Array
		(
		    [advId] => 211
		    [ptPrefillsId] => 13
		    [additionalFormType] => add
		    [formType] => add
		    [newId] => 6179370
		)*/

		$advId = $this->input->post('advId');
		$ptPrefillsId = $this->input->post('ptPrefillsId');
		$additionalFormType = $this->input->post('additionalFormType');
		$formType = $this->input->post('formType');
		$newId = $this->input->post('newId');


		$ptAdvertFieldsRecord = $this->getInfoByIdEloquent($advId); // 246
			$advColumn = $ptAdvertFieldsRecord->adv_column;
			$advColumn_Original = $advColumn;

			$advColumnWithNewId = $advColumn."_".$newId;

		if(empty($additionalFormType)){ // page
			$advColumn = $advColumn;
		}
		else{ // popup
			$advColumn = $advColumn."_".$newId;
			$ptAdvertFieldsRecord->adv_column = $advColumn;
		}

		$tbodySelector = ".".$advColumn."_body";
		$keyupSelector = ".".$advColumn."_field";

		$ptPrefillsData = $this->PtPrefillsModel->getInfoById($ptPrefillsId);
		$ptPrefillsData->json_string_decoded = json_decode($ptPrefillsData->json_string, true);

		$html = '';
		$colorPickerSelectorInitArray = array();

		foreach ($ptPrefillsData->json_string_decoded as $key => $value){
			$colorPickerSelectorInitArray[] = $advColumn.'_initColorPicker';
		}

		$data = array(
			'ptPrefillsData' => $ptPrefillsData,
			'ptAdvertFieldsRecord' => $ptAdvertFieldsRecord,
		);

		if($formType == 'add'){
			$html = $this->load->view('prefill_tbody_add', $data, true);
		}
		else{
			$html = $this->load->view('prefill_tbody_edit', $data, true);
		}
			
		/*if(empty($additionalFormType)){ // page
			if($formType == 'add'){
			}
			else{
			}
		}
		else{ // popup
			if($formType == 'add'){
			}
			else{
			}
		}*/

		$status = true;
		$resultData = array(
			'advId' => $advId,
			'ptPrefillsId' => $ptPrefillsId,
			'additionalFormType' => $additionalFormType,
			'formType' => $formType,

			'tbodySelector' => $tbodySelector,
			'keyupSelector' => $keyupSelector,

			'colorPickerSelectorInitArray' => $colorPickerSelectorInitArray,

			'html' => $html
		);
		$responseData = array( 'status' => $status, 'message' => $message, 'data' => $resultData );
		echo json_encode($responseData);
		exit;
	}
}