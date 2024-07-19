<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Editablefieldlist extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->form_name = 'Editable field list';
		$this->field_name = 'editablefieldlist';
		$this->load->library('form_validation');
		$this->load->model('Advertcategorymodel');
		$this->load->model('Usermodel');

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

	function field_id($c){
		return "{name:'".$c[0]."', type:'string'}";
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

	function update(){

		$this->load->helper(array('form', 'url'));

		$this->form_validation->set_rules('entry_id', 'Entry ID', 'required');
		$this->form_validation->set_rules('field', 'Field', 'required');
		//$this->form_validation->set_rules('value', 'Value', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('status'=>0, 'msg'=>validation_errors()));
		}
		else
		{
			$entry_id 	= $this->input->post('entry_id');
			$field 		= $this->input->post('field');
			$value 		= $this->input->post('value');
			if(!$value){
				$value = 0;
			}
			$table 		= $_SESSION['ft_database_table'];
				
			//echo $entry_id.'#'.$field.'#'.$value;
			$field_exists = $this->db->get_where('advert_fields', ['adv_column' => str_replace('ad_', '', $field), 'adv_table' => $table])->row_array();
			if(!empty($field_exists)){

				$permission = $this->Usermodel->has_permission('update', $_SESSION['formID']);
				if($permission){
					$update[$field] = $value;
					$this->db->where('ad_id', $entry_id);
					$this->db->update($table, $update);
					echo json_encode(array('status'=>1, 'msg'=>'Updated Successfully'));
				}else{
					echo json_encode(array('status'=>0, 'msg'=>'Invalid Permission'));
				}
			}
			//$_SESSION['ft_database_table']
		}
	}


	//if you're creating a new field type, handle its appearance with this function
	function field($data){

		$this->load->view('field', $data);

	}

	
}