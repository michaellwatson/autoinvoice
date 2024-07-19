<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Filemerger extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();
		// session_start();
		$this->form_name = 'Filemerger';
		$this->field_name = 'filemerger';
		$this->load->library('form_validation');
		$this->load->model('Advertcategorymodel');
		$this->load->model('PtBlocksCategoryModel');
		$this->load->model('PtBlocksMultipleDatas');
		$this->lang->load('msg');
		$this->load->helper('url');

		$this->user_role = $this->session->userdata("us_role");
		$this->user_id 	 = $this->session->userdata("us_id");

		$this->load->library('Fpdf/FpdfLib');
	}

	function get($data){
		return $this->$data;
	}

	function test(){
		echo $this->form_name.' Installed';
	}

	function field_id($c){
		return "{name:'".$c[0]."', type:'string'}";
	}

	function config($d){
		//return var_dump($d);
		/*
		$linked_table = FormsTablesmodel::find($d['adv_linkedtableid']);
		
		$linked_fields = Advertfieldsmodel::where('adv_table', $linked_table->ft_database_table)->get();
		//print_r($linkedfields);
		$data['d'] = $d;
		$data['linked_table'] 	= $linked_table;
		$data['linked_fields'] 	= $linked_fields;
		$this->load->view('config', $data);
		*/
	}

	function save_config(){
		//todo add validation
		/*
		$field_id 					= $this->input->post('field');
		$Advertfield 				= Advertfieldsmodel::find($field_id);
		$config 					= $this->input->post('config');
		$adv_config 				= (array)json_decode($Advertfield->adv_config);
		$adv_config[$config] 		= $this->input->post('data');
		$Advertfield->adv_config 	= json_encode($adv_config);
		$Advertfield->update();
		echo json_encode(array('status' => 1, 'msg'=> $this->lang->line('updated_successfully')));
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
		//print_r($data);
		/*
		$linked_fields = json_decode($data['field']['adv_linked_fields']);
		foreach($linked_fields as $l){
			$field = $this->Advertcategorymodel->getField($l);
			$data['linked_fields'][] = $field;
		}
		*/
		/*
		if(isset($_SESSION['two-col']) && (int)$_SESSION['two-col']){
			$this->load->view('field_2col', $data);
		}else{
		*/
		//echo $data['table'].'##';

		
		$advCategory = $data['field']['adv_category']; // 93
		$advColumn = $data['field']['adv_column']; // filemerger

		if(isset($data['customData'])){
			$customName = $advCategory."[ad_".$advColumn."]";
			$data['customData']['customName'] = $customName;
		}
		// _pre($data);

		$this->load->view('field', $data);
		//}
	}

	//if you're creating a new field type, handle its appearance with this function
	function field_pdf($data){
		//print_r($data);
		/*
		$linked_fields = json_decode($data['field']['adv_linked_fields']);
		foreach($linked_fields as $l){
			$field = $this->Advertcategorymodel->getField($l);
			$data['linked_fields'][] = $field;
		}
		*/
		//$this->load->view('field_pdf', $data);
		if(is_numeric($data['entry_id'])){
			$documents = $this->Advertcategorymodel->getDocuments($data['entry_id'], $data['field_id']);
			$r['documents'] = $documents;
			$r['data'] = $data;
			$this->load->view('field_pdf', $r);
		}
	}

	function field_pdf_1($data){
		$r['documents'] = $data['documents'];
		// _pre($r);
		$this->load->view('field_pdf', $r);
	}

	/*public function get_files($entryId, $field_id){
		if(is_numeric($entryId)){
			$documents = $this->Advertcategorymodel->getDocuments($entryId, $field_id);
			// _pre($documents);
			$r['documents'] = $documents;
			$this->load->view('uploaded_documents',$r);
		}
	}*/

	public function get_files($entry_id, $field_id, $ptBlocksMultipleDatasId=''){
		// echo $entry_id;exit;
		//echo $field_id;
		// _pre($_POST);
		
		$additionalFormType = '';
		if(isset($_POST['additionalFormType'])){
			$additionalFormType = $_POST['additionalFormType'];
		}

		$filemergerRepeaterAdvIdsArr = array();
		$categoryId = 56;
		$advertFieldsFraewRepeater = $this->PtBlocksCategoryModel->getAdvertFieldsFraewRepeater($categoryId);
		foreach ($advertFieldsFraewRepeater as $key => $value) {
			$advertFieldsByFileMergerFieldType = $this->PtBlocksCategoryModel->getAdvertFieldsByFileMergerFieldType($value->adv_category);
			foreach ($advertFieldsByFileMergerFieldType as $key => $value) {
				$filemergerRepeaterAdvIdsArr[] = $value->adv_id;
			}
		}
		// _pre($filemergerRepeaterAdvIdsArr);
		if( in_array($field_id, $filemergerRepeaterAdvIdsArr) || !empty($additionalFormType) ){
			// echo "if";exit;
			if($ptBlocksMultipleDatasId == ''){// in repeater section for page
				$ptDocumentsIdsArray = getBalconyAdditionalPtDocumentIds($entry_id);
				// _pre($ptDocumentsIdsArray);
				$documents = $this->Advertcategorymodel->getDocuments1($entry_id, $field_id, $ptDocumentsIdsArray);
				// _pre($documents);
			}
			else{// in repeater section for modal
				$ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getInfoById($ptBlocksMultipleDatasId);
				$single_row_data = $ptBlocksMultipleDatas->single_row_data;
				$single_row_data_array = json_decode($single_row_data, true);
				// _pre($single_row_data_array);
				if(!empty($single_row_data_array['pt_documents_ids'])){
					foreach ($single_row_data_array['pt_documents_ids'] as $key1 => $value1) {
						$ptDocumentsIdsArray[] = $value1;
					}
				}
				if($ptDocumentsIdsArray){
					$documents = $this->Advertcategorymodel->getDocuments2($ptDocumentsIdsArray);
				}
				else{
					$documents = array();
				}
			}
		}
		else{
			// echo "else";exit;
			$documents = $this->Advertcategorymodel->getDocuments($entry_id, $field_id);
		}

		// _pre($documents);

		$r['documents'] = $documents;
		$r['field_id'] = $field_id;

		$r['field'] = $this->Advertcategorymodel->getField($field_id);

		$r['additionalFormType'] = $additionalFormType;
		$r['ptBlocksMultipleDatasId'] = $ptBlocksMultipleDatasId;

		// _pre($r);

		$this->load->view('uploaded_documents',$r);
	}

	public function upload_documents(){

		//var_dump($_GET);
		$formID = $this->input->get('form_id');
		
		$form = FormsTablesmodel::findorFail($formID);

		$this->load->library('qquploadedfilexhr');
		$allowedExtensions = array('pdf', 'jpg', 'png');
		// max file size in bytes
		$sizeLimit = 12 * 1024 * 1024;
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload('assets/uploads/');	

		if( isset($result['success']) && $result['success'] == true ){
			$_SESSION['item'] = $result['filename'];

			// Count number of pages of pdf file
			$upFilename = $result['filename'];
			$upFilepath = FCPATH."assets/uploads/".$upFilename;
			$tempData = array('upFilepath' => $upFilepath);
			$pdfData = $this->fpdflib->getPdfData($tempData);
			if($pdfData['status']){
				$pagesCount = $pdfData['data']['pagesCount'];
			}
			else{
				$pagesCount = 0;
			}

			$insert['up_number_of_pages'] 	= $pagesCount;
			$insert['up_filename'] 	= $result['filename']; 
			$insert['up_field_id'] 	= $this->input->get('field_id'); 
			$insert['up_entry_id'] 	= $this->input->get('entry_id'); 
			$insert['up_date']		= date('Y-m-d H:i:s', strtotime('now'));
			// $insert['up_userid']	= $this->userid;
			$insert['up_userid']	= $this->session->userdata("us_id");
			$insert['up_table']		= $form->ft_database_table;
			$this->db->insert('pt_documents', $insert);

			$doc_id = $this->db->insert_id();
			$result['doc_id'] = $doc_id;

			$Eventsmodel 			= new Eventsmodel();
			$Eventsmodel->note 		= $this->ft_name.' Document Added '.$result['filename'];
			$Eventsmodel->table_id 	= $form->ft_database_table;
			$Eventsmodel->user_id 	= $this->session->userdata("us_id");
			$Eventsmodel->entry_id 	= $insert['up_entry_id'];
			$Eventsmodel->status 	= 1;		
			$Eventsmodel->save();
		}

		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); 
	}

	public function deleteDocuments(){

		$formID = $this->input->get('form_id');
		$form = FormsTablesmodel::findorFail($formID);

		$this->form_validation->set_rules('id', 'id', 'required|numeric');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	
			$this->db->where('up_id', $this->input->post('id'));
			$data = $this->db->get('pt_documents')->row_array();
			if(isset($data['up_filename'])){
				unlink('assets/uploads/'.$data['up_filename']);
				$this->db->where('up_id',$this->input->post('id'));
				$this->db->delete('documents');


				$Eventsmodel 			= new Eventsmodel();
				$Eventsmodel->note 		= $this->ft_name.' Document Deleted '.$data['up_filename'];
				$Eventsmodel->table_id 	= $form->ft_database_table;
				$Eventsmodel->user_id 	= $this->userid;
				$Eventsmodel->entry_id 	= $data['up_entry_id'];		
				$Eventsmodel->status 	= 2;
				$Eventsmodel->save();

				echo json_encode(array('status'=>1));
			}else{
				echo json_encode(array('status'=>0));
			}
		}
	}
	
	public function getFileMergerRepeaterAdd($entry_id, $field_id, $ptBlocksMultipleDatasId=''){
		$status = false; $message = ''; $resultData = array();
		// echo $entry_id;exit;
		//echo $field_id;
		// _pre($_POST);

		/*
		http://localhost/cladding-working/post/getImagesRepeaterAdd/0/206
		entry_id = 0
		field_id = 206
		Array
		(
		    [hdnTempAddFileMerger] => [72]
		    [additionalFormType] => add
		)
		*/

		$hdnTempAddFileMerger = $_POST['hdnTempAddFileMerger'];
		$hdnTempAddFileMergerArray = json_decode($hdnTempAddFileMerger, true);
		$ptDocumentsIdsArray = $hdnTempAddFileMergerArray;

		$additionalFormType = '';
		if(isset($_POST['additionalFormType'])){
			$additionalFormType = $_POST['additionalFormType'];
		}

		$filemergerRepeaterAdvIdsArr = array();
		$categoryId = 56;
		$advertFieldsFraewRepeater = $this->PtBlocksCategoryModel->getAdvertFieldsFraewRepeater($categoryId);
		foreach ($advertFieldsFraewRepeater as $key => $value) {
			$advertFieldsByFileMergerFieldType = $this->PtBlocksCategoryModel->getAdvertFieldsByFileMergerFieldType($value->adv_category);
			foreach ($advertFieldsByFileMergerFieldType as $key => $value) {
				$filemergerRepeaterAdvIdsArr[] = $value->adv_id;
			}
		}
		// _pre($filemergerRepeaterAdvIdsArr);
		$documents = array();
		if( in_array($field_id, $filemergerRepeaterAdvIdsArr) || !empty($additionalFormType) ){
			if($ptDocumentsIdsArray){
				$documents = $this->Advertcategorymodel->getDocuments2($ptDocumentsIdsArray);
			}
		}

		if($documents){
			$status = true;
		}

		$r['documents'] = $documents;
		$r['field_id'] = $field_id;

		$r['field'] = $this->Advertcategorymodel->getField($field_id);

		$r['additionalFormType'] = $additionalFormType;
		$r['ptBlocksMultipleDatasId'] = $ptBlocksMultipleDatasId;

		// _pre($r);

		$html = $this->load->view('uploaded_documents', $r, true);

		$resultData = array(
			'html' => $html
		);

		$responseData = array('status' => $status, 'message' => $message, 'data' => $resultData);

		// return $responseData;
		echo json_encode($responseData);
        exit;
	}
}