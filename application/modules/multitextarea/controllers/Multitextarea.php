<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Multitextarea extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->form_name = 'Multitextarea';
		$this->field_name = 'multitextarea';
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

	//if you're creating a new field type, handle its appearance with this function
	function field($data){

		$this->load->view('field', $data);

	}

		//if you're creating a new field type, handle its appearance with this function
	function field_pdf($data){

		$this->load->view('field_pdf', $data);

	}

	function create($f, $entry_id){
		//not sure why we need this?
		$table 		= $_SESSION['ft_database_table'];
		
		$exists = $this->db->where('ad_'.$f['adv_column'], $this->input->post($f['adv_column']))->get($table)->row();
		//echo $this->db->last_query();
		//print_r($exists);
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
	
}