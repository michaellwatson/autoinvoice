<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;

class Autocompleterepeaterstaffs extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	public $field_name;
	public $user_role;
	public $user_id;
	
	function __construct()
	{
		parent::__construct();
		session_start();
		$this->form_name = 'Autocompleterepeaterstaffs';
		$this->field_name = 'autocompleterepeaterstaffs';
		$this->load->library('form_validation');
		$this->load->model('Advertcategorymodel');
		$this->load->model('PtBlockStaffsModel');
		$this->lang->load('msg');

		$this->user_role = $this->session->userdata("us_role");
		$this->user_id 	 = $this->session->userdata("us_id");

		// Create a new Capsule instance
		$capsule = new Capsule;

		// Configure the database connection
		$capsule->addConnection([
		    'driver'   => 'mysql',
		    'host'     => $this->db->hostname,
		    'database' => $this->db->database,
		    'username' => $this->db->username,
		    'password' => $this->db->password,
		    'charset'  => 'utf8',
		    'collation' => 'utf8_unicode_ci',
		    'prefix'   => $this->db->dbprefix,
		]);

		// Set the Capsule instance as the global instance
		$capsule->setAsGlobal();

		// Boot Eloquent ORM
		$capsule->bootEloquent();

		//ini_set('display_errors', 1);
	    //ini_set('display_startup_errors', 1);
	    //error_reporting(E_ALL);
	}

	function get($data){
		return $this->$data;
	}

	function test(){
		echo $this->form_name.' Installed';
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
		$field = $this->db->where('adv_table', $FormsTablesmodel->ft_database_table)->limit(1)->order_by('adv_order', 'ASC')->get('advert_fields')->row();
		//echo $this->db->last_query();
		//print_r($field);
 
		$linked_tables = $this->db->where('adv_table', $FormsTablesmodel->ft_database_table)->join('forms_tables', 'ft_id = adv_linkedtableid')->order_by('adv_order', 'ASC')->get('advert_fields')->result();
		//echo $this->db->last_query();
		
		//check all the fields if they are linked to the user table
		foreach ($linked_tables as $l){
			if((int)$this->user_role!==1){
				//if so and its not super admin, only show their own records
				if($l->ft_is_users==1){
					$this->db->where('us_'.$l->adv_column, $this->user_id);
				}
			}
		}
		
		//just start the query here
		$ds = $this->db->like(''.$Searchfield->adv_column, $this->input->get('query'))->get($FormsTablesmodel->ft_database_table)->result_array();
		/*
			foreach $where
		*/
		//echo $this->db->last_query();
		//print_r($ds);
		$responses = [];
		$html = "";

		foreach($ds as $d){
			$response = array();
			$html.='<option value="'.$d['ad_id'].'"';
			if(isset($listing) && is_array($listing)){
				if(array_key_exists('us_'.$f['adv_column'],$listing)){
					if($listing['us_'.$f['adv_column']]==$d['ad_id']){
						$html.=' selected=\"selected\"';
					}
				}
			}
// echo $type;exit;
			// echo $Displayfield->adv_column;exit;
			switch($type){
				case 'normal':
					switch($FormsTablesmodel->ft_database_table){
						default:
							$response['value'] = $d[''.$Displayfield->adv_column];
							$response['key'] = $d['ad_id']; 
						break;
					}
				break;
				case 'search_field':
					switch($FormsTablesmodel->ft_database_table){
						default:
							$response['text'] = $d[''.$Displayfield->adv_column];
							$response['ID'] = $d['ad_id']; 
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

		/*$this->db->simple_query("CREATE TABLE `pt_autocompleterepeaterstaffs` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `table_id` int(11) NOT NULL,
		  `linked_table_id` int(11) NOT NULL,
		  `entry_id` int(11) NOT NULL,
		  `item_id` int(11) NOT NULL,
		  `quantity` int(11) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=latin1");

		$this->db->simple_query("ALTER TABLE `pt_autocompleterepeaterstaffs` ADD PRIMARY KEY (`id`);");*/


		$sql = "CREATE TABLE `pt_block_staffs` (
		 `id` bigint(20) NOT NULL AUTO_INCREMENT,
		 `ad_id` bigint(20) DEFAULT NULL,
		 `ad_blockid` bigint(20) DEFAULT NULL,
		 `ad_staffid` bigint(20) DEFAULT NULL,
		 `created_at` timestamp NULL DEFAULT NULL,
		 `updated_at` timestamp NULL DEFAULT NULL,
		 PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
		$query = $this->db->query($sql);
	}

	//If adding the type of field has different fields that come with it


	//if you're creating a new field type, handle its appearance with this function
	function field($data){
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);
		$field 		= $data['field']; 
		//print_r($field);
		$listing 	= $data['listing']; 

		$adv_config 				= (array)json_decode($field['adv_config']);

		$search_field		= $adv_config['search'];
		$display_field		= $adv_config['display'];

		$data['display_field'] 			= Advertfieldsmodel::findorFail($display_field);
		// $data['display_field_column']	= 'ad_'.$data['display_field']->adv_column;
		$data['display_field_column']	= ''.$data['display_field']->adv_column;

		$FormsTablesmodel = new FormsTablesmodel();
		$table = $FormsTablesmodel::where('ft_database_table', '=', $this->default_table)->first();

		if(!empty($listing)){
			$data['repeater_entries'] = $this->db->where('table_id', $table->ft_id)->where('linked_table_id', $field['adv_linkedtableid'])->where('entry_id', $listing['ad_id'])->where('field_id', $field['adv_id'])->get('autocompleterepeater')->result();
		}else{
			$data['repeater_entries'] = new StdClass();
		}
		$linked_table = NULL;
		$count = 0;
		$data['items_rows'] = [];

		foreach($data['repeater_entries'] as $e){
			if(is_null($linked_table)){
				$linked_table = $FormsTablesmodel::where('ft_id', '=', $e->linked_table_id)->first();
			}
			$array = $this->db->where('ad_id', $e->item_id)->get(str_replace('pt_','', $linked_table->ft_database_table))->row();
			$array->quantity = $e->quantity;
			$array->id = $e->id;
			$data['items_rows'][] = $array;
			$count++;
		}
		/*
		$linked_fields = json_decode($data['field']['adv_linked_fields']);
		foreach($linked_fields as $l){
			$field = $this->Advertcategorymodel->getField($l);
			$data['linked_fields'][] = $field;
		}
		*/
		if(!empty($listing)){
			$adId = $listing['ad_id'];
			$whereArray = array('block_staffs.ad_id' => $adId, 'ad_blockid' => $adId);
			$ptClientUsersData = $this->PtBlockStaffsModel->getData($whereArray);
			$data['linked_fields'] = $ptClientUsersData;
		}
		// _pre($data['linked_fields']);
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
		$FormsTablesmodel = new FormsTablesmodel();
		$table = $FormsTablesmodel::where('ft_database_table', '=', $this->default_table)->first();

		$insert = [];
		
		$item_count = 0;
		foreach($_POST[$f['adv_id'].'_item'] as $item){
			$insert[$item_count]['item_id'] = $item;
			$insert[$item_count]['field_id'] = $f['adv_id'];
			$item_count++;
		}

		$quan_count = 0;
		foreach($_POST[$f['adv_id'].'_quan'] as $quan){
			$insert[$quan_count]['quantity'] 		= $quan;
			$insert[$quan_count]['table_id']		= $table->ft_id;
			$insert[$quan_count]['linked_table_id']	= $f['adv_linkedtableid'];
			$insert[$quan_count]['entry_id']		= $entry_id;
			$quan_count++;
		}
		foreach($insert as $i){
			$this->db->insert('autocompleterepeater', $i);
			//echo $this->db->last_query();
		}
	}

	function update($f, $entry_id){
		//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

		$FormsTablesmodel = new FormsTablesmodel();
		$table = $FormsTablesmodel::where('ft_database_table', '=', $this->default_table)->first();
		//$f['adv_linkedtableid']
		//echo $entry_id.'#'.$table;
		// _pre($table);
		
		/*$this->db->where('table_id', $table->ft_id)->where('linked_table_id', $f['adv_linkedtableid'])->where('field_id', $f['adv_id'])->where('entry_id', $entry_id);
		$this->db->delete('autocompleterepeaterstaffs'); */

		$adId = $entry_id;
		$whereArray = array('ad_id' => $adId, 'ad_blockid' => $adId);
		$this->PtBlockStaffsModel->deleteData($whereArray);

		//echo $this->db->last_query();

		/*$insert = [];
		
		$item_count = 0;
		foreach($_POST[$f['adv_id'].'_item'] as $item){
			$insert[$item_count]['item_id'] = $item;
			$item_count++;
		}

		$quan_count = 0;
		foreach($_POST[$f['adv_id'].'_quan'] as $quan){
			$insert[$quan_count]['quantity'] 			= $quan;
			$insert[$quan_count]['table_id']		= $table->ft_id;
			$insert[$quan_count]['linked_table_id']	= $f['adv_linkedtableid'];
			$insert[$quan_count]['entry_id']		= $entry_id;
			$insert[$quan_count]['field_id'] 		= $f['adv_id'];

			$quan_count++;
		}
		//print_r($insert);
		foreach($insert as $i){
			$this->db->insert('autocompleterepeater', $i);
			//echo $this->db->last_query();
		}*/

		$ptBlockStaffsCrud = array();
		//print_r($_POST);
		//echo $f['adv_id']."_".$f['adv_column'].'#';
		$postArray1 = $this->input->post($f['adv_id']."_".$f['adv_column']);

		//print_r($postArray1);
		//exit();

		if(is_array($postArray1)){
			foreach ($postArray1 as $key => $valueUserId) {
				$tempCrud = array(
					'ad_id' => $adId,
					'ad_blockid' => $adId,
					'ad_staffid' => $valueUserId,
					'created_at' => date('Y-m-d H:i:s'),
				);
				$ptBlockStaffsCrud[] = $tempCrud;
			}
		}
		if(!empty($ptBlockStaffsCrud)){
			$this->PtBlockStaffsModel->insertBatch($ptBlockStaffsCrud);
		}
		
		return true;
	}

	function remove(){
		$repeater_id = $this->input->post('repeater_id');
		$item_id = $this->input->post('item_id');

		$ptClientUsersId = $repeater_id;
		$adUserid = $item_id;

		$whereArray = array('id' => $ptClientUsersId, 'ad_staffid' => $adUserid);
		$this->PtBlockStaffsModel->deleteData($whereArray);

		/*$this->db->where('id', $repeater_id)->where('item_id', $item_id);
		$this->db->delete('autocompleterepeater');*/

		$returnArray = array("status" => true);
		echo json_encode($returnArray);
		exit;
	}

	//if you're creating a new field type, handle its appearance with this function
	function field_pdf($data, $data_only = false){
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);
		$field 		= $data['field']; 
		$listing 	= $data['listing']; 

		$adv_config 				= (array)json_decode($field['adv_config']);

		$search_field		= $adv_config['search'];
		$display_field		= $adv_config['display'];

		$data['display_field'] 			= Advertfieldsmodel::findorFail($display_field);
		$data['display_field_column']	= 'ad_'.$data['display_field']->adv_column;

		$FormsTablesmodel = new FormsTablesmodel();
		$table = $FormsTablesmodel::where('ft_database_table', '=', $this->default_table)->first();

		$data['repeater_entries'] = $this->db->where('table_id', $table->ft_id)->where('linked_table_id', $field['adv_linkedtableid'])->where('entry_id', $listing['ad_id'])->get('autocompleterepeater')->result();

		$linked_table = NULL;
		$count = 0;
		$data['items_rows'] = [];

		foreach($data['repeater_entries'] as $e){
			if(is_null($linked_table)){
				$linked_table = $FormsTablesmodel::where('ft_id', '=', $e->linked_table_id)->first();
			}
			$array = $this->db->where('ad_id', $e->item_id)->get(str_replace('pt_','', $linked_table->ft_database_table))->row();
			$array->quantity = $e->quantity;
			$array->id = $e->id;
			$data['items_rows'][] = $array;
			$count++;
		}
		/*
		$linked_fields = json_decode($data['field']['adv_linked_fields']);
		foreach($linked_fields as $l){
			$field = $this->Advertcategorymodel->getField($l);
			$data['linked_fields'][] = $field;
		}
		*/
		if($data_only){
			return $data;
		}else{
			$this->load->view('field_pdf', $data);
		}
	}

	
}