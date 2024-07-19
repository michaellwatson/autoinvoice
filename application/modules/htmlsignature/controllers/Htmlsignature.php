<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Htmlsignature extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->form_name = 'HTML Signature';
		$this->field_name = 'HTMLSignature';
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
	
}