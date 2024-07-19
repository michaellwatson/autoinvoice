<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Autocomplete extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();
		session_start();
		$this->form_name = 'Autocomplete';
		$this->field_name = 'autocomplete';
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
		$config 					= $this->input->post('config');
		$adv_config 				= (array)json_decode($Advertfield->adv_config);
		$adv_config[$config] 		= $this->input->post('data');
		$Advertfield->adv_config 	= json_encode($adv_config);
		$Advertfield->update();
		echo json_encode(array('status' => 1, 'msg'=> $this->lang->line('updated_successfully')));
	}

	function search(){

		$type = 'normal';
		if($this->input->get('type')){
			$type 	= $this->input->get('type');
		}
		$field_id 					= $this->input->get('field');
		$Advertfield 				= Advertfieldsmodel::find($field_id);
		//print_r($Advertfield);

		$adv_config 				= (array)json_decode($Advertfield->adv_config);

		$search_field		= $adv_config['search'];
		$display_field		= $adv_config['display'];

		$Searchfield 				= Advertfieldsmodel::findorFail($search_field);
		$Displayfield 				= Advertfieldsmodel::findorFail($display_field);

		//print_r($Searchfield);
		$FormsTablesmodel = FormsTablesmodel::findOrFail($_GET['linkedtable']);
		//print_r($FormsTablesmodel);
		//echo $FormsTablesmodel->ft_database_table.'#';
		$field = $this->db->where('adv_table', $FormsTablesmodel->ft_database_table)->limit(1)->order_by('adv_order', 'ASC')->get('pt_advert_fields')->row();
		//echo $this->db->last_query();
		//print_r($field);
 
		$linked_tables = $this->db->where('adv_table', $FormsTablesmodel->ft_database_table)->join('forms_tables', 'ft_id = adv_linkedtableid')->order_by('adv_order', 'ASC')->get('pt_advert_fields')->result();
		//echo $this->db->last_query();
		
		//check all the fields if they are linked to the user table
		foreach ($linked_tables as $l){
			if((int)$this->user_role!==1){
				//if so and its not super admin, only show their own records
				if($l->ft_is_users==1){
					$this->db->where('ad_'.$l->adv_column, $this->user_id);
				}
			}
		}
		
		//just start the query here
		$ds = $this->db->like('ad_'.$Searchfield->adv_column, $this->input->get('query'))->get($FormsTablesmodel->ft_database_table)->result_array();
		/*
			foreach $where
		*/
		//echo $this->db->last_query();
		//print_r($ds);
		$responses = [];
		$used_values = [];
		$html = '';

		foreach($ds as $d){

			if (in_array($d['ad_'.$Displayfield->adv_column], $used_values)) {
		        continue; // skip if ad_id has already been used as a value
		    }

			$response = array();
			$html.='<option value="'.$d['ad_id'].'"';
			if(isset($listing) && is_array($listing)){
				if(array_key_exists('ad_'.$f['adv_column'],$listing)){
					if($listing['ad_'.$f['adv_column']]==$d['ad_id']){
						$html.=' selected=\"selected\"';
					}
				}
			}

			switch($type){
				case 'normal':
					switch($FormsTablesmodel->ft_database_table){
						default:
							$response['value'] = $d['ad_'.$Displayfield->adv_column];
							$response['key'] = $d['ad_id']; 
							$used_values[] = $d['ad_'.$Displayfield->adv_column];
						break;
					}
				break;
				case 'search_field':
					switch($FormsTablesmodel->ft_database_table){
						default:
							$response['text'] = $d['ad_'.$Displayfield->adv_column];
							$response['ID'] = $d['ad_id']; 
							$used_values[] = $d['ad_'.$Displayfield->adv_column];
						break;
					}
				break;
			}

			$responses[] = $response;
		}
		echo json_encode($responses);
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
		$insert['fi_hasDatasource'] = 1;
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
		if(isset($_SESSION['two-col']) && (int)$_SESSION['two-col']){
			$this->load->view('field_2col', $data);
		}else{
			$this->load->view('field', $data);
		}
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

	function get_value($data){

		//$data['column']
		//$data['table_id']
		//$data['field_id']
       	$value =  $data['listing']['ad_'.$data['column']];
       	$field_name = $data['column'];
       	$FormsTablesmodel = FormsTablesmodel::findOrFail($data['table_id']);

       	$field_id                   = $data['field_id'];
       	$Advertfield                = Advertfieldsmodel::find($field_id);

       	$adv_config                 = (array)json_decode($Advertfield->adv_config);

       	$search_field       = $adv_config['search'];
       	$display_field      = $adv_config['display'];

       	$Searchfield                = Advertfieldsmodel::findorFail($search_field);
       	$Displayfield               = Advertfieldsmodel::findorFail($display_field);

       	$entry = $this->db->where('ad_id', $value)->get($FormsTablesmodel->ft_database_table)->row_array();

        return '('.$value.') '.$entry['ad_'.$Displayfield->adv_column];
	}

	
}