<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkvalueexists extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();
		session_start();
		$this->form_name = 'Checkvalueexists';
		$this->field_name = 'checkvalueexists';
		$this->load->library('form_validation');
		$this->load->model('Advertcategorymodel');
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

	function search(){


		$field_id 					= $this->input->post('field');

		$Advertfield 				= Advertfieldsmodel::find($field_id);

		$table 		= $_SESSION['ft_database_table'];
		
		$exists = $this->db->get_where($table, ['ad_'.$Advertfield->adv_column => $this->input->post('value')])->row();

		if(!empty($exists)){
			echo json_encode(array('status' => 0, 'msg' => 'This value already exists for '.$Advertfield->adv_column));
		}else{
			echo json_encode(array('status' => 1, 'msg' => 'This value for '.$Advertfield->adv_column.' available'));
		}
	
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
		$this->load->view('field', $data);
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
		$table 		= $_SESSION['ft_database_table'];
		
		$exists = $this->db->where('ad_'.$f['adv_column'], $this->input->post($f['adv_column']))->get($table)->row();

		if(!empty($exists)){
			echo json_encode(array('status' => 0, 'msg' => 'This value already exists for '.$f['adv_text']));
			exit();
		}

	}

	function update($f, $entry_id){
		
		$table 		= $_SESSION['ft_database_table'];
		
		$exists = $this->db->where('ad_id !=',  $this->uri->segment(4))->where('ad_'.$f['adv_column'], $this->input->post($f['adv_column']))->get($table)->row();

		if(!empty($exists)){
			echo json_encode(array('status' => 0, 'msg' => 'This value already exists for '.$f['adv_text']));
			exit();
		}

	}

	
}