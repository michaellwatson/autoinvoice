<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Prefilltext extends My_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('PtPrefillsModel');
		$this->currentDatetime = get_current_date_time();
	}

	private function getInfoByIdEloquent($id){
        $data = Advertfieldsmodel::where('adv_id', $id)->first();
        return $data;
    }

    function index($id){
		//docs
		$ptAdvertFieldsRecord = $this->getInfoByIdEloquent($id); // 246

		$this->data['id'] = $id;
		$this->data['ptAdvertFieldsRecord'] = $ptAdvertFieldsRecord;

		$this->data['image'] = base_url()."/assets/images/contacts.png";
		$this->data['section'] = 'Prefill Text';
		$this->data['subtitle'] = 'Create new prefill text';

		$this->data['field_id'] = $id;
		
		$this->load->view('dashboard/header', $this->data);

		

		$field_id = $id;

		$this->db->select('*');
		$this->db->where('field_id', $field_id);
		$fields = $this->db->get('prefills')->result_array();
		$this->data['fields'] = $fields;

		$data = $this->data;

		$this->data['prefills'] = $this->load->view('prefill/field-prefills', $this->data, true);

		$this->load->view('prefill/prefill_text_gen', $this->data);

		$this->load->view('dashboard/footer');

	}

	function save(){
		// _pre($_POST);
		/*Array
		(
		    [field_id] => 211
		    [id] => 
		    [name] => a
		    [prefill] => [["1","2"],["3","4"],["5","6"]]
		)*/
		/*Array
		(
		    [field_id] => 168
		    [id] => 
		    [name] => a
		    [prefill] => 
		b


		)*/
		$this->form_validation->set_rules('prefill', 'Prefill', 'required');
		$this->form_validation->set_rules('field_id', 'Field ID', 'required|numeric');

		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			$fieldId = $this->input->post('field_id');
			$id = $this->input->post('id');
			$name = $this->input->post('name');
			$prefill = $this->input->post('prefill');

			$ptAdvertFieldsRecord = $this->getInfoByIdEloquent($fieldId);

			if($ptAdvertFieldsRecord->adv_field_type == 23){
				if(!empty($id)){
					$crud = array('json_string'=>$prefill, 'field_id'=>$fieldId, 'name'=>$name);
					$where = array('id' => $id);
					$this->PtPrefillsModel->updateData($crud, $where);
				}
				else{
					$crud = array('json_string'=>$prefill, 'field_id'=>$fieldId, 'name'=>$name);
					$this->PtPrefillsModel->insertData($crud);
				}
			}
			else{
				if(!empty($id)){
					$crud = array('text'=>$prefill, 'field_id'=>$fieldId, 'name'=>$name);
					$where = array('id' => $id);
					$this->PtPrefillsModel->updateData($crud, $where);
				}
				else{
					$crud = array('text'=>$prefill, 'field_id'=>$fieldId, 'name'=>$name);
					$this->PtPrefillsModel->insertData($crud);
				}
			}
		}
		echo json_encode(array('status'=>1));
	}

	function getEntries($field_id){
		$this->db->select('*');
		$this->db->where('field_id', $field_id);
		$fields = $this->db->get('pt_prefills')->result_array();
		$this->data['fields'] = $fields;

		$this->load->view('prefill/field-prefills', $this->data);
	}

	function delete($id){
		$this->db->where('id', $id);
		$this->db->delete('pt_prefills'); 
		echo json_encode(array('status'=>1));
	}

}