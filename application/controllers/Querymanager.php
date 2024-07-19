<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Illuminate\Database\Capsule\Manager
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;

class Querymanager extends My_Controller {
	
	public $menu = '';
		
	public function __construct()
    {
     	parent::__construct();
	}


	function create_query($formID){
		$data = $this->data;
		$data['user'] = $this->data['user'];

		$form = FormsTablesmodel::findorFail($formID);
		$data['form_id'] = $form->ft_id;

		//get any hidden date fields on the table not listed but are date fields
		$db_fields = $this->db->list_fields($form->ft_database_table);

		//date fields only
		$fields = Advertfieldsmodel::where('adv_table', $form->ft_database_table)->join('field_type', 'adv_field_type', '=', 'fi_id')->orderBy('adv_order', 'asc')->get(); 
		$data['fields'] = $fields;

		$table_fields = [];
		foreach($data['fields'] as $f){
			$table_fields[] = 'ad_'.$f->adv_column;
		}

		$not_listed = array_diff($db_fields, $table_fields);

		$fields_meta = $this->db->field_data($form->ft_database_table);
		$fields_meta_sorted = [];
		foreach ($fields_meta as $field)
		{

			$fields_meta_sorted[$field->name] = $field;

		}

		$data['spare_date_fields'] = [];
		foreach($not_listed as $n){
			if($fields_meta_sorted[$n]->type == 'datetime'){
				$data['spare_date_fields'][] = $n;
			}
		}
		//end date fields

		$form = FormsTablesmodel::findorFail($formID);


		$data['messages'] = Messagemodel::all();
		
		$this->load->view('dashboard/header', $data);
		//$this->load->view('forms/user', $data);
		$this->load->view('querymanager/query_info', $data);
		$this->load->view('dashboard/footer');	
	}

	function save_query(){

		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);
		//Capsule::enableQueryLog();

		foreach($this->input->post('where') as $w){
			$Queryconditionsmodel = new Queryconditionsmodel();
			$Queryconditionsmodel->form_id = $this->input->post('form_id');
			$Queryconditionsmodel->field_id = $w;
			$Queryconditionsmodel->operator = $this->input->post('operator_'.$w);
			$Queryconditionsmodel->value = $this->input->post('value_'.$w);
			$Queryconditionsmodel->save();
		}
		//dd(Capsule::getQueryLog());
		//success message
	}

}