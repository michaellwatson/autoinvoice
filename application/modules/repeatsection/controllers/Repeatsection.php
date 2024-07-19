<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Repeatsection extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();
		// session_start();
		$this->form_name = 'Repeat Section';
		$this->field_name = 'repeatsection';
		$this->load->library('form_validation');
		$this->load->model('Advertcategorymodel');
		// $this->load->model('AdvertcategorymodelForMultiple');
		$this->load->model('Categoryrepeater');
		$this->load->model('PtBlocksCategoryModel');
		$this->load->model('PtBlocksMultipleDatas');
		$this->load->model('PtBlocksModel');
		$this->load->model('PtImagesModel');
		$this->load->model('PtDocumentsModel');
		$this->lang->load('msg');

		$this->user_role = $this->session->userdata("us_role");
		$this->user_id 	 = $this->session->userdata("us_id");

	}

	function get($data){
		return $this->$data;
	}

	function test(){
		echo $this->form_name.' Installed';
	}

	function list($data){
		$this->load->view('list', $data);
	}

	function field_id($c){
		return "{name:'".$c[0]."', type:'string'}";
	}

	function config($d){

		//echo $d['adv_category'];
		//return var_dump($d);
		//$linked_table = FormsTablesmodel::find($d['adv_linkedtableid']);
		
		/*
		$linked_fields = Advertfieldsmodel::where(['adv_table' => $linked_table->ft_database_table, 'adv_category' => $d['adv_category']])->get();
		//print_r($linkedfields);

		$data['linked_table'] 	= $linked_table;
		$data['linked_fields'] 	= $linked_fields;
		*/
		//print_r($d);
		//echo $d['adv_category'].'##';
		$data['d'] = $d;
		$data['categories'] = $this->db->where('ad_id >', $d['adv_category'])->get($this->default_category_table)->result();
		//echo $this->db->last_query().'##';
		//print_r($data['categories']);
		$this->load->view('config', $data);
	}

	function save_config(){
		//todo add validation
		$field_id 					= $this->input->post('field_id');
		//echo $field_id.'##';
		$Advertfield 				= Advertfieldsmodel::find($field_id);

		$repeat_entry				= Categoryrepeater::where('field_id', '=', $field_id)->first();
		if(empty($repeat_entry)){

			$repeat_entry = new Categoryrepeater();

			$repeat_entry->field_id	= $field_id;
			$repeat_entry->cat_start= $this->input->post('start');	
			$repeat_entry->cat_end	= $this->input->post('end');
			$repeat_entry->save();

		}else{
			
			$repeat_entry->cat_start= $this->input->post('start');	
			$repeat_entry->cat_end	= $this->input->post('end');
			$repeat_entry->update();

		}

		echo json_encode(array('status' => 1, 'msg'=> $this->lang->line('updated_successfully')));
		
	}

	function search(){

		/*
		$field_id 					= $this->input->post('field');

		$Advertfield 				= Advertfieldsmodel::find($field_id);

		$table 		= $_SESSION['ft_database_table'];
		
		$exists = $this->db->get_where($table, ['ad_'.$Advertfield->adv_column => $this->input->post('value')])->row();

		if(!empty($exists)){
			echo json_encode(array('status' => 0, 'msg' => 'This value already exists for '.$Advertfield->adv_column));
		}else{
			echo json_encode(array('status' => 1, 'msg' => 'This value for '.$Advertfield->adv_column.' available'));
		}
		*/
	
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

	//If adding the type of field has different fields that come with it


	//if you're creating a new field type, handle its appearance with this function
	function field($data){
		// _pre($data);
		//print_r($data);
		/*
		$linked_fields = json_decode($data['field']['adv_linked_fields']);
		foreach($linked_fields as $l){
			$field = $this->Advertcategorymodel->getField($l);
			$data['linked_fields'][] = $field;
		}
		*/
		$moduleCategoryId = $data['moduleCategory']['ad_id'];
		$listing = $data['listing'];
		if(!empty($listing)){
			$adId = $listing['ad_id']; // blockId

			$whereArray = array('pt_block_id' => $adId, 'pt_blocks_category_id' => $moduleCategoryId);
			$ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getData($whereArray, '', 'asc');
			// _pre($ptBlocksMultipleDatas);
		}
		$data['ptBlocksMultipleDatas'] = $ptBlocksMultipleDatas;
		// _pre($data);
		$this->load->view('field', $data);
	}

	function field_pdf($data){
		//print_r($data);
		/*
		$linked_fields = json_decode($data['field']['adv_linked_fields']);
		foreach($linked_fields as $l){
			$field = $this->Advertcategorymodel->getField($l);
			$data['linked_fields'][] = $field;
		}
		*/
		$this->load->view('field_pdf', $data);
	}

	function search_field($field){
		//print_r($data);
		$data['f'] = $field;
		/*
		$linked_fields = json_decode($data['field']['adv_linked_fields']);
		foreach($linked_fields as $l){
			$field = $this->Advertcategorymodel->getField($l);
			$data['linked_fields'][] = $field;
		}
		*/
		$this->load->view('search_field', $data);
	}

	function create($f, $entry_id){
		$_SESSION['REPEAT_TIMES'] = $this->input->post($f['adv_column']);
		$_SESSION['ORIGINAL_ENTRY_ID'] = $entry_id;
		/*
		$table 		= $_SESSION['ft_database_table'];
		
		$exists = $this->db->where('ad_'.$f['adv_column'], $this->input->post($f['adv_column']))->get($table)->row();

		if(!empty($exists)){
			echo json_encode(array('status' => 0, 'msg' => 'This value already exists for '.$f['adv_text']));
			exit();
		}
		*/

	}

	function update($f, $entry_id){
		$_SESSION['REPEAT_TIMES'] = $this->input->post($f['adv_column']);
		$_SESSION['ORIGINAL_ENTRY_ID'] = $entry_id;
		/*
		$table 		= $_SESSION['ft_database_table'];
		
		$exists = $this->db->where('ad_id !=',  $this->uri->segment(4))->where('ad_'.$f['adv_column'], $this->input->post($f['adv_column']))->get($table)->row();

		if(!empty($exists)){
			echo json_encode(array('status' => 0, 'msg' => 'This value already exists for '.$f['adv_text']));
			exit();
		}
		*/

	}

	private function getInfoByIdEloquent($id){
        $data = Advertfieldsmodel::where('adv_id', $id)->first();
        return $data;
    }
    /*private function getDataByAdvCategory($advCategoryId){
        // $data = Advertfieldsmodel::where('adv_category', $advCategoryId)->get();
        $data = Advertfieldsmodel::where('adv_category', $advCategoryId)
        	->where('adv_field_type', '<>', 30)
        	->get();
        return $data;
    }*/
    private function _removeRepeatsectionFieldType($fields){
    	if($fields){
			$fields = array_filter($fields, function ($value) {
				return $value['adv_field_type'] != 30;
			});
			$fields = array_values($fields);
    	}
    	return $fields;
    }
    public function addRepeatsection(){
		$status = false; $message = ''; $responseData = array();

		// _pre($this->input->post());

		/*Array
		(
		    [advId] => 246
		    [newId] => 1
		)*/

		$advId = $this->input->post('advId');
		$adId = $this->input->post('adId'); // Edit form for add repeater modal
		$formID = $this->input->post('formID'); // Edit form for add repeater modal
		$listingAdId = $adId;
		// $newId = $this->input->post('newId');
		// _pre($advId);

		$ptAdvertFieldsRecord = $this->getInfoByIdEloquent($advId); // 246

		if($ptAdvertFieldsRecord){
			// _pre($ptAdvertFieldsRecord);

			$advCategory = $ptAdvertFieldsRecord['adv_category']; // 93 - Balconies, Walkways
			$advColumn = $ptAdvertFieldsRecord['adv_column']; // 93 - repeatbalconies, repeatwalkways

			$ptBlocksCategoryRecord = $this->PtBlocksCategoryModel->getInfoById($advCategory);

			$newId = _generateRandomNumber(7);
			$randomNumberFileMerger = _generateRandomNumber(7);
			
			$html = '';
			$category = $ptBlocksCategoryRecord->ad_parent; // 56 - FRAEW
			$adId = $advCategory; // 93
			$idForMultiple = $newId;

			$data = array(
				'listing' => null,
				'default_table' => 'pt_blocks',
				'formID' => $formID
			);
			
			$result = $this->PtBlocksCategoryModel->getDataByAdParentAdId($category, $adId); // Balconies
			// _pre($result);

			foreach($result as $keyR => $r){
				$status = true;

				$customData = array(
					'newId' => $newId,
					// 'advColumn' => $advColumn

					'additionalFormType' => 'add',
					
					'listingAdId' => $listingAdId,
				);

				// $categoryId = NULL, $frontEnd = false, $listing = NULL, $allFields = false, $noHTML = false, $dsOnly = false

				$fields = $this->Advertcategorymodel->getFields($r['ad_id'], true, $data['listing'], false, false, false, $customData);

				$fields = $this->_removeRepeatsectionFieldType($fields);

				$data['fields'] = $fields;
				$data['adId'] = $adId;
				$data['newId'] = $newId;
				$data['advCategory'] = $advCategory;
				$data['listingAdId'] = $listingAdId;

				$data['additionalFormType'] = 'add';
				
				$data['randomNumberFileMerger'] = $randomNumberFileMerger;
				$data['customData'] = $customData;

				$html .= $this->load->view('_dynamic_form_elements', $data, true);
			}

			$htmlModalHeader = '<h5 class="modal-title">'.$ptAdvertFieldsRecord['adv_text'].'</h5>';
			$htmlModalHeader .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';

			$responseData = array(
				'html' => $html,
				'htmlModalHeader' => $htmlModalHeader,
			);
		}

		$resultData = array(
			'status' => $status, 'message' => $message, 'data' => $responseData,
		);
		echo json_encode($resultData);
        exit;
	}
	public function editRepeatsection(){
		$status = false; $message = ''; $responseData = array();

		// _pre($this->input->post());
		/*Array
		(
		    [advId] => 247
		    [ptBlocksMultipleDatasId] => 2
		    [formID] => 13
		)*/

		$advId = $this->input->post('advId');
		$formID = $this->input->post('formID'); // Edit form for add repeater modal
		$ptBlocksMultipleDatasId = $this->input->post('ptBlocksMultipleDatasId');

		$ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getInfoById($ptBlocksMultipleDatasId);

			// _pre($ptBlocksMultipleDatas);

				$id = $ptBlocksMultipleDatas->id;
				$pt_block_id = $ptBlocksMultipleDatas->pt_block_id;
				$pt_blocks_category_id = $ptBlocksMultipleDatas->pt_blocks_category_id;

				$single_row_data = $ptBlocksMultipleDatas->single_row_data;
				$single_row_data_array = json_decode($single_row_data, true);

				$userid = $ptBlocksMultipleDatas->userid;

				// _pre($single_row_data_array);
				$pt_images_ids = $single_row_data_array['pt_images_ids'];

				$ptBlocksRecord = $this->PtBlocksModel->getInfoById($pt_block_id);
				$ptBlocksRecord = (array)$ptBlocksRecord;
				// _pre($ptBlocksRecord);

		$ptAdvertFieldsRecord = $this->getInfoByIdEloquent($advId); // 246
		if($ptAdvertFieldsRecord){
			// _pre($ptAdvertFieldsRecord);

			$advCategory = $ptAdvertFieldsRecord['adv_category']; // 93 - Balconies, Walkways
			$advColumn = $ptAdvertFieldsRecord['adv_column']; // 93 - repeatbalconies, repeatwalkways

			$fieldsData = $this->Advertcategorymodel->getFieldsData($advCategory);
			// _pre($fieldsData);

			if($fieldsData){
				foreach ($fieldsData as $key => $value) {
					$tempAdvColumn = $value['adv_column'];
					$tempAdvColumn = 'ad_'.$tempAdvColumn;

					if(array_key_exists($tempAdvColumn, $single_row_data_array)){
					// if(isset($single_row_data_array[$tempAdvColumn])){
						$ptBlocksRecord[$tempAdvColumn] = $single_row_data_array[$tempAdvColumn];
					}
				}
			}

			$ptBlocksCategoryRecord = $this->PtBlocksCategoryModel->getInfoById($advCategory);

			$newId = _generateRandomNumber(7);
			$randomNumberFileMerger = _generateRandomNumber(7);
			
			$html = '';
			$category = $ptBlocksCategoryRecord->ad_parent; // 56 - FRAEW
			$adId = $advCategory; // 93
			$idForMultiple = $newId;

			$data = array(
				'listing' => $ptBlocksRecord,
				'default_table' => 'pt_blocks',
				'formID' => $formID
			);
			
			$result = $this->PtBlocksCategoryModel->getDataByAdParentAdId($category, $adId); // Balconies
			// echo $this->db->last_query();exit;
			// _pre($result);

			foreach($result as $keyR => $r){
				$status = true;

				$customData = array(
					'newId' => $newId,
					// 'advColumn' => $advColumn,
					
					'additionalFormType' => 'edit',
					'ptBlocksMultipleDatasId' => $ptBlocksMultipleDatasId,

					// 'ptBlocksMultipleDatas' => $ptBlocksMultipleDatas,
				);

				$r['listing'] = $ptBlocksRecord;
				$r['default_table'] = $pt_blocks;

				// $categoryId = NULL, $frontEnd = false, $listing = NULL, $allFields = false, $noHTML = false, $dsOnly = false

				// _pre($r, 0);
				// _pre($data);

				$fields = $this->Advertcategorymodel->getFields($r['ad_id'], true, $data['listing'], false, false, false, $customData);

				$fields = $this->_removeRepeatsectionFieldType($fields);

				// _pre($fields);

				// unset($fields[1]);
				// unset($fields[2]);

				$data['fields'] = $fields;
				$data['adId'] = $adId;
				$data['newId'] = $newId;
				$data['ptBlocksMultipleDatasId'] = $ptBlocksMultipleDatasId;

				$data['ptBlocksMultipleDatas'] = $ptBlocksMultipleDatas;
				// $data['fieldsData'] = $fieldsData;
				
				$data['advCategory'] = $advCategory;

				$data['randomNumberFileMerger'] = $randomNumberFileMerger;
				$data['customData'] = $customData;

				// _pre($data);

				$html .= $this->load->view('_dynamic_form_elements', $data, true);
			}

			$htmlModalHeader = '<h5 class="modal-title">'.$ptAdvertFieldsRecord['adv_text'].'</h5>';
			$htmlModalHeader .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';

			$responseData = array(
				'html' => $html,
				'htmlModalHeader' => $htmlModalHeader,
				// 'previousElementId' => $previousElementId,
				// 'newId' => $newId,
			);
		}

		$resultData = array(
			'status' => $status, 'message' => $message, 'data' => $responseData,
		);
		echo json_encode($resultData);
        exit;
	}
	public function deleteRepeatsection(){
		$status = false; $message = ''; $responseData = array();

		// _pre($this->input->post());
		/*Array
		(
		    [ptBlocksMultipleDatasId] => 1
		)*/

		$advId = $this->input->post('advId');
		$ptBlocksMultipleDatasId = $this->input->post('ptBlocksMultipleDatasId');

		$ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getInfoById($ptBlocksMultipleDatasId);

		// _pre($ptBlocksMultipleDatas);

		if($ptBlocksMultipleDatas){
			$id = $ptBlocksMultipleDatas->id;
			$pt_block_id = $ptBlocksMultipleDatas->pt_block_id;
			$pt_blocks_category_id = $ptBlocksMultipleDatas->pt_blocks_category_id;

			$single_row_data = $ptBlocksMultipleDatas->single_row_data;
			$single_row_data_array = json_decode($single_row_data, true);

			$userid = $ptBlocksMultipleDatas->userid;

			$pt_images_ids = $single_row_data_array['pt_images_ids'];
			$pt_documents_ids = $single_row_data_array['pt_documents_ids'];

			if(!empty($pt_images_ids)){
				foreach ($pt_images_ids as $key => $value) {
					$ptImagesData = $this->PtImagesModel->getInfoById($value);
					if($ptImagesData){
						@unlink('assets/uploaded_images/'.$ptImagesData->up_filename);
					}
					$whereArray = array('up_id' => $value);
					$this->PtImagesModel->deleteData($whereArray);
				}
			}

			if(!empty($pt_documents_ids)){
				foreach ($pt_documents_ids as $key => $value) {
					$ptDocumentsData = $this->PtDocumentsModel->getInfoById($value);
					if($ptDocumentsData){
						@unlink('assets/uploads/'.$ptDocumentsData->up_filename);
					}
					$whereArray = array('up_id' => $value);
					$this->PtDocumentsModel->deleteData($whereArray);
				}
			}

			$whereArray = array('id' => $id);
			$this->PtBlocksMultipleDatas->deleteData($whereArray);

			$status = true;
		}
		else{
			$message = 'Record not found';
		}

		$resultData = array(
			'status' => $status, 'message' => $message, 'data' => $responseData,
		);
		echo json_encode($resultData);
        exit;
	}
}